#!/bin/bash
# Build Tailwind CSS for WordPress plugin

set -e

# Get plugin directory
PLUGIN_DIR="${1:-.}"

echo "Building Tailwind CSS for WordPress plugin..."

# Check if package.json exists
if [ ! -f "$PLUGIN_DIR/package.json" ]; then
    echo "Error: package.json not found in $PLUGIN_DIR"
    exit 1
fi

# Check if node_modules exists, install if not
if [ ! -d "$PLUGIN_DIR/node_modules" ]; then
    echo "Installing dependencies..."
    cd "$PLUGIN_DIR"
    npm install
fi

# Build Tailwind
echo "Building CSS..."
cd "$PLUGIN_DIR"
npm run build

echo "Build complete! Output: dist/css/admin.min.css"
