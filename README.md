# 🏟️ Booking API — Сервис бронирования слотов на спортивной площадке

**REST API для бронирования временных слотов на спортивной площадке.**

**Booking API** Позволяет пользователям создавать и управлять бронированиями с несколькими слотами, используя `api_token` для защиты доступа.

---

## 📌 Возможности

✅ Создание бронирований с несколькими временными слотами  
✅ Обновление и добавление слотов  
✅ Проверка временных пересечений по всей системе  
✅ Авторизация через `api_token` (без Passport/Sanctum)  
✅ Поддержка OpenAPI (Swagger)  
✅ Покрытие функциональности `Feature`-тестами

---

## ⚙️ Установка и запуск

### 1. Клонируй репозиторий
```bash
git clone https://github.com/Mikhalych-KRSK/slots-sports-ground-app.git
cd slots-sports-ground-app
```
### 2. Создай `.env`
```bash
cp .env.example .env
```
### 3. Настройка `.env`
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=slots
DB_USERNAME=slots
DB_PASSWORD=secret
DB_OPTIONS="--ssl-mode=DISABLED&allowPublicKeyRetrieval=true"
```
### 4. Установи зависимости
```bash
composer install
npm install && npm run build
```
### 5. Запусти Docker
```bash
docker-compose up -d
```
- MySQL поднимется с базами `slots` и `slots_testing` (см. `init-databases.sql`)

### 6. Сгенерируй ключ и мигрируй базу
```bash
docker exec -it slots-app php artisan key:generate
docker exec -it slots-app php artisan migrate --seed
```

---

## 🧪 Запуск тестов
```bash
docker exec -it slots-app php artisan test
```
- По умолчанию используется база `slots_testing`

---

## 🔐 Авторизация

API использует простую аутентификацию по токену.

📥 В Authorization передаётся токен:

```Authorization: Bearer <api_token>```

🧪 Примеры токенов можно найти в сидерах (`database/seeders`).

---

## 🗂️ Эндпоинты

| Метод  | URI                                    | Описание                                   |
| ------ | -------------------------------------- | ------------------------------------------ |
| GET    | `/api/bookings`                        | Получить список своих бронирований         |
| POST   | `/api/bookings`                        | Создать бронирование с несколькими слотами |
| PATCH  | `/api/bookings/{booking}/slots/{slot}` | Обновить конкретный слот                   |
| POST   | `/api/bookings/{booking}/slots`        | Добавить слот к существующему бронированию |
| DELETE | `/api/bookings/{booking}`              | Удалить бронирование                       |

---

## 🧠 Бизнес-логика
- Один заказ может содержать несколько временных слотов
- Слоты не должны пересекаться:
  - С другими слотами в системе
  - Между собой внутри одного заказа
- Пользователь не может управлять чужими бронированиями

---

## ⚙️ Генерация Swagger-документации
```bash
docker exec -it slots-app php artisan l5-swagger:generate
```

Просмотр документации:
`http://127.0.0.1:8000/api/documentation`

---

## ⚙️ Docker-окружение
- PHP 8.3 (FPM) — `docker/php/Dockerfile`
- MySQL 8 — с автосозданием БД `slots` и `slots_testing`
- Nginx — конфиг: `docker/nginx/default.conf`
- Redis — доступен
- Vite + Vue — фронтенд собирается вручную через `npm run build`

---

## 📦 Стек технологий

- Laravel 10+
- PHP 8.2+
- MySQL 8
- Docker
- Eloquent ORM
- DTO + Сервис-слой
- Middleware авторизация
- Feature-тесты (PHPUnit)
- Swagger / OpenAPI

---

## 👤 Автор
💬 Telegram: `@MIMIKA8`
