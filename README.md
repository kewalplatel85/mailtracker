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
- 🔧 **Admin Dashboard** - Comprehensive administrative interface with system monitoring
- 🎯 **Auto-fill Customer Data** - Smart customer lookup when entering mailbox numbers
- 🍞 **Toast Notifications** - Modern notification system with visual feedback
- 👥 **Customer Database** - CSV-based customer information management
- 📱 **Responsive Design** - Works seamlessly on desktop and mobile devices
- 🔐 **Secure Authentication** - User login and access control
- 💬 **Communication System** - SMS/Email notification capabilities (configurable)

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
- 📥 Scan and register incoming packages with auto-fill customer data
- 📊 Track package status with optimized workflow transitions
- 📮 Associate packages with customer mailboxes automatically
- 🔄 Bulk package operations and status management
- 🍞 Real-time toast notifications for all operations

### Admin Dashboard
- 📈 System statistics and health monitoring
- ⚙️ Configuration management and settings
- 📋 Administrative reports and analytics
- 🔧 User management tools
- 🎛️ Accessible via profile dropdown menu

### Customer Management
- 📁 CSV-based customer database with enhanced validation
- 🔢 Automatic mailbox number to customer name mapping
- 📞 Contact information management with phone lookup
- 🔍 Advanced customer search and filtering
- ✨ Auto-fill functionality for efficient data entry

### Storage Label System
- 🏷️ Generate 4"×6" barcode labels with enhanced design
- 🖨️ Print-optimized label layouts
- 📦 Batch label printing capabilities
- 📱 Mobile-friendly label interface

### Communication System
- 📧 Email notification system (primary)
- 📱 SMS integration capabilities (configurable)
- 📝 Custom message templates and personalization
- 📬 Message history and inbox management
- 🍞 Toast notifications for delivery status

## 📱 Usage

### Main Dashboard
- 📁 Upload customer CSV files with enhanced validation
- ⚡ Quick package entry with auto-fill customer data
- 🎯 Smart mailbox number to customer name mapping
- 📊 Real-time package tracking with toast notifications
- 🔍 Efficient customer information lookup

### Admin Dashboard
- 🎛️ Access via profile dropdown menu
- 📈 View system statistics and health metrics
- ⚙️ Manage system settings and configurations
- 📋 Generate administrative reports
- 👥 User management and access control

### Package Management
- 📦 Streamlined package entry with auto-fill
- 🍞 Toast notifications for all operations
- 🔄 Optimized package status transitions
- 📊 Enhanced tracking and lifecycle management
- 🎯 Smart workflow automation

### Communication Center
- 📧 Email notification management (primary)
- 📱 SMS capabilities (when enabled)
- 📝 Custom message templates
- 📬 Notification history and tracking
- 🍞 Real-time delivery status updates

## 🔧 Configuration

### Communication Setup
1. **Email Configuration** (Primary notification method)
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=your-smtp-host
   MAIL_PORT=587
   MAIL_USERNAME=your-email
   MAIL_PASSWORD=your-password
   ```

2. **SMS Setup** (Optional - Twilio integration)
   - Create a Twilio account at [twilio.com](https://www.twilio.com)
   - Get your Account SID and Auth Token
   - Purchase a phone number
   - Add credentials to `.env` file:
   ```env
   TWILIO_SID=your_twilio_sid
   TWILIO_AUTH_TOKEN=your_twilio_token
   TWILIO_PHONE_NUMBER=your_twilio_phone
   ```

### Admin Dashboard Access
- Admin dashboard is accessible from the profile dropdown menu
- Provides system statistics, health monitoring, and configuration options
- Requires appropriate user permissions

### Toast Notifications
- Modern notification system replaces traditional alerts
- Visual feedback for all user actions
- Auto-dismissing with manual close options
- Different types: success, error, warning, info

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
