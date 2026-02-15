@echo off
REM Setup script for new WordPress plugin with Tailwind CSS
REM Usage: setup-plugin.bat "Plugin Name" [Plugin Directory]

setlocal enabledelayedexpansion

REM Get inputs
set "PLUGIN_NAME=%~1"
if "%PLUGIN_NAME%"=="" set "PLUGIN_NAME=my-custom-plugin"

set "PLUGIN_DIR=%~2"
if "%PLUGIN_DIR%"=="" set "PLUGIN_DIR=.\\%PLUGIN_NAME%"

REM Convert to slug
set "PLUGIN_SLUG=%PLUGIN_NAME%"
set "PLUGIN_SLUG=%PLUGIN_SLUG: =-%"
for %%a in (A B C D E F G H I J K L M N O P Q R S T U V W X Y Z) do set "PLUGIN_SLUG=!PLUGIN_SLUG:%%a=%%a!"
set "PLUGIN_SLUG=%PLUGIN_SLUG:A=a%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:B=b%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:C=c%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:D=d%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:E=e%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:F=f%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:G=g%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:H=h%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:I=i%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:J=j%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:K=k%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:L=l%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:M=m%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:N=n%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:O=o%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:P=p%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:Q=q%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:R=r%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:S=s%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:T=t%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:U=u%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:V=v%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:W=w%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:X=x%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:Y=y%"
set "PLUGIN_SLUG=%PLUGIN_SLUG:Z=z%"

echo Setting up WordPress plugin: %PLUGIN_NAME%
echo Output directory: %PLUGIN_DIR%

REM Create plugin directory
if not exist "%PLUGIN_DIR%" mkdir "%PLUGIN_DIR%"

REM Copy template files (using xcopy for Windows)
xcopy /E /I /Y "%~dp0assets\plugin-template" "%PLUGIN_DIR%\"

REM Rename PHP file
if exist "%PLUGIN_DIR%\{plugin-name}.php" (
    ren "%PLUGIN_DIR%\{plugin-name}.php" "%PLUGIN_SLUG%.php"
)

REM Replace placeholders using PowerShell for better Unicode support
powershell -Command "Get-ChildItem -Path '%PLUGIN_DIR%' -Recurse -File | ForEach-Object { (Get-Content $_.FullName -Raw) -replace '{{plugin-name}}', '%PLUGIN_NAME%' -replace '{{plugin-slug}}', '%PLUGIN_SLUG%' -replace '{{plugin-description}}', 'A custom WordPress plugin' -replace '{{class-prefix}}', 'MY_PLUGIN' -replace '{{text-domain}}', '%PLUGIN_SLUG%' -replace '{{menu-slug}}', '%PLUGIN_SLUG%' -replace '{{option-group}}', '%PLUGIN_SLUG%_options' -replace '{{author-name}}', 'Your Name' -replace '{{author-uri}}', 'https://example.com' -replace '{{author-username}}', 'yourusername' -replace '{{plugin-uri}}', 'https://example.com' -replace '{{tags}}', 'wordpress' -replace '{{usage-instructions}}', 'Configure the plugin settings in the admin area.' -replace '{{long-description}}', 'This plugin provides custom functionality for WordPress.' | Set-Content $_.FullName -NoNewline }"

REM Create dist directory
if not exist "%PLUGIN_DIR%\dist\css" mkdir "%PLUGIN_DIR%\dist\css"

echo.
echo Plugin setup complete!
echo.
echo Next steps:
echo 1. cd %PLUGIN_DIR%
echo 2. npm install
echo 3. npm run dev   :: For development with watch mode
echo 4. npm run build :: For production build
echo.
pause
