
## Backend README

**backend/README.md**
```markdown
# SupportFlow Backend - Customer Support Ticket System API

A robust Laravel-based REST API for managing customer support tickets with authentication, validation, and database integration.

## 🚀 Features

- **RESTful API**: Full CRUD operations for tickets and replies
- **Authentication**: JWT-based authentication using Laravel Sanctum
- **Validation**: Comprehensive request validation with custom error messages
- **Demo System**: Pre-configured demo accounts with role-based access
- **Database**: MySQL with Eloquent ORM and migrations
- **Relationships**: Ticket-User and Ticket-Reply relationships
- **Filtering**: Advanced query filtering and searching
- **Pagination**: Built-in pagination for large datasets
- **CORS**: Configured for frontend integration
- **Error Handling**: Consistent error response format
- **Security**: Protected routes with middleware

## 📋 Prerequisites

- PHP >= 8.1
- Composer
- MySQL >= 5.7
- Node.js & npm (for Laravel Mix)
- Postman (for API testing)

## 🛠️ Tech Stack

- **Framework**: Laravel 10.x
- **Authentication**: Laravel Sanctum
- **Database**: MySQL with Eloquent ORM
- **API**: RESTful with JSON responses
- **Validation**: Laravel Validator
- **CORS**: Fruitcake/laravel-cors
- **Testing**: PHPUnit (optional)

## 📦 Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd backend