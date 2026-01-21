#!/bin/bash

# Define the container name
CONTAINER_NAME="arborisis-wordpress"

# Define the pages to create: "Slug:Title:Template"
# Note: Template filename relative to theme root, or 'default'
PAGES=(
    "explore:Explore:page-explore.php"
    "graph:Graph:page-graph.php"
    "map:Map:page-map.php"
    "profile:Profile:page-profile.php"
    "stats:Stats:page-stats.php"
    "upload:Upload:page-upload.php"
)

echo "Checking for WordPress container..."
if ! docker ps | grep -q "$CONTAINER_NAME"; then
    echo "Error: Container '$CONTAINER_NAME' is not running."
    exit 1
fi

echo "Ensuring required pages exist..."

for page_info in "${PAGES[@]}"; do
    IFS=':' read -r slug title template <<< "$page_info"
    
    # Check if page exists
    if docker exec "$CONTAINER_NAME" wp post list --post_type=page --name="$slug" --format=ids --allow-root | grep -q "[0-9]"; then
        echo "✅ Page '$title' ($slug) already exists."
        
        # Update template just in case
        ID=$(docker exec "$CONTAINER_NAME" wp post list --post_type=page --name="$slug" --format=ids --allow-root)
        docker exec "$CONTAINER_NAME" wp post update "$ID" --meta_input='{"_wp_page_template":"'"$template"'"}' --quiet --allow-root
    else
        echo "➕ Creating page '$title' ($slug)..."
        docker exec "$CONTAINER_NAME" wp post create --post_type=page --post_title="$title" --post_name="$slug" --post_status=publish --meta_input='{"_wp_page_template":"'"$template"'"}' --allow-root
    fi
done

echo "Done! All pages detected."
