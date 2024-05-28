<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>


###### Задача:

>Необходимо стянуть все данные по описанным эндпоинтам и сохранить в БД.

###### Стек:

- Laravel 11.0
- MySQL 5.5.62
- PHP 8.1 и выше

###### Установка проекта

1. Клонируем репозиторий

```bash
git clone https://github.com/KonstantinKliman/test-task-wb-api.git
```

2. Устанавливаем зависимости

```bash
composer install
```

3. Для того, чтобы настроить файл окружения (.env), нужно переименовать файл .env.example в .env. Все настройки базы данных там написаны.

5. Сгенерируем ключ приложения

```bash
php artisan key:generate
```

6. Выполняем миграцию для создания таблиц в базе данных

```bash
php artisan migrate
```

7. Для переноса данных запустите команду

```bash
php artisan fetch:api-data {название сущности} --dateFrom={дата с} --dateTo={дата по} --key={API-ключ} --limit={лимит}
```

- `{название сущности}` - название сущностей во множественном числе: `stocks`,
  `incomes`, `sales`, `orders`.
- `{дата с}` - дата начала выгрузки (в формате `YYYY-MM-DD`)
- `{дата по}` - дата окончания выгрузки (в формате `YYYY-MM-DD`)``
- `{API-ключ}` - ваш API-ключ
- `{лимит}` - лимит для выгрузки записи

Пример успешного запроса:

```bash
php artisan fetch:api-data orders  --dateFrom=2024-05-01 --dateTo=2024-05-28 --key=E6kUTYrYwZq2tN4QEtyzsbEBk3ie --limit=50
```

Ответ

```bash
Orders data fetched successfully
```

###### Доступ к БД через PhpMyAdmin:

[PhpMyAdmin](https://www.phpmyadmin.co/)

Сервер : sql7.freemysqlhosting.net:3306
Пользователь : sql7709747
Пароль : TBRCNt7g8g

###### Названия таблиц

- `stocks`
- `incomes`
- `sales`
- `orders`
