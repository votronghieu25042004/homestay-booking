# Homestay Booking System

A modern homestay and room booking management system built with Laravel, Vue 3, and Vite. This project provides a seamless experience for both administrators managing their properties and customers looking for high-quality stays.

## 🚀 Key Features

- **Dynamic Room Management**: Administrators can manage homestays, buildings, and individual rooms with ease.
- **Advanced Booking System**: Real-time room status tracking (available, booked, in-use, maintenance).
- **Voucher & Reward Points**: integrated voucher loyalty system and gamified reward points for customers.
- **Reviews & Ratings**: Secure customer feedback system with admin moderation.
- **Automated Notifications**: Database-driven notifications for new bookings, reviews, and status updates.
- **Performance Optimized**: Database indexing, eager loading, and API Resources for fast responses.
- **Security Hardened**: Rate limiting on sensitive routes, input sanitization, and strict CORS configuration.

## 🛠️ Tech Stack

- **Backend**: Laravel 10
- **Frontend**: Vue 3 + Vite + Tailwind CSS 4
- **Database**: MySQL / MariaDB
- **Icons**: Lucide Vue Next
- **Charts**: Chart.js / Vue-Chartjs
- **Animations**: Motion

## 📦 Installation & Setup

### 1. Prerequisites
- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL / MariaDB

### 2. Clone the Repository
```bash
git clone <repository-url>
cd homestay-booking
```

### 3. Backend Setup
```bash
composer install
cp .env.example .env
# Update .env with your database credentials
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

### 4. Frontend Setup
```bash
npm install
```

### 5. Start the Project
In two separate terminals:
```bash
# Terminal 1: Laravel Server
php artisan serve

# Terminal 2: Vite Dev Server
npm run dev
```

## 🔐 Security Configuration

### API Rate Limiting
The system implements strict rate limiting:
- **Login**: 5 attempts per minute per IP.
- **Register**: 3 attempts per minute per IP.
- **General API**: 100 requests per minute.

### Production CORS
Update your `.env` file to include the allowed frontend URL:
```env
FRONTEND_URL=https://your-domain.com
```

## 📄 API Documentation

Comprehensive API documentation is available in the `docs/` directory.
- **Postman Collection**: [docs/api_collection.json](./docs/api_collection.json)

## 👤 Guides

- [Admin Management Guide](./docs/ADMIN_GUIDE.md)
- [Customer User Guide](./docs/USER_GUIDE.md)
- [Project Presentation Summary](./docs/PROJECT_SUMMARY.md)

## 📝 License

This project is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
