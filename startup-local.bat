@echo off
echo.
echo ========================================
echo  SISTEM ABSENSI - LOCAL STARTUP
echo ========================================
echo.
echo Memulai Laravel dan Streamlit...
echo.

REM Buka terminal untuk Laravel
start cmd /k "cd /d C:\xampp\htdocs\sistem absensi\app-temp && echo Memulai Laravel Server... && php artisan serve"

REM Tunggu 3 detik agar Laravel siap
timeout /t 3 /nobreak

REM Buka terminal untuk Streamlit
start cmd /k "cd /d C:\xampp\htdocs\sistem absensi && echo Memulai Streamlit... && python -m streamlit run python-ai/app.py"

REM Buka browser dengan Laravel
timeout /t 3 /nobreak
start http://127.0.0.1:8000

echo.
echo ========================================
echo  STARTUP SELESAI!
echo ========================================
echo.
echo Laravel:  http://127.0.0.1:8000
echo Streamlit: http://localhost:8501
echo.
echo Tutup window ini untuk melanjutkan monitoring
echo.
pause
