# Fix Database Connection Error

## Problem
The live site shows: `Fatal error: Call to a member function prepare() on null`

This happens because the database connection is failing, returning `null` instead of a valid connection.

## Solution

### Step 1: Update Database Credentials in config.php

On your live server at `/home/ljopfhtu/alumastergh.com/includes/config.php`, update these lines:

```php
// Database Configuration
define('DB_HOST', 'localhost'); // Usually 'localhost' for cPanel
define('DB_NAME', 'ljopfhtu_alumaster'); // Your actual database name (check cPanel)
define('DB_USER', 'ljopfhtu_alumaster'); // Your database username (check cPanel)
define('DB_PASS', 'YOUR_DATABASE_PASSWORD'); // Your database password
```

### Step 2: Find Your Database Credentials in cPanel

1. Log into your cPanel
2. Go to **MySQL Databases**
3. Look for:
   - **Database name** (probably starts with `ljopfhtu_`)
   - **Database user** (probably starts with `ljopfhtu_`)
4. If you don't remember the password, you can:
   - Create a new database user with a strong password
   - Grant ALL PRIVILEGES to that user for your database

### Step 3: Verify Database Exists

Make sure your database has been created and populated with tables. You can:

1. Go to **phpMyAdmin** in cPanel
2. Select your database
3. Check if these tables exist:
   - `navigation_menus`
   - `navigation_items`
   - `pages`
   - `page_sections`
   - `services`
   - `projects`
   - `admins`

If tables don't exist, you need to import the database schema:
- Use the file `database/alumaster.sql` or `database/schema.sql`
- Import it via phpMyAdmin

### Step 4: Test the Connection

Create a test file `test-db.php` in your root directory:

```php
<?php
require_once 'includes/config.php';
require_once 'includes/database.php';

$db = new Database();
$conn = $db->getConnection();

if ($conn) {
    echo "✓ Database connection successful!";
    
    // Test a query
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<br><br>Tables found: " . count($tables);
    echo "<br>" . implode(", ", $tables);
} else {
    echo "✗ Database connection failed!";
    echo "<br>Check your credentials in includes/config.php";
}
?>
```

Visit `https://alumastergh.com/test-db.php` to test.

**IMPORTANT:** Delete this file after testing for security!

### Step 5: Check Error Logs

If still having issues, check the error log:
- In cPanel, go to **Error Log** or **Metrics > Errors**
- Look for PDO connection errors
- The error will tell you exactly what's wrong (wrong password, database doesn't exist, etc.)

## What I Fixed in the Code

I updated `includes/functions.php` to handle null database connections gracefully:
- Added null checks before calling `prepare()` on the connection
- Functions now return empty arrays instead of crashing when DB is unavailable
- Errors are logged for debugging

This prevents the fatal error, but you still need to fix the database credentials for the site to work properly.

## Quick Checklist

- [ ] Update DB_HOST, DB_NAME, DB_USER, DB_PASS in `includes/config.php`
- [ ] Verify database exists in cPanel
- [ ] Verify tables exist (import schema if needed)
- [ ] Test connection with test-db.php
- [ ] Delete test-db.php after testing
- [ ] Check that site loads without errors

## Common cPanel Database Naming

cPanel typically prefixes database names and users with your account username:
- Account: `ljopfhtu`
- Database: `ljopfhtu_alumaster` (or similar)
- User: `ljopfhtu_alumaster` (or similar)

The full credentials should be visible in cPanel > MySQL Databases.
