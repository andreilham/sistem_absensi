#!/bin/bash
# Quick Setup Script untuk Railway Deployment
# Jalankan ini setelah semua configuration siap

echo "🚀 Railway Deployment Quick Setup"
echo "=================================="

# Step 1: Environment Variables
echo ""
echo "Step 1: Setup environment variables di Railway dashboard"
echo "==========================================================="
echo ""
echo "Laravel Service Environment Variables:"
cat <<EOF
APP_ENV=production
APP_DEBUG=false
APP_KEY=(copy dari .env lokal atau generate: php artisan key:generate)
APP_URL=https://your-domain.com
DB_HOST=your-mysql-host.com
DB_PORT=3306
DB_DATABASE=absensi_wajah
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password
STREAMLIT_URL=https://your-domain.com/streamlit
EOF

echo ""
echo "Streamlit Service Environment Variables:"
cat <<EOF
DB_HOST=your-mysql-host.com
DB_PORT=3306
DB_DATABASE=absensi_wajah
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password
LARAVEL_URL=https://your-domain.com
API_KEY=absensi-umb-2026
MODEL_URL=https://your-domain.com/SistemAbsensi/model.json
METADATA_URL=https://your-domain.com/SistemAbsensi/metadata.json
MIN_CONFIDENCE=70
MIN_MARGIN=15
EOF

echo ""
echo "✅ Copy environment variables ke Railway dashboard"
echo ""

# Step 2: Database Setup
echo "Step 2: Setup Database (PlanetScale)"
echo "===================================="
echo ""
echo "1. Kunjungi: https://planetscale.com"
echo "2. Create new database: absensi_wajah"
echo "3. Get connection string dan copy ke Railway env"
echo ""

# Step 3: Custom Domain
echo "Step 3: Setup Custom Domain (Optional)"
echo "======================================"
echo ""
echo "Option A: Railway Subdomain (Free)"
echo "- Railway auto-assign domain"
echo ""
echo "Option B: Custom Domain"
echo "1. Beli domain di Namecheap, GoDaddy, dll"
echo "2. Point nameserver ke Cloudflare"
echo "3. Setup reverse proxy di Cloudflare Worker"
echo ""

echo "🎉 Deployment siap!"
echo "Dokumentasi lengkap: DEPLOYMENT_CLOUD_UNIFIED.md"
