#!/bin/bash

# Script simple pour afficher les disques disponibles (non-interactif)
# Simple script to display available disks (non-interactive)

set -e

# Couleurs
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

echo -e "${BLUE}╔═══════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║  Analyse des Disques pour Docker                      ║${NC}"
echo -e "${BLUE}╚═══════════════════════════════════════════════════════╝${NC}\n"

# 1. Disques montés avec espaces disponibles
echo -e "${GREEN}📀 Disques montés:${NC}"
echo -e "${CYAN}─────────────────────────────────────────────────────────${NC}"
df -h | head -1
df -h | grep -E '^/dev/' | awk '{printf "%-20s %-15s %-10s %-10s %-6s %s\n", $1, $6, $2, $4, $5, ""}'
echo ""

# 2. Volumes externes
echo -e "${GREEN}💾 Volumes externes disponibles:${NC}"
echo -e "${CYAN}─────────────────────────────────────────────────────────${NC}"
if ls /Volumes/ 2>/dev/null | grep -v "Macintosh HD" | grep -v "^$" > /dev/null; then
    for vol in $(ls /Volumes/ | grep -v "Macintosh HD"); do
        size=$(du -sh "/Volumes/$vol" 2>/dev/null | awk '{print $1}' || echo "N/A")
        echo -e "  • ${YELLOW}$vol${NC} (Taille: $size)"
        echo -e "    Chemin: ${CYAN}/Volumes/$vol${NC}"
    done
else
    echo "  Aucun volume externe trouvé"
fi
echo ""

# 3. Volumes Docker actuels
echo -e "${GREEN}🐳 Volumes Docker configurés:${NC}"
echo -e "${CYAN}─────────────────────────────────────────────────────────${NC}"
if command -v docker &> /dev/null; then
    docker volume ls 2>/dev/null | grep arborisis || echo "  Aucun volume Arborisis trouvé"
else
    echo "  Docker n'est pas en cours d'exécution"
fi
echo ""

# 4. Recommandations
echo -e "${GREEN}💡 Recommandations pour docker-compose.yml:${NC}"
echo -e "${CYAN}─────────────────────────────────────────────────────────${NC}"

# Vérifie les volumes externes pour faire des recommandations
external_vols=($(ls /Volumes/ 2>/dev/null | grep -v "Macintosh HD" || true))

if [ ${#external_vols[@]} -gt 0 ]; then
    echo "  Pour monter un disque externe, ajoutez dans docker-compose.yml:"
    echo ""
    for vol in "${external_vols[@]}"; do
        # Nettoie le nom pour Docker (remplace espaces et caractères spéciaux)
        docker_vol_name=$(echo "$vol" | tr '[:upper:]' '[:lower:]' | tr ' ' '_' | tr -cd '[:alnum:]_-')
        echo -e "  ${YELLOW}# Pour le volume: $vol${NC}"
        echo "  volumes:"
        echo "    ${docker_vol_name}_storage:"
        echo "      driver: local"
        echo "      driver_opts:"
        echo "        type: none"
        echo "        o: bind"
        echo "        device: /Volumes/$vol"
        echo ""
        echo "  # Puis dans le service wordpress:"
        echo "  #   volumes:"
        echo "  #     - ${docker_vol_name}_storage:/var/www/html/storage"
        echo ""
    done
else
    echo "  Aucun disque externe trouvé pour le moment."
    echo "  Les volumes Docker utilisent l'espace du disque principal."
fi

echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
