@echo off
echo ========================================
echo  Laravel - Sistem Absensi Wajah UMB
echo  URL: http://127.0.0.1:8000
echo ========================================
cd /d "%~dp0app-temp"
C:\xampp\php\php.exe artisan serve --host=127.0.0.1 --port=8000
pause
