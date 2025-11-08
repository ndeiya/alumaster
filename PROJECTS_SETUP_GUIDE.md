# Projects Page Setup Guide

## Overview
This guide will help you set up the new Projects page for the Alumaster website with a masonry grid layout, expandable cards, and before/after image galleries.

## Files Created

### Database Files
1. `database/create_projects_tables.sql` - SQL schema for projects tables
2. `database/setup_projects.php` - Database setup script
3. `database/populate_projects.php` - Script to import existing projects from folders
4. `database/add_projects_navigation.php` - Adds Projects link to navigation

### Frontend Files
1. `projects.php` - Main projects page with masonry grid and modals

### Admin Panel Files
1. `admin/projects/list.php` - View and manage all projects
2. `admin/projects/add.php` - Add new projects
3. `admin/projects/edit.php` - Edit existing projects

## Setup Instructions

### Step 1: Create Database Tables
Run the setup script to create the necessary database tables:

```
Navigate to: http://yourdomain.com/database/setup_projects.php
```

This will create:
- `projects` table - stores project information
- `project_images` table - stores before/after images

### Step 2: Import Existing Projects
If you have existing project folders in `assets/images/projects/`, run:

```
Navigate to: http://yourdomain.com/database/populate_projects.php
```

This script will:
- Scan all folders in `assets/images/projects/`
- Read `project_details.txt` files for project information
- Import before/after images
- Set the first "after" image as the thumbnail

### Step 3: Add Projects to Navigation
Add the Projects link to your main navigation:

```
Navigate to: http://yourdomain.com/database/add_projects_navigation.php
```

### Step 4: Access Admin Panel
Log in to your admin panel and navigate to:

```
Admin > Projects > All Projects
```

You can now:
- View all projects
- Add new projects
- Edit existing projects
- Upload before/after images
- Mark projects as featured
- Set display order
- Toggle active/inactive status

## Project Folder Structure

When manually adding projects to `assets/images/projects/`, use this structure:

```
assets/images/projects/
├── ProjectName/
│   ├── project_details.txt
│   ├── before/
│   │   ├── before-image-1.jpg
│   │   ├── before-image-2.jpg
│   │   └── ...
│   └── after/
│       ├── after-image-1.jpg
│       ├── after-image-2.jpg
│       └── ...
```

### project_details.txt Format

```
Name: Project Name Here
Location: City, Region
Scope: Services provided (e.g., Alucobond cladding, Spider glass)
```

## Features

### Frontend (projects.php)
- Responsive masonry grid layout (1/2/3 columns)
- Featured project badge ("MOST RECENT")
- Click to open modal with before/after galleries
- Breadcrumb navigation
- Lazy loading images for performance

### Admin Panel
- Full CRUD operations for projects
- Multiple image upload (before/after)
- Thumbnail management
- Featured project toggle
- Display order control
- Active/inactive status
- Image deletion

## Database Schema

### projects Table
- `id` - Primary key
- `name` - Project name
- `location` - Project location
- `scope` - Services offered
- `thumbnail` - Main image path
- `is_featured` - Featured flag (0/1)
- `status` - active/inactive
- `display_order` - Sort order
- `created_at` - Timestamp

### project_images Table
- `id` - Primary key
- `project_id` - Foreign key to projects
- `image_path` - Image file path
- `image_type` - 'before' or 'after'
- `display_order` - Sort order
- `created_at` - Timestamp

## Design Notes

The design follows the mockup provided in `assets/images/projects/alumaster_projects_page/code.html`:
- Tailwind CSS for styling
- Masonry grid layout
- Modal popups with CSS-only implementation (using :target pseudo-class)
- Responsive design (mobile-first)
- Consistent with existing Alumaster branding

## Troubleshooting

### Projects not showing
1. Check database connection in `includes/database.php`
2. Verify projects table exists: Run `setup_projects.php`
3. Check project status is 'active'

### Images not displaying
1. Verify image paths are correct (relative to root)
2. Check file permissions on `assets/images/projects/`
3. Ensure images are in supported formats (jpg, jpeg, png, webp)

### Modal not opening
1. Clear browser cache
2. Check for JavaScript errors in console
3. Verify modal IDs are unique

## Next Steps

1. Run the setup scripts in order
2. Test the projects page: `http://yourdomain.com/projects.php`
3. Add/edit projects through admin panel
4. Customize styling if needed in `projects.php`

## Support

For issues or questions, refer to:
- `projects-page-guide.md` - Original requirements
- `assets/images/projects/alumaster_projects_page/code.html` - Design reference
