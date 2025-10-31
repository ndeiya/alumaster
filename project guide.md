ROLE & EXPERTISE
You are a Senior Full-Stack Web Developer with 12+ years of experience building custom CMS-driven business websites using vanilla PHP and MySQL. You specialize in creating secure, scalable, and user-friendly content management systems for SMEs in emerging markets, with particular expertise in the construction and architectural services sector.

PROJECT BRIEF
Develop a complete, production-ready website with comprehensive admin portal for AluMaster Aluminum System, a Ghana-based provider of architectural aluminum and glass solutions. The solution must be built entirely with vanilla PHP (no frameworks) and MySQL, featuring a powerful CMS that allows non-technical staff to manage all website content.

Business Context
Company Profile
Business Name: AluMaster Aluminum System
Industry: Architectural Aluminum & Glass Solutions
Core Value Proposition: "Where Quality Meets Affordability"
Location: 16 Palace Street, Madina-Accra, Ghana
Target Market: Mixed B2B (architects, contractors, real estate developers, construction firms) and B2C (homeowners, property owners) audiences within Ghana
Brand Positioning: Professional, innovative, reliable partner for large-scale architectural projects
Contact Information
Primary Phone: +233-541-737-575
Secondary Phone: +233-502-777-703
Email: alumaster75@gmail.com
Website: www.alumastergh.com
Social Media Presence
Facebook: alumastergh
Instagram: alumaster75
X (Twitter): alumaster75
TikTok: alumaster75
Core Services (8 Categories)
Category	Service Offered
Cladding & Walls	Alucobond Cladding, Curtain Wall
Glass Systems	Spider Glass, Sliding Windows And Doors
Doors & Windows	Frameless Door, PVC Windows
Specialty Systems	Sun-breakers, Stainless Steel Balustrades
Each service requires individual pages with descriptions, image galleries, and inquiry forms.

Technical Stack Requirements
Backend:

Pure PHP (version 8.0+, no frameworks like Laravel/CodeIgniter)
Object-oriented programming architecture
PDO for secure database interactions
Session-based authentication for admin
Database:

MySQL (version 5.7+ or 8.0+)
Normalized database design
Foreign key relationships
Full-text search capabilities
Frontend:

Responsive HTML5/CSS3 (mobile-first design)
Vanilla JavaScript (ES6+) or jQuery for interactions
Modern CSS frameworks allowed (Bootstrap 5 or Tailwind CSS)
Ghana-optimized loading (lightweight, fast performance on 3G/4G networks)
Media Management:

Image upload and optimization
Support for common formats (JPEG, PNG, WebP)
Automatic thumbnail generation
Organized file storage structure
WEBSITE FEATURES & FUNCTIONALITY
A. PUBLIC-FACING WEBSITE
1. Homepage
Required Elements:

Dynamic hero section with company tagline: "Where Quality Meets Affordability"
Editable hero image/slider (admin-manageable)
Overview of all 8 core services (dynamically pulled from database)
Call-to-action buttons (Get Quote, Contact Us)
Featured/recent projects gallery (if admin uploads projects)
Trust indicators (years of experience, projects completed - admin editable)
Contact quick-links section
Social media integration (clickable icons to all platforms)
WhatsApp click-to-chat button (using primary phone number)
2. Services Section
Individual Service Pages:

Dynamic routing (e.g., /service/alucobond-cladding)
Service name, category, and detailed description
Image gallery for each service (multiple images)
Technical specifications section (admin-editable)
Related services suggestions
Service-specific inquiry form
Breadcrumb navigation
Services Overview Page:

Grid/card layout displaying all services
Category filtering (Cladding & Walls, Glass Systems, etc.)
Search functionality
Click-through to individual service pages
3. About Us Page
Company history and mission (admin-editable content)
Team section (optional, admin can add team members)
Why Choose AluMaster (key differentiators)
Certifications/affiliations (if applicable)
4. Projects/Portfolio Gallery (Optional but Recommended)
Project showcase with before/after images
Project categories matching services
Lightbox image viewer
Project details (location, service type, completion date)
5. Contact Page
Contact form (name, email, phone, service interest, message)
Interactive embedded Google Map (16 Palace Street, Madina-Accra)
All contact details with click-to-call, click-to-email functionality
WhatsApp contact button
Business hours (admin-editable)
Social media links
6. Dynamic Navigation & Footer
Header/Navbar:
Logo (admin-uploadable)
Dynamic menu items (admin can add/remove/reorder)
Mobile-responsive hamburger menu
Contact phone number display
Footer:
Company info section (admin-editable)
Quick links (admin-manageable)
Services links (auto-populated from database)
Contact information
Social media icons with links
Copyright text (auto-updating year)
B. ADMIN PORTAL (CMS)
Admin Authentication & Security
Secure login system (username/password)
Password hashing (PHP password_hash/password_verify)
Session management with timeout
CSRF protection for all forms
Role-based access control (Super Admin, Editor roles)
Login attempt limiting (brute force protection)
Activity logging (who changed what and when)
Admin Dashboard
Welcome screen with quick stats:
Total services
Total inquiries (unread/read)
Recent form submissions
Recent website visits (if analytics implemented)
Quick action buttons (Add Service, View Inquiries, etc.)
Content Management Modules
1. Site Settings Management

Logo Management:
Upload/replace site logo
Preview before saving
Automatic resizing/optimization
Contact Information Editor:
Edit all contact details (phones, email, address)
Business hours settings
Social media handles
Google Maps location (coordinates or address)
Homepage Settings:
Edit hero section (heading, subheading, tagline)
Upload hero images/slider images
Edit company statistics (years in business, projects completed)
Manage call-to-action buttons
2. Navigation & Menu Manager

Add/edit/delete menu items
Drag-and-drop menu reordering
Set menu item URL (internal pages or external links)
Enable/disable menu items
Set menu item as dropdown parent
Footer links management (separate from main nav)
3. Services Management (Full CRUD)

Add Service:
Service name
Service category (dropdown: Cladding & Walls, Glass Systems, Doors & Windows, Specialty Systems)
Detailed description (rich text editor - CKEditor or TinyMCE)
Technical specifications
Multiple image upload
SEO fields (meta title, meta description, keywords)
URL slug (auto-generated from service name, editable)
Status (Published/Draft)
Edit Service:
Modify all service fields
Reorder service images
Delete individual service images
View service on frontend (preview link)
Delete Service:
Soft delete option (archive)
Confirmation dialog
Cascade delete related images
Manage Service Categories:
Add/edit/delete categories
Category descriptions
Category images/icons
4. Media Library Manager

Centralized media library for all uploaded images
Grid view with thumbnails
Upload multiple images at once
Search and filter media
View image details (size, dimensions, upload date)
Delete unused media
Organize into folders/categories
Image optimization on upload (resize, compress)
5. Pages Management

Edit About Us Page:
Rich text editor for content
Upload team member photos and bios
Edit company mission/vision
Homepage Content Blocks:
Editable content sections
Add/remove homepage sections
Reorder sections (drag-and-drop)
6. Portfolio/Projects Management (Optional)

Add project (name, description, category, images)
Edit/delete projects
Featured projects toggle
Project ordering
7. Inquiry Management

View all contact form submissions
Filter by date, service interest, read/unread status
Mark as read/unread
Delete inquiries
Export to CSV
Email notifications for new inquiries
8. SEO Settings

Global meta tags (site title, description)
Google Analytics integration (paste tracking code)
Facebook Pixel integration
XML sitemap generation
Robots.txt editor
9. Admin User Management

Add/edit/delete admin users
Assign roles (Super Admin, Editor)
Change passwords
View login history
10. Footer Management

Edit footer sections (About, Quick Links, Contact Info)
Add/remove footer columns
Edit copyright text
Social media links management
C. TECHNICAL REQUIREMENTS
Security Implementation
SQL injection prevention (parameterized queries with PDO)
XSS protection (htmlspecialchars on all outputs)
CSRF tokens for all forms
Secure password storage (password_hash with bcrypt)
File upload validation (type, size, extension checking)
Directory traversal protection
Session hijacking prevention (regenerate session ID)
HTTPS enforcement (redirect HTTP to HTTPS)
Secure headers (X-Frame-Options, X-XSS-Protection, etc.)
Input validation and sanitization
Admin area IP whitelisting (optional configuration)
Performance Optimization
Database query optimization (proper indexing)
Image optimization on upload (compression, WebP conversion)
Lazy loading for images
Minified CSS/JS for production
Browser caching headers
Efficient database connection management
Pagination for large datasets (services, inquiries, media)
Ghana-Specific Optimizations
Lightweight codebase (fast loading on slower connections)
Progressive enhancement approach
Graceful degradation for older browsers
Mobile-first responsive design (majority mobile traffic)
WhatsApp integration (popular in Ghana)
Click-to-call functionality (mobile-optimized)
Reduced dependency on external CDNs (host assets locally)
SEO Best Practices
Semantic HTML5 markup
Clean, readable URLs (slug-based routing)
Auto-generated XML sitemap
Proper heading hierarchy (H1, H2, H3)
Alt text for all images
Open Graph tags for social sharing
Schema.org markup for business information
Meta tags management per page
Fast page load times (Core Web Vitals)
Mobile-responsive (Google mobile-first indexing)
Responsive Design Requirements
Mobile-first approach (breakpoints: 320px, 768px, 1024px, 1440px)
Touch-friendly interface elements
Readable typography on all devices
Optimized images for different screen sizes
Hamburger navigation for mobile
Thumb-friendly button sizes
Fast mobile performance
DATABASE SCHEMA REQUIREMENTS
Design a complete, normalized MySQL database schema including but not limited to:

Required Tables:

admins - Admin user authentication and roles
site_settings - Global site configuration (logo, contact info, tagline, etc.)
services - All service listings
service_categories - Service categorization
service_images - Multiple images per service
pages - Static pages content (About, etc.)
navigation_menu - Dynamic menu items
footer_content - Footer sections and links
inquiries - Contact form submissions
projects - Portfolio/project showcase (optional)
project_images - Project gallery images
media_library - Centralized media management
social_media - Social media platform links
activity_logs - Admin action tracking
sessions - Secure session management
For Each Table Specify:

All columns with data types and constraints
Primary keys and auto-increment fields
Foreign keys with ON DELETE/ON UPDATE rules
Indexes for frequently queried fields
Default values
UNIQUE constraints where appropriate
TIMESTAMP fields for created_at/updated_at tracking
PROJECT DELIVERABLES
Provide a complete, organized codebase with the following structure:

1. PROJECT FOLDER STRUCTURE
text

alumaster/
├── admin/
│   ├── index.php (dashboard)
│   ├── login.php
│   ├── logout.php
│   ├── services/
│   ├── pages/
│   ├── media/
│   ├── settings/
│   ├── inquiries/
│   ├── navigation/
│   └── includes/
├── assets/
│   ├── css/
│   ├── js/
│   ├── images/
│   └── fonts/
├── uploads/
│   ├── services/
│   ├── projects/
│   ├── logos/
│   └── media/
├── includes/
│   ├── config.php
│   ├── database.php
│   ├── functions.php
│   └── header.php, footer.php
├── index.php (homepage)
├── services.php
├── service-detail.php
├── about.php
├── contact.php
├── projects.php (optional)
└── .htaccess
2. COMPLETE CODE FILES
Provide well-commented, production-ready code for:

A. Core Configuration Files:

includes/config.php - Database credentials, site settings, constants
includes/database.php - PDO database connection class
includes/functions.php - Reusable helper functions (sanitization, validation, image upload, etc.)
.htaccess - URL rewriting, security headers, redirect rules
B. Frontend PHP Pages:

index.php - Homepage with dynamic content
services.php - Services listing page
service-detail.php - Individual service page with dynamic routing
about.php - About page with CMS content
contact.php - Contact page with form processing
projects.php - Portfolio gallery (optional)
includes/header.php - Dynamic header/navbar
includes/footer.php - Dynamic footer
C. Admin Panel Files:

Authentication:

admin/login.php - Secure login form and authentication
admin/logout.php - Session destruction
admin/includes/auth-check.php - Session validation for protected pages
Dashboard:

admin/index.php - Admin dashboard with statistics
Services Management:

admin/services/list.php - All services table with edit/delete
admin/services/add.php - Add new service form
admin/services/edit.php - Edit service form
admin/services/delete.php - Delete service handler
admin/services/categories.php - Manage categories
Site Settings:

admin/settings/general.php - Logo, contact info, social media
admin/settings/homepage.php - Homepage content management
admin/settings/seo.php - SEO settings
Navigation Management:

admin/navigation/menu.php - Header menu manager
admin/navigation/footer.php - Footer links manager
Media Library:

admin/media/library.php - Media grid view
admin/media/upload.php - Multi-file upload handler
admin/media/delete.php - Delete media
Pages Management:

admin/pages/about.php - Edit About page content
admin/pages/list.php - All pages manager
Inquiries:

admin/inquiries/list.php - All form submissions
admin/inquiries/view.php - Individual inquiry details
admin/inquiries/delete.php - Delete inquiry
User Management:

admin/users/list.php - Admin users
admin/users/add.php - Add admin user
admin/users/edit.php - Edit admin user
D. Frontend Assets:

assets/css/style.css - Custom stylesheet (mobile-responsive)
assets/js/main.js - Interactive features (form validation, lightbox, etc.)
Specify any CSS framework integration (Bootstrap/Tailwind)
E. Database:

database/alumaster.sql - Complete SQL dump with table creation statements and sample data
database/schema-diagram.txt - Text description of table relationships
3. DOCUMENTATION
Provide comprehensive documentation including:

A. Installation Guide:

Server requirements (PHP version, MySQL, Apache/Nginx)
Database setup instructions (import SQL file)
Configuration steps (editing config.php)
File permissions setup (uploads folder)
.htaccess configuration
Initial admin account creation
Post-installation security checklist
B. Admin User Manual:

How to login
Step-by-step guide for each admin function:
Adding/editing services
Managing navigation
Uploading media
Changing contact information
Managing inquiries
SEO settings
Troubleshooting common issues
C. Developer Documentation:

Code architecture explanation
How to extend functionality
Database schema documentation
Security best practices implemented
API endpoints (if any)
How to add new admin modules
D. Deployment Checklist:

Pre-launch testing checklist
Security hardening steps
Performance optimization tips
Backup procedures
Monitoring setup
4. ADDITIONAL FEATURES
Email Functionality:

Contact form email notifications (to alumaster75@gmail.com)
Email configuration (SMTP or PHP mail())
Email templates (inquiry notification, contact confirmation)
Form Features:

Client-side validation (JavaScript)
Server-side validation (PHP)
Honeypot spam protection
Google reCAPTCHA integration (optional but recommended)
Success/error message handling
Analytics & Tracking:

Google Analytics integration (admin can paste tracking ID)
Facebook Pixel placeholder
Page view tracking (optional)
CODE QUALITY STANDARDS
Your code must adhere to:

PSR-12 PHP coding standards (or close approximation)
Consistent indentation and formatting
Comprehensive inline comments explaining complex logic
Descriptive variable and function names
Separation of concerns (logic vs. presentation)
DRY principles (Don't Repeat Yourself)
Error handling and logging
Input validation on all user inputs
Output escaping to prevent XSS
Prepared statements for all database queries
SPECIFIC GHANA CONTEXT CONSIDERATIONS
Currency: Display prices in Ghanaian Cedi (GHS) if pricing is shown
Phone Format: Ghana phone number format (+233-XXX-XXX-XXX)
Location: Google Maps integration for Madina-Accra location
Mobile Usage: Assume majority mobile traffic (mobile-first design critical)
Connectivity: Optimize for 3G/4G speeds (lightweight, efficient loading)
Payment Integration: If quote system is expanded, consider local payment options (Mobile Money - MTN, Vodafone Cash)
Language: English (official business language in Ghana)
WhatsApp: Primary messaging platform (integrate click-to-WhatsApp)
SECURITY CHECKLIST
Ensure implementation includes:

 All database queries use prepared statements (PDO)
 All user inputs are validated and sanitized
 All outputs are escaped (htmlspecialchars)
 CSRF tokens on all forms
 Passwords hashed with password_hash()
 Session security (httponly, secure flags)
 File upload restrictions (type, size validation)
 Admin area protected by authentication
 SQL injection prevention
 XSS prevention
 Directory listing disabled
 Error messages don't expose sensitive info
 HTTPS redirect rules
 Secure headers configured
OUTPUT ORGANIZATION
Structure your response as follows:

PART 1: PROJECT OVERVIEW & ARCHITECTURE
Executive summary
Technology stack justification
System architecture diagram (described textually)
File structure explanation
Key design decisions
PART 2: DATABASE SCHEMA
Complete SQL CREATE TABLE statements
Table relationship explanations
Sample INSERT statements for initial data
Indexing strategy
PART 3: CORE CONFIGURATION FILES
config.php (with placeholder credentials)
database.php (PDO connection class)
functions.php (all helper functions)
.htaccess (security and routing rules)
PART 4: FRONTEND CODE
Homepage (index.php)
Services listing and detail pages
About page
Contact page with form processing
Header and footer includes
Frontend CSS and JavaScript
PART 5: ADMIN PANEL CODE
Authentication system (login/logout)
Dashboard
Services management (CRUD)
Site settings management
Navigation management
Media library
Inquiry management
All other admin modules
PART 6: DOCUMENTATION
Installation guide
Admin user manual
Developer documentation
Deployment checklist
PART 7: TESTING & QUALITY ASSURANCE
Security testing checklist
Cross-browser testing notes
Mobile responsiveness testing
Performance optimization notes
Provide complete, copy-paste-ready code with clear explanations and comments throughout.

KEY IMPROVEMENTS:

• Complete Technical Specification: Transformed content document into comprehensive development brief with exact technical requirements, eliminating all ambiguity about deliverables

• Exhaustive CMS Requirements: Detailed every admin panel feature (navbar management, logo upload, service CRUD, contact editing, media management) with specific functionality expectations

• Ghana-Specific Optimization: Integrated local market considerations (mobile-first for 3G/4G, WhatsApp integration, Madina-Accra mapping, Ghana phone formats) for market-appropriate solution

• Security-First Architecture: Embedded comprehensive security requirements (CSRF protection, SQL injection prevention, XSS protection, secure file uploads) with implementation checklist

• Structured Deliverable Framework: Created 7-part output organization ensuring Claude delivers organized, production-ready code rather than fragmented snippets

• Database Schema Precision: Specified 15 required tables with explicit field requirements, relationships, and constraints for complete data architecture

• Dual Audience Optimization: Structured frontend to serve both B2B (architects, contractors) and B2C (homeowners) audiences with appropriate content hierarchy and conversion paths

• Mixed B2B/B2C UX Flow: Designed information architecture and CTAs to serve both professional contractors seeking bulk services and homeowners seeking individual installations

TECHNIQUES APPLIED:

✓ Constraint-Driven Refinement – Vanilla PHP requirement, specific CMS features, and Ghana context create precise solution boundaries

✓ Layered Contextual Information – Business context, services catalog, contact details, and market specifics provide complete development foundation

✓ Explicit Output Specifications – 7-part structured deliverable with file-by-file breakdown eliminates interpretation gaps

✓ Persona-Based Expertise Assignment – Senior Full-Stack Developer role with emerging markets experience activates relevant knowledge domains

✓ Task Breakdown Architecture – Separated frontend, backend, admin, database, and documentation into distinct deliverable sections

✓ Chain-of-Thought Framework – Organized sections guide Claude through systematic development from architecture → database → code → documentation

✓ Claude-Optimized Structure – Leverages Claude's strength with extensive context, complex technical requirements, and multi-part deliverables

PRO TIP:

Given the extensive codebase Claude will generate, request the output in phases to stay within response limits:

Phase 1 Request: "Start with Part 1 (Architecture) and Part 2 (Database Schema)"

Phase 2 Request: "Now provide Part 3 (Core Configuration) and Part 4 (Frontend Code)"

Phase 3 Request: "Continue with Part 5 (Admin Panel Code) - start with authentication and services management"

Phase 4 Request: "Complete Part 5 (remaining admin modules) and Part 6 (Documentation)"

Alternatively, after receiving the complete architecture and database schema, you can create targeted follow-up prompts like: "Now generate the complete code for the admin services management module (add.php, edit.php, delete.php, list.php) based on the architecture we defined."

This iterative approach prevents truncated responses while maintaining architectural consistency across all code files.