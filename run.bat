@echo off
title IPCam Viewer Local Server
echo Verifying Python installation...
python --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ================================================================
    echo [ERROR] Python non e' installato o non e' presente nel PATH di sistema.
    echo.
    echo Scarica Python 3 da:
    echo https://www.python.org/downloads/
    echo Assicurati di spuntare la casella "Add python.exe to PATH" durante l'installazione.
    echo ================================================================
    echo.
    echo Premi un tasto per uscire.
    pause >nul
    exit /b 1
)

:: Avvia il server locale con python
python server.py
