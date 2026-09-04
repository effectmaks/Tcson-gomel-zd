#!/usr/bin/env python3
"""Create a small, static, local copy of a public website.

The mirror contains HTML pages, CSS, JavaScript, fonts, and images. It does
not fetch documents, video/audio, API responses, or third-party trackers.
"""

from __future__ import annotations

import hashlib
import io
import logging
import os
import re
import shutil
from collections import deque
from dataclasses import dataclass
from pathlib import Path
from typing import Iterable
from urllib.parse import urldefrag, urljoin, urlsplit, urlunsplit
from urllib.robotparser import RobotFileParser

import requests
from bs4 import BeautifulSoup, Comment
from PIL import Image, UnidentifiedImageError
from rjsmin import jsmin


LOG = logging.getLogger("mirror")
USER_AGENT = "LocalStaticMirror/1.0 (+local authorized archive)"
HTML_SUFFIXES = {"", ".html", ".htm", ".php", ".asp", ".aspx"}
IMAGE_MIME_PREFIX = "image/"
FONT_SUFFIXES = {".woff", ".woff2", ".ttf", ".otf", ".eot"}
SKIP_SCHEMES = {"data", "mailto", "tel", "javascript", "about", "blob"}
CSS_URL = re.compile(r"url\(\s*(['\"]?)(.*?)\1\s*\)", re.IGNORECASE)
CSS_IMPORT = re.compile(r"@import\s+(?:url\()?\s*(['\"])(.*?)\1\s*\)?", re.IGNORECASE)
SAFE_PART = re.compile(r"[^A-Za-z0-9._-]+")


@dataclass(frozen=True)
class Settings:
    target_url: str
    output_dir: Path
    max_pages: int
    max_news: int
    respect_robots: bool
    timeout: int
    image_max_width: int
    image_quality: int
    minify_js: bool

    @classmethod
    def from_env(cls) -> "Settings":
        def env_bool(name: str, default: bool) -> bool:
            return os.getenv(name, str(default)).strip().lower() in {"1", "true", "yes", "on"}

        target = os.getenv("TARGET_URL", "https://verhnedvinsk-tcson.by/").strip()
        if not target.startswith(("https://", "http://")):
            raise ValueError("TARGET_URL must start with http:// or https://")
        return cls(
            target_url=target,
            output_dir=Path(os.getenv("OUTPUT_DIR", "/output")).resolve(),
            max_pages=int(os.getenv("MAX_PAGES", "0")),
            max_news=int(os.getenv("MAX_NEWS", "2")),
            respect_robots=env_bool("RESPECT_ROBOTS", False),
            timeout=int(os.getenv("REQUEST_TIMEOUT", "30")),
            image_max_width=int(os.getenv("IMAGE_MAX_WIDTH", "1600")),
            image_quality=int(os.getenv("IMAGE_WEBP_QUALITY", "72")),
            minify_js=env_bool("MINIFY_JS", True),
        )


class StaticMirror:
    def __init__(self, settings: Settings) -> None:
        self.settings = settings
        self.target = self.canonical_url(settings.target_url)
        self.target_host = urlsplit(self.target).hostname
        self.session = requests.Session()
        self.session.headers.update({"User-Agent": USER_AGENT, "Accept": "text/html,application/xhtml+xml,image/avif,image/webp,image/*,*/*;q=0.8"})
        self.pages: deque[str] = deque([self.target])
        self.queued_pages = {self.target}
        self.queued_news: set[str] = set()
        self.fetched_pages: set[str] = set()
        self.asset_cache: dict[str, str | None] = {}
        self.robots: RobotFileParser | None = None

    @staticmethod
    def canonical_url(url: str) -> str:
        parts = urlsplit(url)
        path = parts.path or "/"
        return urlunsplit((parts.scheme.lower(), parts.netloc.lower(), path, parts.query, ""))

    def is_local_url(self, url: str) -> bool:
        parts = urlsplit(url)
        return parts.scheme in {"http", "https"} and parts.hostname == self.target_host

    def prepare_output(self) -> None:
        self.settings.output_dir.mkdir(parents=True, exist_ok=True)
        for child in self.settings.output_dir.iterdir():
            if child.name != ".gitkeep":
                if child.is_dir():
                    shutil.rmtree(child)
                else:
                    child.unlink()

    def load_robots(self) -> None:
        if not self.settings.respect_robots:
            LOG.warning("Robots rules are disabled. Use this only with the website owner's permission.")
            return
        robots_url = urljoin(self.target, "/robots.txt")
        parser = RobotFileParser()
        parser.set_url(robots_url)
        try:
            response = self.session.get(robots_url, timeout=self.settings.timeout)
            if response.ok:
                parser.parse(response.text.splitlines())
                self.robots = parser
                LOG.info("Loaded robots.txt")
            else:
                LOG.warning("Could not read robots.txt (%s); continuing without its rules", response.status_code)
        except requests.RequestException as exc:
            LOG.warning("Could not read robots.txt (%s); continuing without its rules", exc)

    def allowed_by_robots(self, url: str) -> bool:
        return self.robots is None or self.robots.can_fetch(USER_AGENT, url)

    def safe_relative_path(self, url: str, *, page: bool = False, extension: str | None = None) -> Path:
        parts = urlsplit(url)
        raw_parts = [part for part in parts.path.split("/") if part]
        safe_parts = [self.safe_path_part(part) for part in raw_parts]
        if page:
            suffix = Path(safe_parts[-1]).suffix.lower() if safe_parts else ""
            if not safe_parts or parts.path.endswith("/") or suffix not in HTML_SUFFIXES - {""}:
                safe_parts.append("index.html")
            elif suffix != ".html":
                safe_parts[-1] = f"{safe_parts[-1]}.html"
            if parts.query:
                stem = Path(safe_parts[-1]).stem
                safe_parts[-1] = f"{stem}--q-{self.short_hash(parts.query)}.html"
            return Path(*safe_parts)

        name = safe_parts[-1] if safe_parts else "asset"
        stem = Path(name).stem or "asset"
        ext = extension or Path(name).suffix or ".bin"
        if not ext.startswith("."):
            ext = f".{ext}"
        group = "assets"
        return Path(group, f"{stem}-{self.short_hash(url)}{ext.lower()}")

    def safe_path_part(self, raw_part: str) -> str:
        """Keep every output filename below the filesystem's 255-byte limit."""
        safe_part = SAFE_PART.sub("-", raw_part).strip(".-") or "item"
        if len(safe_part.encode("utf-8")) <= 120:
            return safe_part
        suffix = Path(safe_part).suffix
        reserved = len(suffix) + 14  # two dashes and a 12-character hash
        prefix = safe_part[: max(1, 120 - reserved)].rstrip(".-")
        return f"{prefix}--{self.short_hash(raw_part)}{suffix}"

    @staticmethod
    def short_hash(value: str) -> str:
        return hashlib.sha256(value.encode("utf-8")).hexdigest()[:12]

    def local_reference(self, relative: str) -> str:
        return "/" + relative.replace(os.sep, "/")

    def resolve(self, base_url: str, value: str) -> tuple[str | None, str]:
        value = value.strip()
        if not value or value.startswith("#"):
            return None, value
        absolute, fragment = urldefrag(urljoin(base_url, value))
        parts = urlsplit(absolute)
        if parts.scheme.lower() in SKIP_SCHEMES or not parts.scheme:
            return None, value
        return self.canonical_url(absolute), (f"#{fragment}" if fragment else "")

    def page_local_path(self, url: str) -> str:
        return self.safe_relative_path(url, page=True).as_posix()

    def is_html_candidate(self, url: str) -> bool:
        path = urlsplit(url).path
        suffix = Path(path).suffix.lower()
        return suffix in HTML_SUFFIXES

    @staticmethod
    def is_news_path(url: str) -> bool:
        return "/novosti" in urlsplit(url).path.lower()

    @staticmethod
    def is_news_item(url: str) -> bool:
        return "/novosti/item/" in urlsplit(url).path.lower()

    def queue_page(self, url: str) -> str | None:
        if not self.is_local_url(url) or not self.is_html_candidate(url):
            return None
        if self.is_news_path(url):
            if not self.is_news_item(url) or self.settings.max_news <= 0:
                LOG.info("News section skipped: %s", url)
                return None
            if url not in self.queued_news and len(self.queued_news) >= self.settings.max_news:
                LOG.info("News limit reached; skipped: %s", url)
                return None
            self.queued_news.add(url)
        if not self.allowed_by_robots(url):
            LOG.info("robots.txt skipped page: %s", url)
            return None
        if url not in self.queued_pages and url not in self.fetched_pages:
            self.queued_pages.add(url)
            self.pages.append(url)
        return self.local_reference(self.page_local_path(url))

    def request(self, url: str, redirects_left: int = 5) -> requests.Response | None:
        if not self.allowed_by_robots(url):
            LOG.info("robots.txt skipped resource: %s", url)
            return None
        try:
            response = self.session.get(url, timeout=self.settings.timeout, allow_redirects=False)
            if response.is_redirect:
                location = response.headers.get("location")
                redirect_url = self.canonical_url(urljoin(url, location or ""))
                if not location or not self.is_local_url(redirect_url):
                    LOG.info("Skipped redirect from %s to external URL", url)
                    return None
                if redirects_left <= 0:
                    LOG.warning("Skipped %s (too many redirects)", url)
                    return None
                return self.request(redirect_url, redirects_left - 1)
            response.raise_for_status()
            return response
        except requests.RequestException as exc:
            LOG.warning("Skipped %s (%s)", url, exc)
            return None

    @staticmethod
    def suffix_from_response(response: requests.Response, kind: str) -> str:
        content_type = response.headers.get("content-type", "").split(";", 1)[0].strip().lower()
        if kind == "css":
            return ".css"
        if kind == "js":
            return ".js"
        if content_type == "image/svg+xml":
            return ".svg"
        if content_type in {"image/jpeg", "image/jpg"}:
            return ".jpg"
        if content_type == "image/png":
            return ".png"
        if content_type == "image/gif":
            return ".gif"
        if content_type == "image/webp":
            return ".webp"
        if content_type == "image/x-icon":
            return ".ico"
        if content_type.startswith("font/") or content_type.startswith("application/font"):
            return ".woff2"
        suffix = Path(urlsplit(response.url).path).suffix.lower()
        return suffix or (".bin" if kind == "other" else ".dat")

    def download_asset(self, url: str, kind: str) -> str | None:
        if not self.is_local_url(url):
            return None
        cached = self.asset_cache.get(url, "_not_seen_")
        if cached != "_not_seen_":
            return cached
        self.asset_cache[url] = None
        response = self.request(url)
        if response is None:
            return None
        content_type = response.headers.get("content-type", "").split(";", 1)[0].strip().lower()
        data = response.content
        if kind == "css":
            data = self.rewrite_css(data.decode(response.encoding or "utf-8", errors="replace"), response.url).encode("utf-8")
        elif kind == "js" and self.settings.minify_js:
            try:
                data = jsmin(data.decode(response.encoding or "utf-8", errors="replace")).encode("utf-8")
            except Exception as exc:  # keep the original script if it is non-standard
                LOG.info("Could not minify JavaScript %s (%s)", url, exc)
        suffix = self.suffix_from_response(response, kind)
        relative = self.safe_relative_path(url, extension=suffix)
        destination = self.settings.output_dir / relative
        destination.parent.mkdir(parents=True, exist_ok=True)
        if kind == "image" or content_type.startswith(IMAGE_MIME_PREFIX):
            relative = self.write_optimized_image(data, url, destination, relative)
        else:
            destination.write_bytes(data)
        result = self.local_reference(relative.as_posix())
        self.asset_cache[url] = result
        return result

    def write_optimized_image(self, data: bytes, url: str, destination: Path, relative: Path) -> Path:
        try:
            with Image.open(io.BytesIO(data)) as image:
                if getattr(image, "is_animated", False):
                    raise UnidentifiedImageError("animated image is kept in its original format")
                image.load()
                if image.width > self.settings.image_max_width:
                    height = round(image.height * self.settings.image_max_width / image.width)
                    image.thumbnail((self.settings.image_max_width, height), Image.Resampling.LANCZOS)
                if image.mode not in {"RGB", "RGBA"}:
                    image = image.convert("RGBA" if "transparency" in image.info else "RGB")
                webp_relative = relative.with_suffix(".webp")
                webp_destination = self.settings.output_dir / webp_relative
                webp_destination.parent.mkdir(parents=True, exist_ok=True)
                image.save(webp_destination, "WEBP", quality=self.settings.image_quality, method=6)
                return webp_relative
        except (UnidentifiedImageError, OSError, ValueError) as exc:
            LOG.info("Kept original image %s (%s)", url, exc)
            destination.write_bytes(data)
            return relative

    def rewrite_css(self, css: str, source_url: str) -> str:
        def rewrite_import(match: re.Match[str]) -> str:
            local = self.asset_reference(source_url, match.group(2), "css")
            return f'@import url("{local}")' if local else match.group(0)

        css = CSS_IMPORT.sub(rewrite_import, css)

        def rewrite_url(match: re.Match[str]) -> str:
            value = match.group(2).strip()
            local = self.asset_reference(source_url, value, self.asset_kind(value))
            return f'url("{local}")' if local else match.group(0)

        return CSS_URL.sub(rewrite_url, css)

    @staticmethod
    def asset_kind(value: str) -> str:
        suffix = Path(urlsplit(value).path).suffix.lower()
        if suffix in FONT_SUFFIXES:
            return "font"
        if suffix == ".css":
            return "css"
        return "image"

    def asset_reference(self, base_url: str, value: str, kind: str) -> str | None:
        absolute, fragment = self.resolve(base_url, value)
        if absolute is None or not self.is_local_url(absolute):
            return None
        local = self.download_asset(absolute, kind)
        return f"{local}{fragment}" if local else None

    def rewrite_srcset(self, source_url: str, value: str) -> str:
        candidates: list[str] = []
        for candidate in value.split(","):
            bits = candidate.strip().split()
            if not bits:
                continue
            local = self.asset_reference(source_url, bits[0], "image")
            candidates.append(" ".join([local or bits[0], *bits[1:]]))
        return ", ".join(candidates)

    def rewrite_page(self, html: str, page_url: str) -> str:
        soup = BeautifulSoup(html, "html.parser")
        for comment in soup.find_all(string=lambda value: isinstance(value, Comment)):
            comment.extract()
        for tag in soup.find_all("base"):
            tag.decompose()
        for tag in soup.find_all(["iframe", "object", "embed", "audio", "video"]):
            tag.decompose()
        for tag in soup.find_all("link"):
            rel = {item.lower() for item in (tag.get("rel") or [])}
            href = tag.get("href")
            if not href:
                continue
            if "stylesheet" in rel:
                local = self.asset_reference(page_url, href, "css")
                if local:
                    tag["href"] = local
                else:
                    tag.decompose()
            elif rel & {"icon", "shortcut"}:
                local = self.asset_reference(page_url, href, "image")
                if local:
                    tag["href"] = local
                else:
                    tag.decompose()
            elif "preconnect" in rel or "dns-prefetch" in rel:
                tag.decompose()
        for tag in soup.find_all("script"):
            source = tag.get("src")
            if source:
                local = self.asset_reference(page_url, source, "js")
                if local:
                    tag["src"] = local
                else:
                    tag.decompose()
        for tag in soup.find_all(["img", "source"]):
            if tag.get("src"):
                local = self.asset_reference(page_url, tag["src"], "image")
                if local:
                    tag["src"] = local
                else:
                    tag.decompose()
                    continue
            if tag.get("srcset"):
                tag["srcset"] = self.rewrite_srcset(page_url, tag["srcset"])
            if tag.get("poster"):
                local = self.asset_reference(page_url, tag["poster"], "image")
                if local:
                    tag["poster"] = local
        # Joomla templates on the target use lazy-image attributes such as
        # bg-src instead of a normal img/src. Keep those backgrounds offline too.
        for tag in soup.find_all(True):
            for attribute in ("data-src", "data-original", "data-lazy-src", "bg-src", "data-bg", "data-background"):
                if tag.get(attribute):
                    local = self.asset_reference(page_url, tag[attribute], "image")
                    if local:
                        tag[attribute] = local
            if tag.get("data-srcset"):
                tag["data-srcset"] = self.rewrite_srcset(page_url, tag["data-srcset"])
        for tag in soup.find_all(style=True):
            tag["style"] = self.rewrite_css(tag["style"], page_url)
        for tag in soup.find_all("style"):
            if tag.string:
                tag.string.replace_with(self.rewrite_css(str(tag.string), page_url))
        for tag in soup.find_all("a"):
            href = tag.get("href")
            if not href:
                continue
            absolute, fragment = self.resolve(page_url, href)
            if absolute and self.is_local_url(absolute):
                local = self.queue_page(absolute)
                if local:
                    tag["href"] = f"{local}{fragment}"
                elif self.is_news_path(absolute):
                    # News beyond MAX_NEWS stay available on the original site
                    # instead of becoming broken local links.
                    tag["href"] = f"{absolute}{fragment}"
        return str(soup)

    def download_page(self, url: str) -> bool:
        response = self.request(url)
        if response is None:
            return False
        content_type = response.headers.get("content-type", "").lower()
        if "html" not in content_type:
            LOG.info("Skipped non-HTML page %s (%s)", url, content_type)
            return False
        relative = self.page_local_path(url)
        destination = self.settings.output_dir / relative
        destination.parent.mkdir(parents=True, exist_ok=True)
        html = response.content.decode(response.encoding or "utf-8", errors="replace")
        destination.write_text(self.rewrite_page(html, response.url), encoding="utf-8")
        LOG.info("Saved page %s", url)
        return True

    def run(self) -> None:
        self.prepare_output()
        self.load_robots()
        while self.pages:
            if self.settings.max_pages and len(self.fetched_pages) >= self.settings.max_pages:
                LOG.info("Reached MAX_PAGES=%s", self.settings.max_pages)
                break
            url = self.pages.popleft()
            if url in self.fetched_pages:
                continue
            self.fetched_pages.add(url)
            self.download_page(url)
        LOG.info("Finished: %d pages and %d resources", len(self.fetched_pages), len([item for item in self.asset_cache.values() if item]))


def main() -> None:
    logging.basicConfig(level=os.getenv("LOG_LEVEL", "INFO").upper(), format="%(levelname)s %(message)s")
    StaticMirror(Settings.from_env()).run()


if __name__ == "__main__":
    main()
