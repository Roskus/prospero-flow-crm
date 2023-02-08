# Prospect Flow CRM (ex Hammer CRM)
We made a free open source CRM. Our Community Edition (CE) 
- Prospect Flow CRM is based in Laravel 9

## Features
* Multi company (White label)
* Multi language
* REST API

## Setup

### Clone the project:
```terminal
git clone git@github.com:Roskus/prospect-flow-crm.git
```

### Setup docker
```terminal
docker-compose build
docker-compose up -d
```

### Inside the container

```
docker exec -it hammercrm_php_1 /bin/bash
```

Install dependencies:
```
composer install
```
Copy template config
```
cp .env.example .env
```
Edit your .env config file and set language, database
```
php artisan key:generate
```
Run migrations and seeders
```
php artisan migrate
php artisan db:seed
```
Generate JWT Secret
```
php artisan jwt:secret
```

## Demo
![](doc/screenshoot.jpg)
* User: admin@admin.com
* Pass: admin

## API
We will provide a REST API for exchange information with the CRM

API Docs
http://hammercrm.localhost/api/documentation

Regenerate documentation
```bash
php artisan l5-swagger:generate
```

Endpoint:
/api

Some API Endpoint for the full list check the doc:

[POST] /api/auth

[GET] /api/lead

[GET] /api/lead/{id}

[POST] /api/lead

[PUT] /api/lead/{id}

[DELETE] /api/lead/{id}

[GET] /api/customer

## Resources
Icon font Line Awesome
https://icons8.com/line-awesome
