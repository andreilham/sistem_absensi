@echo off
echo.
echo ========================================
echo  HENTIKAN SEMUA SERVICES
echo ========================================
echo.

REM Kill Laravel process
taskkill /F /IM php.exe 2>nul

REM Kill Python/Streamlit process
taskkill /F /IM python.exe 2>nul

echo.
echo Semua services berhasil dihentikan!
echo.
pause
