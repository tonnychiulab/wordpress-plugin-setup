#!/bin/bash
# Setup script for new WordPress plugin with Tailwind CSS
# Usage: ./setup-plugin.sh [plugin-name] [plugin-dir]

set -e

# Get inputs
PLUGIN_NAME="${1:-my-custom-plugin}"
PLUGIN_DIR="${2:-./$PLUGIN_NAME}"

# Convert to proper case and slug
PLUGIN_SLUG=$(echo "$PLUGIN_NAME" | tr '[:upper:]' '[:lower:]' | tr ' ' '-')
PLUGIN_CLASS_PREFIX=$(echo "$PLUGIN_NAME" | tr '[:lower:]' '[:upper:]' | tr ' ' '_' | sed 's/-/_/g')

echo "Setting up WordPress plugin: $PLUGIN_NAME"
echo "Output directory: $PLUGIN_DIR"

# Create plugin directory
mkdir -p "$PLUGIN_DIR"

# Copy template files
TEMPLATE_DIR="$(dirname "$0")/../assets/plugin-template"

# Copy all template files
cp -r "$TEMPLATE_DIR"/* "$PLUGIN_DIR/"

# Rename PHP file
if [ -f "$PLUGIN_DIR/{{plugin-name}}.php" ]; then
    mv "$PLUGIN_DIR/{{plugin-name}}.php" "$PLUGIN_DIR/$PLUGIN_SLUG.php"
fi

# Replace placeholders in all files
find "$PLUGIN_DIR" -type f \( -name "*.php" -o -name "*.json" -o -name "*.js" -o -name "*.css" -o -name "*.txt" \) -exec sed -i \
    -e "s/{{plugin-name}}/$PLUGIN_NAME/g" \
    -e "s/{{plugin-slug}}/$PLUGIN_SLUG/g" \
    -e "s/{{plugin-description}}/A custom WordPress plugin/g" \
    -e "s/{{class-prefix}}/$PLUGIN_CLASS_PREFIX/g" \
    -e "s/{{text-domain}}/$PLUGIN_SLUG/g" \
    -e "s/{{menu-slug}}/$PLUGIN_SLUG/g" \
    -e "s/{{option-group}}/${PLUGIN_SLUG}_options/g" \
    -e "s/{{author-name}}/Your Name/g" \
    -e "s/{{author-uri}}/https:\/\/example.com/g" \
    -e "s/{{author-username}}/yourusername/g" \
    -e "s/{{plugin-uri}}/https:\/\/example.com/g" \
    -e "s/{{tags}}/wordpress/g" \
    -e "s/{{usage-instructions}}/Configure the plugin settings in the admin area./g" \
    -e "s/{{long-description}}/This plugin provides custom functionality for WordPress./g" \
    {} \;

# Create dist directory for compiled CSS
mkdir -p "$PLUGIN_DIR/dist/css"

echo ""
echo "Plugin setup complete!"
echo ""
echo "Next steps:"
echo "1. cd $PLUGIN_DIR"
echo "2. npm install"
echo "3. npm run dev  # For development with watch mode"
echo "4. npm run build  # For production build"
echo ""
