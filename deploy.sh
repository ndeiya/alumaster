#!/bin/bash
# AluMaster Deployment Script
# This script helps prepare the site for production deployment

echo "==================================="
echo "AluMaster Deployment Preparation"
echo "==================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if we're in the right directory
if [ ! -f "includes/config.php" ]; then
    echo -e "${RED}Error: This script must be run from the project root directory${NC}"
    exit 1
fi

echo "Step 1: Running cleanup script..."
php cleanup-for-deployment.php

echo ""
echo "Step 2: Checking critical files..."

# Check if .env exists
if [ -f ".env" ]; then
    echo -e "${GREEN}✓${NC} .env file exists"
else
    echo -e "${RED}✗${NC} .env file missing - copy from .env.example"
fi

# Check if config.php is set to production
if grep -q "define('ENVIRONMENT', 'production')" includes/config.php; then
    echo -e "${GREEN}✓${NC} Config set to production mode"
else
    echo -e "${YELLOW}⚠${NC} Config still in development mode - update includes/config.php"
fi

# Check if .gitignore exists
if [ -f ".gitignore" ]; then
    echo -e "${GREEN}✓${NC} .gitignore file exists"
else
    echo -e "${RED}✗${NC} .gitignore file missing"
fi

echo ""
echo "Step 3: Setting file permissions..."

# Set file permissions
find . -type f -exec chmod 644 {} \; 2>/dev/null
find . -type d -exec chmod 755 {} \; 2>/dev/null

# Secure sensitive files
if [ -f ".env" ]; then
    chmod 600 .env
    echo -e "${GREEN}✓${NC} Secured .env file (600)"
fi

# Make uploads and logs writable
if [ -d "uploads" ]; then
    chmod 755 uploads
    echo -e "${GREEN}✓${NC} Set uploads directory permissions (755)"
fi

if [ -d "logs" ]; then
    chmod 755 logs
    echo -e "${GREEN}✓${NC} Set logs directory permissions (755)"
fi

echo ""
echo "Step 4: Installing production dependencies..."
if command -v composer &> /dev/null; then
    composer install --no-dev --optimize-autoloader
    echo -e "${GREEN}✓${NC} Composer dependencies installed"
else
    echo -e "${YELLOW}⚠${NC} Composer not found - install dependencies manually"
fi

echo ""
echo "Step 5: Creating necessary directories..."
mkdir -p uploads logs
echo -e "${GREEN}✓${NC} Directories created"

echo ""
echo "==================================="
echo "Deployment Preparation Complete!"
echo "==================================="
echo ""
echo "Next steps:"
echo "1. Review PRE_DEPLOYMENT_CHECKLIST.md"
echo "2. Update includes/config.php with production database credentials"
echo "3. Update .env with production SMTP settings"
echo "4. Enable HTTPS redirect in .htaccess"
echo "5. Test on staging environment"
echo "6. Deploy to production server"
echo ""
echo "For detailed instructions, see PRE_DEPLOYMENT_CHECKLIST.md"
