@echo off
echo ========================================
echo       POS System Setup Script
echo ========================================
echo.

echo Checking prerequisites...

:: Check if PHP is installed
php --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: PHP is not installed or not in PATH
    echo Please install PHP from https://windows.php.net/download/
    pause
    exit /b 1
)
echo ✓ PHP is installed

:: Check if Composer is installed
composer --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Composer is not installed or not in PATH
    echo Please install Composer from https://getcomposer.org/download/
    pause
    exit /b 1
)
echo ✓ Composer is installed

:: Check if Node.js is installed
node --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Node.js is not installed or not in PATH
    echo Please install Node.js from https://nodejs.org/
    pause
    exit /b 1
)
echo ✓ Node.js is installed

echo.
echo All prerequisites are installed!
echo.

:: Setup Backend
echo ========================================
echo       Setting up Laravel Backend
echo ========================================
cd backend

echo Installing PHP dependencies...
composer install
if errorlevel 1 (
    echo ERROR: Failed to install PHP dependencies
    pause
    exit /b 1
)

echo Setting up environment file...
if not exist .env (
    copy .env.example .env
    echo ✓ Environment file created
) else (
    echo ✓ Environment file already exists
)

echo Generating application key...
php artisan key:generate
if errorlevel 1 (
    echo ERROR: Failed to generate application key
    pause
    exit /b 1
)

echo.
echo ========================================
echo       Setting up Nuxt.js Frontend
echo ========================================
cd ..\frontend

echo Installing Node.js dependencies...
npm install
if errorlevel 1 (
    echo ERROR: Failed to install Node.js dependencies
    pause
    exit /b 1
)

echo Setting up environment file...
if not exist .env (
    copy .env.example .env
    echo ✓ Environment file created
) else (
    echo ✓ Environment file already exists
)

echo.
echo ========================================
echo              Setup Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Configure your database settings in backend\.env
echo 2. Create a MySQL database named 'pos_system'
echo 3. Run database migrations: cd backend && php artisan migrate
echo 4. Start the backend server: cd backend && php artisan serve
echo 5. Start the frontend server: cd frontend && npm run dev
echo.
echo Then access the application at http://localhost:3000
echo.

pause