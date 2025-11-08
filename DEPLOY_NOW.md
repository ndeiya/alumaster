# 🚀 Deploy AluMaster Website - Quick Start

## ⏱️ 5-Minute Deployment Prep

Follow these steps in order. Total time: ~15 minutes.

### Step 1: Update Configuration (5 min)

**Edit `includes/config.php`** - Change these 4 lines:

```php
// Line 8: Change to production
define('ENVIRONMENT', 'production');

// Line 12-14: Update database credentials
define('DB_USER', 'your_production_username');  // Change from 'root'
define('DB_PASS', 'your_secure_password');      // Add strong password

// Line 21: Update to your domain
define('SITE_URL', 'https://www.alumastergh.com');  // Change from localhost
```

### Step 2: Update Email Settings (2 min)

**Edit `.env`** - Verify production SMTP settings:

```env
SMTP_HOST=server113.web-hosting.com
SMTP_PORT=465
SMTP_USERNAME=contact@alumastergh.com
SMTP_PASSWORD=AlumasterghPassword
SMTP_ENCRYPTION=ssl
FROM_EMAIL=contact@alumastergh.com
```

### Step 3: Enable HTTPS (1 min)

**Edit `.htaccess`** - Find lines 48-50 and remove the `#` comments:

```apache
# FROM:
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# TO:
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### Step 4: Clean Up Test Files (2 min)

Run the cleanup script:

```bash
php cleanup-for-deployment.php
```

This removes 30+ test and debug files automatically.

### Step 5: Verify Security (1 min)

Run the security audit:

```bash
php security-audit.php
```

You should see:
- ✅ 0 Critical Issues
- ⚠️ 0-2 Warnings (acceptable)
- ✓ 12+ Passed Checks

### Step 6: Deploy to Server (varies)

**Upload these files to your production server:**

```
✅ Include:
- All .php files (except test-*.php, debug-*.php)
- includes/ folder
- admin/ folder
- assets/ folder
- templates/ folder
- vendor/ folder
- .htaccess files
- .env file (with production credentials)

❌ Exclude:
- test-*.php files
- debug-*.php files
- database/ folder (use separately for migration)
- .git/ folder
- node_modules/ (if exists)
- *.md documentation files (optional)
```

### Step 7: Database Setup (5 min)

1. Create production database
2. Import your database:
   ```bash
   mysql -u username -p database_name < alumaster_backup.sql
   ```
3. Update admin passwords if needed

### Step 8: Test Everything (5 min)

Visit your live site and test:

- [ ] Homepage loads: `https://www.alumastergh.com`
- [ ] Contact form works: Submit a test inquiry
- [ ] Email arrives: Check your inbox
- [ ] Admin login: `https://www.alumastergh.com/admin`
- [ ] Mobile view: Test on phone
- [ ] HTTPS works: Check for padlock icon

## ✅ Done!

Your site is now live and secure!

## 🆘 Troubleshooting

### "Database connection failed"
- Check database credentials in `includes/config.php`
- Verify database exists on server
- Check database user has correct permissions

### "Contact form not sending emails"
- Check `.env` SMTP settings
- Verify SMTP credentials are correct
- Check `logs/email.log` for errors
- Test with: `php test-email.php` (before removing it)

### "Admin login not working"
- Clear browser cookies
- Check database has admin users
- Verify session directory is writable
- Check `logs/php_errors.log`

### "HTTPS not working"
- Verify SSL certificate is installed
- Check `.htaccess` HTTPS redirect is uncommented
- Clear browser cache
- Wait for DNS propagation (if new domain)

### "Page not found" errors
- Check `.htaccess` file uploaded correctly
- Verify mod_rewrite is enabled on server
- Check file permissions (644 for files, 755 for directories)

## 📞 Need More Help?

- **Detailed Guide**: See `PRE_DEPLOYMENT_CHECKLIST.md`
- **Security Check**: Run `php security-audit.php`
- **Full Summary**: See `DEPLOYMENT_SUMMARY.md`

## 🎉 Post-Deployment

After successful deployment:

1. **Monitor logs** for first 24 hours
2. **Test contact form** with real inquiry
3. **Submit to Google Search Console**
4. **Set up automated backups**
5. **Update DNS if needed**
6. **Announce your launch!** 🎊

---

**Deployment Time**: ~30 minutes total
**Difficulty**: Easy (just follow the steps)
**Support**: All tools and scripts provided

Good luck! 🚀
