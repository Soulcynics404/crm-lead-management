#!/bin/bash
# ============================================
# HK CRM - Setup & Health Check Script
# Run this before starting the application
# ============================================

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}========================================${NC}"
echo -e "${YELLOW}  HK CRM - Setup & Health Check        ${NC}"
echo -e "${YELLOW}========================================${NC}"
echo ""

# ---- Step 1: Check if MariaDB/MySQL is installed ----
echo -n "[1/6] Checking if MariaDB/MySQL is installed... "
if command -v mariadb &> /dev/null || command -v mysql &> /dev/null; then
    echo -e "${GREEN}✓ Found${NC}"
else
    echo -e "${RED}✗ Not found${NC}"
    echo -e "${RED}Please install MariaDB: sudo apt install mariadb-server${NC}"
    exit 1
fi

# ---- Step 2: Check if MariaDB/MySQL service is running ----
echo -n "[2/6] Checking if database service is running... "
if systemctl is-active --quiet mariadb 2>/dev/null; then
    echo -e "${GREEN}✓ MariaDB is running${NC}"
elif systemctl is-active --quiet mysql 2>/dev/null; then
    echo -e "${GREEN}✓ MySQL is running${NC}"
else
    echo -e "${YELLOW}⚠ Database service is not running. Starting...${NC}"
    sudo systemctl start mariadb 2>/dev/null || sudo systemctl start mysql 2>/dev/null
    if systemctl is-active --quiet mariadb 2>/dev/null || systemctl is-active --quiet mysql 2>/dev/null; then
        echo -e "   ${GREEN}✓ Database service started successfully${NC}"
    else
        echo -e "   ${RED}✗ Failed to start database service${NC}"
        exit 1
    fi
fi

# ---- Step 3: Enable service on boot ----
echo -n "[3/6] Ensuring database starts on boot... "
if systemctl is-enabled --quiet mariadb 2>/dev/null || systemctl is-enabled --quiet mysql 2>/dev/null; then
    echo -e "${GREEN}✓ Already enabled${NC}"
else
    sudo systemctl enable mariadb 2>/dev/null || sudo systemctl enable mysql 2>/dev/null
    echo -e "${GREEN}✓ Enabled${NC}"
fi

# ---- Step 4: Load .env variables ----
echo -n "[4/6] Reading database config from .env... "
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ENV_FILE="$SCRIPT_DIR/.env"

if [ ! -f "$ENV_FILE" ]; then
    echo -e "${RED}✗ .env file not found!${NC}"
    echo -e "${RED}Copy .env.example to .env and configure it.${NC}"
    exit 1
fi

DB_DATABASE=$(grep -E "^DB_DATABASE=" "$ENV_FILE" | cut -d'=' -f2 | tr -d '"' | tr -d "'")
DB_USERNAME=$(grep -E "^DB_USERNAME=" "$ENV_FILE" | cut -d'=' -f2 | tr -d '"' | tr -d "'")
DB_PASSWORD=$(grep -E "^DB_PASSWORD=" "$ENV_FILE" | cut -d'=' -f2 | tr -d '"' | tr -d "'")
DB_HOST=$(grep -E "^DB_HOST=" "$ENV_FILE" | cut -d'=' -f2 | tr -d '"' | tr -d "'")

echo -e "${GREEN}✓ Database: ${DB_DATABASE}, User: ${DB_USERNAME}, Host: ${DB_HOST}${NC}"

# ---- Step 5: Create database and user if they don't exist ----
echo -n "[5/6] Setting up database and user... "
DB_CMD="mariadb"
command -v mariadb &> /dev/null || DB_CMD="mysql"

# Create database
sudo $DB_CMD -e "CREATE DATABASE IF NOT EXISTS \`${DB_DATABASE}\`;" 2>/dev/null

# Create user and grant privileges (for both localhost and 127.0.0.1)
sudo $DB_CMD -e "CREATE USER IF NOT EXISTS '${DB_USERNAME}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';" 2>/dev/null
sudo $DB_CMD -e "CREATE USER IF NOT EXISTS '${DB_USERNAME}'@'127.0.0.1' IDENTIFIED BY '${DB_PASSWORD}';" 2>/dev/null
sudo $DB_CMD -e "GRANT ALL PRIVILEGES ON \`${DB_DATABASE}\`.* TO '${DB_USERNAME}'@'localhost';" 2>/dev/null
sudo $DB_CMD -e "GRANT ALL PRIVILEGES ON \`${DB_DATABASE}\`.* TO '${DB_USERNAME}'@'127.0.0.1';" 2>/dev/null
sudo $DB_CMD -e "FLUSH PRIVILEGES;" 2>/dev/null
echo -e "${GREEN}✓ Database and user ready${NC}"

# ---- Step 6: Test connection and run migrations ----
echo -n "[6/6] Testing database connection... "
cd "$SCRIPT_DIR"

if php artisan db:monitor --databases=mysql 2>/dev/null | grep -q "OK"; then
    echo -e "${GREEN}✓ Connection successful${NC}"
elif php artisan migrate:status &>/dev/null; then
    echo -e "${GREEN}✓ Connection successful${NC}"
else
    echo -e "${YELLOW}⚠ Running connection test via migration...${NC}"
fi

echo ""
echo -e "${YELLOW}Running database migrations...${NC}"
php artisan migrate --force
echo ""

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  ✓ Setup complete! Ready to launch.    ${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "Start the server with: ${YELLOW}php artisan serve${NC}"
