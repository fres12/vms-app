# Visitor Management System (VMS)

A web-based Visitor Management System (VMS) built with Laravel.  
This system allows external visitors to register, generate visitor permits, and check in via barcode scanning.

---

## 🚀 Getting Started

### 1. Clone Repository
```bash
git clone https://github.com/your-username/vms.git
cd vms
```
### 2. Install Dependencies
```bash
composer install
npm install
```
### 3. Configure Environment
Copy .env.example to .env and generate the application key:
```bash
cp .env.example .env
php artisan key:generate
```
### 4. Configure mail
Update your mail configuration through .env for automatic vms notification
Example
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=xxxxxx
MAIL_PASSWORD="xxxxxx"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=xxxxxx@example.com
MAIL_FROM_NAME="${APP_NAME}"
```
### 5. Run Database Migration & Seed
```bash
php artisan migrate:fresh --seed
```
### 6. Create Storage Symlink For Upload File Handling
```bash
php artisan storage:link
```
### 7. Build Frontend Assets
```bash
npm run build
```
### 8. Start Local Development Server
```bash
php artisan serve
```
