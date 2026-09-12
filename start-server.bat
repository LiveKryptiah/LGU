@echo off
title Government Workflow OS - Local Server Launcher
cd /d "%~dp0"

echo ============================================================
echo   GOVERNMENT WORKFLOW OS
echo   Philippine Government Workplace Management System
echo ============================================================
echo.

:: 1. Check for PHP
where php >nul 2>nul
if %errorlevel% equ 0 (
    echo [*] Starting PHP built-in server on http://localhost:8000 ...
    start "" "http://localhost:8000/index.php"
    php -S localhost:8000
    exit /b 0
)

if exist "C:\xampp\php\php.exe" (
    echo [*] Using XAMPP PHP on http://localhost:8000 ...
    start "" "http://localhost:8000/index.php"
    "C:\xampp\php\php.exe" -S localhost:8000
    exit /b 0
)

:: 2. Check for Python (Zero-dependency local server)
where python >nul 2>nul
if %errorlevel% equ 0 (
    echo [*] PHP not detected. Starting Python local web server on http://localhost:8000 ...
    python serve.py
    exit /b 0
)

:: 3. Check for PowerShell (.NET HttpListener)
where powershell >nul 2>nul
if %errorlevel% equ 0 (
    echo [*] Starting Windows PowerShell server on http://localhost:3000 ...
    powershell -ExecutionPolicy Bypass -File serve.ps1
    exit /b 0
)

:: 4. Direct Browser Fallback
echo [*] Launching application directly in default web browser...
start "" "index.html"
exit /b 0
