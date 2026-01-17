@echo off
title Bill Scanner Service
color 0A

echo.
echo ============================================
echo    Bill Scanner Service - Installation
echo ============================================
echo.

:: Check if Node.js is installed
where node >nul 2>nul
if %ERRORLEVEL% neq 0 (
    echo [ERROR] Node.js is not installed!
    echo.
    echo Please download and install Node.js from:
    echo https://nodejs.org/
    echo.
    echo After installing Node.js, run this script again.
    echo.
    pause
    exit /b 1
)

echo [OK] Node.js is installed

:: Check if dependencies are installed
if not exist "node_modules" (
    echo.
    echo Installing dependencies...
    echo This may take a minute...
    echo.
    npm install
    if %ERRORLEVEL% neq 0 (
        echo [ERROR] Failed to install dependencies!
        pause
        exit /b 1
    )
)

echo [OK] Dependencies installed
echo.
echo ============================================
echo    Starting Scanner Service...
echo ============================================
echo.

:: Start the service
node index.js

pause
