# Homepage Management System

## Overview

The AluMaster website now features a dynamic, editable homepage that matches the design from the provided screenshot. The admin can easily modify all content sections without touching code.

## Features

### 1. Dynamic Content Sections
- **Hero Section**: Main banner with title, description, and call-to-action buttons
- **Services Section**: Grid of services with icons and descriptions
- **Why Choose Us**: Features list with image
- **Contact CTA**: Contact information and social media links

### 2. Admin Interface
- Visual editor for the Hero section (no JSON required)
- JSON editor for advanced sections
- Live preview functionality
- Section activation/deactivation
- Settings for colors and layout options

## How to Use

### Accessing Homepage Management
1. Log into the admin panel
2. Navigate to **Pages > Homepage** in the sidebar
3. You'll see all homepage sections listed

### Editing the Hero Section
The Hero section has a user-friendly visual editor:

- **Title**: Main heading text (e.g., "Where Quality")
- **Highlight Text**: Colored text part (e.g., "Meets Affordability")
- **Description**: Paragraph text below the title
- **Primary Button**: Text and link for the main call-to-action
- **Secondary Button**: Text and link for the secondary action
- **Background Image**: Path to the hero image

### Editing Other Sections
Other sections use JSON format for flexibility:

#### Services Section JSON Structure:
```json
{
  "title": "Our Expertise",
  "subtitle": "Comprehensive aluminum and glass solutions...",
  "services": [
    {
      "name": "Service Name",
      "description": "Service description",
      "icon": "icon_name"
    }
  ]
}
```

#### Available Icons:
- `building` - For construction/building services
- `grid` - For structural systems
- `lightbulb` - For innovative solutions
- `columns` - For architectural elements
- `lock` - For security/access systems
- `clipboard` - For documentation/planning
- `sun` - For solar/environmental solutions
- `shield` - For protection/safety systems

### Settings
Each section has settings for:
- Background colors
- Text colors
- Layout options (like number of columns for services)

## Database Structure

The system uses a `homepage_sections` table with:
- `section_key`: Unique identifier (hero, services, why_choose, contact_cta)
- `section_name`: Human-readable name
- `content`: JSON content data
- `settings`: JSON settings data
- `is_active`: Enable/disable sections
- `sort_order`: Display order

## File Structure

### Frontend Files:
- `index.php` - Dynamic homepage (replaces static version)
- `index-static-backup.php` - Backup of original static homepage
- `includes/functions.php` - Helper functions for icons and content

### Admin Files:
- `admin/pages/homepage.php` - Homepage management interface
- `admin/preview-homepage.php` - Preview functionality
- `database/setup_homepage.php` - Database setup script

## Customization

### Adding New Sections:
1. Insert new record in `homepage_sections` table
2. Add rendering logic in `index.php`
3. Add editing interface in `admin/pages/homepage.php`

### Adding New Icons:
1. Add SVG code to `getServiceIcon()` function in `includes/functions.php`
2. Use the new icon name in the services JSON

### Styling:
- Main styles are in `assets/css/style.css`
- Homepage sections use existing CSS classes
- Colors can be customized via section settings

## Backup and Recovery

The original static homepage is backed up as `index-static-backup.php`. To revert:
1. Copy `index-static-backup.php` to `index.php`
2. Remove or rename the dynamic version

## Security Notes

- All user input is sanitized and escaped
- JSON validation prevents malformed data
- Admin authentication required for all changes
- Database uses prepared statements

## Troubleshooting

### Common Issues:
1. **JSON Syntax Errors**: Use the JSON validator in the admin interface
2. **Missing Images**: Check file paths in the content
3. **Styling Issues**: Verify CSS classes match the design system
4. **Database Errors**: Check database connection and table structure

### Getting Help:
- Check browser console for JavaScript errors
- Review server error logs for PHP issues
- Validate JSON syntax using online tools
- Test with the preview functionality before publishing

## Future Enhancements

Potential improvements:
- Visual editor for all sections
- Image upload interface
- Drag-and-drop section reordering
- Template system for different homepage layouts
- A/B testing capabilities
- SEO optimization tools