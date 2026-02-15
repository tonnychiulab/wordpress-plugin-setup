# 🔧 WordPress Plugin Setup — AI 外掛開發工作流程

> 一套為 [Google Antigravity](https://antigravity.google/) 設計的 **Agent Skill**，讓 AI 能從零開始幫你建立、驗證、記錄、打包 WordPress 外掛。

---

## ✨ 這是什麼？

如果你用 Google Antigravity（AI 輔助程式開發）來開發 WordPress 外掛，這個 Skill 就像一本**標準作業手冊**——AI 會按照手冊裡的步驟，從建立外掛到打包發布，每一步都幫你做到位。

**適合誰？**
- 🆕 不太會寫程式，但想做 WordPress 外掛的人
- 🔧 想讓 AI 按照一致的標準幫你開發外掛的人
- 📚 想學習 WordPress 外掛開發最佳實踐的人

---

## 🚀 完整 8 步工作流程

```
Step 1 → Step 2 → Step 3 → Step 4 → Step 5 → Step 6 → Step 7 → Step 8
建立架構   安裝依賴   編譯 CSS   部署啟用   語法檢查   產生文件   品質掃描   打包發布
```

| Step | 名稱 | 自動化程度 | 說明 |
|:----:|------|:---:|------|
| 1 | **Generate Plugin Template** | 🤖 全自動 | 用腳本一鍵建立外掛骨架，自動替換名稱 |
| 2 | **Install Dependencies** | 🤖 全自動 | `npm install` 安裝 Tailwind CSS 依賴 |
| 3 | **Build CSS** | 🤖 全自動 | 編譯 Tailwind CSS，輸出到 `dist/` |
| 4 | **Activate in WordPress** | 👤 手動 | 上傳到 WordPress 並啟用外掛 |
| 5 | **PHP Syntax Check** | 🤖 全自動 | 用 `php -l` 檢查所有 PHP 檔案語法 |
| 6 | **Generate Documentation** | 🤝 半自動 | AI 產生文件骨架，你提供截圖 |
| 7 | **SonarCloud Quality Check** | 🤝 半人工 | AI 準備檔案清單，你到 SonarCloud 掃描 |
| 8 | **Package for Release** | 🤖 全自動 | 產生 README + LEARN.md + 打包 .zip |

---

## 📁 目錄結構

```
wordpress-plugin-setup/
│
├── SKILL.md                         ← AI 讀取的主指令檔
│
├── scripts/                         ← 🔧 自動化腳本
│   ├── setup-plugin.bat / .sh       ← 建立外掛模板
│   └── build-tailwind.bat / .sh     ← 編譯 Tailwind CSS
│
├── assets/                          ← 📦 靜態資源
│   ├── plugin-template/             ← 外掛模板檔案
│   │   ├── {{plugin-name}}.php      ← 主檔案（含佔位符）
│   │   ├── admin/settings-page.php  ← 設定頁面範例
│   │   ├── inc/class-validator.php  ← 輸入驗證類別
│   │   └── ...                      ← Tailwind 設定檔等
│   └── plugin-doc-template.md       ← 使用說明 MD 模板
│
├── examples/                        ← 📝 範例外掛
│   └── validation-demo/             ← 輸入驗證示範外掛（完整可用）
│
└── references/                      ← 📚 參考文件
    ├── validation-rules.md          ← 驗證規則大全
    └── plugin-structure.md          ← WordPress 外掛結構規範
```

---

## ⚡ 快速開始

### 前置需求

- [XAMPP](https://www.apachefriends.org/) 或其他 PHP 環境（需要 `php` 指令）
- [Node.js](https://nodejs.org/)（用於 Tailwind CSS 編譯）
- [Google Antigravity](https://antigravity.google/)（AI 編程助手）

### 使用方式

**方法一：直接作為 Skill 使用**

將此專案 clone 到你的工作區，Antigravity 會自動讀取 `SKILL.md` 並按照流程操作：

```bash
git clone https://github.com/你的帳號/wordpress-plugin-setup.git
```

然後在 Antigravity 中對話：
> 「幫我建立一個叫 My Cool Plugin 的 WordPress 外掛」

AI 就會自動按照 8 步流程開始工作。

**方法二：放到其他專案的 `.agent/skills/` 中**

```bash
# 在你的 WordPress 專案中
mkdir -p .agent/skills
git clone https://github.com/你的帳號/wordpress-plugin-setup.git .agent/skills/wordpress-plugin-setup
```

### 手動建立外掛

不用 AI 也能用腳本建立外掛：

```cmd
# Windows
scripts\setup-plugin.bat "My Plugin Name" .\output-directory

# Mac / Linux
./scripts/setup-plugin.sh "My Plugin Name" ./output-directory
```

---

## 🛡️ 輸入驗證三層優先順序

本 Skill 遵循的驗證優先順序原則：

```
🔵 WordPress 官方函式（第一優先）
  ↓ 如果 WordPress 沒有
🐘 PHP 內建函式（第二優先）
  ↓ 如果 PHP 也沒有
🟠 OWASP 正規表示式（第三優先）
```

| 優先順序 | 來源 | 範例 |
|:--------:|------|------|
| 1️⃣ | 🔵 WordPress | `sanitize_email()`、`is_email()`、`sanitize_hex_color()` |
| 2️⃣ | 🐘 PHP | `filter_var()`、`ctype_alpha()`、`preg_match()` |
| 3️⃣ | 🟠 OWASP | 密碼複雜度 regex、MAC 位址 regex |

詳見 [references/validation-rules.md](references/validation-rules.md)

---

## 📎 範例外掛：Validation Demo

`examples/validation-demo/` 包含一個完整可用的示範外掛，展示 12 種輸入驗證：

- **Email**、**CSS 色碼**、**URL Slug**（WordPress 函式）
- **IPv4**、**純數字**、**正整數**、**英文字母**、**英數字**（PHP 函式）
- **台灣手機號碼**、**身分證字號**、**日期格式**（自訂 regex）
- **密碼複雜度**、**MAC 位址**（OWASP regex）

> 可直接安裝到 WordPress 測試站使用。

---

## 🤖 什麼是 Agent Skill？

Agent Skill 是一種開放、輕量的格式，用來教 AI 助手特定領域的知識和工作流程。Skill 的核心是一個 `SKILL.md` 檔案，AI 會在需要時自動讀取並按照指示操作。

想像你僱了一個新助手，你不需要每次都從頭教他怎麼做事——你給他一本 SOP 手冊（`SKILL.md`），他就能按照手冊自己完成工作。

更多資訊：[Antigravity Skills 文件](https://antigravity.google/docs/skills)

---

## 📄 授權

MIT License

---

## 🙌 貢獻

歡迎提交 Issue 和 Pull Request！如果你有好用的驗證規則或外掛模板想分享，歡迎貢獻。
