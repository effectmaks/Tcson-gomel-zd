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

## Загрузка на сайт через API

Для отправки уже скачанных публикаций на сайт используется скрипт `upload_news_api.py`.

Что делает:

- читает `post.json` из `data/<channel>/<date>/...`
- формирует slug вида `tg-<post_id>-<slug>`
- очищает описание от служебных строк вроде `VIEW IN TELEGRAM`
- загружает фото и первое видео, если оно есть
- для постов с видео добавляет локальные скриншоты в фотогалерею
- работает через `update.php` + `create_if_missing=1`
- при повторном запуске синхронизирует фотогалерею с локальной папкой и не создает дубль записи

### Подготовка

```bash
cd /Users/leo/VS/TCSONS/102.Gomel\ ZhD/telegramnews
TOKEN='<вставь X-Service-Token>'
```

По умолчанию скрипт отправляет записи на:

```text
https://tcsonrw-gomel.by/api/news/update.php
```

### Основная команда

Загрузить только мероприятия:

```bash
python3 upload_news_api.py --token "$TOKEN"
```

### Полезные варианты

Загрузить все публикации:

```bash
python3 upload_news_api.py --token "$TOKEN" --kind all
```

Загрузить все публикации, кроме одной или нескольких дат:

```bash
python3 upload_news_api.py --token "$TOKEN" --kind all --exclude-dates 2026-04-11
```

Загрузить только конкретные post id:

```bash
python3 upload_news_api.py --token "$TOKEN" --post-ids 8587 8599 8603
```

Загрузить только новости:

```bash
python3 upload_news_api.py --token "$TOKEN" --kind новость
```

Загрузить в другой endpoint:

```bash
python3 upload_news_api.py \
  --token "$TOKEN" \
  --endpoint "https://tcsonrw-gomel.by/api/news/update.php"
```

Указать автора, который будет записан в API:

```bash
python3 upload_news_api.py \
  --token "$TOKEN" \
  --kind all \
  --author-login "usernews" \
  --author-name "Telegram Import"
```

Если нужно оставить старые фото на сайте и просто добавить новые поверх существующих:

```bash
python3 upload_news_api.py --token "$TOKEN" --kind all --append-gallery
```

Если для видеопостов не нужно добавлять скриншоты в галерею:

```bash
python3 upload_news_api.py --token "$TOKEN" --kind all --skip-video-screenshots
```

### Параметры

- `--token` - обязательный `X-Service-Token`
- `--kind` - `мероприятие`, `новость` или `all`
- `--post-ids` - список конкретных `post_id`
- `--exclude-dates` - даты, которые нужно пропустить, формат `YYYY-MM-DD`
- `--channel` - имя каталога канала, по умолчанию `tcsonrw_gomel`
- `--data-dir` - базовая папка с данными, по умолчанию `./data`
- `--endpoint` - URL API для загрузки
- `--author-login` - логин автора для API
- `--author-name` - имя автора для API
- `--append-gallery` - не удалять существующие фото на сайте перед загрузкой
- `--skip-video-screenshots` - не добавлять локальные скриншоты видео в фотогалерею

### Что выводит скрипт

Для каждой записи выводится строка:

```text
post_id    local_date    operation    id    public_url
```

Где:

- `operation = created` - запись создана
- `operation = updated` - существующая запись обновлена

### Типовой сценарий

Сначала скачать посты из Telegram:

```bash
python3 main.py --since 2026-04-13 --channel tcsonrw_gomel
```

Потом загрузить их на сайт, исключив уже обработанную дату:

```bash
python3 upload_news_api.py --token "$TOKEN" --kind all --exclude-dates 2026-04-11
```
