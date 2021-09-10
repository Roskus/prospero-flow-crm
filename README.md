# Hammer CRM
We made a free open source CRM. Our Community Edition (CE) Hammer CRM is bassed in Laravel 7

## Features
* Multi company
* Multi language

## Setup

* Clone:
* git ```git clone git@github.com:Roskus/hammercrm.git```
* Run: ```composer install```
* Check permisions
* Run ```cp .env.example .env```
* Edit .env set language, database.
* Run ```php artisan key:generate```
* Run ```php artisan migrate```
* Generate JWT Secret ```php artisan jwt:secret```

## Demo
User: admin@admin.com

Pass: admin
