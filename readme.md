# Slotegrator Test Project

Проект парсинга товаров с сайта Alza.cz с использованием Symfony 7.2 и Docker.

## Требования

- Docker и Docker Compose
- Git

## Установка и запуск

1. Клонируйте репозиторий:

```bash
git clone https://github.com/yourusername/slotegrator-test-project.git
cd slotegrator-test-project
```

2. Запустите Docker контейнеры:

```bash
docker compose up -d
```

3. Установите зависимости:

```bash
docker compose exec app composer install
```

4. Выполните миграции:

```bash
docker compose exec app bin/console doctrine:migrations:migrate
```

5. Доступ к приложению:

```bash
http://localhost:8080
```

6. Запуск тестов:

```bash
docker compose exec app composer test
```







