@echo off
echo ========================================
echo  Streamlit - Sistem Cerdas (Python)
echo  URL: http://127.0.0.1:8501
echo ========================================
cd /d "%~dp0python-ai"
python -m pip install -r requirements.txt -q
python -m streamlit run app.py --server.port 8501
pause
