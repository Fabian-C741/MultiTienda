#!/bin/bash

# 🚀 Deploy Script for MultiTienda
# Usage: ./deploy.sh [staging|production]

set -e  # Exit on any error

ENVIRONMENT=${1:-staging}
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🚀 MultiTienda Deployment Script${NC}"
echo -e "${YELLOW}Environment: ${ENVIRONMENT}${NC}"
echo ""

# Validate environment
if [[ "$ENVIRONMENT" != "staging" && "$ENVIRONMENT" != "production" ]]; then
    echo -e "${RED}❌ Error: Environment must be 'staging' or 'production'${NC}"
    exit 1
fi

# Pre-deployment checks
echo -e "${BLUE}📋 Running pre-deployment checks...${NC}"

# Check if we're on the correct branch
if [[ "$ENVIRONMENT" == "production" ]]; then
    EXPECTED_BRANCH="main"
    REMOTE_PATH="/home/u464516792/domains/multitienda.kcrsf.com/public_html"
    REMOTE_URL="multitienda.kcrsf.com"
else
    EXPECTED_BRANCH="staging"
    REMOTE_PATH="/home/u464516792/domains/multitienda.kcrsf.com/staging"
    REMOTE_URL="staging.multitienda.kcrsf.com"
fi

CURRENT_BRANCH=$(git branch --show-current)
if [[ "$CURRENT_BRANCH" != "$EXPECTED_BRANCH" ]]; then
    echo -e "${YELLOW}⚠️  Warning: You're on branch '$CURRENT_BRANCH' but deploying to '$ENVIRONMENT'${NC}"
    echo -e "${YELLOW}Expected branch: '$EXPECTED_BRANCH'${NC}"
    read -p "Continue anyway? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo -e "${RED}❌ Deployment cancelled${NC}"
        exit 1
    fi
fi

# Run tests for staging
if [[ "$ENVIRONMENT" == "staging" ]]; then
    echo -e "${BLUE}🧪 Running automated tests...${NC}"
    
    # PHP Syntax Check
    find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \; > /dev/null
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ PHP syntax check passed${NC}"
    else
        echo -e "${RED}❌ PHP syntax errors found${NC}"
        exit 1
    fi
    
    # Check required files
    REQUIRED_FILES=("index.php" "multitienda-simple.php" ".htaccess")
    for file in "${REQUIRED_FILES[@]}"; do
        if [[ ! -f "$file" ]]; then
            echo -e "${RED}❌ Missing required file: $file${NC}"
            exit 1
        fi
    done
    echo -e "${GREEN}✅ Required files check passed${NC}"
    
    # Laravel-specific checks
    if [[ -f "backend/composer.json" ]]; then
        echo -e "${BLUE}🔍 Checking Laravel configuration...${NC}"
        
        # Check .env exists
        if [[ ! -f "backend/.env" ]]; then
            echo -e "${RED}❌ Missing .env file${NC}"
            exit 1
        fi
        
        # Check APP_KEY is set
        if ! grep -q "APP_KEY=base64:" "backend/.env"; then
            echo -e "${RED}❌ APP_KEY not properly set in .env${NC}"
            exit 1
        fi
        
        echo -e "${GREEN}✅ Laravel configuration check passed${NC}"
    fi
fi

# Deployment confirmation for production
if [[ "$ENVIRONMENT" == "production" ]]; then
    echo -e "${RED}⚠️  PRODUCTION DEPLOYMENT${NC}"
    echo -e "${YELLOW}This will deploy to: ${REMOTE_URL}${NC}"
    echo ""
    read -p "Are you absolutely sure? Type 'YES' to continue: " -r
    if [[ $REPLY != "YES" ]]; then
        echo -e "${RED}❌ Production deployment cancelled${NC}"
        exit 1
    fi
fi

# Create deployment package
echo -e "${BLUE}📦 Creating deployment package...${NC}"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
DEPLOY_DIR="deploy_${ENVIRONMENT}_${TIMESTAMP}"
mkdir -p "$DEPLOY_DIR"

# Copy files (excluding development files)
rsync -av --exclude='node_modules' \
          --exclude='.git' \
          --exclude='tests' \
          --exclude='storage/logs/*' \
          --exclude='*.log' \
          --exclude='deploy_*' \
          --exclude='.env.example' \
          ./ "$DEPLOY_DIR/"

echo -e "${GREEN}✅ Deployment package created: $DEPLOY_DIR${NC}"

# Generate deployment info
cat > "$DEPLOY_DIR/DEPLOYMENT_INFO.txt" << EOF
MultiTienda Deployment Information
==================================

Environment: $ENVIRONMENT
Deployed: $(date)
Branch: $CURRENT_BRANCH
Commit: $(git rev-parse HEAD)
Deployer: $(whoami)
Target URL: $REMOTE_URL

Files included in this deployment:
$(find "$DEPLOY_DIR" -type f | wc -l) files total
EOF

echo -e "${BLUE}📤 Deployment package ready for upload to Hostinger${NC}"
echo -e "${YELLOW}Manual steps required:${NC}"
echo ""
echo "1. Upload the contents of '$DEPLOY_DIR' to:"
echo "   $REMOTE_PATH"
echo ""
echo "2. Set correct permissions on Hostinger:"
echo "   chmod -R 644 files"
echo "   chmod -R 755 directories" 
echo "   chmod 666 backend/storage/logs (if exists)"
echo ""
echo "3. Test the deployment:"
echo "   Visit: https://$REMOTE_URL"
echo ""

# Post-deployment tests
if [[ "$ENVIRONMENT" == "staging" ]]; then
    echo -e "${BLUE}🔄 Staging deployment complete. Run tests after upload:${NC}"
    echo "./test-deployment.sh staging"
fi

echo -e "${GREEN}🎉 Deployment script completed successfully!${NC}"