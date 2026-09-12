@echo off
title Government Workflow OS - PHP & MySQL Stack
cd /d "%~dp0"

echo ============================================================
echo   GOVERNMENT WORKFLOW OS
echo   Philippine Government Workplace Management System
echo ============================================================
echo.

set PHP_BIN=php
where php >nul 2>nul
if %errorlevel% neq 0 (
    if exist "C:\xampp\php\php.exe" (
        set PHP_BIN=C:\xampp\php\php.exe
    ) else if exist "C:\laragon\bin\php\current\php.exe" (
        set PHP_BIN=C:\laragon\bin\php\current\php.exe
    ) else (
        echo [!] PHP was not found in system PATH or standard XAMPP/Laragon locations.
        echo.
        echo To run with XAMPP:
        echo   1. Start Apache and MySQL in the XAMPP Control Panel.
        echo   2. Copy or symlink this folder into your XAMPP htdocs:
        echo      C:\xampp\htdocs\LGU-OS
        echo   3. Open your browser to: http://localhost/LGU-OS/install.php
        echo.
        echo If using Windows built-in server without PHP in PATH, run:
        echo   powershell -ExecutionPolicy Bypass -File serve.ps1
        echo.
        pause
        exit /b 0
    )
)

echo [OK] Using PHP executable: %PHP_BIN%
echo [*] Starting local PHP server on http://localhost:8000 ...
echo [*] Launching application in default web browser...
echo.
echo Demo User: juan.delacruz@pgov.ph
echo Password:  Password123!
echo.
echo Press Ctrl+C in this window to stop the server at any time.
echo ============================================================
echo.

start "" "http://localhost:8000/install.php"
"%PHP_BIN%" -S localhost:8000
