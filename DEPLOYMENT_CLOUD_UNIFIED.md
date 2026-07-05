# ☁️ Cloud Unified Deployment — Laravel + Streamlit (Railway.app)

## 🎯 Hasil Akhir
```
https://www.sistem-absensi.com/
├─ /admin           → Laravel Admin Panel
├─ /scan            → Streamlit AI Scan
├─ /api             → API Endpoint
└─ /                → Landing Page
```

---

## 📋 Prasyarat

1. ✅ GitHub repository aktif: https://github.com/andreilham/sistem_absensi
2. ✅ Kode sudah di-push ke GitHub
3. ⬜ Akun Railway: https://railway.app (gratis)
4. ⬜ Database MySQL online
5. ⬜ Custom domain (opsional, bisa pakai railway.app subdomain)

---

## Step 1: Setup Railway Account

1. Kunjungi: https://railway.app
2. Klik **"Start Free"**
3. Login dengan GitHub (pilih GitHub auth)
4. Authorize Railway untuk akses GitHub Anda
5. Buat project baru atau skip untuk lanjut

---

## Step 2: Deploy Laravel Service

### 2.1 Create Laravel Service

1. Di Railway dashboard, klik **"New Project"** → **"Deploy from GitHub repo"**
2. Pilih repo: `andreilham/sistem_absensi`
3. Railway akan auto-detect ini adalah PHP project

### 2.2 Konfigurasi Environment Variables

Railway akan minta environment variables. Set ini:

| Variable | Value |
|----------|-------|
| `APP_ENV` | production |
| `APP_DEBUG` | false |
| `APP_KEY` | Copy dari `.env` lokal (atau generate baru) |
| `APP_URL` | https://your-domain.com |
| `DB_HOST` | your-mysql-host.com |
| `DB_PORT` | 3306 |
| `DB_DATABASE` | absensi_wajah |
| `DB_USERNAME` | your-db-user |
| `DB_PASSWORD` | your-db-password |
| `STREAMLIT_URL` | https://your-domain.com/streamlit |

### 2.3 Setup Procfile untuk Laravel

Buat file `Procfile` di root proyek:

```procfile
web: cd app-temp && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
```

### 2.4 Konfigurasi nginx/routing

Di file `app-temp/.htaccess` atau Nginx config, pastikan semua request `/admin/*` diarahkan ke Laravel.

---

## Step 3: Deploy Streamlit Service

### 3.1 Create Streamlit Service

1. Di Railway dashboard, klik **"+ New Service"** → **"GitHub Repo"**
2. Pilih repo yang sama: `andreilham/sistem_absensi`
3. Railway akan detect Python project (dari `requirements.txt`)

### 3.2 Konfigurasi Streamlit Environment

Set environment variables untuk Streamlit:

| Variable | Value |
|----------|-------|
| `DB_HOST` | your-mysql-host.com |
| `DB_PORT` | 3306 |
| `DB_DATABASE` | absensi_wajah |
| `DB_USERNAME` | your-db-user |
| `DB_PASSWORD` | your-db-password |
| `LARAVEL_URL` | https://your-domain.com |
| `API_KEY` | absensi-umb-2026 |
| `MODEL_URL` | https://your-domain.com/SistemAbsensi/model.json |
| `METADATA_URL` | https://your-domain.com/SistemAbsensi/metadata.json |

### 3.3 Setup Procfile untuk Streamlit

Buat file `Procfile.streamlit` di `python-ai/`:

```procfile
web: cd python-ai && python -m streamlit run app.py --server.port=$PORT --server.address=0.0.0.0
```

Atau buat di root dengan nama `Procfile.streamlit`:

```procfile
web: python -m streamlit run python-ai/app.py --server.port=$PORT --server.address=0.0.0.0
```

### 3.4 Setup streamlit config

Di `python-ai/.streamlit/config.toml`, tambahkan:

```toml
[server]
port = 8501
enableCORS = true
enableXsrfProtection = false

[client]
toolbarMode = "viewer"
```

---

## Step 4: Setup Reverse Proxy (Kesatuan Website)

Untuk membuat terlihat seperti satu website:

### Opsi A: Menggunakan Railway's Service Routing

Railway belum support routing multi-service langsung. Gunakan **Cloudflare Workers** atau **Nginx** di tengah.

### Opsi B: Cloudflare Workers (PALING MUDAH)

1. Daftar Cloudflare: https://dash.cloudflare.com
2. Add domain Anda atau pakai Cloudflare Nameserver
3. Buat Worker script:

```javascript
export default {
  async fetch(request) {
    const url = new URL(request.url);
    const path = url.pathname;

    // Route ke Streamlit jika path /streamlit atau /scan
    if (path.startsWith('/streamlit') || path.startsWith('/scan')) {
      url.hostname = 'your-streamlit-service.railway.app';
      // Hapus /streamlit prefix
      url.pathname = path.replace('/streamlit', '') || '/';
      return fetch(new Request(url, request));
    }

    // Route ke Laravel untuk sisanya
    url.hostname = 'your-laravel-service.railway.app';
    return fetch(new Request(url, request));
  }
};
```

4. Deploy Worker
5. Update domain DNS menunjuk ke Cloudflare

### Opsi C: Nginx Reverse Proxy di Server Terpisah

Jika mau full control, deploy Nginx di server terpisah:

```nginx
upstream laravel_backend {
    server your-laravel-service.railway.app;
}

upstream streamlit_backend {
    server your-streamlit-service.railway.app;
}

server {
    listen 80;
    server_name your-domain.com;

    # Redirect HTTP to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com;

    ssl_certificate /etc/ssl/certs/your-cert.pem;
    ssl_certificate_key /etc/ssl/private/your-key.pem;

    # Laravel routes
    location / {
        proxy_pass http://laravel_backend;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    # Streamlit routes
    location /streamlit {
        rewrite ^/streamlit(.*) $1 break;
        proxy_pass http://streamlit_backend;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
    }

    # API endpoints
    location /api {
        proxy_pass http://laravel_backend;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

---

## Step 5: Setup Database Online

Pilih salah satu:

### PlanetScale (Recommended - Free MySQL)

1. Daftar: https://planetscale.com
2. Create database `absensi_wajah`
3. Get connection string: `mysql://user:password@host/absensi_wajah`
4. Gunakan di Railway environment variables

### AWS RDS

1. Create RDS MySQL instance
2. Setup security group untuk allow Railway IP
3. Get endpoint: `your-rds-instance.xxxx.us-east-1.rds.amazonaws.com`
4. Gunakan di Railway

### DigitalOcean Managed Database

1. Create Managed MySQL
2. Get host: `db-xxxx-do-user-xxxx.ondigitalocean.com`
3. Gunakan di Railway

---

## Step 6: Setup Custom Domain

### Jika pakai Railway subdomain (free)

1. Railway auto-assign: `your-app-xxxx.railway.app`
2. Gunakan URL ini langsung

### Jika pakai custom domain

1. Beli domain: Namecheap, GoDaddy, dll
2. Update nameserver ke Cloudflare atau point A record ke Railway
3. Setup SSL di Railway/Cloudflare

---

## Step 7: Database Migrations

Railway harus jalankan migration Laravel saat deploy.

Update `Procfile`:

```procfile
web: cd app-temp && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
```

---

## ✅ Testing Checklist

- [ ] Laravel buka di: https://your-domain.com/admin
- [ ] Streamlit buka di: https://your-domain.com/streamlit
- [ ] Database terhubung (cek status di app)
- [ ] Model AI loaded (cek Streamlit console)
- [ ] Scan wajah bisa akses API (Laravel endpoint)
- [ ] Admin panel bisa CRUD data

---

## 🆘 Troubleshooting

| Error | Solusi |
|-------|--------|
| "Couldn't connect to Docker daemon" | Railway auto handle, tunggu deployment |
| "Database connection refused" | Cek `DB_HOST`, `DB_USERNAME`, `DB_PASSWORD` di Railway env |
| "502 Bad Gateway" | Service belum ready, tunggu 2-3 menit |
| "Model not found" | `MODEL_URL` di Streamlit env salah, cek path `/SistemAbsensi/` |
| "CORS error" | Set `enableCORS = true` di `streamlit config` |

---

## 📊 Biaya Perkiraan

| Service | Free Tier | Bayar |
|---------|-----------|-------|
| Railway | $5 credit/bulan | Dari $5/bulan |
| PlanetScale | 5GB free | Dari $8/bulan |
| Cloudflare | Free | Dari $20/bulan |
| **Total** | ~Free | ~$15-30/bulan |

---

## 🚀 Deployment Timeline

1. **Railway setup**: 10 menit
2. **Deploy Laravel**: 5-10 menit
3. **Deploy Streamlit**: 5-10 menit
4. **Database setup**: 10 menit
5. **Routing/Proxy**: 15-30 menit
6. **Testing**: 10 menit

**Total: 1-2 jam**

---

## 📝 Dokumentasi Berguna

- Railway Docs: https://docs.railway.app
- Streamlit Cloud: https://docs.streamlit.io/deploy
- Cloudflare Workers: https://developers.cloudflare.com/workers
- PlanetScale: https://planetscale.com/docs

