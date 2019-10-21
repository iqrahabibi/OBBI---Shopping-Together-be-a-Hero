## API - v2.1
[![pipeline status](https://gitlab.com/obbi-dev/api-obbi-v2.1/badges/master/pipeline.svg)](https://gitlab.com/obbi-dev/api-obbi-v2.1/commits/master)
[![coverage report](https://gitlab.com/obbi-dev/api-obbi-v2.1/badges/master/coverage.svg)](https://gitlab.com/obbi-dev/api-obbi-v2.1/commits/master)

## After Clone
1. php artisan migrate:refresh --seed
2. php artisan passport:install

- Git pull
- cp .env.example .env
- composer install
- php artisan key:generate
- php artisan migrate
- php artisan passport:install
- php artisan storage:link
- npm install
- sudo chown -R www-data:www-data storage/ bootstrap/cache/