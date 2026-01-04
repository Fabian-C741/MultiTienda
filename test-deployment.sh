#!/bin/bash

# 🧪 Test Deployment Script for MultiTienda
# Usage: ./test-deployment.sh [staging|production]

set -e

ENVIRONMENT=${1:-staging}

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# URLs
if [[ "$ENVIRONMENT" == "production" ]]; then
    BASE_URL="https://multitienda.kcrsf.com"
else
    BASE_URL="https://staging.multitienda.kcrsf.com"
fi

echo -e "${BLUE}🧪 Testing MultiTienda Deployment${NC}"
echo -e "${YELLOW}Environment: $ENVIRONMENT${NC}"
echo -e "${YELLOW}Base URL: $BASE_URL${NC}"
echo ""

TESTS_PASSED=0
TESTS_FAILED=0

# Test function
run_test() {
    local test_name="$1"
    local url="$2"
    local expected_code="${3:-200}"
    
    echo -n "Testing $test_name... "
    
    response=$(curl -s -w "%{http_code}" -o /dev/null "$url" --max-time 10)
    
    if [[ "$response" == "$expected_code" ]]; then
        echo -e "${GREEN}✅ PASS${NC}"
        ((TESTS_PASSED++))
    else
        echo -e "${RED}❌ FAIL (HTTP $response)${NC}"
        ((TESTS_FAILED++))
    fi
}

# Content test function
test_content() {
    local test_name="$1"
    local url="$2"
    local expected_text="$3"
    
    echo -n "Testing $test_name... "
    
    content=$(curl -s "$url" --max-time 10)
    
    if [[ "$content" == *"$expected_text"* ]]; then
        echo -e "${GREEN}✅ PASS${NC}"
        ((TESTS_PASSED++))
    else
        echo -e "${RED}❌ FAIL (Content not found)${NC}"
        ((TESTS_FAILED++))
        echo -e "${YELLOW}Expected: $expected_text${NC}"
    fi
}

echo -e "${BLUE}📡 Running HTTP Tests...${NC}"

# Basic connectivity tests
run_test "Home page" "$BASE_URL/"
run_test "Central dashboard" "$BASE_URL/central"
run_test "Tenants page" "$BASE_URL/central/tenants"
run_test "Stats page" "$BASE_URL/central/stats"

echo ""
echo -e "${BLUE}📄 Running Content Tests...${NC}"

# Content validation tests
test_content "Home page title" "$BASE_URL/" "MultiTienda"
test_content "Central dashboard" "$BASE_URL/central" "Panel Central"
test_content "Tenants management" "$BASE_URL/central/tenants" "Gestionar Tiendas"
test_content "Stats page" "$BASE_URL/central/stats" "Estadísticas"

echo ""
echo -e "${BLUE}🔧 Running Configuration Tests...${NC}"

# Test PHP errors (should not contain error messages)
echo -n "Testing for PHP errors... "
error_content=$(curl -s "$BASE_URL/" | grep -i "error\|warning\|notice\|fatal")
if [[ -z "$error_content" ]]; then
    echo -e "${GREEN}✅ PASS${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ FAIL (PHP errors found)${NC}"
    ((TESTS_FAILED++))
fi

# Test for 404 errors on main navigation
echo -n "Testing navigation links... "
nav_errors=0
for path in "/central" "/central/tenants" "/central/stats"; do
    response=$(curl -s -w "%{http_code}" -o /dev/null "$BASE_URL$path" --max-time 5)
    if [[ "$response" != "200" ]]; then
        ((nav_errors++))
    fi
done

if [[ $nav_errors -eq 0 ]]; then
    echo -e "${GREEN}✅ PASS${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ FAIL ($nav_errors navigation errors)${NC}"
    ((TESTS_FAILED++))
fi

echo ""
echo -e "${BLUE}📊 Test Results${NC}"
echo "=================="
echo -e "Passed: ${GREEN}$TESTS_PASSED${NC}"
echo -e "Failed: ${RED}$TESTS_FAILED${NC}"
echo -e "Total:  $((TESTS_PASSED + TESTS_FAILED))"

if [[ $TESTS_FAILED -eq 0 ]]; then
    echo ""
    echo -e "${GREEN}🎉 All tests passed! Deployment is successful.${NC}"
    exit 0
else
    echo ""
    echo -e "${RED}❌ Some tests failed. Please check the deployment.${NC}"
    echo ""
    echo -e "${YELLOW}Common issues to check:${NC}"
    echo "1. File permissions (755 for dirs, 644 for files)"
    echo "2. .htaccess configuration"
    echo "3. PHP syntax errors"
    echo "4. Missing files"
    exit 1
fi