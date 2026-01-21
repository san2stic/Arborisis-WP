#!/bin/bash
# Quick deploy script - loads env vars and builds/starts containers
# Usage: ./docker-compose.env.sh [build|up|restart|logs]

set -e

ENV_FILE=".env.production.local"
COMMAND="${1:-up}"

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${YELLOW}🚀 Arborisis WordPress Docker Deployment${NC}"
echo ""

# Check if env file exists
if [ ! -f "$ENV_FILE" ]; then
    echo -e "${RED}❌ Error: $ENV_FILE not found${NC}"
    echo "Please create it from .env.example:"
    echo "  cp .env.example .env.production.local"
    echo "  nano .env.production.local"
    exit 1
fi

# Load environment variables safely
echo -e "${YELLOW}📝 Loading environment variables...${NC}"
while IFS= read -r line || [ -n "$line" ]; do
    if [[ -z "$line" || "$line" =~ ^[[:space:]]*# ]]; then
        continue
    fi
    export "$line"
done < "$ENV_FILE"

# Verify critical variables are set
REQUIRED_VARS=("DB_NAME" "DB_USER" "DB_PASSWORD" "MYSQL_ROOT_PASSWORD")
MISSING_VARS=()

for var in "${REQUIRED_VARS[@]}"; do
    if [ -z "${!var}" ]; then
        MISSING_VARS+=("$var")
    fi
done

if [ ${#MISSING_VARS[@]} -gt 0 ]; then
    echo -e "${RED}❌ Missing required environment variables:${NC}"
    printf '   - %s\n' "${MISSING_VARS[@]}"
    echo ""
    echo "Please set them in $ENV_FILE"
    exit 1
fi

echo -e "${GREEN}✅ Environment loaded${NC}"
echo ""

# Execute docker compose command
case "$COMMAND" in
    build)
        echo -e "${YELLOW}🔨 Building containers...${NC}"
        sudo docker compose build
        ;;
    up)
        echo -e "${YELLOW}🚀 Starting containers...${NC}"
        sudo docker compose up -d
        echo ""
        echo -e "${GREEN}✅ Containers started${NC}"
        sudo docker compose ps
        ;;
    restart)
        echo -e "${YELLOW}🔄 Restarting containers...${NC}"
        sudo docker compose down
        sudo docker compose up -d
        echo ""
        echo -e "${GREEN}✅ Containers restarted${NC}"
        sudo docker compose ps
        ;;
    logs)
        echo -e "${YELLOW}📋 Showing logs...${NC}"
        sudo docker compose logs -f
        ;;
    *)
        echo -e "${RED}❌ Unknown command: $COMMAND${NC}"
        echo ""
        echo "Usage: $0 [build|up|restart|logs]"
        echo ""
        echo "Commands:"
        echo "  build   - Build Docker images"
        echo "  up      - Start containers (default)"
        echo "  restart - Stop and start containers"
        echo "  logs    - Show container logs"
        exit 1
        ;;
esac
