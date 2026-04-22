#!/usr/bin/env python3
from __future__ import annotations

import argparse
import json
import re
import subprocess
import sys
from pathlib import Path

from main import slugify


DEFAULT_DATA_DIR = Path(__file__).resolve().parent / "data"
DEFAULT_ENDPOINT = "https://tcsonrw-gomel.by/api/news/update.php"
NOISE_LINES = {
    "VIEW IN TELEGRAM",
    "Please open Telegram to view this post",
}


def run_curl(command: list[str]) -> dict[str, object]:
    completed = subprocess.run(command, check=False, capture_output=True, text=True)
    if completed.returncode != 0:
        raise RuntimeError(completed.stderr.strip() or f"curl exited with code {completed.returncode}")
    try:
        return json.loads(completed.stdout)
    except json.JSONDecodeError as error:
        raise RuntimeError(f"invalid JSON response: {error}: {completed.stdout[:500]}") from error


def clean_description(text: str) -> str:
    lines: list[str] = []
    for raw_line in text.splitlines():
        line = raw_line.replace("\ufeff", "").strip()
        if not line:
            continue
        if line.startswith("#") and "💥" in line:
            line = line[line.index("💥"):].strip()
        elif line.startswith("#"):
            line = re.sub(r"^(?:#\S+\s*)+", "", line).strip()
        if not line or line in NOISE_LINES:
            continue
        if "Подписывайтесь!" in line:
            continue
        if re.fullmatch(r"[.]+", line):
            continue
        line = re.sub(r"[ \t]+", " ", line).strip()
        if line:
            lines.append(line)
    return "\n".join(lines).strip()


def build_gallery_files(post_dir: Path, include_video_screenshots: bool) -> list[Path]:
    gallery_files = [path for path in sorted((post_dir / "photos").glob("*")) if path.is_file()]
    if include_video_screenshots and any((post_dir / "videos").glob("*")):
        gallery_files.extend(
            path for path in sorted((post_dir / "screenshots").glob("*/*")) if path.is_file()
        )
    return gallery_files


def find_posts(
    data_dir: Path,
    channel: str,
    kind: str,
    post_ids: set[int] | None,
    exclude_dates: set[str] | None,
) -> list[dict[str, object]]:
    result: list[dict[str, object]] = []
    base = data_dir / channel
    for json_path in sorted(base.glob("*/*/post.json")):
        post = json.loads(json_path.read_text(encoding="utf-8"))
        if kind != "all" and post.get("kind") != kind:
            continue
        post_id = int(post["post_id"])
        if post_ids and post_id not in post_ids:
            continue
        local_date = str(post.get("local_date", ""))
        if exclude_dates and local_date in exclude_dates:
            continue
        result.append(
            {
                "meta": post,
                "dir": json_path.parent,
            }
        )
    return result


def fetch_existing_post(endpoint: str, token: str, slug: str) -> dict[str, object] | None:
    command = [
        "curl",
        "-sS",
        "-X",
        "POST",
        endpoint,
        "-H",
        f"X-Service-Token: {token}",
        "-F",
        f"lookup_slug={slug}",
    ]
    payload = run_curl(command)
    if payload.get("ok"):
        return payload
    error_data = payload.get("error") or {}
    if error_data.get("code") == "not_found":
        return None
    raise RuntimeError(f"{error_data.get('code')}: {error_data.get('message')}")


def upload_post(
    endpoint: str,
    token: str,
    author_login: str,
    author_name: str,
    item: dict[str, object],
    sync_gallery: bool,
    include_video_screenshots: bool,
) -> dict[str, object]:
    post = item["meta"]
    post_dir = Path(item["dir"])
    title = str(post["title"]).strip()
    post_id = int(post["post_id"])
    slug = f"tg-{post_id}-{slugify(title)}"
    description = clean_description(str(post.get("text", "")))
    if not description:
        description = title
    gallery_files = build_gallery_files(post_dir, include_video_screenshots)
    existing_payload = fetch_existing_post(endpoint, token, slug) if sync_gallery else None
    existing_photo_ids: list[int] = []
    if existing_payload and existing_payload.get("data"):
        existing_photo_ids = [
            int(photo.get("id", 0))
            for photo in existing_payload["data"].get("photos", [])
            if int(photo.get("id", 0)) > 0
        ]

    command = [
        "curl",
        "-sS",
        "-X",
        "POST",
        endpoint,
        "-H",
        f"X-Service-Token: {token}",
        "-F",
        f"lookup_slug={slug}",
        "-F",
        "create_if_missing=1",
        "-F",
        f"slug={slug}",
        "-F",
        f"type={post['kind']}",
        "-F",
        f"title={title}",
        "-F",
        f"description={description}",
        "-F",
        f"date={post['local_date']}",
        "-F",
        f"author_login={author_login}",
        "-F",
        f"author_name={author_name}",
    ]

    if sync_gallery:
        for photo_id in existing_photo_ids:
            command.extend(["-F", f"photos_to_delete[]={photo_id}"])

    for photo in gallery_files:
        command.extend(["-F", f"photos[]=@{photo}"])

    videos = sorted((post_dir / "videos").glob("*"))
    if videos:
        command.extend(["-F", f"video=@{videos[0]}"])

    payload = run_curl(command)
    if not payload.get("ok"):
        error_data = payload.get("error") or {}
        raise RuntimeError(f"{error_data.get('code')}: {error_data.get('message')}")
    return payload


def build_parser() -> argparse.ArgumentParser:
    parser = argparse.ArgumentParser(description="Upload Telegram posts to the remote news API.")
    parser.add_argument("--token", required=True, help="X-Service-Token for the news API")
    parser.add_argument("--endpoint", default=DEFAULT_ENDPOINT, help="Remote news update endpoint")
    parser.add_argument("--data-dir", default=str(DEFAULT_DATA_DIR), help="Base telegramnews data directory")
    parser.add_argument("--channel", default="tcsonrw_gomel", help="Channel directory name")
    parser.add_argument("--kind", default="мероприятие", help="Post kind to upload or 'all'")
    parser.add_argument("--post-ids", nargs="*", type=int, help="Specific post IDs to upload")
    parser.add_argument("--exclude-dates", nargs="*", default=[], help="Local dates to skip, format YYYY-MM-DD")
    parser.add_argument("--author-login", default="usernews", help="Author login to stamp on created records")
    parser.add_argument("--author-name", default="Telegram Import", help="Author name to stamp on created records")
    parser.add_argument(
        "--append-gallery",
        action="store_true",
        help="Do not delete existing remote photos before upload; append new gallery files instead",
    )
    parser.add_argument(
        "--skip-video-screenshots",
        action="store_true",
        help="Do not add local video screenshots to the remote photo gallery",
    )
    return parser


def main() -> int:
    args = build_parser().parse_args()
    post_ids = set(args.post_ids or [])
    exclude_dates = set(args.exclude_dates or [])
    items = find_posts(Path(args.data_dir), args.channel, args.kind, post_ids or None, exclude_dates or None)
    if not items:
        print("No posts selected.", file=sys.stderr)
        return 1

    failures = 0
    for item in items:
        post = item["meta"]
        try:
            payload = upload_post(
                endpoint=args.endpoint,
                token=args.token,
                author_login=args.author_login,
                author_name=args.author_name,
                item=item,
                sync_gallery=not args.append_gallery,
                include_video_screenshots=not args.skip_video_screenshots,
            )
            data = payload["data"]
            print(
                "\t".join(
                    [
                        str(post["post_id"]),
                        str(post["local_date"]),
                        str(data.get("operation", "")),
                        str(data.get("id", "")),
                        str(data.get("public_url", "")),
                    ]
                )
            )
        except Exception as error:
            failures += 1
            print(f"{post['post_id']}\tERROR\t{error}", file=sys.stderr)
    return 1 if failures else 0


if __name__ == "__main__":
    raise SystemExit(main())
