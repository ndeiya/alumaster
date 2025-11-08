# Projects Page - Quick Start Guide

## 🚀 Setup (One-Time Only)

### Step 1: Initialize Database
Visit this URL in your browser:
```
http://yourdomain.com/database/setup_projects.php
```

This will:
- ✅ Create database tables
- ✅ Import existing projects from folders
- ✅ Add "Projects" to your navigation menu

### Step 2: Verify
Check that everything works:
- **Frontend:** http://yourdomain.com/projects.php
- **Admin:** http://yourdomain.com/admin/projects/list.php

---

## 📋 Daily Usage

### Adding a New Project

**Via Admin Panel (Easiest):**
1. Login to admin panel
2. Go to **Projects > Add Project**
3. Fill in:
   - Project Name (e.g., "Mantrac Ghana Ltd.")
   - Location (e.g., "Kaneshie, Accra")
   - Scope (e.g., "Alucobond and Curtain Wall Glasses")
   - Upload thumbnail image
   - Upload before images (optional)
   - Upload after images (optional)
4. Check "Mark as Featured" if this is your most recent project
5. Click **Create Project**

### Editing a Project
1. Go to **Projects > All Projects**
2. Click the **Edit** button (pencil icon)
3. Update information or add/remove images
4. Click **Update Project**

### Managing Projects
From **Projects > All Projects** you can:
- ⭐ Toggle featured status (star button)
- ✅ Toggle active/inactive (status button)
- ✏️ Edit project details
- 🗑️ Delete project

---

## 📁 Project Folder Structure (Alternative Method)

If you prefer to organize projects in folders first:

```
assets/images/projects/
└── YourProjectName/
    ├── project_details.txt
    ├── before/
    │   ├── before-image-1.jpg
    │   └── before-image-2.jpg
    └── after/
        ├── after-image-1.jpg
        └── after-image-2.jpg
```

**project_details.txt format:**
```
Name: Your Project Name
Location: City, Region
Scope: Services you provided
```

Then run: `http://yourdomain.com/database/populate_projects.php`

---

## 🎨 Frontend Features

Your projects page includes:
- ✅ Responsive masonry grid (1/2/3 columns)
- ✅ Featured project badge ("MOST RECENT")
- ✅ Click cards to open before/after gallery
- ✅ Mobile-friendly design
- ✅ Fast loading with lazy images

---

## ⚙️ Admin Features

- ✅ Add/Edit/Delete projects
- ✅ Upload multiple images at once
- ✅ Drag-and-drop image upload
- ✅ Set featured project
- ✅ Control display order
- ✅ Toggle active/inactive status
- ✅ Delete individual images

---

## 💡 Tips

1. **Featured Project:** Only mark ONE project as featured - it shows the "MOST RECENT" badge
2. **Image Quality:** Use high-resolution images for best results
3. **Thumbnail:** Choose your best "after" image as the thumbnail
4. **Display Order:** Lower numbers appear first (0, 1, 2, 3...)
5. **Status:** Set to "Inactive" to hide a project without deleting it

---

## 🆘 Troubleshooting

**Projects not showing?**
- Check project status is "Active"
- Verify database connection
- Run setup script again

**Images not displaying?**
- Check file paths are correct
- Verify image file permissions
- Ensure images are JPG, PNG, or WEBP

**Modal not opening?**
- Clear browser cache
- Check for JavaScript errors in console

---

## 📞 Need Help?

Refer to detailed documentation:
- **PROJECTS_SETUP_GUIDE.md** - Complete setup instructions
- **PROJECTS_IMPLEMENTATION_SUMMARY.md** - Technical details
- **projects-page-guide.md** - Original requirements

---

**That's it! You're ready to showcase your projects! 🎉**
