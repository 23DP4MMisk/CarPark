@echo off
REM dev-setup.bat - helper to install deps and prepare local env for Laragon/XAMPP users
SETLOCAL ENABLEDELAYEDEXPANSION

REM Move to script directory (assume script placed in project root where composer.json and artisan live)
cd /d "%~dp0"

echo Detecting PHP...
set "PHP_CMD="
for /f "usebackq tokens=*" %%i in (`where php 2^>nul`) do (
  set "PHP_CMD=%%i"
  goto :gotphp
)
if exist "C:\xampp\php\php.exe" (
  set "PHP_CMD=C:\xampp\php\php.exe"
  goto :gotphp
)
if exist "C:\laragon\bin\php\php-8.3\php.exe" (
  set "PHP_CMD=C:\laragon\bin\php\php-8.3\php.exe"
  goto :gotphp
)
if exist "C:\laragon\bin\php\php-8.2\php.exe" (
  set "PHP_CMD=C:\laragon\bin\php\php-8.2\php.exe"
  goto :gotphp
)
:gotphp
if not defined PHP_CMD (
  echo PHP not found on PATH and common XAMPP/Laragon locations. Please install PHP 8.2+ or run this script from Laragon/XAMPP shell.
  exit /b 1
)

echo Using PHP: %PHP_CMD%

REM Check PHP version (best-effort)
for /f "usebackq tokens=1*" %%a in (`"%PHP_CMD%" -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;" 2^>nul`) do set PHPVER=%%a
echo Detected PHP version: %PHPVER%
if defined PHPVER (
  rem if version starts with 8.0 or 8.1 warn user
  echo %PHPVER% | findstr /b /c:"8.0" /c:"8.1" >nul && (
    echo WARNING: Detected PHP %PHPVER% — project may require PHP 8.2+ or 8.3+.
    set /p CONT="Continue anyway? (y/N): "
    if /i not "%CONT%"=="y" exit /b 1
  )
)

REM Locate composer
set "COMPOSER_CMD="
for /f "usebackq tokens=*" %%i in (`where composer 2^>nul`) do (
  set "COMPOSER_CMD=%%i"
  goto :gotcomposer
)
if exist composer.phar (
  set "COMPOSER_CMD=%PHP_CMD% %~dp0composer.phar"
  goto :gotcomposer
)
:gotcomposer
if not defined COMPOSER_CMD (
  echo Composer not found. Downloading composer.phar...
  %PHP_CMD% -r "copy('https://getcomposer.org/installer','composer-setup.php');"
  %PHP_CMD% composer-setup.php --quiet
  del composer-setup.php
  if exist composer.phar (
    set "COMPOSER_CMD=%PHP_CMD% %~dp0composer.phar"
  ) else (
    echo Failed to obtain composer.phar. Please install Composer manually.
    exit /b 1
  )
)

echo Running: %COMPOSER_CMD% install --no-interaction --prefer-dist
%COMPOSER_CMD% install --no-interaction --prefer-dist || (
  echo Composer install failed. See output above.
  exit /b 1
)

REM Setup .env
if not exist .env (
  if exist .env.example (
    copy /Y .env.example .env >nul
    echo Created .env from .env.example
  ) else (
    echo .env.example not found — create .env manually
  )
)

REM Ensure sqlite file exists and configure .env for sqlite
if not exist database\database.sqlite (
  if not exist database mkdir database
  type nul > database\database.sqlite
  echo Created database\database.sqlite
)

REM Replace DB_CONNECTION and DB_DATABASE in .env using PowerShell (safe)
powershell -Command "(Get-Content -Raw .env) -replace 'DB_CONNECTION=.*','DB_CONNECTION=sqlite' -replace 'DB_DATABASE=.*','DB_DATABASE=database/database.sqlite' | Set-Content .env"

REM Generate app key
%PHP_CMD% artisan key:generate --ansi

REM Run migrations and seed
%PHP_CMD% artisan migrate --seed --force --no-interaction

echo Setup complete.

REM If --serve passed, start built-in server
if "%1"=="--serve" (
  echo Starting php artisan serve on http://127.0.0.1:8000
  %PHP_CMD% artisan serve --host=127.0.0.1 --port=8000
)

ENDLOCAL
