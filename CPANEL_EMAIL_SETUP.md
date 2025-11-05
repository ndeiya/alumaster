# cPanel Email Setup Guide for AluMaster

This guide will help you set up email functionality using your cPanel hosting email service.

## 🎯 **Why Use cPanel Email?**

✅ **Reliable & Professional** - Uses your domain name (alumaster75@yourdomain.com)
✅ **No External Dependencies** - Works with your existing hosting
✅ **Better Deliverability** - Emails from your domain are less likely to be marked as spam
✅ **Cost Effective** - Usually included with your hosting plan

## 📋 **Step-by-Step Setup**

### 1. Create Email Account in cPanel

1. **Login to cPanel**
   - Access your hosting control panel
   - Look for "Email Accounts" or "Email" section

2. **Create New Email Account**
   - Click "Create" or "Add Email Account"
   - **Email:** `alumaster75@yourdomain.com` (replace yourdomain.com with your actual domain)
   - **Password:** Create a strong password
   - **Mailbox Quota:** Set appropriate size (1GB+ recommended)

3. **Note Your Settings**
   - After creation, cPanel will show your SMTP settings
   - Common format: `mail.yourdomain.com`

### 2. Find Your SMTP Settings

In cPanel, look for "Email Accounts" → "Connect Devices" or "Mail Client Configuration":

**Typical cPanel SMTP Settings:**
```
SMTP Host: mail.yourdomain.com
SMTP Port: 587 (TLS) or 465 (SSL)
Username: alumaster75@yourdomain.com
Password: [your email password]
Encryption: TLS or SSL
```

**Alternative SMTP Hosts to Try:**
- `smtp.yourdomain.com`
- `yourdomain.com`
- `server.yourdomain.com`

### 3. Configure AluMaster Email System

1. **Copy Environment File:**
   ```bash
   cp .env.example .env
   ```

2. **Edit .env File:**
   ```env
   # cPanel SMTP Configuration
   SMTP_HOST=mail.yourdomain.com
   SMTP_PORT=587
   SMTP_USERNAME=alumaster75@yourdomain.com
   SMTP_PASSWORD=your-email-password
   SMTP_ENCRYPTION=tls
   
   # Email Settings
   FROM_EMAIL=alumaster75@yourdomain.com
   FROM_NAME="AluMaster Aluminum System"
   REPLY_TO=alumaster75@yourdomain.com
   ADMIN_EMAIL=alumaster75@yourdomain.com
   ```

3. **Replace Placeholders:**
   - `yourdomain.com` → your actual domain name
   - `your-email-password` → the password you set for the email account

### 4. Test Your Configuration

**Option A: Admin Interface**
1. Visit `/admin/settings/email.php`
2. Enter your settings
3. Click "Send Test Email"

**Option B: Command Line**
```bash
php test-email.php your-test-email@example.com
```

## 🔧 **Common cPanel SMTP Configurations**

### Standard Configuration
```env
SMTP_HOST=mail.yourdomain.com
SMTP_PORT=587
SMTP_ENCRYPTION=tls
```

### SSL Configuration (Alternative)
```env
SMTP_HOST=mail.yourdomain.com
SMTP_PORT=465
SMTP_ENCRYPTION=ssl
```

### Non-Encrypted (Not Recommended)
```env
SMTP_HOST=mail.yourdomain.com
SMTP_PORT=25
SMTP_ENCRYPTION=
```

## 🚨 **Troubleshooting**

### "Connection Refused" or "Connection Timeout"

**Try Different Ports:**
- Port 587 (TLS) - Most common
- Port 465 (SSL) - Alternative
- Port 25 (Plain) - Last resort

**Try Different Hosts:**
```env
# Try these in order:
SMTP_HOST=mail.yourdomain.com
SMTP_HOST=smtp.yourdomain.com  
SMTP_HOST=yourdomain.com
```

### "Authentication Failed"

1. **Verify Credentials:**
   - Username must be full email address
   - Password is case-sensitive
   - Check for typos

2. **Check Email Account:**
   - Ensure email account exists in cPanel
   - Verify password is correct
   - Account might be suspended

### "SSL/TLS Errors"

**Try Different Encryption:**
```env
# Try TLS first
SMTP_ENCRYPTION=tls
SMTP_PORT=587

# If TLS fails, try SSL
SMTP_ENCRYPTION=ssl
SMTP_PORT=465
```

### Check with Your Hosting Provider

If nothing works, contact your hosting provider and ask for:
- SMTP server hostname
- Supported ports
- Authentication requirements
- Any firewall restrictions

## 📧 **Email Account Best Practices**

### Professional Email Setup
- Use: `info@yourdomain.com` or `contact@yourdomain.com`
- Avoid: Generic names like `admin@` or `noreply@`

### Security
- Use strong passwords
- Enable spam filtering in cPanel
- Set up email forwarding if needed

### Deliverability
- Set up SPF records in cPanel DNS
- Configure DKIM if available
- Avoid sending too many emails at once

## 🔍 **Verification Steps**

### 1. Test SMTP Connection
```bash
php test-email.php
```

### 2. Check Email Logs
```bash
tail -f logs/email.log
```

### 3. Verify Contact Form
1. Submit a test inquiry on your website
2. Check if you receive admin notification
3. Verify customer receives auto-reply

## 📞 **Getting Help**

### From Your Hosting Provider
Ask for:
- "SMTP settings for sending email"
- "Outgoing mail server configuration"
- "Email client setup instructions"

### Common Hosting Providers

**Shared Hosting:**
- Usually `mail.yourdomain.com`
- Ports 587 (TLS) or 465 (SSL)

**VPS/Dedicated:**
- May use server hostname
- Check server documentation

## ✅ **Success Indicators**

You'll know it's working when:
- Test email sends successfully
- Contact form submissions trigger emails
- No errors in `/logs/email.log`
- Emails appear professional with your domain

## 🎉 **Final Notes**

- cPanel email is usually the most reliable option for websites
- Professional appearance with your domain name
- Better spam filtering and deliverability
- Included with most hosting plans

Once configured, your AluMaster website will automatically send professional emails for every contact form submission!