# API новостей

API позволяет:

- создавать новости и мероприятия
- редактировать существующие записи
- при необходимости создавать запись через `update.php`, если она еще не существует
- загружать фото и одно видео для новости

Endpoint'ы:

- локально: `http://localhost:8090/api/news/create.php`
- локально: `http://localhost:8090/api/news/update.php`
- сервер: `https://tcsonrw-gomel.by/api/news/create.php`
- сервер: `https://tcsonrw-gomel.by/api/news/update.php`

## Авторизация

Рекомендуемый вариант:

- заголовок `X-Service-Token: <токен>`

Также поддерживается:

- `Authorization: Bearer <токен>`
- авторизованная сессия сайта с правом `news` и CSRF-токеном

## Формат запроса

- метод: `POST`
- формат: `multipart/form-data`
- фото передаются повторяющимся полем `photos[]`
- видео передается полем `video`

## Общие поля

- `type` - `новость` или `мероприятие`
- `title` - заголовок, от 3 до 60 символов
- `description` - текст новости
- `date` - дата в формате `YYYY-MM-DD`
- `freim` - необязательный iframe/встраиваемый блок
- `slug` - необязательный slug; если не передан, генерируется автоматически
- `photos[]` - одно или несколько изображений `jpg`, `png`, `gif`
- `video` - необязательное видео `mp4`, `webm`, `ogv`

Дополнительно для авторизации сервисным токеном:

- `author_login` - необязательно
- `author_name` - необязательно
- `author_user_id` - необязательно

## Создание новости

`create.php` всегда создает новую запись.

Пример:

```bash
curl -X POST 'http://localhost:8090/api/news/create.php' \
  -H 'X-Service-Token: <your-service-token>' \
  -F 'type=новость' \
  -F 'title=Тестовая новость через API' \
  -F 'description=Проверка загрузки новости через API.' \
  -F 'date=2026-04-12' \
  -F 'author_login=api-bot' \
  -F 'author_name=API Bot' \
  -F 'photos[]=@/absolute/path/to/photo.jpg' \
  -F 'video=@/absolute/path/to/video.mp4'
```

## Редактирование новости

`update.php` сначала ищет запись для изменения:

- по `id`
- или по `lookup_slug`
- или по `current_slug`
- если `id` не передан, может использовать `slug` как ключ поиска

Можно передавать как полный набор полей, так и только изменяемые поля.

Дополнительные поля для редактирования:

- `photos_to_delete[]` - ID фотографий, которые нужно удалить
- `photo_sequence[]` - порядок фото в формате `existing:17` или `new:client-id`
- `new_photo_client_ids[]` - client id для новых фото, если нужно задать точный порядок
- `delete_video=1` - удалить текущее видео
- `create_if_missing=1` - если запись не найдена, создать новую

Пример обновления существующей новости:

```bash
curl -X POST 'http://localhost:8090/api/news/update.php' \
  -H 'X-Service-Token: <your-service-token>' \
  -F 'id=123' \
  -F 'title=Обновленный заголовок' \
  -F 'description=Новый текст новости' \
  -F 'video=@/absolute/path/to/new-video.mp4'
```

Пример удаления видео у существующей новости:

```bash
curl -X POST 'http://localhost:8090/api/news/update.php' \
  -H 'X-Service-Token: <your-service-token>' \
  -F 'id=123' \
  -F 'delete_video=1'
```

Пример сценария "обновить, а если нет - создать":

```bash
curl -X POST 'http://localhost:8090/api/news/update.php' \
  -H 'X-Service-Token: <your-service-token>' \
  -F 'lookup_slug=telegram-post-8531' \
  -F 'create_if_missing=1' \
  -F 'type=новость' \
  -F 'title=Импорт из Telegram' \
  -F 'description=Текст публикации' \
  -F 'date=2026-04-12'
```

## Успешный ответ

Оба endpoint'а возвращают JSON вида:

```json
{
  "ok": true,
  "data": {
    "operation": "created",
    "id": 123,
    "type": "новость",
    "title": "Тестовая новость через API",
    "slug": "testovaya-novost-cherez-api",
    "description": "Проверка загрузки новости через API.",
    "freim": "",
    "date": "2026-04-12",
    "url": "/news/testovaya-novost-cherez-api",
    "public_url": "http://localhost:8090/news/testovaya-novost-cherez-api",
    "photo_count": 1,
    "photos": [
      {
        "id": 456,
        "filename": "generated-file.jpg",
        "sort_order": 1,
        "url": "/photos/generated-file.jpg",
        "public_url": "http://localhost:8090/photos/generated-file.jpg"
      }
    ],
    "video": {
      "filename": "generated-video.mp4",
      "url": "/videos/generated-video.mp4",
      "public_url": "http://localhost:8090/videos/generated-video.mp4"
    },
    "has_video": true,
    "created_by": {
      "user_id": null,
      "login": "api-bot",
      "name": "API Bot"
    },
    "auth_mode": "service_token"
  },
  "error": null
}
```

Для `update.php` поле `operation` будет:

- `updated`, если запись изменена
- `created`, если запись была создана через `create_if_missing=1`

Если видео нет:

- `video: null`
- `has_video: false`

## Ошибки

- `403 access_denied` - неверный токен или нет прав
- `404 not_found` - запись для `update.php` не найдена и `create_if_missing` не передан
- `422 validation_error` - не прошла проверка полей
- `500 internal_error` - ошибка сервера
