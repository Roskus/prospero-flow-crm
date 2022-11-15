# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
- WIP: New feature "Web Forms" will be accessible from Settings
- New migration for web_form table, run: ```bash php artisan migrate```
- Promote Lead to Customer working
- We add custom console commands:
- Improve database, add migration, order relation with company and customer

Create a new company
  ```bash
  company:create
  ```
Create a new user for a company
  ```bash
  user:create
  ```

### 2022-11-15 Update Laravel version
```bash
composer install
composer dump-autoload
php artisan migrate
```

