# Мини-сервис заметок

SPA на Laravel 13 и Vue 3 с REST API для создания, просмотра, изменения и удаления заметок.

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
