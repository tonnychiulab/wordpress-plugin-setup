# {{plugin-display-name}} - 使用說明

<!-- 
  此文件由 wordpress-plugin-setup Skill 自動產生
  產生日期：{{date}}
  外掛版本：{{version}}
-->

> 本說明適用於 WordPress 5.0 以上版本。

---

## 📦 安裝步驟

### 方法一：透過 WordPress 後台安裝（推薦）

1. 下載外掛的 `.zip` 壓縮檔
2. 登入你的 WordPress 後台
3. 前往 **外掛** → **安裝外掛**
4. 點擊頁面上方的 **「上傳外掛」** 按鈕
5. 選擇你下載的 `.zip` 檔案，然後點擊 **「立即安裝」**
6. 安裝完成後，點擊 **「啟用外掛」**

<!-- SCREENSHOT: WordPress 後台 → 外掛 → 安裝外掛 → 上傳外掛畫面 -->
<!-- 檔名建議：screenshots/install-upload.png -->

### 方法二：透過 FTP 手動安裝

1. 解壓縮外掛 `.zip` 檔案
2. 使用 FTP 工具將解壓後的資料夾上傳到 `wp-content/plugins/` 目錄
3. 到 WordPress 後台 → **外掛** → 找到此外掛並點擊 **「啟用」**

---

## ⚙️ 設定說明

啟用外掛後，到 WordPress 後台找到外掛的設定頁面：

**路徑：** {{settings-path}}

<!-- SCREENSHOT: 外掛設定頁面的完整畫面 -->
<!-- 檔名建議：screenshots/settings-page.png -->

### 設定項目說明

<!-- 以下區塊會根據外掛的實際設定項目自動產生 -->

| 設定項目 | 說明 | 預設值 |
|----------|------|--------|
| {{setting-name}} | {{setting-description}} | {{setting-default}} |

<!-- SCREENSHOT: 各個設定項目的特寫截圖（如果需要） -->
<!-- 檔名建議：screenshots/settings-detail.png -->

---

## 🎯 功能介紹

<!-- 以下區塊會根據外掛的功能清單自動產生 -->

### {{feature-name}}

{{feature-description}}

<!-- SCREENSHOT: 此功能的操作畫面 -->
<!-- 檔名建議：screenshots/feature-{{feature-slug}}.png -->

---

## ❓ 常見問題

### Q：外掛啟用後看不到設定頁面？
**A：** 請確認你使用的是管理員帳號登入。部分外掛的設定頁面只有管理員可以看到。

### Q：外掛與其他外掛衝突怎麼辦？
**A：** 請先停用其他外掛，確認是否為衝突問題。如果確認衝突，請到外掛的 GitHub Issues 頁面回報。

---

## 📋 系統需求

- WordPress 5.0 或以上
- PHP 7.4 或以上
- MySQL 5.6 或以上

---

*此文件由 [wordpress-plugin-setup](https://github.com/) Skill 協助產生*
