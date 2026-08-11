# Мини-сервис заметок

SPA на Laravel 13 и Vue 3 с REST API для создания, просмотра, изменения и удаления заметок.

## Запуск в Docker

```bash
docker compose up --build
```

После успешного запуска:

- приложение: `http://localhost:8080`;
- Swagger UI: `http://localhost:8080/api/docs`;
- OpenAPI-файл: `http://localhost:8080/docs/openapi.yaml`.

Compose запускает Laravel на PHP 8.4, Vue/Vite на Node 18, MySQL 8.4 и внешний
Nginx reverse proxy. Исходники подключены как bind volumes, поэтому изменения PHP,
Vue, Blade и CSS применяются в режиме разработки без пересборки образов.

## Запуск

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
composer run dev
```

Откройте `http://localhost:8000`.

## API

| Метод | URL | Назначение |
| --- | --- | --- |
| GET | `/api/notes` | Список (`per_page` от 1 до 100) |
| POST | `/api/notes` | Создание |
| GET | `/api/notes/{id}` | Просмотр |
| PUT/PATCH | `/api/notes/{id}` | Изменение |
| DELETE | `/api/notes/{id}` | Удаление |

Поля: обязательный `title` (до 255 символов), необязательный `content` (до 10 000 символов).

## Проверка

```bash
composer test
npm run build
```
