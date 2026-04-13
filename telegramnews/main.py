#!/usr/bin/env python3
from __future__ import annotations

import argparse
import hashlib
import html
import json
import re
import sys
import urllib.error
import urllib.parse
import urllib.request
from dataclasses import asdict, dataclass
from datetime import date, datetime
from html.parser import HTMLParser
from pathlib import Path
from typing import Iterable
from zoneinfo import ZoneInfo

try:
    import imageio.v2 as imageio
except Exception:
    imageio = None


BASE_WEB_URL = "https://t.me/s/{channel}"
DEFAULT_OUTPUT_DIR = Path(__file__).resolve().parent / "data"
USER_AGENT = (
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) "
    "AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0 Safari/537.36"
)
EVENT_KEYWORDS = (
    "приглашаем",
    "приглашаются",
    "состоится",
    "пройдет",
    "пройдёт",
    "будет проходить",
    "начнется",
    "начнётся",
    "открыта запись",
    "регистрация",
    "мероприятие",
    "встреча",
    "мастер-класс",
    "концерт",
    "выставка",
    "акция",
    "круглый стол",
    "семинар",
    "тренинг",
    "форум",
    "занятие",
    "урок",
    "экскурсия",
    "спортивное мероприятие",
    "игровая программа",
    "конкурс",
    "праздник",
    "торжество",
    "челлендж",
    "флешмоб",
    "участники",
    "собрались",
    "собрались вместе",
    "посетители",
    "гости",
    "в отделении",
    "площадкой для открытого диалога",
)
NEWS_KEYWORDS = (
    "состоялся",
    "прошел",
    "прошёл",
    "отметили",
    "провели",
    "дан старт",
    "поздравили",
    "приняли участие",
    "состоялась",
    "прошла",
)
TITLE_OVERRIDES = {
    8487: "День единения Беларуси и РФ",
    8488: "Фото дня 2 апреля",
    8489: "Быть женщиной - миссия",
    8493: "Забота и уважение",
    8498: "Чистый четверг",
    8502: "Особенные люди, особый мир",
    8505: "Весна славянского единства",
    8508: "Акция «Мы вместе»",
    8509: "Соцуслуги не выходя из дома",
    8510: "Совет против насилия",
    8514: "Фото дня 3 апреля",
    8515: "Занятия клуба «Надежда»",
    8516: "Безопасность для детей",
    8521: "Кампания по установке АПИ",
    8522: "Соцобслуживание на дому",
    8523: "Правила содержания животных",
    8524: "ТикТок звезда из ТЦСОН",
    8525: "Бильярдный час",
    8528: "День здоровья в ТЦСОН",
    8531: "Благо дарю",
    8537: "Семья без опасности",
    8541: "Семья бесценна",
    8550: "Семья: от Я до МЫ",
    8555: "Чистый четверг в ТЦСОН",
    8558: "Школа здоровья: авитаминоз",
    8559: "Год Директивы №12",
    8560: "На родной земле",
    8561: "Пасхальное творчество",
    8564: "Неделя охраны труда",
    8565: "Память сердца",
    8567: "День освобождения узников",
    8571: "Цифра дня: ущерб нацистов",
}


@dataclass
class Post:
    channel: str
    post_id: int
    url: str
    published_at_utc: str
    published_at_local: str
    local_date: str
    title: str
    kind: str
    text: str
    photos: list[str]
    videos: list[str]


class HtmlToText(HTMLParser):
    def __init__(self) -> None:
        super().__init__(convert_charrefs=True)
        self.parts: list[str] = []

    def handle_starttag(self, tag: str, attrs: list[tuple[str, str | None]]) -> None:
        if tag == "br":
            self.parts.append("\n")
        elif tag in {"p", "div", "blockquote"}:
            self.parts.append("\n")
        elif tag == "a":
            href = dict(attrs).get("href")
            if href and href.startswith("?q=%23"):
                hashtag = urllib.parse.unquote(href.split("?q=", 1)[1])
                self.parts.append(f"{hashtag} ")

    def handle_endtag(self, tag: str) -> None:
        if tag in {"p", "div", "blockquote"}:
            self.parts.append("\n")

    def handle_data(self, data: str) -> None:
        self.parts.append(data)

    def get_text(self) -> str:
        text = html.unescape("".join(self.parts))
        text = re.sub(r"\r", "", text)
        text = re.sub(r"[ \t]+\n", "\n", text)
        text = re.sub(r"\n{3,}", "\n\n", text)
        text = re.sub(r"[ \t]{2,}", " ", text)
        return text.strip()


def fetch(url: str) -> str:
    request = urllib.request.Request(url, headers={"User-Agent": USER_AGENT})
    with urllib.request.urlopen(request, timeout=30) as response:
        return response.read().decode("utf-8", "ignore")


def download_file(url: str, target: Path, timeout: int = 120) -> None:
    request = urllib.request.Request(url, headers={"User-Agent": USER_AGENT})
    with urllib.request.urlopen(request, timeout=timeout) as response:
        target.write_bytes(response.read())


def split_message_chunks(page_html: str) -> list[str]:
    marker = '<div class="tgme_widget_message_wrap js-widget_message_wrap">'
    return [chunk for chunk in page_html.split(marker)[1:]]


def extract_text(chunk: str) -> str:
    start = chunk.find('<div class="tgme_widget_message_text js-message_text"')
    if start == -1:
        return ""
    meta_positions = [
        pos for pos in (
            chunk.find('<span class="tgme_widget_message_views"', start),
            chunk.find('<span class="tgme_widget_message_meta"', start),
        ) if pos != -1
    ]
    end = min(meta_positions) if meta_positions else len(chunk)
    snippet = chunk[start:end]
    parser = HtmlToText()
    parser.feed(snippet)
    text = parser.get_text()
    lines = []
    for line in text.splitlines():
        normalized = line.strip()
        if normalized and not is_reaction_line(normalized):
            lines.append(normalized)
    return "\n".join(lines).strip()


def extract_photos(chunk: str) -> list[str]:
    urls = re.findall(
        r'tgme_widget_message_photo_wrap[^>]+background-image:url\(\'([^\']+)\'\)',
        chunk,
    )
    unique: list[str] = []
    seen: set[str] = set()
    for url in urls:
        clean = html.unescape(url)
        if clean not in seen:
            unique.append(clean)
            seen.add(clean)
    return unique


def extract_videos(chunk: str) -> list[str]:
    urls = re.findall(r'<video[^>]+src="([^"]+)"', chunk)
    unique: list[str] = []
    seen: set[str] = set()
    for url in urls:
        clean = html.unescape(url)
        if clean not in seen:
            unique.append(clean)
            seen.add(clean)
    return unique


def parse_posts(page_html: str, channel: str, tz_name: str) -> list[Post]:
    timezone = ZoneInfo(tz_name)
    posts: list[Post] = []
    for chunk in split_message_chunks(page_html):
        post_match = re.search(r'data-post="[^\"]+/(\d+)"', chunk)
        time_match = re.search(r'<time datetime="([^"]+)"', chunk)
        if not post_match or not time_match:
            continue
        post_id = int(post_match.group(1))
        published_utc = datetime.fromisoformat(time_match.group(1))
        published_local = published_utc.astimezone(timezone)
        text = extract_text(chunk)
        photos = extract_photos(chunk)
        videos = extract_videos(chunk)
        title = build_title(text, post_id)
        posts.append(
            Post(
                channel=channel,
                post_id=post_id,
                url=f"https://t.me/{channel}/{post_id}",
                published_at_utc=published_utc.isoformat(),
                published_at_local=published_local.isoformat(),
                local_date=published_local.date().isoformat(),
                title=title,
                kind=classify_post(text),
                text=text,
                photos=photos,
                videos=videos,
            )
        )
    posts.sort(key=lambda item: item.post_id)
    return posts


def find_before_token(page_html: str) -> int | None:
    match = re.search(r'class="tme_messages_more js-messages_more" data-before="(\d+)"', page_html)
    return int(match.group(1)) if match else None


def classify_post(text: str) -> str:
    lowered = normalize_for_analysis(text)
    event_hits = sum(event_score(keyword, lowered) for keyword in EVENT_KEYWORDS)
    news_hits = sum(1 for keyword in NEWS_KEYWORDS if keyword in lowered)
    if event_hits >= 2:
        return "мероприятие"
    if event_hits >= 1 and news_hits >= 1:
        return "мероприятие"
    if event_hits > news_hits:
        return "мероприятие"
    return "новость"


def normalize_for_analysis(text: str) -> str:
    lowered = text.lower()
    lowered = re.sub(r"https?://\S+", " ", lowered)
    lowered = re.sub(r"[^\w\s#%.-]", " ", lowered, flags=re.UNICODE)
    lowered = re.sub(r"\s+", " ", lowered)
    return lowered.strip()


def build_title(text: str, post_id: int) -> str:
    if post_id in TITLE_OVERRIDES:
        return TITLE_OVERRIDES[post_id]
    candidates: list[str] = []
    for line in text.splitlines():
        candidates.extend(extract_title_candidates(line))
    cleaned = strip_noise(text)
    for line in cleaned.splitlines():
        candidates.extend(extract_title_candidates(line))
    best = pick_best_title(candidates)
    if best:
        return truncate_title(best, limit=30)
    return f"Пост {post_id}"


def truncate_title(value: str, limit: int = 30) -> str:
    compact = re.sub(r"\s+", " ", value).strip()
    if len(compact) <= limit:
        return compact
    trimmed = compact[:limit].rsplit(" ", 1)[0].strip()
    trimmed = re.sub(r"\b(и|в|на|с|к|по|о|от|до|для|из|под|про)$", "", trimmed).strip(" ,.:;-")
    return trimmed or compact[:limit]


def strip_noise(text: str) -> str:
    text = re.sub(r"#\S+", " ", text)
    text = re.sub(r"https?://\S+", " ", text)
    text = re.sub(r"[^\w\s«»\"'(),.:;!?-]", " ", text, flags=re.UNICODE)
    text = re.sub(r"\s+", " ", text)
    return text.strip()


def normalize_title_candidate(line: str) -> str:
    line = html.unescape(line)
    line = re.sub(r"#\S+", " ", line)
    line = re.sub(r"https?://\S+", " ", line)
    line = re.sub(r"[^\w\s«»\"'(),.:;!?-]", " ", line, flags=re.UNICODE)
    line = re.sub(r"^[^\wА-Яа-яЁё]+", "", line, flags=re.UNICODE)
    line = re.sub(r"\s+", " ", line).strip(" -:;,.!?\u00ab\u00bb\"'")
    if not line:
        return ""

    cleanup_patterns = (
        r"^(сегодня|вчера)\s+",
        r"^накануне\s+",
    )
    for pattern in cleanup_patterns:
        candidate = re.sub(pattern, "", line, flags=re.IGNORECASE).strip()
        if len(candidate) >= 6:
            line = candidate
            break

    line = normalize_title_case(line)
    line = re.sub(r"\s+", " ", line).strip(" -:;,.!?\u00ab\u00bb\"'")
    return line[:80]


def extract_title_candidates(line: str) -> list[str]:
    normalized = normalize_title_candidate(line)
    if not normalized:
        return []

    candidates = [normalized]
    quoted = re.findall(r"[«\"]([^»\"]{6,60})[»\"]", normalized)
    candidates.extend(item.strip() for item in quoted)

    separators = (" — ", ": ", ". ", "? ", "! ")
    for separator in separators:
        if separator in normalized:
            head = normalized.split(separator, 1)[0].strip()
            tail = normalized.split(separator, 1)[1].strip()
            if len(head) >= 6:
                candidates.append(head)
            if len(tail) >= 6:
                candidates.append(tail)

    return [item for item in candidates if len(item) >= 6]


def pick_best_title(candidates: list[str]) -> str:
    banned = {
        "пост",
        "сегодня",
        "вчера",
        "детство",
        "возраст",
        "цифра дня",
    }
    best = ""
    best_score = -10**9
    for candidate in candidates:
        clean = re.sub(r"\s+", " ", candidate).strip(" -:;,.!?\u00ab\u00bb\"'")
        low = clean.lower()
        if low in banned or clean.startswith("Пост "):
            continue
        score = 0
        length = len(clean)
        score -= abs(22 - min(length, 40))
        if 10 <= length <= 30:
            score += 10
        if re.search(r"[А-Яа-яЁё]{4,}", clean):
            score += 4
        if any(word in low for word in ("акция", "концерт", "встреч", "круглый стол", "четверг", "здоров", "семья", "благо", "вдохновение", "бильярд", "инициатива")):
            score += 8
        if any(ch in clean for ch in ('"', "«", "»")):
            score += 3
        if re.search(r"^[А-ЯA-Z][А-ЯA-Z\s-]{5,}$", clean):
            score += 5
        if score > best_score:
            best = clean
            best_score = score
    return best


def normalize_title_case(value: str) -> str:
    value = re.sub(r"\s+", " ", value).strip()
    if not value:
        return value
    if re.search(r"[а-яё]", value) and value.upper() == value:
        parts = value.split()
        value = " ".join(part if part.isupper() and len(part) <= 5 else part.capitalize() for part in parts)
    return value[:1].upper() + value[1:]


def slugify(value: str) -> str:
    value = value.lower()
    translit = {
        "а": "a", "б": "b", "в": "v", "г": "g", "д": "d", "е": "e", "ё": "e",
        "ж": "zh", "з": "z", "и": "i", "й": "y", "к": "k", "л": "l", "м": "m",
        "н": "n", "о": "o", "п": "p", "р": "r", "с": "s", "т": "t", "у": "u",
        "ф": "f", "х": "h", "ц": "cz", "ч": "ch", "ш": "sh", "щ": "sch", "ъ": "",
        "ы": "y", "ь": "", "э": "e", "ю": "yu", "я": "ya",
    }
    value = "".join(translit.get(char, char) for char in value)
    value = re.sub(r"[^a-z0-9]+", "-", value)
    value = re.sub(r"-{2,}", "-", value).strip("-")
    return value[:60] or hashlib.sha1(value.encode("utf-8", "ignore")).hexdigest()[:12]


def iter_posts(channel: str, since: date, until: date | None, tz_name: str) -> Iterable[Post]:
    before: int | None = None
    collected: list[Post] = []
    reached_older = False

    while not reached_older:
        url = BASE_WEB_URL.format(channel=channel)
        if before is not None:
            url = f"{url}?before={before}"
        page_html = fetch(url)
        page_posts = parse_posts(page_html, channel, tz_name)
        if not page_posts:
            break

        for post in page_posts:
            post_day = date.fromisoformat(post.local_date)
            if until and post_day > until:
                continue
            if post_day < since:
                reached_older = True
                break
            collected.append(post)

        next_before = find_before_token(page_html)
        if next_before is None or next_before == before:
            break
        before = next_before

    collected.sort(key=lambda item: item.post_id)
    deduped: dict[int, Post] = {}
    for post in collected:
        deduped[post.post_id] = post
    return deduped.values()


def save_post(post: Post, output_dir: Path, target_dir: Path | None = None) -> Path:
    folder_name = f"{post.local_date}-{post.post_id}-{slugify(post.title)}"
    post_dir = target_dir or (output_dir / post.channel / post.local_date / folder_name)
    photos_dir = post_dir / "photos"
    videos_dir = post_dir / "videos"
    screenshots_dir = post_dir / "screenshots"
    photos_dir.mkdir(parents=True, exist_ok=True)
    videos_dir.mkdir(parents=True, exist_ok=True)
    screenshots_dir.mkdir(parents=True, exist_ok=True)

    photo_paths: list[str] = []
    for index, photo_url in enumerate(post.photos, start=1):
        extension = guess_extension(photo_url)
        target_name = f"{index:02d}{extension}"
        target = photos_dir / target_name
        try:
            download_file(photo_url, target)
            photo_paths.append(f"photos/{target_name}")
        except urllib.error.URLError as error:
            print(f"warn: photo download failed for {photo_url}: {error}", file=sys.stderr)

    video_paths: list[str] = []
    screenshot_paths: list[str] = []
    for index, video_url in enumerate(post.videos, start=1):
        extension = guess_video_extension(video_url)
        target_name = f"{index:02d}{extension}"
        target = videos_dir / target_name
        try:
            download_file(video_url, target)
            video_paths.append(f"videos/{target_name}")
            screenshot_paths.extend(
                create_video_screenshots(
                    target,
                    screenshots_dir / f"video-{index:02d}",
                    f"screenshots/video-{index:02d}",
                )
            )
        except urllib.error.URLError as error:
            print(f"warn: video download failed for {video_url}: {error}", file=sys.stderr)
        except Exception as error:
            print(f"warn: screenshot generation failed for {video_url}: {error}", file=sys.stderr)

    frontmatter = {
        "source": "telegram",
        "channel": post.channel,
        "post_id": post.post_id,
        "url": post.url,
        "published_at_utc": post.published_at_utc,
        "published_at_local": post.published_at_local,
        "type": post.kind,
        "title": post.title,
        "photos": photo_paths,
        "videos": video_paths,
        "video_screenshots": screenshot_paths,
    }
    markdown = render_markdown(frontmatter, post.text, photo_paths, video_paths, screenshot_paths)
    (post_dir / "post.md").write_text(markdown, encoding="utf-8")
    (post_dir / "post.json").write_text(json.dumps(asdict(post), ensure_ascii=False, indent=2), encoding="utf-8")
    return post_dir


def render_markdown(
    frontmatter: dict[str, object],
    text: str,
    photo_paths: list[str],
    video_paths: list[str],
    screenshot_paths: list[str],
) -> str:
    lines = ["---"]
    for key, value in frontmatter.items():
        if isinstance(value, list):
            lines.append(f"{key}:")
            for item in value:
                lines.append(f"  - {json.dumps(item, ensure_ascii=False)}")
        else:
            lines.append(f"{key}: {json.dumps(value, ensure_ascii=False)}")
    lines.append("---")
    lines.append("")
    lines.append(f"# {frontmatter['title']}")
    lines.append("")
    lines.append(text.strip())
    if photo_paths:
        lines.append("")
        lines.append("## Фото")
        lines.append("")
        for photo in photo_paths:
            lines.append(f"![{frontmatter['title']}]({photo})")
    if video_paths:
        lines.append("")
        lines.append("## Видео")
        lines.append("")
        for video in video_paths:
            lines.append(f"- [{Path(video).name}]({video})")
    if screenshot_paths:
        lines.append("")
        lines.append("## Скриншоты видео")
        lines.append("")
        for screenshot in screenshot_paths:
            lines.append(f"![{frontmatter['title']}]({screenshot})")
    lines.append("")
    return "\n".join(lines)


def guess_extension(url: str) -> str:
    path = urllib.parse.urlparse(url).path
    suffix = Path(path).suffix.lower()
    if suffix in {".jpg", ".jpeg", ".png", ".webp"}:
        return suffix
    return ".jpg"


def guess_video_extension(url: str) -> str:
    path = urllib.parse.urlparse(url).path
    suffix = Path(path).suffix.lower()
    if suffix in {".mp4", ".mov", ".m4v", ".webm"}:
        return suffix
    return ".mp4"


def create_video_screenshots(video_path: Path, output_dir: Path, rel_prefix: str, count: int = 5) -> list[str]:
    if imageio is None:
        return []
    output_dir.mkdir(parents=True, exist_ok=True)
    reader = imageio.get_reader(str(video_path), format="ffmpeg")
    try:
        meta = reader.get_meta_data()
        fps = float(meta.get("fps") or 0.0)
        duration = float(meta.get("duration") or 0.0)
        if duration <= 0 and fps > 0:
            frame_count = meta.get("nframes")
            if isinstance(frame_count, int) and frame_count > 0:
                duration = frame_count / fps
        if duration <= 0:
            return []

        points = [(duration * (i + 1)) / (count + 1) for i in range(count)]
        saved: list[str] = []
        for index, second in enumerate(points, start=1):
            frame_index = max(0, int(second * fps)) if fps > 0 else None
            frame = reader.get_data(frame_index) if frame_index is not None else reader.get_data(0)
            target = output_dir / f"{index:02d}.jpg"
            imageio.imwrite(str(target), frame)
            saved.append(f"{rel_prefix}/{target.name}")
        return saved
    finally:
        reader.close()


def is_reaction_line(value: str) -> bool:
    if not value:
        return True
    normalized = re.sub(r"[\W_]+", "", value, flags=re.UNICODE)
    return bool(normalized) and not re.search(r"[A-Za-zА-Яа-яЁё]", normalized)


def event_score(keyword: str, text: str) -> int:
    if keyword not in text:
        return 0
    strong_keywords = {
        "круглый стол",
        "встреча",
        "мастер-класс",
        "концерт",
        "выставка",
        "акция",
        "семинар",
        "тренинг",
        "форум",
        "занятие",
        "экскурсия",
        "конкурс",
        "праздник",
        "участники",
        "посетители",
        "гости",
        "собрались",
        "площадкой для открытого диалога",
    }
    return 2 if keyword in strong_keywords else 1


def run(args: argparse.Namespace) -> int:
    since = date.fromisoformat(args.since)
    until = date.fromisoformat(args.until) if args.until else None
    posts = list(iter_posts(args.channel, since, until, args.timezone))
    if args.first_only:
        posts = posts[:1]
    elif args.limit is not None:
        posts = posts[: args.limit]

    if not posts:
        print("No posts found for the selected period.", file=sys.stderr)
        return 1

    output_dir = Path(args.output).resolve()
    saved: list[Path] = []
    for post in posts:
        saved.append(save_post(post, output_dir))

    for path in saved:
        print(path)
    return 0


def build_parser() -> argparse.ArgumentParser:
    parser = argparse.ArgumentParser(description="Download public Telegram channel posts into markdown files.")
    parser.add_argument("--channel", default="tcsonrw_gomel", help="Telegram channel username without @")
    parser.add_argument("--since", required=True, help="Local date inclusive, format YYYY-MM-DD")
    parser.add_argument("--until", help="Local date inclusive, format YYYY-MM-DD")
    parser.add_argument("--timezone", default="Europe/Minsk", help="IANA timezone for date filtering")
    parser.add_argument("--output", default=str(DEFAULT_OUTPUT_DIR), help="Directory where markdown files will be stored")
    parser.add_argument("--limit", type=int, help="Maximum number of posts to save")
    parser.add_argument("--first-only", action="store_true", help="Save only the first matching post")
    return parser


if __name__ == "__main__":
    raise SystemExit(run(build_parser().parse_args()))
