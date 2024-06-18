# Inventory Management System
## Project Overview

The Inventory Management System is a web application designed to help businesses manage their inventory efficiently. It provides tools for tracking stock levels, managing orders for customer and supplier (adding stocks), and forecasting inventory needs.

## Features

- **Authentication & Authorization:** Secure login system with multi-role authorization. Each role (e.g., system admin, admin, user) has specific permissions tailored to its responsibilities.

- **Inventory Management:** Track and manage products, stocks, suppliers and categories.

- **Order Management:** Handling customer orders, return orders and supplier orders for adding stocks.

- **Shift Management:** Manage shifts with the ability to start and end shifts, and display relevant shift information.

## Technologies Used

- **Backend:** Laravel
- **Frontend:** Vue Js
- **Database:** MySQL
- **Authentication:** Simple Authentication
- **Testing:** PHPUnit

## Installation

1. Clone the repository
```bash
git clone https://github.com/faresmuhammad/ims.git

#using github cli
gh repo clone faresmuhammad/ims

cd ims
```
2. Install Backend & Frontend Dependencies
```bash
composer install && npm install
```
3. Set Environment Variables
```bash
cp .env.example .env

php artisan key:generate
```

4. Run Database Migrations
```bash
php artisan migrate --seed
```

5. Start Development Server
```bash
php artisan serve

npm run dev
```

## Usage

- Access the application at `http://localhost:8000`
- Login using these credentials:
  * **System Admin:** `user: fares` `password: 123`
  * **Admin:** `user: amr` `password: 123`
  * **User:** `user: ahmed` `password: 123`
