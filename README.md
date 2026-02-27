# Сервис коротких ссылок + QR

Сервис для сокращения URL-ссылок с генерацией QR-кодов.

## Стек

- **PHP 8.2** + **Yii2 Basic**
- **MySQL 8.0**
- **jQuery** + **Bootstrap 5**
- **Docker** + **Docker Compose**
- QR-коды генерируются локально через `chillerlan/php-qrcode` (без сторонних API)

## Быстрый старт

### Требования

- [Docker](https://www.docker.com/) и [Docker Compose](https://docs.docker.com/compose/)

### Запуск

```bash
git clone <repo-url> url-shortener
cd url-shortener
docker compose up -d --build
```

При первом запуске контейнер `php`:
1. Установит зависимости через Composer
2. Дождётся готовности MySQL
3. Применит миграции
4. Запустит PHP-FPM

После успешного старта сервис доступен по адресу: **http://localhost:8080**

### Остановка

```bash
docker compose down
```

Для полного удаления данных (включая БД):

```bash
docker compose down -v
```

## Как пользоваться

1. Откройте **http://localhost:8080**
2. Вставьте длинный URL в поле ввода
3. Нажмите **OK**
4. Сервис проверит валидность и доступность URL
5. При успехе — отобразится короткая ссылка и QR-код
6. QR-код можно отсканировать камерой телефона
7. Короткую ссылку можно скопировать кнопкой рядом с ней

При переходе по короткой ссылке происходит редирект на оригинальный URL. Каждый переход логируется: IP-адрес посетителя, User-Agent, Referer, время. Счётчик переходов инкрементируется атомарно.

## Структура БД

### Таблица `link`

| Поле          | Тип              | Описание                    |
|---------------|------------------|-----------------------------|
| id            | INT UNSIGNED PK  | Идентификатор               |
| original_url  | VARCHAR(2048)    | Оригинальная ссылка         |
| short_code    | VARCHAR(10) UQ   | Короткий код                |
| clicks_count  | INT UNSIGNED     | Количество переходов        |
| created_at    | DATETIME         | Дата создания               |
| updated_at    | DATETIME         | Дата обновления             |

### Таблица `click_log`

| Поле       | Тип               | Описание                         |
|------------|--------------------|----------------------------------|
| id         | BIGINT UNSIGNED PK | Идентификатор                    |
| link_id    | INT UNSIGNED FK    | Связь с таблицей `link`          |
| ip_address | VARCHAR(45)        | IP-адрес посетителя (IPv4/IPv6)  |
| user_agent | VARCHAR(512)       | User-Agent браузера              |
| referer    | VARCHAR(2048)      | Источник перехода                |
| created_at | DATETIME           | Дата и время перехода            |

## Структура проекта

```
├── assets/              # Asset-бандлы
├── commands/            # Консольные команды
├── config/              # Конфигурация приложения
├── controllers/         # Контроллеры
│   ├── SiteController       — главная страница + AJAX-сокращение
│   └── RedirectController   — редирект по короткой ссылке
├── docker/              # Docker-конфигурация
│   ├── nginx/               — конфиг Nginx
│   └── php/                 — Dockerfile + entrypoint
├── migrations/          # Миграции БД
├── models/              # Модели
│   ├── Link                 — ActiveRecord для ссылок
│   ├── ClickLog             — ActiveRecord для логов переходов
│   └── ShortenForm          — форма валидации URL
├── runtime/             # Логи и кеш (gitignored)
├── views/               # Шаблоны
├── web/                 # Document root (точка входа)
├── docker-compose.yml
├── composer.json
└── yii                  # Консольная точка входа
```

## Конфигурация

Переменные окружения задаются в `docker-compose.yml` или через `.env` файл (см. `.env.example`):

| Переменная              | По умолчанию | Описание                 |
|-------------------------|--------------|--------------------------|
| DB_HOST                 | mysql        | Хост БД                  |
| DB_PORT                 | 3306         | Порт БД                  |
| DB_NAME                 | shortener    | Имя базы данных          |
| DB_USER                 | shortener    | Пользователь БД          |
| DB_PASS                 | secret       | Пароль БД                |
| YII_DEBUG               | true         | Режим отладки            |
| YII_ENV                 | dev          | Окружение (dev/prod)     |
| COOKIE_VALIDATION_KEY   | —            | Ключ валидации cookie    |

## Версии

- PHP: 8.2
- MySQL: 8.0
- Yii2: ~2.0.51
- Bootstrap: 5 (через yii2-bootstrap5)
- jQuery: подключается через yii2-core
- QR: chillerlan/php-qrcode ^4.4
