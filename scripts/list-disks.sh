#!/bin/bash

# Script pour lister les disques disponibles et générer la configuration Docker
# List available disks and generate Docker volume configuration

set -e

# Couleurs pour l'affichage
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}  Disques disponibles / Available Disks${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}\n"

# Liste tous les disques montés
echo -e "${GREEN}1. Disques montés (Mounted Disks):${NC}"
df -h | grep -E '^/dev/' | awk '{print "   " $1 " - " $6 " (" $2 " total, " $4 " disponible)"}'

echo -e "\n${GREEN}2. Tous les volumes (All Volumes):${NC}"
diskutil list | grep -E '(disk[0-9]|Volume)' | head -20

echo -e "\n${GREEN}3. Points de montage externes (External Mount Points):${NC}"
ls -la /Volumes/ 2>/dev/null | tail -n +4 | awk '{print "   " $NF}'

echo -e "\n${BLUE}═══════════════════════════════════════════════════════${NC}"
echo -e "${YELLOW}Configuration Docker suggérée:${NC}\n"

# Génère un exemple de configuration docker-compose
cat << 'EOF'
# Ajoutez ces lignes dans votre docker-compose.yml sous la section 'volumes:'

# Exemple pour monter un disque externe:
volumes:
  # Volumes Docker existants
  wordpress_data:
    driver: local
  mysql_data:
    driver: local
  redis_data:
    driver: local
  opensearch_data:
    driver: local
  minio_data:
    driver: local
  
  # Nouveau volume pour disque externe
  external_storage:
    driver: local
    driver_opts:
      type: none
      o: bind
      device: /Volumes/NomDeVotreDisque

# Puis dans votre service, ajoutez:
#   wordpress:
#     volumes:
#       - external_storage:/var/www/html/wp-content/backup
#       # ou pour un autre usage

EOF

echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
echo -e "${YELLOW}Mode interactif - Générer la configuration${NC}\n"

read -p "Voulez-vous générer une configuration personnalisée? (o/n): " response

if [[ "$response" == "o" || "$response" == "O" ]]; then
    echo -e "\n${GREEN}Disques externes disponibles:${NC}"
    volumes=($(ls /Volumes/ 2>/dev/null | grep -v "Macintosh HD"))
    
    if [ ${#volumes[@]} -eq 0 ]; then
        echo "   Aucun disque externe trouvé"
        exit 0
    fi
    
    for i in "${!volumes[@]}"; do
        echo "   $((i+1)). ${volumes[$i]}"
    done
    
    echo ""
    read -p "Sélectionnez le numéro du disque (ou 0 pour annuler): " disk_num
    
    if [[ "$disk_num" -gt 0 && "$disk_num" -le "${#volumes[@]}" ]]; then
        selected_disk="${volumes[$((disk_num-1))]}"
        echo ""
        read -p "Nom du volume Docker (ex: backup_storage): " volume_name
        read -p "Point de montage dans le container (ex: /var/www/html/backup): " mount_point
        
        # Génère le fichier de configuration
        config_file="docker-volume-config-$(date +%Y%m%d-%H%M%S).yml"
        
        cat > "$config_file" << EOF
# Configuration générée le $(date)
# À ajouter dans docker-compose.yml

volumes:
  ${volume_name}:
    driver: local
    driver_opts:
      type: none
      o: bind
      device: /Volumes/${selected_disk}

# Dans le service wordpress, ajoutez:
#   volumes:
#     - ${volume_name}:${mount_point}
EOF
        
        echo -e "\n${GREEN}✓ Configuration générée dans: ${config_file}${NC}"
        echo -e "${YELLOW}Contenu du fichier:${NC}\n"
        cat "$config_file"
    fi
fi

echo -e "\n${BLUE}═══════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}Script terminé!${NC}"
