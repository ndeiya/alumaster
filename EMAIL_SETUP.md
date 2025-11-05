# AluMaster Email System Setup Guide

This guide will help you set up the professional email system for your AluMaster website using PHPMailer.

## Features

✅ **Professional HTML Email Templates**
- Beautiful, responsive email designs
- Automatic admin notifications for new inquiries
- Auto-reply emails to customers
- Email activity logging

✅ **Multiple Email Provider Support**
- Gmail, Outlook, Yahoo, and custom SMTP
- Secure authentication with app passwords
- TLS/SSL encryption support

✅ **Admin Interface**
- Easy configuration through web interface
- Email testing functionality
- Activity monitoring and logs
- Fallback to basic PHP mail() function

## Quick Setup

### 1. Install PHPMailer

**Option A: Using Composer (Recommended)**
```bash
composer install
```

**Option B: Manual Installation**
```bash
php install-phpmailer.php
```

### 2. Configure Email Settings

1. Copy the example environment file:
```bash
cp .env.example .env
```

2. Edit `.env` with your email credentials:
```env
# For cPanel hosting (recommended)
SMTP_HOST=mail.yourdomain.com
SMTP_PORT=587
SMTP_USERNAME=alumaster75@yourdomain.com
SMTP_PASSWORD=your-email-password
SMTP_ENCRYPTION=tls
FROM_EMAIL=alumaster75@yourdomain.com
FROM_NAME="AluMaster Aluminum System"
ADMIN_EMAIL=alumaster75@yourdomain.com
```

### 3. cPanel Email Setup (Recommended for Hosting)

1. **Create Email Account in cPanel**
   - Login to your cPanel hosting control panel
   - Go to "Email Accounts" section
   - Create: alumaster75@yourdomain.com

2. **Get SMTP Settings**
   - Usually: mail.yourdomain.com
   - Port: 587 (TLS) or 465 (SSL)
   - Username: full email address
   - Password: email account password

3. **Update Configuration**
```env
SMTP_HOST=mail.yourdomain.com
SMTP_PORT=587
SMTP_USERNAME=alumaster75@yourdomain.com
SMTP_PASSWORD=your-email-password
```

### 3b. Gmail Setup (Alternative)

1. **Enable 2-Factor Authentication**
   - Go to your Google Account settings
   - Security → 2-Step Verification → Turn on

2. **Generate App Password**
   - Security → App passwords
   - Select "Mail" and generate password
   - Use this password in your `.env` file

3. **Update Configuration**
```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=alumaster75@gmail.com
SMTP_PASSWORD=your-16-character-app-password
```

### 4. Test Your Setup

**Command Line Test:**
```bash
php test-email.php your-email@example.com
```

**Admin Interface Test:**
1. Visit `/admin/settings/email.php`
2. Configure your settings
3. Use the "Send Test Email" feature

## Email Provider Settings

### cPanel Hosting (Recommended)
```env
SMTP_HOST=mail.yourdomain.com
SMTP_PORT=587
SMTP_ENCRYPTION=tls
```

### Gmail
```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_ENCRYPTION=tls
```

### Outlook/Hotmail
```env
SMTP_HOST=smtp-mail.outlook.com
SMTP_PORT=587
SMTP_ENCRYPTION=tls
```

### Yahoo Mail
```env
SMTP_HOST=smtp.mail.yahoo.com
SMTP_PORT=587
SMTP_ENCRYPTION=tls
```

### Custom SMTP
Contact your hosting provider for specific settings.

## File Structure

```
├── includes/
│   └── mailer.php              # Email service class
├── templates/
│   ├── email-contact-inquiry.html    # Admin notification template
│   └── email-auto-reply.html         # Customer auto-reply template
├── admin/settings/
│   └── email.php               # Admin email configuration
├── logs/
│   └── email.log              # Email activity log
├── .env                       # Email configuration (create from .env.example)
├── composer.json              # PHPMailer dependency
├── install-phpmailer.php      # Manual installation script
└── test-email.php            # Email testing utility
```

## How It Works

### Contact Form Flow
1. Customer submits contact form
2. Inquiry saved to database
3. **Admin notification email** sent to your email
4. **Auto-reply email** sent to customer
5. Activity logged for monitoring

### Email Templates
- **Admin Notification**: Professional email with customer details and inquiry information
- **Auto-Reply**: Branded response thanking customer and providing contact information

### Fallback System
If PHPMailer fails, the system automatically falls back to PHP's built-in `mail()` function to ensure inquiries are never lost.

## Admin Features

### Email Settings Page (`/admin/settings/email.php`)
- Configure SMTP settings
- Test email functionality
- View email activity logs
- Setup instructions and provider guides

### Email Activity Monitoring
- All email activity is logged to `logs/email.log`
- View recent activity in admin interface
- Track successful sends and failures

## Troubleshooting

### Common Issues

**"Authentication failed"**
- Verify username/password are correct
- For Gmail, ensure you're using an App Password, not your regular password
- Check that 2-factor authentication is enabled

**"Connection failed"**
- Verify SMTP host and port settings
- Check firewall/hosting restrictions
- Try different ports (587, 465, 25)

**"PHPMailer not found"**
- Run `composer install` or `php install-phpmailer.php`
- Verify `vendor/autoload.php` exists

### Testing Commands

**Test SMTP Connection:**
```bash
php test-email.php
```

**Check Email Logs:**
```bash
tail -f logs/email.log
```

**Verify Configuration:**
```bash
php -r "
require 'includes/config.php';
if (file_exists('.env')) {
    \$lines = file('.env', FILE_IGNORE_NEW_LINES);
    foreach (\$lines as \$line) {
        if (strpos(\$line, '=')) echo \$line . PHP_EOL;
    }
}"
```

## Security Notes

- Never commit `.env` file to version control
- Use app passwords instead of regular passwords
- Keep PHPMailer updated for security patches
- Monitor email logs for suspicious activity

## Support

If you need help with setup:
1. Check the admin interface at `/admin/settings/email.php`
2. Run the test script: `php test-email.php`
3. Check email logs in `logs/email.log`
4. Verify your email provider's SMTP settings

The system is designed to be robust with automatic fallbacks, so your contact form will work even if advanced features aren't configured yet.