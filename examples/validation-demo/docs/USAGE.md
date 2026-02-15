# Validation Demo - 使用說明

<!-- 
  此文件由 wordpress-plugin-setup Skill 自動產生
  產生日期：2026-02-15
  外掛版本：3.0.0
-->

> 本說明適用於 WordPress 5.0 以上版本。

---

## 📦 安裝步驟

### 方法一：透過 WordPress 後台安裝（推薦）

1. 下載外掛的 `.zip` 壓縮檔
2. 登入你的 WordPress 後台
3. 前往左側選單 **外掛** → **安裝外掛**
4. 點擊頁面上方的 **「上傳外掛」** 按鈕
5. 選擇你下載的 `validation-demo.zip` 檔案，然後點擊 **「立即安裝」**
6. 安裝完成後，點擊 **「啟用外掛」**

<!-- SCREENSHOT: WordPress 後台 → 外掛 → 安裝外掛 → 上傳外掛畫面 -->
![上傳外掛畫面](screenshots/install-upload.png)

### 方法二：透過 FTP 手動安裝

1. 解壓縮外掛 `.zip` 檔案
2. 使用 FTP 工具將 `validation-demo` 資料夾上傳到 `wp-content/plugins/` 目錄
3. 到 WordPress 後台 → **外掛** → 找到 **Validation Demo** 並點擊 **「啟用」**

---

## ⚙️ 設定說明

本外掛**不需要額外設定**。啟用後即可直接使用。

啟用後，你會在 WordPress 後台的左側選單看到一個新的選項：

**路徑：** 左側選單 → **Validation Demo**（盾牌圖示 🛡️）

<!-- SCREENSHOT: WordPress 後台左側選單，標示 Validation Demo 的位置 -->
![選單位置](screenshots/menu-location.png)

---

## 🎯 功能介紹

Validation Demo 是一個**輸入驗證教學工具**，展示如何在 WordPress 中正確驗證各種使用者輸入。

### 分頁標籤系統

外掛頁面頂部有 **4 個分頁標籤**，按照驗證方式的優先順序排列：

| 標籤 | 說明 | 包含的驗證類型 |
|------|------|----------------|
| 🔵 WordPress 官方 | WordPress 有內建函式，直接用 | Email、CSS 色碼、URL Slug |
| 🐘 PHP 內建 | WordPress 沒有，用 PHP 的函式 | IPv4、純數字、正整數、英文字母、英數字組合 |
| 🇹🇼 台灣在地 | 自訂的台灣格式驗證 | 台灣手機號碼、身分證字號、日期 |
| 🟠 OWASP 補充 | WP 和 PHP 都沒有，用 OWASP 規範 | 密碼複雜度、MAC 位址 |

<!-- SCREENSHOT: 外掛主頁面，顯示 4 個分頁標籤 -->
![分頁標籤](screenshots/tabs-overview.png)

### 如何使用

1. 點擊你想測試的分頁標籤
2. 在對應的卡片輸入框中，輸入你想驗證的內容
3. 點擊 **「驗證」** 按鈕
4. 系統會即時顯示驗證結果：
   - ✅ **綠色** = 驗證通過，顯示詳細資訊
   - ❌ **紅色** = 驗證失敗，顯示錯誤原因

<!-- SCREENSHOT: 驗證成功的範例（例如輸入合法 Email） -->
![驗證成功範例](screenshots/validation-success.png)

<!-- SCREENSHOT: 驗證失敗的範例（例如輸入無效 Email） -->
![驗證失敗範例](screenshots/validation-fail.png)

### 各驗證類型範例

| 類型 | 合法輸入範例 | 不合法輸入範例 |
|------|-------------|---------------|
| Email | `user@example.com` | `user@` |
| CSS 色碼 | `#FF5733` | `FF5733`（缺少 #） |
| URL Slug | `My Blog Post!` → 自動轉換 | （任何輸入都會被轉換） |
| IPv4 | `192.168.1.100` | `999.999.999.999` |
| 純數字 | `12345` | `123abc` |
| 台灣手機 | `0912345678` | `1234567890` |
| 密碼 | `MyPass1` | `abc`（太短，缺大寫和數字） |

---

## 📋 系統需求

- WordPress 5.0 或以上
- PHP 7.2 或以上

---

## 📸 需要截圖清單

> 以下是需要你手動截圖的頁面，截圖後請放到 `docs/screenshots/` 資料夾。

| # | 截圖內容 | 建議檔名 | 如何到達該頁面 |
|---|----------|----------|---------------|
| 1 | 上傳外掛畫面 | `install-upload.png` | 後台 → 外掛 → 安裝外掛 → 上傳外掛 |
| 2 | 左側選單位置 | `menu-location.png` | 後台左側選單，找到 Validation Demo |
| 3 | 分頁標籤總覽 | `tabs-overview.png` | 點擊 Validation Demo，看到 4 個分頁 |
| 4 | 驗證成功範例 | `validation-success.png` | 輸入 `user@example.com` 並驗證 |
| 5 | 驗證失敗範例 | `validation-fail.png` | 輸入 `user@` 並驗證 |

---

*此文件由 [wordpress-plugin-setup](https://github.com/) Skill 協助產生*
