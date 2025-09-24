# POS System - Point of Sale Web Application

A modern Point of Sale (POS) web application built with Laravel (backend API) and Nuxt.js (frontend), using MySQL as the database. This system provides comprehensive retail management features including inventory control, sales processing, customer management, and reporting.

## Features

### 🛍️ Sales & Checkout
- Intuitive POS interface with product search and barcode scanning
- Shopping cart management with quantity adjustments
- Multiple payment methods (Cash, Card, Digital Wallet, Bank Transfer)
- Tax and discount calculations
- Receipt generation and order completion

### 📦 Inventory Management
- Product catalog with categories, images, and detailed information
- Stock tracking and low inventory alerts
- Barcode/SKU-based product identification
- Bulk product import/export capabilities

### 👥 Customer Management
- Customer profiles with purchase history
- Loyalty tracking and customer analytics
- Quick customer lookup during checkout

### 📊 Reporting & Analytics
- Sales reports (daily, weekly, monthly)
- Inventory reports and stock alerts
- Customer analytics and insights
- Real-time dashboard with key metrics

### 🔐 User Management
- Secure authentication with JWT tokens
- Role-based access control
- Multi-user support for different cashiers

## Technology Stack

### Backend (Laravel API)
- **Laravel 10** - PHP framework for robust API development
- **MySQL** - Relational database for data storage
- **Laravel Sanctum** - API token authentication
- **RESTful API** - Clean and standardized API endpoints

### Frontend (Nuxt.js)
- **Nuxt.js 3** - Vue.js framework for modern web applications
- **Vue 3** - Reactive JavaScript framework
- **Tailwind CSS** - Utility-first CSS framework for styling
- **Pinia** - State management for Vue applications
- **Axios** - HTTP client for API communication

## Prerequisites

Before setting up the POS system, ensure you have the following installed:

- **PHP 8.1 or higher**
- **Composer** (PHP dependency manager)
- **Node.js 16+ and npm** (for frontend dependencies)
- **MySQL 5.7 or higher**
- **Web server** (Apache, Nginx, or PHP built-in server)

### Windows Installation Links
- PHP: https://windows.php.net/download/
- Composer: https://getcomposer.org/download/
- Node.js: https://nodejs.org/ (download LTS version)
- MySQL: https://dev.mysql.com/downloads/mysql/
- Alternatively, use XAMPP: https://www.apachefriends.org/ (includes PHP, MySQL, Apache)

## Installation & Setup

### 1. Clone or Extract the Project
```bash
cd directory\pos-system
```

### 2. Backend Setup (Laravel API)

Navigate to the backend directory:
```bash
cd backend
```

Install PHP dependencies:
```bash
composer install
```

Create environment file:
```bash
copy .env.example .env
```

Edit the `.env` file with your database credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_system
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Generate application key:
```bash
php artisan key:generate
```

Create the database:
```bash
mysql -u your_username -p
CREATE DATABASE pos_system;
```

Run database migrations:
```bash
php artisan migrate
```

Seed the database (optional - creates sample data):
```bash
php artisan db:seed
```

Start the Laravel development server:
```bash
php artisan serve
```
The API will be available at `http://localhost:8000`

### 3. Frontend Setup (Nuxt.js)

Open a new terminal and navigate to the frontend directory:
```bash
cd C:\\Users\\Dimas12\\pos-system\\frontend
```

Install Node.js dependencies:
```bash
npm install
```

Create environment file:
```bash
copy .env.example .env
```

Edit the `.env` file if needed:
```
NUXT_PUBLIC_API_BASE=http://localhost:8000/api
```

Start the Nuxt.js development server:
```bash
npm run dev
```
The frontend will be available at `http://localhost:3000`

## Usage

### 1. Access the Application
Open your web browser and navigate to `http://localhost:3000`

### 2. Register/Login
- Register a new account or login with existing credentials
- The system will redirect you to the dashboard after successful authentication

### 3. Using the POS Interface
- Navigate to the POS section
- Search for products using name, SKU, or barcode
- Add items to cart by clicking on products
- Adjust quantities using +/- buttons
- Set tax amount, discounts, and payment method
- Complete the sale by clicking "Complete Sale"

### 4. Managing Inventory
- Go to Products section to add, edit, or remove products
- Create categories to organize your inventory
- Set stock levels and low stock thresholds
- Upload product images for better identification

### 5. Customer Management
- Add customer information for tracking purchases
- View customer purchase history and analytics
- Select customers during checkout for personalized service

### 6. Viewing Reports
- Access the Reports section for sales analytics
- View daily, weekly, and monthly sales reports
- Monitor inventory levels and low stock alerts
- Track customer behavior and preferences

## API Endpoints

### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login user
- `POST /api/auth/logout` - Logout user
- `GET /api/auth/user` - Get authenticated user

### Products
- `GET /api/products` - List products with filters
- `POST /api/products` - Create new product
- `GET /api/products/{id}` - Get specific product
- `PUT /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product
- `GET /api/products/search/query` - Search products

### Orders/Sales
- `GET /api/orders` - List orders
- `POST /api/orders` - Create new order/sale
- `GET /api/orders/{id}` - Get specific order
- `POST /api/orders/{id}/complete` - Complete order
- `POST /api/orders/{id}/cancel` - Cancel order

### Customers
- `GET /api/customers` - List customers
- `POST /api/customers` - Create new customer
- `GET /api/customers/{id}` - Get specific customer
- `PUT /api/customers/{id}` - Update customer

### Reports
- `GET /api/sales/summary` - Sales summary by period
- `GET /api/sales/daily` - Daily sales report

## Database Schema

### Core Tables
- `users` - System users (cashiers, managers)
- `categories` - Product categories
- `products` - Product inventory
- `customers` - Customer information
- `orders` - Sales transactions
- `order_items` - Individual items in orders

## Customization

### Adding New Features
1. **Backend**: Create new controllers, models, and routes in Laravel
2. **Frontend**: Add new pages, components, and store modules in Nuxt.js
3. **Database**: Create migrations for new tables or modifications

### Styling Customization
- Modify `frontend/assets/css/main.css` for custom styles
- Use Tailwind CSS classes throughout the Vue components
- Customize color schemes and branding as needed

## Security Features

- JWT-based API authentication
- Input validation and sanitization
- SQL injection prevention through Eloquent ORM
- CORS protection
- Rate limiting on API endpoints

## Support & Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Verify MySQL is running
   - Check database credentials in `.env` file
   - Ensure database exists

2. **API Not Responding**
   - Confirm Laravel server is running on port 8000
   - Check for PHP errors in the console
   - Verify `.env` configuration

3. **Frontend Build Issues**
   - Clear node_modules and reinstall: `rm -rf node_modules && npm install`
   - Check Node.js version compatibility
   - Verify API base URL in Nuxt configuration

### Performance Optimization

- Enable database indexing for frequently queried fields
- Implement caching for product catalogs and categories
- Optimize images and assets for faster loading
- Use pagination for large datasets

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request with detailed description

## License

This project is open-source and available under the MIT License.

## Support

For support, feature requests, or bug reports, please create an issue in the project repository or contact the development team.

---


**Built with ❤️ for modern retail businesses**
