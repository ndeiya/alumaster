# Deploy Video Embedding Feature - Step by Step Guide

## Overview
This guide will help you deploy the video embedding feature to your live website.

## Files That Were Modified/Created

### Modified Files:
1. `admin/pages/homepage.php` - Added video fields and improved styling
2. `admin/pages/list.php` - Hidden unwanted homepage from list
3. `admin/assets/js/editor.js` - Fixed to not interfere with hero section
4. `index.php` - Added video rendering code
5. `assets/css/style.css` - Added video styling

### New Files Created:
1. `database/add_hero_video.php` - Database update script
2. Documentation files (VIDEO_*.md) - For reference only

---

## Deployment Steps

### Step 1: Backup Your Live Site
**IMPORTANT: Always backup before deploying!**

```bash
# On your live server, create a backup
cd /path/to/your/website
tar -czf backup-$(date +%Y%m%d).tar.gz .

# Or use cPanel File Manager to create a backup
```

### Step 2: Upload Modified Files via FTP/cPanel

#### Option A: Using FTP (FileZilla, WinSCP, etc.)
1. Connect to your live server via FTP
2. Upload these files (overwrite existing):
   ```
   admin/pages/homepage.php
   admin/pages/list.php
   admin/assets/js/editor.js
   index.php
   assets/css/style.css
   ```
3. Upload new file:
   ```
   database/add_hero_video.php
   ```

#### Option B: Using cPanel File Manager
1. Log into cPanel
2. Open File Manager
3. Navigate to your website directory
4. Upload files one by one to their respective folders
5. Choose "Overwrite" when prompted

### Step 3: Update Database

#### Option A: Via Browser (Recommended)
1. Open your browser
2. Navigate to: `https://yourdomain.com/database/add_hero_video.php`
3. You should see: "✓ Hero section updated with video support"
4. **Delete the file after running** for security:
   ```
   rm database/add_hero_video.php
   ```

#### Option B: Via SSH/Terminal
```bash
cd /path/to/your/website
php database/add_hero_video.php
rm database/add_hero_video.php
```

#### Option C: Via cPanel Terminal
1. Log into cPanel
2. Open Terminal
3. Run:
   ```bash
   cd public_html  # or your website directory
   php database/add_hero_video.php
   ```

### Step 4: Test the Admin Panel
1. Log into your admin panel: `https://yourdomain.com/admin/`
2. Go to: **Pages → Homepage**
3. You should see:
   - Hero Section with all fields
   - Blue "Video Settings" section with:
     - Enable Video Background checkbox
     - Video Type dropdown
     - Video URL input
     - Autoplay checkbox

### Step 5: Add Your Video
1. In the admin panel, scroll to the "Video Settings" section
2. Check "Enable Video Background"
3. Select video type (YouTube or Vimeo)
4. Paste your video URL, for example:
   - YouTube: `https://www.youtube.com/watch?v=YOUR_VIDEO_ID`
   - Vimeo: `https://vimeo.com/YOUR_VIDEO_ID`
5. Check "Autoplay Video (muted)" (recommended)
6. Click "Update Section"

### Step 6: Verify on Live Site
1. Visit your homepage: `https://yourdomain.com`
2. The video should appear in the hero section
3. Check on mobile devices too
4. Verify autoplay works (video should be muted)

---

## Troubleshooting

### Video Not Showing?
1. **Check database was updated:**
   - Run: `php database/check_homepage_sections.php`
   - Should show hero section exists

2. **Check video URL is correct:**
   - Must be full URL (not shortened)
   - YouTube: `https://www.youtube.com/watch?v=VIDEO_ID`
   - Vimeo: `https://vimeo.com/VIDEO_ID`

3. **Check "Enable Video" is checked:**
   - Go to Admin → Pages → Homepage
   - Verify checkbox is checked
   - Click "Update Section"

4. **Clear browser cache:**
   - Hard refresh: Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)
   - Or clear browser cache completely

### Admin Panel Not Showing Video Fields?
1. **Clear browser cache**
2. **Check file was uploaded:**
   - Verify `admin/pages/homepage.php` was uploaded
   - Check file size matches local version
3. **Check JavaScript file:**
   - Verify `admin/assets/js/editor.js` was uploaded
   - Open browser console (F12) for errors

### CSS Not Applied?
1. **Clear browser cache**
2. **Check CSS file uploaded:**
   - Verify `assets/css/style.css` was uploaded
   - Check file size (should be larger now)
3. **Hard refresh:** Ctrl+F5

### Database Connection Error?
1. **Check database credentials:**
   - Verify `includes/config.php` has correct credentials
2. **Check database exists:**
   - Log into phpMyAdmin
   - Verify `homepage_sections` table exists
3. **Run setup script:**
   - `php database/setup_homepage.php`

---

## Quick Deployment Checklist

- [ ] Backup live site
- [ ] Upload modified files via FTP/cPanel
- [ ] Run database update script
- [ ] Delete database script after running
- [ ] Test admin panel access
- [ ] Verify video fields are visible
- [ ] Add test video URL
- [ ] Check homepage displays video
- [ ] Test on mobile devices
- [ ] Clear CDN cache (if using)

---

## File Upload Checklist

### Must Upload (Core Functionality):
- [ ] `admin/pages/homepage.php`
- [ ] `admin/assets/js/editor.js`
- [ ] `index.php`
- [ ] `assets/css/style.css`
- [ ] `database/add_hero_video.php` (temporary)

### Optional Upload (Improvements):
- [ ] `admin/pages/list.php` (hides unwanted homepage from list)

### Don't Upload (Documentation Only):
- VIDEO_*.md files (keep locally for reference)
- database/check_*.php files (for testing only)

---

## Security Notes

1. **Delete database script after running:**
   ```bash
   rm database/add_hero_video.php
   ```

2. **Protect admin directory:**
   - Ensure `.htaccess` is in place
   - Use strong admin passwords

3. **Keep backups:**
   - Regular automated backups
   - Before any major changes

---

## Rollback Plan (If Something Goes Wrong)

### Quick Rollback:
1. Restore from backup:
   ```bash
   tar -xzf backup-YYYYMMDD.tar.gz
   ```

2. Or restore individual files via FTP/cPanel

### Partial Rollback (Keep Database Changes):
1. Only restore the PHP/CSS/JS files
2. Keep database changes (they're safe)

---

## Post-Deployment

### Test Everything:
- [ ] Homepage loads correctly
- [ ] Video plays (if enabled)
- [ ] Admin panel accessible
- [ ] Other pages still work
- [ ] Mobile responsive
- [ ] Page load speed acceptable

### Monitor:
- Check error logs for any issues
- Monitor page load times
- Verify video autoplay works
- Test on different browsers

---

## Support

If you encounter issues:

1. **Check browser console** (F12) for JavaScript errors
2. **Check server error logs** in cPanel
3. **Run diagnostic scripts:**
   ```bash
   php database/check_homepage_sections.php
   php database/check_both_systems.php
   ```

---

## Summary

The video embedding feature is now ready to deploy! Follow the steps above carefully, and you'll have professional video backgrounds on your homepage in minutes.

**Key Points:**
- Always backup first
- Upload all modified files
- Run database update script
- Test thoroughly before announcing
- Delete temporary scripts after use

Good luck with your deployment! 🚀
