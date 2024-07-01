# Blog Post API

## Технические требования

- Laravel Sail для управления контейнерами Docker.
- PostgreSQL в качестве базы данных.

### Предварительные требования

- Docker
- Docker Compose

### Шаги по установке

1. **Клонируйте репозиторий:**

    ```sh
    git clone https://github.com/nbekposynov/blog-api.git
    cd your-repo-name
    ```

2. **Установите зависимости:**

    Laravel Sail использует Composer для управления зависимостями. 

    ```sh
    composer install
    ```

3. **Создайте файл окружения:**

    Скопируйте `.env.example` в `.env`:

    ```sh
    cp .env.example .env
    ```

4. **Настройте файл окружения:**

    В файле `.env` укажите следующие параметры:

    ```env
        DB_CONNECTION=pgsql
        DB_HOST=blog-api-pgsql-1
        DB_PORT=5432
        DB_DATABASE=laravel
        DB_USERNAME=sail
        DB_PASSWORD=password
    ```

5. **Запустите контейнеры Laravel Sail:**

    ```sh
    ./vendor/bin/sail up -d
    ```

6. **Выполните миграции:**

    ```sh
    ./vendor/bin/sail artisan migrate
    ```

7. **Сгенерируйте ключ приложения:**

    ```sh
    ./vendor/bin/sail artisan key:generate
    ```

### Использование API

API включает следующие эндпоинты:

- **Регистрация пользователя:**

    ```http
    POST /api/register
    ```

    Параметры:
    - `name`: имя пользователя
    - `email`: email пользователя
    - `password`: пароль пользователя
    - `password_confirmation`: подтверждение пароля

- **Аутентификация пользователя:**

    ```http
    POST /api/login
    ```
    Параметры:
    - `email`: email пользователя
    - `password`: пароль пользователя

- **Список постов:**

    ```http
    GET /api/show_posts
    ```

- **Создание поста:**

    ```http
    POST /api/add_post
    ```
    Authorization: BearerToken

  Параметры:
    - `title`: название поста (обязательно)
    - `body`: содержание поста (обязательно)
      

- **Редактирование поста:**

    ```http
    PUT /api/update_post/{id}
    ```
    Authorization: BearerToken

    Параметры:
    - `title`: название поста (обязательно)
    - `body`: содержание поста (обязательно)

- **Удаление поста:**
    Authorization: BearerToken
    ```http
    DELETE /api/delete_post/{id}
    ```
