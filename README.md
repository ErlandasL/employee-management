# 🧑‍💼 Employee Management System

A web-based Employee Management System built with **Symfony 6** and **PHP 8.1+**.

---

## ✅ Requirements

- **PHP** 8.1 or higher  
- **Composer** 2.x  
- **Symfony** 6.x  
- **MySQL** (or compatible DB)  
- **Node.js** (optional, if using Webpack Encore for assets)

---

## 🚀 Setup Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/ErlandasL/employee-management.git
cd employee-management
```

### 2. Install PHP Dependencies
composer install

### 2. Add database path to .env:

### 3. Create and Migrate Database
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate

### 4. Create some departments in database:
php bin/console doctrine:fixtures:load

### 5. Finally start the application:
symfony serve
