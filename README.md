# Product Inventory Management API

A RESTful API for managing products, categories, and suppliers, built with **Laravel 11.x**.

## Features

* Product CRUD
* Category and Supplier relationships
* Product filtering by:

  * Category
  * Price range
  * Stock level
* Pagination
* Laravel Sanctum authentication
* Form Request validation
* API Resources
* Eloquent query scope
* Product accessor and mutator
* Product soft deletes
* Feature tests
* Database migrations and seeders

## Requirements

* PHP 8.2+
* Composer
* MySQL 8+
* Laravel 11.x

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/Connecter95/product-inventory-api.git
cd product-inventory-api
```

### 2. Install dependencies

```bash
composer install
```

### 3. Configure environment

Copy the example environment file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Configure your MySQL database in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=product_inventory
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Run migrations and seeders

```bash
php artisan migrate --seed
```

### 5. Start the development server

```bash
php artisan serve
```

The API will be available at:

```text
http://127.0.0.1:8000
```

## Authentication

The API uses **Laravel Sanctum**.

### Register

```http
POST /api/register
```

Example request:

```json
{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

### Login

```http
POST /api/login
```

Example request:

```json
{
    "email": "test@example.com",
    "password": "password123"
}
```

The response contains an authentication token.

For protected endpoints, send:

```http
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```

### Logout

```http
POST /api/logout
```

Requires authentication.

## Product API

All Product endpoints require Sanctum authentication.

### List Products

```http
GET /api/products
```

### Get Product

```http
GET /api/products/{id}
```

### Create Product

```http
POST /api/products
```

Example:

```json
{
    "category_id": 1,
    "name": "Wireless Mouse",
    "description": "2.4GHz wireless mouse",
    "price": 49.90,
    "stock": 100,
    "supplier_ids": [1, 2]
}
```

### Update Product

```http
PUT /api/products/{id}
```

### Delete Product

```http
DELETE /api/products/{id}
```

Products use soft deletes.

## Filtering

Products can be filtered using query parameters.

### Category

```http
GET /api/products?category_id=1
```

### Minimum price

```http
GET /api/products?min_price=100
```

### Maximum price

```http
GET /api/products?max_price=500
```

### Price range

```http
GET /api/products?min_price=100&max_price=500
```

### Stock level

```http
GET /api/products?min_stock=20
```

### Maximum stock

```http
GET /api/products?max_stock=100
```

### Combined filters

```http
GET /api/products?category_id=1&min_price=100&max_price=500&min_stock=20
```

## Pagination

Products are paginated with 10 records per page.

```http
GET /api/products?page=2
```

## Running Tests

The project uses a separate MySQL testing database.

Configure `.env.testing`:

```env
APP_ENV=testing

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=product_inventory_testing
DB_USERNAME=root
DB_PASSWORD=
```

Create the testing database:

```sql
CREATE DATABASE product_inventory_testing;
```

Run migrations:

```bash
php artisan migrate --env=testing
```

Run all tests:

```bash
php artisan test
```

Run Product API tests only:

```bash
php artisan test --filter=ProductApiTest
```

## Project Structure

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php
│   │       └── ProductController.php
│   ├── Requests/
│   │   ├── StoreProductRequest.php
│   │   └── UpdateProductRequest.php
│   └── Resources/
│       └── ProductResource.php
│
├── Models/
│   ├── Category.php
│   ├── Product.php
│   ├── Supplier.php
│   └── User.php
│
database/
├── factories/
├── migrations/
└── seeders/

routes/
└── api.php

tests/
└── Feature/
    └── ProductApiTest.php
```

## Testing

The Product API includes feature tests covering:

* Listing products
* Creating products
* Showing a product
* Updating products
* Deleting products
* Product validation

## License

This project was created as part of a technical assessment.
