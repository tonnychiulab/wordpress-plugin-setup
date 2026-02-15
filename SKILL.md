---
name: wordpress-plugin-setup
description: Initialize WordPress plugin projects with Tailwind CSS. Use when creating new plugins, setting up dev environments, or needing a starter template with modern CSS tooling.
---

# WordPress Plugin Setup with Tailwind CSS

## Quick Start

**Windows (使用 .bat 腳本):**
```cmd
scripts\setup-plugin.bat "My Plugin Name" .\my-plugin-directory
```

**Mac/Linux (使用 .sh 腳本):**
```bash
./scripts/setup-plugin.sh "My Plugin Name" ./my-plugin-directory
```

然後按照指示安裝依賴並編譯。

## Workflow

### Step 1: Generate Plugin Template

Run the setup script with your plugin name and desired directory:

**Windows:**
```cmd
scripts\setup-plugin.bat "Custom Plugin" .\wp-content\plugins\custom-plugin
```

**Mac/Linux:**
```bash
./scripts/setup-plugin.sh "Custom Plugin" ./wp-content/plugins/custom-plugin
```

The script will:
- Create the plugin directory structure
- Copy template files
- Replace all placeholders with your plugin name
- Set up proper WordPress plugin header

### Step 2: Install Dependencies

```bash
cd ./my-plugin-directory
npm install
```

### Step 3: Build CSS

**Development (watch mode):**
```bash
npm run dev
```

**Production (minified):**
```bash
npm run build
```

### Step 4: Activate in WordPress

1. Upload or symlink the plugin to your WordPress installation
2. Activate via Plugins admin page
3. Configure settings if applicable

### Step 5: PHP Syntax Check (必做)

每次寫完或修改 PHP 程式碼後，必須對所有 `.php` 檔案執行語法檢查：

**Windows:**
```cmd
C:\xampp\php\php.exe -l path\to\file.php
```

**批次檢查整個外掛目錄的所有 PHP 檔案：**
```powershell
Get-ChildItem -Path .\path\to\plugin -Filter *.php -Recurse | ForEach-Object { C:\xampp\php\php.exe -l $_.FullName }
```

- `php -l` 只做語法檢查（lint），不會執行程式碼，完全安全
- 模板檔案（含 `{{placeholder}}`）會報錯，這是正常的，可以跳過
- 所有非模板的 `.php` 檔案必須通過 `No syntax errors detected` 才算合格

### Step 6: Generate Plugin Documentation（半自動）

完成外掛開發後，產生使用說明文件（Markdown 格式）：

**a) 部署外掛到測試站：**
```powershell
Copy-Item -Path .\path\to\plugin -Destination "C:\xampp\htdocs\my-tiny-wpdev\wp-content\plugins\" -Recurse
```

**b) 產生文件骨架：**
- 參考 `assets/plugin-doc-template.md` 模板
- 在外掛目錄下建立 `docs/USAGE.md`
- 將模板中的 `{{placeholder}}` 替換為外掛的實際資訊
- 文件會包含截圖佔位符 `<!-- SCREENSHOT: 說明 -->`

**c) 請用戶截圖：**
- 列出需要截圖的頁面清單（安裝畫面、設定頁面、功能頁面等）
- 請用戶手動截圖後放到 `docs/screenshots/` 資料夾
- 截圖命名規則：`install-upload.png`、`settings-page.png`、`feature-xxx.png`

**d) 完成文件：**
- 將截圖嵌入文件，替換所有 `<!-- SCREENSHOT -->` 佔位符
- 產出的 `docs/USAGE.md` 適合兩種讀者：
  - (A) 完全不懂的一般使用者（包含詳細步驟截圖）
  - (B) 有基本 WordPress 操作能力的人

### Step 7: SonarCloud Code Quality Check（半人工）

打包發布前，使用 SonarCloud 做程式碼品質掃描：

**a) AI 準備：**
- 列出所有需要掃描的 `.php` 檔案清單
- 確認程式碼已通過 Step 5 的 `php -l` 語法檢查
- 提醒用戶「該做 SonarQube 掃描了」

**b) 用戶操作：**
- 到 [SonarCloud](https://sonarcloud.io/) 網頁介面執行掃描
- 檢查報告中的 Bugs、Vulnerabilities、Code Smells
- 將掃描結果（問題清單）回報給 AI

**c) AI 修復：**
- 根據 SonarCloud 報告的問題逐一修復
- 修復後重新執行 `php -l` 語法檢查
- 請用戶再次掃描確認問題已解決

**品質門檻（Quality Gate）建議：**
- Bugs：0（不能有任何 bug）
- Vulnerabilities：0（不能有安全漏洞）
- Code Smells：可容忍少量，但應盡量修復
- Duplicated Lines：< 3%

### Step 8: Package for Release（打包發布）

外掛開發完成、文件齊全後，準備正式發布：

**a) 產生 README.md：**
- 用於 GitHub 頁面的外掛簡介
- 包含：概述、功能列表、安裝步驟、系統需求、授權

**b) 產生 LEARN.md：**
- 用平實語言記錄開發過程中學到的東西
- 包含：技術架構、重要觀念、經驗教訓、使用的技術
- 用比喻讓技術概念易於理解

**c) 清理開發檔案（如有）：**
- 移除 `node_modules/`、`src/css/`（Tailwind 原始檔）
- 只保留 `dist/css/`（編譯後的 CSS）
- 移除其他開發專用檔案（`package.json`、`tailwind.config.js` 等）

**d) 打包 .zip：**
```powershell
Compress-Archive -Path .\plugin\* -DestinationPath .\plugin-name.zip -Force
```
- `.zip` 內容只包含正式發布需要的檔案
- 可直接上傳到 WordPress 或 GitHub

## Local Development Environment

- WordPress 測試站：http://localhost/my-tiny-wpdev/
- WordPress 路徑：`C:\xampp\htdocs\my-tiny-wpdev\`
- 外掛目錄：`C:\xampp\htdocs\my-tiny-wpdev\wp-content\plugins\`
- 管理員帳號：`my_tiny_wpdev`
- PHP 路徑：`C:\xampp\php\php.exe`
- 密碼：不記錄在此，需要時請詢問用戶

## Available Scripts

### setup-plugin.bat (Windows) / setup-plugin.sh (Mac/Linux)

Creates a new plugin from the template with placeholders replaced.

**Windows:** `scripts\setup-plugin.bat "Plugin Name" [output-directory]`

**Mac/Linux:** `./scripts/setup-plugin.sh "Plugin Name" [output-directory]`

### build-tailwind.bat (Windows) / build-tailwind.sh (Mac/Linux)

Builds Tailwind CSS for an existing plugin project.

**Windows:** `scripts\build-tailwind.bat \path\to\plugin`

**Mac/Linux:** `./scripts/build-tailwind.sh /path/to/plugin`

## Template Structure

The plugin template includes:

```
plugin-template/
├── {{plugin-name}}.php    # Main plugin file with header
├── package.json           # Node dependencies
├── tailwind.config.js     # Tailwind configuration
├── postcss.config.js      # PostCSS configuration
├── src/css/main.css       # Tailwind entry point
├── admin/
│   └── settings-page.php  # Example admin page
├── inc/
│   └── class-validator.php # Input validation helper class
├── readme.txt             # WordPress.org readme template
└── dist/                  # Compiled CSS output
```

## Customization

### Adding Custom Tailwind Components

Edit `src/css/main.css` to add custom components:

```css
@layer components {
  .my-plugin-button {
    @apply bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded;
  }
}
```

### Configuring Content Paths

Update `tailwind.config.js` content array to match your plugin structure:

```javascript
content: [
  "./admin/**/*.php",
  "./inc/**/*.php",
  "*.php"
],
```

## Input Validation

Every plugin should validate all user inputs. The template includes a `class-validator.php` helper class with common validation methods (IP, email, numeric, string length, etc.).

For detailed validation rules and examples, see [validation-rules.md](references/validation-rules.md).

## WordPress Plugin Standards

For WordPress coding standards and best practices, see [plugin-structure.md](references/plugin-structure.md).

Key requirements:
- Valid plugin header in main PHP file
- Proper text domain for translations
- Security: sanitize input, escape output, use nonces
- Prefix all functions with your plugin's text domain

## WordPress Resources

- [WordPress Plugin Developer Handbook](https://developer.wordpress.org/plugins/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/)
