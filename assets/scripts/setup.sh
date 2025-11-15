#!/bin/bash

# Masilia Consent Bundle - React Package Setup Script
# This script installs dependencies and builds the package

set -e

echo "🚀 Setting up Masilia Consent Bundle React Package..."
echo ""

# Check Node.js version
NODE_VERSION=$(node -v | cut -d'v' -f2 | cut -d'.' -f1)
if [ "$NODE_VERSION" -lt 18 ]; then
    echo "❌ Error: Node.js 18 or higher is required"
    echo "   Current version: $(node -v)"
    exit 1
fi

echo "✅ Node.js version: $(node -v)"
echo ""

# Install dependencies
echo "📦 Installing dependencies..."
npm install

echo ""
echo "✅ Dependencies installed"
echo ""

# Build the package
echo "🔨 Building package..."
npm run build

echo ""
echo "✅ Build complete"
echo ""

# Check build output
if [ -d "dist" ]; then
    echo "📁 Build output:"
    ls -lh dist/
    echo ""
fi

echo "🎉 Setup complete!"
echo ""
echo "Next steps:"
echo "  - Run 'npm run dev' to start development mode"
echo "  - Run 'npm run build' to rebuild"
echo "  - Run 'npm run lint' to check code quality"
echo ""
