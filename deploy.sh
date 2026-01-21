#!/bin/bash
# Docker Compose deployment script with proper environment handling
# This version creates a .env file for docker-compose to read automatically

set -e

SOURCE_ENV="${1:-.env.production.local}"
DOCKER_ENV=".env"
COMMAND="${2:-up}"

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${YELLOW}🚀 Arborisis WordPress Docker Deployment${NC}"
echo ""

# Check if source env file exists
if [ ! -f "$SOURCE_ENV" ]; then
    echo -e "${RED}❌ Error: $SOURCE_ENV not found${NC}"
    echo "Please create it from .env.example:"
    echo "  cp .env.example .env.production.local"
    echo "  nano .env.production.local"
    exit 1
fi

# Process and quote the env file properly for Docker Compose
echo -e "${YELLOW}📝 Preparing environment for Docker Compose...${NC}"

# Use quote-env.sh to properly format the file
if [ -f "./quote-env.sh" ]; then
    ./quote-env.sh "$SOURCE_ENV" "$DOCKER_ENV"
    if [ $? -ne 0 ]; then
        echo -e "${RED}❌ Failed to prepare environment file${NC}"
        exit 1
    fi
else
    # Fallback: simple copy with warning
    echo -e "${YELLOW}⚠️  quote-env.sh not found, using simple copy${NC}"
    echo -e "${YELLOW}⚠️  This may cause issues with special characters${NC}"
    cp "$SOURCE_ENV" "$DOCKER_ENV"
fi

# Verify critical variables exist in the file
echo -e "${YELLOW}🔍 Validating environment variables...${NC}"
REQUIRED_VARS=("DB_NAME" "DB_USER" "DB_PASSWORD" "MYSQL_ROOT_PASSWORD")
MISSING_VARS=()

for var in "${REQUIRED_VARS[@]}"; do
    if ! grep -q "^${var}=" "$DOCKER_ENV"; then
        MISSING_VARS+=("$var")
    fi
done

if [ ${#MISSING_VARS[@]} -gt 0 ]; then
    echo -e "${RED}❌ Missing required environment variables:${NC}"
    printf '   - %s\n' "${MISSING_VARS[@]}"
    echo ""
    echo "Please set them in $SOURCE_ENV"
    rm -f "$DOCKER_ENV"
    exit 1
fi

echo -e "${GREEN}✅ Environment prepared${NC}"
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
    down)
        echo -e "${YELLOW}🛑 Stopping containers...${NC}"
        sudo docker compose down
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
    ps)
        sudo docker compose ps
        ;;
    *)
        echo -e "${RED}❌ Unknown command: $COMMAND${NC}"
        echo ""
        echo "Usage: $0 [env-file] [command]"
        echo ""
        echo "Commands:"
        echo "  build   - Build Docker images"
        echo "  up      - Start containers (default)"
        echo "  down    - Stop containers"
        echo "  restart - Stop and start containers"
        echo "  logs    - Show container logs"
        echo "  ps      - Show container status"
        rm -f "$DOCKER_ENV"
        exit 1
        ;;
esac

# Note: We keep .env file for docker-compose to use
# Remove it only on explicit down command
if [ "$COMMAND" = "down" ]; then
    rm -f "$DOCKER_ENV"
fi
