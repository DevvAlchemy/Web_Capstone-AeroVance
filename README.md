# Web_Capstone

# 🚁 Helicopter Marketplace - Capstone Project

A comprehensive ecommerce platform for buying and selling helicopters, specializing in personal, business, and emergency service aircraft.

## 🎯 Project Overview

This capstone project showcases a full-stack ecommerce solution built with PHP backend and React frontend, designed specifically for the helicopter industry. The platform features an orange-themed design and caters to three main market segments:

- **Personal Use**: Private helicopters for recreational flying
- **Business**: Corporate and commercial helicopters
- **Emergency Services**: Medical, rescue, and law enforcement aircraft

## 🛠️ Technology Stack

### Backend (Current Phase)
- **PHP 8.0+** - Server-side logic and API endpoints
- **MySQL** - Database management
- **Apache/Nginx** - Web server
- **Composer** - Dependency management

### Frontend (Future Phase)
- **React.js** - User interface framework
- **JavaScript ES6+** - Client-side scripting
- **CSS3/SCSS** - Styling with orange theme
- **Bootstrap/Tailwind** - Responsive design framework (if time permits)

## 📁 Project Structure

```
helicopter-marketplace/
├── config/
│   ├── database.php
│   └── config.php
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── functions.php
├── models/
│   ├── Helicopter.php
│   ├── User.php
│   └── Order.php
├── controllers/
│   ├── HelicopterController.php
│   ├── UserController.php
│   └── OrderController.php
├── views/
│   ├── home.php
│   ├── catalog.php
│   └── product.php
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── api/
│   └── endpoints/
├── public/
│   └── index.php
├── vendor/
├── composer.json
└── README.md
```

## 🚀 Features

### Core Functionality
- [ ] User registration and authentication
- [ ] Helicopter catalog with filtering and search
- [ ] Product detail pages with specifications
- [ ] Shopping cart and checkout system
- [ ] Order management and tracking
- [ ] Admin panel for inventory management
- [ ] Category management (Personal, Business, Emergency)

### Advanced Features
- [ ] Live chat support
- [ ] Helicopter comparison tool
- [ ] Financing calculator
- [ ] Virtual helicopter tours (360° views)
- [ ] Maintenance tracking system
- [ ] Pilot certification verification
- [ ] Insurance integration

## 🎨 Design Theme

The platform features a vibrant **orange color scheme** representing:
- Energy and enthusiasm for aviation
- Safety and visibility (important in aviation)
- Innovation and modern technology
- Professional yet approachable brand identity

## 📊 Database Schema

### Main Tables
- `helicopters` - Product catalog
- `users` - Customer and admin accounts
- `orders` - Purchase transactions
- `categories` - Product categorization
- `reviews` - Customer feedback
- `inventory` - Stock management

## 🔧 Installation

### Prerequisites
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Composer
- Apache/Nginx web server

### Setup Steps
1. Clone the repository
```bash
git clone https://github.com/devvalchemy/web_capstone.git
cd web_capstone(will change to Helicoptor name in future)
```

2. Install dependencies
```bash
composer install
```

3. Configure database
```bash
cp config/config.example.php config/config.php
# Edit config.php with your database credentials
```

4. Import database schema
```bash
mysql -u username -p database_name < database/schema.sql
```

5. Set up web server to point to `/public` directory

## 🔑 Environment Variables

Create a `.env` file in the root directory:
```
DB_HOST=localhost
DB_NAME=helicopter_marketplace
DB_USER=your_username
DB_PASS=your_password
STRIPE_SECRET_KEY=your_stripe_key
PAYPAL_CLIENT_ID=your_paypal_id
```


## 📈 Development Roadmap

### Phase 1: Backend Foundation (Current)
- [x] Project structure setup
- [ ] Database design and implementation
- [ ] Core PHP models and controllers
- [ ] Basic CRUD operations
- [ ] User authentication system

### Phase 2: Frontend Integration
- [ ] React.js setup and configuration
- [ ] Component-based architecture
- [ ] API integration
- [ ] Responsive design implementation

### Phase 3: Advanced Features
- [ ] Payment processing
- [ ] Email notifications
- [ ] Search and filtering
- [ ] Admin dashboard

### Phase 4: Optimization & Deployment
- [ ] Performance optimization
- [ ] Security hardening
- [ ] Testing and quality assurance
- [ ] Production deployment


## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👨‍💻 Developer

**Carlito Kabambi**
- GitHub: [@DevvAlchemy](https://github.com/devvalchemy)
- Email: carlito.kabambi@triosstudent.com

## 🙏 Acknowledgments

- Aviation industry professionals for domain expertise
- Open source community for tools and libraries
- Educational institution for project guidance

---

*Built with ❤️ and lots of ☕ for the aviation community*
