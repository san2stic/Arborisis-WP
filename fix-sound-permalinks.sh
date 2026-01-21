#!/bin/bash
# Script de diagnostic et réparation des permalinks pour les sons via Docker
# 
# Usage: ./fix-sound-permalinks.sh

set -e

echo "=== Diagnostic des permalinks de sons Arborisis ==="
echo ""

# Check if docker-compose is running
if ! docker-compose ps wordpress | grep -q "Up"; then
    echo "❌ Le conteneur WordPress n'est pas en cours d'exécution"
    echo "   Démarrez-le avec: docker-compose up -d"
    exit 1
fi

echo "1. Vérification des posts 'sound'..."
SOUND_COUNT=$(docker-compose exec -T wordpress wp post list --post_type=sound --format=count 2>/dev/null || echo "0")

if [ "$SOUND_COUNT" -eq "0" ]; then
    echo "   ❌ Aucun post 'sound' trouvé"
    echo "   → Uploadez d'abord un son via l'interface"
    echo ""
else
    echo "   ✅ $SOUND_COUNT son(s) trouvé(s)"
    echo ""
    echo "   Posts trouvés:"
    docker-compose exec -T wordpress wp post list --post_type=sound --format=table --fields=ID,post_title,post_status,post_date 2>/dev/null || echo "   Erreur lors de la récupération des posts"
    echo ""
fi

echo "2. Vérification du custom post type 'sound'..."
CPT_CHECK=$(docker-compose exec -T wordpress wp post-type list --format=csv --fields=name 2>/dev/null | grep -c "^sound$" || echo "0")

if [ "$CPT_CHECK" -eq "0" ]; then
    echo "   ❌ Le custom post type 'sound' n'est pas enregistré!"
    echo "   → Vérifiez que le plugin 'arborisis-core' est activé"
    exit 1
else
    echo "   ✅ Custom post type 'sound' enregistré"
fi
echo ""

echo "3. Vérification de la structure des permalinks..."
PERMALINK_STRUCTURE=$(docker-compose exec -T wordpress wp option get permalink_structure 2>/dev/null || echo "")

if [ -z "$PERMALINK_STRUCTURE" ]; then
    echo "   ⚠️  Les permalinks utilisent la structure par défaut (plain)"
    echo "   → Changez la structure via: Réglages > Permaliens"
else
    echo "   ✅ Structure: $PERMALINK_STRUCTURE"
fi
echo ""

echo "=== RÉPARATION ==="
echo "Voulez-vous forcer le flush des permalinks ? (o/n): "
read -r RESPONSE

if [[ "$RESPONSE" =~ ^[oOyY]$ ]]; then
    echo ""
    echo "Flush des permalinks en cours..."
    docker-compose exec -T wordpress wp rewrite flush --hard 2>/dev/null
    echo "✅ Permalinks flushés avec succès!"
    echo ""
    
    # Get a test URL
    if [ "$SOUND_COUNT" -gt "0" ]; then
        echo "=== URLS DE TEST ==="
        echo "Voici quelques URLs de test:"
        docker-compose exec -T wordpress wp post list --post_type=sound --format=csv --fields=ID,post_title,guid --posts_per_page=3 2>/dev/null | tail -n +2 | while IFS=',' read -r id title guid; do
            # Clean up the ID and title
            id=$(echo "$id" | tr -d '"')
            title=$(echo "$title" | tr -d '"')
            
            # Get the proper permalink
            url=$(docker-compose exec -T wordpress wp post url "$id" 2>/dev/null | tr -d '\r\n')
            echo "- $title: $url"
        done
        echo ""
    fi
else
    echo "Annulé. Aucune modification effectuée."
fi

echo ""
echo "=== DIAGNOSTIC TERMINÉ ==="
echo ""
echo "Pour tester manuellement, accédez à:"
echo "https://arborisis.social/sound/<ID>"
