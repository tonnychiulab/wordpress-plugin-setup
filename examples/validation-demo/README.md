# Validation Demo

示範 WordPress 外掛中的輸入驗證最佳實踐。

## 概述

這個外掛提供一個互動式的驗證測試介面，展示如何在 WordPress 中正確驗證各種使用者輸入。依照**三層優先順序**：

1. 🔵 **WordPress 官方函式** — `sanitize_email()`、`is_email()`、`sanitize_hex_color()` 等
2. 🐘 **PHP 內建函式** — `filter_var()`、`ctype_alpha()`、`preg_match()` 等
3. 🟠 **OWASP 正規表示式** — 密碼複雜度、MAC 位址等

## 支援的驗證類型

| 類別 | 驗證項目 |
|------|---------|
| 🔵 WordPress | Email、CSS 色碼、URL Slug |
| 🐘 PHP | IPv4、純數字、正整數、英文字母、英數字組合 |
| 🇹🇼 台灣在地 | 手機號碼、身分證字號、日期格式 |
| 🟠 OWASP | 密碼複雜度、MAC 位址 |

## 安裝

1. 下載 `validation-demo.zip`
2. WordPress 後台 → 外掛 → 安裝外掛 → 上傳外掛
3. 啟用後到左側選單 **Validation Demo** 開始使用

## 系統需求

- WordPress 5.0+
- PHP 7.2+

## 使用說明

詳見 [docs/USAGE.md](docs/USAGE.md)

## 授權

GPL v2 or later
