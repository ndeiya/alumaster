# Pre-Deployment Checklist for AluMaster Website

## ✅ Critical Issues Found & Recommendations

### 🔴 HIGH PRIORITY - Must Fix Before Deployment

#### 1. **Environment Configuration**
- [ ] **CRITICAL**: Change `ENVIRONMENT` from 'development' to 'production' in `includes/config.php`
- [ ] **CRITICAL**: Update database credentials in `includes/config.php`:
  - Change `DB_USER` from 'root' to production username
  - Set secure `DB_PASS` for production database
- [ ] **CRITICAL**: Update `SITE_URL` in `includes/config.php` to production domain
- [ ] **SECURITY**: Verify `.env` file is NOT committed to git (check `.gitignore`)
- [ ] **SECURITY**: Update `.env` with production SMTP credentials
- [ ] **SECURITY**: Remove or secure `.env.example` (it's safe to keep as template)

#### 2. **Security Headers & HTTPS**
- [ ] **CRITICAL**: Uncomment HTTPS redirect in `.htaccess` (lines 48-50):
```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```
- [ ] **CRITICAL**: Ensure SSL certificate is installed on server
- [ ] **SECURITY**: Verify security headers are working (X-Frame-Options, X-XSS-Protection, etc.)

#### 3. **File Permissions & Access**
- [ ] **CRITICAL**: Set proper file permissions:
  - Files: `644` (readable by all, writable by owner)
  - Directories: `755` (executable/searchable by all)
  - `.env`: `600` (readable/writable by owner only)
  - `uploads/`: `755` with write permissions for web server
  - `logs/`: `755` with write permissions for web server
- [ ] **SECURITY**: Verify `.htaccess` files are protecting sensitive directories
- [ ] **SECURITY**: Test that `config.php` cannot be accessed directly via browser

#### 4. **Remove Test & Debug Files**
- [ ] **CRITICAL**: Delete all test files before deployment:
  - `test-*.php` (11 files found)
  - `debug-*.php` (3 files found)
  - `check_projects_scope.php`
  - `detect-cpanel-settings.php`
  - `install-phpmailer.php`
  - `setup-inquiries-table.php`
  - All files in `database/` folder (keep only if needed for migration)
- [ ] **CLEANUP**: Remove backup files:
  - `*-backup.php`
  - `*-static-backup.php`
  - `*-debug*.php`
  - `*-simple.php`
  - `*-no-*.php`

#### 5. **Database Security**
- [ ] **CRITICAL**: Create production database user with limited privileges
- [ ] **CRITICAL**: Ensure database backups are configured
- [ ] **SECURITY**: Remove or secure database setup scripts in `database/` folder
- [ ] **SECURITY**: Verify all SQL queries use prepared statements (already done ✓)

#### 6. **Admin Panel Security**
- [ ] **CRITICAL**: Change default admin passwords
- [ ] **CRITICAL**: Verify admin lockout mechanism is working (5 attempts, 15 min lockout)
- [ ] **SECURITY**: Test session timeout (2 hours)
- [ ] **SECURITY**: Verify CSRF protection on all admin forms
- [ ] **SECURITY**: Test that admin pages redirect to login when not authenticated

### 🟡 MEDIUM PRIORITY - Recommended Before Deployment

#### 7. **Email Configuration**
- [ ] Test contact form email delivery with production SMTP
- [ ] Verify auto-reply emails are working
- [ ] Test email templates render correctly
- [ ] Check spam score of outgoing emails
- [ ] Verify email logging is working (`logs/email.log`)

#### 8. **Performance Optimization**
- [ ] Run `composer install --no-dev --optimize-autoloader` for production
- [ ] Enable PHP OPcache on server
- [ ] Verify browser caching headers are working
- [ ] Test Gzip compression is enabled
- [ ] Optimize images (compress large images in `assets/images/`)
- [ ] Consider implementing CDN for static assets

#### 9. **Error Handling**
- [ ] Verify custom error pages work (404.php, 500.php)
- [ ] Test error logging is working (not displaying errors to users)
- [ ] Set up error monitoring/alerting
- [ ] Verify PHP error_log location and permissions

#### 10. **Content & Functionality**
- [ ] Test all navigation links work correctly
- [ ] Verify all forms submit correctly (contact, admin forms)
- [ ] Test file upload functionality
- [ ] Verify all images load correctly
- [ ] Test responsive design on mobile devices
- [ ] Check all social media links are correct

### 🟢 LOW PRIORITY - Nice to Have

#### 11. **SEO & Analytics**
- [ ] Add Google Analytics or tracking code
- [ ] Verify meta descriptions on all pages
- [ ] Submit sitemap to Google Search Console
- [ ] Set up Google My Business listing
- [ ] Add structured data (Schema.org markup)

#### 12. **Documentation**
- [ ] Document deployment process
- [ ] Create admin user guide
- [ ] Document backup procedures
- [ ] Create troubleshooting guide

#### 13. **Monitoring & Maintenance**
- [ ] Set up uptime monitoring
- [ ] Configure automated backups
- [ ] Set up SSL certificate renewal reminders
- [ ] Plan regular security updates

## 📋 Quick Deployment Commands

### 1. Clean Up Test Files
```bash
# Remove test files (review list first!)
rm test-*.php debug-*.php check_projects_scope.php detect-cpanel-settings.php install-phpmailer.php setup-inquiries-table.php
rm contact-debug*.php contact-simple.php contact-no-*.php fix-contact-form.php
rm about-static-backup.php about-dynamic.php about-new.php
rm index-static-backup.php index-dynamic.php
```

### 2. Set File Permissions
```bash
# Set proper permissions
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod 600 .env
chmod 755 uploads logs
```

### 3. Install Production Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

### 4. Database Setup
```bash
# Export database from development
mysqldump -u root alumaster > alumaster_backup.sql

# Import to production (update credentials)
mysql -u production_user -p production_db < alumaster_backup.sql
```

## 🔍 Testing Checklist

### Before Going Live
- [ ] Test on staging environment first
- [ ] Test all forms with real data
- [ ] Test admin login and all admin functions
- [ ] Test email delivery
- [ ] Test file uploads
- [ ] Test on multiple browsers (Chrome, Firefox, Safari, Edge)
- [ ] Test on mobile devices
- [ ] Run security scan (e.g., OWASP ZAP)
- [ ] Check SSL certificate is valid
- [ ] Verify HTTPS redirect works
- [ ] Test 404 and 500 error pages

### After Going Live
- [ ] Monitor error logs for first 24 hours
- [ ] Test contact form from live site
- [ ] Verify Google Analytics is tracking
- [ ] Check all external links work
- [ ] Monitor server resources (CPU, memory, disk)
- [ ] Test backup restoration process

## 🚨 Emergency Rollback Plan

If issues occur after deployment:
1. Keep backup of previous version
2. Keep database backup before migration
3. Document rollback procedure
4. Have FTP/SSH access ready
5. Keep contact info for hosting support

## 📞 Support Contacts

- Hosting Provider: [Add contact info]
- Domain Registrar: [Add contact info]
- SSL Certificate Provider: [Add contact info]
- Developer: [Add contact info]

---

**Last Updated**: Pre-deployment review
**Status**: Ready for deployment after addressing HIGH PRIORITY items
