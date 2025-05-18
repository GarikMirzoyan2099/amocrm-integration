# Laravel Docker Project

Интеграция с сервисом AmoCrm, которая при создании и обновлении сделок/контакнтов в этой сделке, создает примечания с информацией об операции

## 📦 Стек технологий

- Laravel 12
- PHP 8.2
- Nginx
- PostgreSQL
- Docker + Docker Compose

## 🚀 Быстрый старт

### 1. Клонируйте репозиторий

git clone https://github.com/ваш-пользователь/laravel-docker-project.git
cd laravel-docker-project

### 2. Скопируйте .env файл, но его необходимо будет заполнить своими данными аккаунта амо
cp .env.example .env

### 3. Запустите контейнеры
docker-compose up -d --build

### 4. Установите зависимости и выполните миграции
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate

### 5. AmoCRM интеграция
Проект использует OAuth2 для авторизации с AmoCRM.

Убедитесь, что следующие переменные в .env корректны:


AMO_CLIENT_ID=your_client_id
AMO_CLIENT_SECRET=your_client_secret
AMO_REDIRECT_URI=https://your-app.com/amocrm/auth-callback
Для начала авторизации перейдите по URL:

/amo-auth

выдайте права интеграции.
После авторизации токены сохраняются в базе данных.