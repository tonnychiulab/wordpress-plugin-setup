@echo off
REM Build Tailwind CSS for WordPress plugin
REM Usage: build-tailwind.bat [plugin-directory]

set "PLUGIN_DIR=%~1"
if "%PLUGIN_DIR%"=="" set "PLUGIN_DIR=."

echo Building Tailwind CSS for WordPress plugin...

REM Check if package.json exists
if not exist "%PLUGIN_DIR%\package.json" (
    echo Error: package.json not found in %PLUGIN_DIR%
    exit /b 1
)

REM Check if node_modules exists, install if not
if not exist "%PLUGIN_DIR%\node_modules" (
    echo Installing dependencies...
    cd /d "%PLUGIN_DIR%"
    call npm install
    if errorlevel 1 (
        echo npm install failed
        pause
        exit /b 1
    )
)

REM Build Tailwind
echo Building CSS...
cd /d "%PLUGIN_DIR%"
call npm run build

if errorlevel 1 (
    echo Build failed
    pause
    exit /b 1
)

echo.
echo Build complete! Output: dist\css\admin.min.css
echo.
pause
