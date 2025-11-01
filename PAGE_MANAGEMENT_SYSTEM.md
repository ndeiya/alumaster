# AluMaster Page Management System

## Overview

The AluMaster website now features a complete dynamic page management system that allows admins to edit Homepage, About, and Contact pages without touching code. All pages maintain the original design while being fully editable through the admin panel.

## Features

### 1. Dynamic Pages
- **Homepage**: Hero section, services grid, why choose us, contact CTA
- **About Page**: Hero, company story, mission & vision, benefits, statistics
- **Contact Page**: Hero, contact methods, contact form, map

### 2. Admin Management Interface
- Visual editors for common sections (no JSON required)
- JSON editors for advanced customization
- Live preview functionality
- Section activation/deactivation
- Settings for colors, layout, and styling

### 3. Database Structure
- `homepage_sections` table for homepage content
- `page_sections` table for About and Contact pages
- JSON-based content storage for flexibility
- Settings system for styling and layout options

## Admin Interface Access

### Navigation
1. Log into the admin panel
2. Navigate to **Pages** in the sidebar
3. Choose from:
   - **Homepage** - Manage homepage sections
   - **About Page** - Manage about page content
   - **Contact Page** - Manage contact page content

### Page Management Features
- **Preview Button**: View changes on the live page
- **Section Status**: Enable/disable individual sections
- **Visual Editors**: User-friendly forms for common content
- **JSON Editors**: Advanced editing for complex sections

## Content Management

### Homepage Sections

#### Hero Section (Visual Editor)
- **Title**: Main heading text
- **Highlight Text**: Colored accent text
- **Description**: Paragraph below title
- **Primary Button**: Text and link for main CTA
- **Secondary Button**: Text and link for secondary action
- **Background Image**: Path to hero image

#### Services Section (JSON Editor)
```json
{
  "title": "Our Expertise",
  "subtitle": "Description text",
  "services": [
    {
      "name": "Service Name",
      "description": "Service description",
      "icon": "icon_name"
    }
  ]
}
```

#### Other Sections
- Why Choose Us: Features list with icons
- Contact CTA: Contact info and social links

### About Page Sections

#### Hero Section (Visual Editor)
- **Page Title**: Main heading
- **Subtitle**: Description text
- **Breadcrumb Text**: Navigation text

#### Company Story (Visual Editor)
- **Section Eyebrow**: Small text above title
- **Section Title**: Main heading
- **Paragraphs**: Three editable text blocks
- **Image Path**: Path to story image

#### Mission & Vision (JSON Editor)
```json
{
  "mission": {
    "title": "Our Mission",
    "description": "Mission statement text"
  },
  "vision": {
    "title": "Our Vision", 
    "description": "Vision statement text"
  }
}
```

#### Benefits Section (JSON Editor)
```json
{
  "eyebrow": "Why Choose AluMaster",
  "title": "What Makes Us Different",
  "description": "Section description",
  "benefits": [
    {
      "title": "Benefit Title",
      "description": "Benefit description",
      "icon": "icon_name"
    }
  ]
}
```

#### Statistics Section (JSON Editor)
```json
{
  "stats": [
    {
      "number": 15,
      "label": "Years Experience"
    }
  ]
}
```

### Contact Page Sections

#### Hero Section (Visual Editor)
- Same as About page hero

#### Contact Methods (JSON Editor)
```json
{
  "methods": [
    {
      "icon": "phone",
      "label": "Call Us",
      "values": ["+233-541-737-575", "+233-502-777-703"]
    }
  ]
}
```

#### Contact Form (Visual Editor)
- **Form Title**: Heading text
- **Form Description**: Subtitle text
- **Services List**: One service per line

#### Map Section (Visual Editor)
- **Map Embed URL**: Google Maps iframe URL
- **Map Title**: Accessibility title

## Available Icons

### Service Icons
- `building` - Construction/building services
- `grid` - Structural systems
- `lightbulb` - Innovative solutions
- `columns` - Architectural elements
- `lock` - Security/access systems
- `clipboard` - Documentation/planning
- `sun` - Solar/environmental solutions
- `shield` - Protection/safety systems

### Contact Icons
- `phone` - Phone contact
- `email` - Email contact
- `location` - Address/location
- `clock` - Business hours

### Benefit Icons
- `check-circle` - Quality/verification
- `dollar-sign` - Pricing/affordability
- `users` - Team/people
- `zap` - Speed/efficiency
- `shield` - Protection/guarantee
- `globe` - Local/global expertise

## Settings System

Each section supports JSON settings for:

### Visual Settings
```json
{
  "background_color": "#ffffff",
  "text_color": "#000000",
  "overlay_opacity": 0.7
}
```

### Layout Settings
```json
{
  "columns": 4,
  "height": 400
}
```

### Image Settings
```json
{
  "background_image": "assets/images/hero.jpg"
}
```

## File Structure

### Frontend Files
- `index.php` - Dynamic homepage
- `about.php` - Dynamic about page
- `contact.php` - Dynamic contact page
- `*-static-backup.php` - Backup of original static pages

### Admin Files
- `admin/pages/homepage.php` - Homepage management
- `admin/pages/about.php` - About page management
- `admin/pages/contact.php` - Contact page management

### Database Files
- `database/setup_homepage.php` - Homepage sections setup
- `database/setup_page_sections.php` - About/Contact sections setup

### Helper Functions
- `includes/functions.php` - Icon helpers and page section loaders

## Database Tables

### homepage_sections
- `id` - Primary key
- `section_key` - Unique section identifier
- `section_name` - Human-readable name
- `content` - JSON content data
- `settings` - JSON settings data
- `is_active` - Enable/disable flag
- `sort_order` - Display order

### page_sections
- `id` - Primary key
- `page_slug` - Page identifier (about, contact)
- `section_key` - Section identifier within page
- `section_name` - Human-readable name
- `content` - JSON content data
- `settings` - JSON settings data
- `is_active` - Enable/disable flag
- `sort_order` - Display order

## Customization Guide

### Adding New Sections
1. Insert record in appropriate table
2. Add rendering logic in page file
3. Add editing interface in admin file
4. Update helper functions if needed

### Adding New Icons
1. Add SVG code to appropriate icon function
2. Use new icon name in content JSON

### Styling Changes
- Main styles in `assets/css/style.css`
- Section-specific styles via settings JSON
- Color customization through admin interface

## Backup and Recovery

### Static Backups
- `index-static-backup.php` - Original homepage
- `about-static-backup.php` - Original about page
- `contact-static-backup.php` - Original contact page

### To Revert to Static
1. Copy backup file to original name
2. Remove or rename dynamic version

### Database Backup
- Export `homepage_sections` and `page_sections` tables
- Keep SQL setup scripts for recreation

## Security Features

- Input sanitization and validation
- JSON syntax validation
- Admin authentication required
- CSRF protection on forms
- Prepared SQL statements
- XSS prevention

## Performance Considerations

- Database queries optimized with indexes
- JSON content cached in memory during page load
- Minimal database calls per page
- Efficient section loading

## Troubleshooting

### Common Issues

1. **JSON Syntax Errors**
   - Use admin interface JSON validator
   - Check for missing quotes or commas
   - Validate with online JSON tools

2. **Missing Images**
   - Verify file paths in content
   - Check file permissions
   - Ensure images exist in assets/images/

3. **Styling Problems**
   - Verify CSS classes match design system
   - Check settings JSON for color values
   - Clear browser cache

4. **Database Errors**
   - Check database connection
   - Verify table structure
   - Run setup scripts if needed

### Debug Tools
- Browser developer console
- Server error logs
- Database query logs
- JSON validation tools

## Future Enhancements

### Planned Features
- Visual drag-and-drop editors
- Image upload interface
- Section reordering
- Template system
- Multi-language support
- SEO optimization tools
- A/B testing capabilities

### Extension Points
- Custom section types
- Additional page templates
- Integration with external APIs
- Advanced analytics
- Content scheduling
- Version control

## Support

### Getting Help
- Check this documentation first
- Review error logs for specific issues
- Test with preview functionality
- Validate JSON syntax
- Check database connectivity

### Best Practices
- Always preview changes before publishing
- Keep backups of working configurations
- Test on different devices and browsers
- Use descriptive section names
- Document custom modifications

This system provides a powerful, flexible foundation for managing the AluMaster website content while maintaining the professional design and user experience.