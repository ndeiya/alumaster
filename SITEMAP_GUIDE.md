# Sitemap Setup Guide

## What Was Created

1. **sitemap.php** - Dynamic sitemap (auto-updates from database)
2. **generate-sitemap.php** - Script to create static sitemap.xml
3. **robots.txt** - Search engine instructions

## How to Use

### Option 1: Dynamic Sitemap (Recommended)
The dynamic sitemap automatically includes all your pages, services, and projects from the database.

**URL:** `https://alumastergh.com/sitemap.php`

**Advantages:**
- Always up-to-date
- No manual regeneration needed
- Includes all database content automatically

### Option 2: Static Sitemap
Generate a static sitemap.xml file.

**Run locally:**
```bash
php generate-sitemap.php
```

**Or visit in browser:**
```
https://alumastergh.com/generate-sitemap.php
```

This creates `sitemap.xml` which you can upload to your server.

## Submit to Search Engines

### Google Search Console
1. Go to: https://search.google.com/search-console
2. Add your property: `alumastergh.com`
3. Verify ownership (HTML file or DNS)
4. Go to "Sitemaps" in left menu
5. Add sitemap URL: `https://alumastergh.com/sitemap.php`
6. Click "Submit"

### Bing Webmaster Tools
1. Go to: https://www.bing.com/webmasters
2. Add your site
3. Verify ownership
4. Submit sitemap: `https://alumastergh.com/sitemap.php`

## What's Included in Sitemap

✓ Homepage (priority: 1.0)
✓ About page (priority: 0.8)
✓ Services page (priority: 0.9)
✓ Projects page (priority: 0.9)
✓ Contact page (priority: 0.8)
✓ All published CMS pages (priority: 0.7)
✓ All active services (priority: 0.7)
✓ All active projects (priority: 0.6)

## Robots.txt

The `robots.txt` file tells search engines:
- ✓ Allow all pages except admin/database
- ✓ Points to sitemap location
- ✓ Protects sensitive areas

**URL:** `https://alumastergh.com/robots.txt`

## Automatic Updates

The dynamic sitemap (`sitemap.php`) automatically updates when you:
- Add/edit/delete pages
- Add/edit/delete services
- Add/edit/delete projects
- Update any content

No manual regeneration needed!

## Testing

### Test Sitemap
Visit: `https://alumastergh.com/sitemap.php`

You should see XML output with all your URLs.

### Test Robots.txt
Visit: `https://alumastergh.com/robots.txt`

You should see the robots directives.

### Validate Sitemap
Use Google's validator:
https://www.xml-sitemaps.com/validate-xml-sitemap.html

## Troubleshooting

### Sitemap shows blank page
- Check PHP errors in error log
- Verify database connection
- Check file permissions

### URLs missing from sitemap
- Check if pages/services/projects are published/active
- Verify database connection
- Check sitemap.php code

### Search engines not finding sitemap
- Submit manually to Google/Bing
- Check robots.txt is accessible
- Verify sitemap URL is correct

## SEO Best Practices

1. **Submit to search engines** - Don't wait for them to find it
2. **Update regularly** - Use dynamic sitemap for auto-updates
3. **Monitor in Search Console** - Check for errors
4. **Keep URLs clean** - Use descriptive slugs
5. **Set proper priorities** - Homepage = 1.0, others lower

## Maintenance

### Weekly
- Check Search Console for errors
- Verify sitemap is accessible

### Monthly
- Review indexed pages count
- Check for 404 errors
- Update meta descriptions if needed

### When Adding Content
- Nothing! Dynamic sitemap updates automatically

## Advanced: Cron Job (Optional)

To regenerate static sitemap.xml automatically:

```bash
# Add to crontab (runs daily at 2 AM)
0 2 * * * /usr/bin/php /path/to/your/site/generate-sitemap.php
```

## Files to Upload

Upload these files to your website root:
- [ ] sitemap.php
- [ ] robots.txt
- [ ] generate-sitemap.php (optional, for static generation)

## Summary

✓ Dynamic sitemap created
✓ Robots.txt configured
✓ Ready to submit to search engines
✓ Auto-updates with content changes

Your sitemap is ready! Submit it to Google and Bing to improve your SEO.
