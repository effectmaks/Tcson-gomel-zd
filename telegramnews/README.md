# Telegram News Export

Скрипт выгружает посты из публичного Telegram-канала в `md` вместе с фотографиями.

Что делает:

- читает публичную web-ленту `https://t.me/s/<channel>`
- фильтрует посты по локальной дате
- определяет тип: `новость` или `мероприятие`
- придумывает краткий заголовок по тексту поста
- сохраняет `post.md`, `post.json` и фотографии

Старт:

```bash
cd /Users/leo/VS/TCSONS/102.Gomel\ ZhD/telegramnews
python3 main.py --since 2026-04-02 --channel tcsonrw_gomel --first-only
```

Примеры:

```bash
python3 main.py --since 2026-04-02 --until 2026-04-02
python3 main.py --since 2026-04-02 --limit 10
python3 main.py --since 2026-04-02 --output ./data
```

Структура результата:

```text
data/<channel>/<date>/<date>-<post_id>-<slug>/
  post.md
  post.json
  photos/
```

Ограничения:

- скрипт работает только с публичными каналами, доступными через `t.me/s/...`
- если Telegram изменит HTML-разметку, парсер нужно будет подправить
