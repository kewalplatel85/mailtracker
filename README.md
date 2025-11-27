# 📮 Mail Tracker System

<div align="center">
  <img src="https://via.placeholder.com/200x200/4F46E5/FFFFFF?text=📮📫" alt="Mail Tracker Logo" width="200"/>
  
  <p><strong>Comprehensive Mailbox Management System</strong></p>
  
  [![PHP Version](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php)](https://www.php.net/)
  [![Laravel](https://img.shields.io/badge/Laravel-11+-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
  [![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.0-06B6D4?style=flat-square&logo=tailwind-css)](https://tailwindcss.com)
  [![License](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](LICENSE)
</div>

## 🎯 Overview

**Mail Tracker** is a modern, efficient mailbox management system designed for mail centers to streamline package tracking, customer communications, and storage operations.

### ✨ Key Features

- 📦 **Package Management** - Track incoming/outgoing packages with full lifecycle management
- 🏷️ **Storage Label Printing** - Generate 4"×6" barcode labels for easy identification
- 💬 **SMS Integration** - Automated customer notifications via Twilio
- 👥 **Customer Database** - CSV-based customer information management
- 📱 **Responsive Design** - Works seamlessly on desktop and mobile devices
- 🔐 **Secure Authentication** - User login and access control

## 🛠️ Technology Stack

- **Backend**: Laravel 11+ (PHP 8.2+)
- **Frontend**: Blade Templates + Tailwind CSS v4
- **Database**: MySQL
- **SMS Service**: Twilio SDK
- **Barcode Generation**: Milon/Barcode
- **Build Tools**: Vite + Laravel Mix

## 🚀 Quick Start

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL
- GD Extension (for barcode generation)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/kewalplatel85/mailtracker.git
   cd mailtracker
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure your `.env` file**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=mailtracker
   DB_USERNAME=your_username
   DB_PASSWORD=your_password

   TWILIO_SID=your_twilio_sid
   TWILIO_AUTH_TOKEN=your_twilio_token
   TWILIO_PHONE_NUMBER=your_twilio_phone
   ```

5. **Database setup**
   ```bash
   php artisan migrate
   ```

6. **Install PHP GD Extension** (Required for barcode generation)
   ```bash
   # Ubuntu/Debian
   sudo apt-get install php8.2-gd
   
   # CentOS/RHEL
   sudo yum install php-gd
   ```

7. **Build assets and start development server**
   ```bash
   npm run dev
   php artisan serve
   ```

## 📋 Features Overview

### Package Management
- Scan and register incoming packages
- Track package status (Incoming/Outgoing)
- Associate packages with customer mailboxes
- Bulk package operations

### Customer Management
- CSV-based customer database
- Mailbox number assignment
- Contact information management
- Customer search and filtering

### Storage Label System
- Generate 4"×6" barcode labels
- Print-optimized label design
- Batch label printing
- Mobile-friendly label interface

### Communication
- Automated SMS notifications
- Custom message templates
- SMS inbox management
- Customer notification history

## 📱 Usage

### Dashboard
- Upload customer CSV files
- Quick package entry with barcode scanning
- Real-time package tracking
- Customer information lookup

### Package Logs
- View all packages by status
- Filter and search packages
- Print storage labels
- Manage package lifecycle

### Storage Labels
- Select mailboxes for label printing
- Generate scannable barcodes
- Print 4"×6" labels for storage boxes
- Support for various label printers

## 🔧 Configuration

### Twilio SMS Setup
1. Create a Twilio account at [twilio.com](https://www.twilio.com)
2. Get your Account SID and Auth Token
3. Purchase a phone number
4. Add credentials to `.env` file

### CSV Format
Customer data should follow this structure:
```
Row 7+: mailbox_number, [empty], [empty], customer_name, phone_number, [additional_fields]
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:
- 📧 Email: support@mailtracker.com
- 📞 Phone: +1 (555) 123-4567
- 🐛 Issues: [GitHub Issues](https://github.com/kewalplatel85/mailtracker/issues)

## 🙏 Acknowledgments

- Laravel Framework for the robust backend foundation
- Tailwind CSS for the modern UI components
- Twilio for reliable SMS delivery
- Milon/Barcode for barcode generation capabilities

---

<div align="center">
  <p>Built with ❤️ by the Mail Tracker Team</p>
</div>
