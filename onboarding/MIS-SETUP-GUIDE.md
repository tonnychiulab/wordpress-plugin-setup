# 🔧 MIS 環境建置手冊 — Azure Virtual Desktop (AVD)

> **文件對象**：MIS / IT 同事
> **目的**：確保每台 VDI 虛擬桌面都有一致的開發環境，新人拿到帳號就能直接開始工作。
> **最後更新**：2026-02-15

---

## 📋 總覽

```
新人報到流程：

  MIS 建 AD 帳號 → 佈建 AVD 虛擬桌面 → 預裝軟體 → 設定服務 → 驗收 → 交給新人
  ──────────────    ─────────────────    ─────────    ─────────    ────    ────────
    Step 1              Step 2            Step 3      Step 4     Step 5    完成！
```

---

## Step 1：建立 AD 帳號

### 1.1 Windows Active Directory 帳號

在 AD 中建立新使用者帳號，以下欄位必填：

| 欄位 | 說明 | 範例 |
|------|------|------|
| 使用者名稱 | 統一格式（建議：名字拼音） | `tony.lin` |
| 顯示名稱 | 中文全名 | `林東尼` |
| 電子郵件 | 公司信箱 | `tony.lin@company.com` |
| 部門 | 所屬部門 | `WordPress 外掛開發組` |
| 群組 | 加入開發者群組 | `WP-Developers` |

### 1.2 外部服務帳號（AD SSO 連動）

以下服務已與 AD 做 Single Sign-On（單一登入），建完 AD 帳號後需到各平台邀請新成員加入：

| 服務 | 用途 | 操作 |
|------|------|------|
| **GitHub** | 程式碼版本控制與發布 | 到 GitHub Organization → People → Invite member，使用新人的公司信箱邀請 |
| **SonarCloud** | 程式碼品質掃描 | 到 SonarCloud Organization → Members → Add member，使用 AD SSO 帳號加入 |

> [!IMPORTANT]
> 新人必須在兩個外部平台都能成功登入，才算帳號設定完成。請在驗收時確認。

---

## Step 2：佈建 AVD 虛擬桌面

### 2.1 建立 AVD Session Host

在 Azure Portal 中為新人分配虛擬桌面：

1. 進入 **Azure Portal** → **Azure Virtual Desktop** → **Host pools**
2. 選擇開發團隊使用的 Host Pool
3. 新增 Session Host 或將新使用者加入現有的 Host Pool
4. **指派使用者**：將新人的 AD 帳號加入 **Application Group** 的使用者清單

### 2.2 建議的 VM 規格

| 項目 | 建議規格 |
|------|----------|
| 作業系統 | Windows 11 Enterprise |
| CPU | 4 vCPU 以上 |
| 記憶體 | 8 GB 以上（建議 16 GB） |
| 儲存空間 | 128 GB SSD 以上 |

---

## Step 3：預裝軟體

> [!TIP]
> 建議將以下軟體打包成 **AVD 黃金映像檔（Golden Image）**，這樣每次開新 VDI 就不用重裝。

### 3.1 軟體清單

以下是必須預裝的 6 套軟體：

| # | 軟體 | 用途 | 下載位置 | 安裝備註 |
|:-:|------|------|----------|----------|
| 1 | **XAMPP 8.x** | 本地 PHP + Apache + MariaDB 環境 | [apachefriends.org](https://www.apachefriends.org/) | 安裝到 `C:\xampp\`，只勾選 Apache、MySQL、PHP、phpMyAdmin |
| 2 | **Node.js LTS** | npm 套件管理、Tailwind CSS 編譯 | [nodejs.org](https://nodejs.org/) | 安裝 LTS 版本，安裝時勾選「Add to PATH」 |
| 3 | **Git for Windows** | 版本控制 | [git-scm.com](https://git-scm.com/) | 安裝時選「Use Git from the Windows Command Prompt」 |
| 4 | **VS Code** | 程式碼編輯器 | [code.visualstudio.com](https://code.visualstudio.com/) | System Installer（所有使用者都能用） |
| 5 | **Google Antigravity** | AI 編程助手 | [antigravity.google](https://antigravity.google/) | 依官網指示安裝 |
| 6 | **7-Zip** | 壓縮/解壓縮工具 | [7-zip.org](https://www.7-zip.org/) | 安裝 64-bit 版本 |

### 3.2 VS Code 擴充套件

安裝完 VS Code 後，預裝以下 4 個擴充套件：

```
可用命令列批次安裝：
code --install-extension bmewburn.vscode-intelephense-client
code --install-extension johnbillion.vscode-wordpress-hooks
code --install-extension bradlc.vscode-tailwindcss
code --install-extension eamodio.gitlens
```

| 擴充套件 | ID | 用途 |
|----------|-----|------|
| **PHP Intelephense** | `bmewburn.vscode-intelephense-client` | PHP 智慧提示、自動完成、錯誤檢查 |
| **WordPress Hooks IntelliSense** | `johnbillion.vscode-wordpress-hooks` | WordPress Hook（鉤子）的自動完成 |
| **Tailwind CSS IntelliSense** | `bradlc.vscode-tailwindcss` | Tailwind CSS class 的自動完成和預覽 |
| **GitLens** | `eamodio.gitlens` | Git 歷史紀錄視覺化、程式碼作者標示 |

> [!NOTE]
> 如果要建立黃金映像檔，可以用腳本一次裝好所有擴充套件。把上面四行 `code --install-extension` 指令放到一個 `.bat` 檔案裡，映像檔製作時執行一次即可。

---

## Step 4：環境設定

### 4.1 系統環境變數

確保以下路徑已加入系統 `PATH`：

```
C:\xampp\php          ← PHP 執行檔（用於 php -l 語法檢查）
C:\Program Files\Git\bin   ← Git（通常安裝時會自動加入）
C:\Program Files\nodejs    ← Node.js + npm（通常安裝時會自動加入）
```

**驗證方式**（打開 PowerShell 執行）：
```powershell
php -v        # 應顯示 PHP 版本
node -v       # 應顯示 Node.js 版本
npm -v        # 應顯示 npm 版本
git --version # 應顯示 Git 版本
```

### 4.2 WordPress 測試站部署

在 XAMPP 環境中建立共用的 WordPress 測試站：

1. **下載 WordPress**：到 [wordpress.org](https://wordpress.org/download/) 下載最新版
2. **解壓到**：`C:\xampp\htdocs\my-tiny-wpdev\`
3. **建立資料庫**：
   - 啟動 XAMPP 的 Apache + MySQL
   - 打開 `http://localhost/phpmyadmin`
   - 建立名為 `my_tiny_wpdev` 的資料庫（編碼選 `utf8mb4_general_ci`）
4. **執行安裝**：
   - 打開 `http://localhost/my-tiny-wpdev/`
   - 依照安裝精靈完成設定
   - 管理員帳號統一使用：`my_tiny_wpdev`
   - 密碼：請找貓哥取得統一密碼

### 4.3 Git 全域設定

為新人設定 Git 基本資訊（登入後由新人自行修改為自己的資訊）：

```bash
git config --global core.autocrlf true
git config --global init.defaultBranch main
```

### 4.4 桌面捷徑

在桌面建立以下捷徑，方便新人快速找到工具：

| 捷徑 | 指向 |
|------|------|
| 📝 VS Code | VS Code 主程式 |
| 🌐 XAMPP Control Panel | `C:\xampp\xampp-control.exe` |
| 🔗 WordPress 測試站 | `http://localhost/my-tiny-wpdev/wp-admin/` |
| 📂 外掛開發目錄 | `C:\xampp\htdocs\my-tiny-wpdev\wp-content\plugins\` |

---

## Step 5：驗收檢查表

> [!CAUTION]
> 在把 VDI 交給新人之前，請逐項確認以下所有項目都打勾 ✅

### 帳號

- [ ] AD 帳號已建立，可登入 Windows
- [ ] GitHub Organization 已邀請，新人可透過 SSO 登入
- [ ] SonarCloud 已加入團隊，新人可透過 SSO 登入

### 軟體

- [ ] XAMPP 已安裝，Apache + MySQL 可正常啟動
- [ ] Node.js + npm 已安裝（`node -v` 可執行）
- [ ] Git 已安裝（`git --version` 可執行）
- [ ] VS Code 已安裝
- [ ] VS Code 4 個擴充套件已安裝
- [ ] Google Antigravity 已安裝
- [ ] 7-Zip 已安裝

### 環境

- [ ] PHP 路徑已加入系統 PATH（`php -v` 可執行）
- [ ] WordPress 測試站可正常存取（`http://localhost/my-tiny-wpdev/`）
- [ ] WordPress 管理後台可登入
- [ ] 桌面捷徑已建立（VS Code、XAMPP、WordPress、外掛目錄）

### 連線

- [ ] AVD 遠端桌面可正常連入
- [ ] 網路可存取 GitHub.com
- [ ] 網路可存取 SonarCloud.io

---

## 📝 維護紀錄

| 日期 | 異動內容 | 負責人 |
|------|----------|--------|
| 2026-02-15 | 初版建立 | — |

---

> 💡 **提示**：如果軟體有版本更新需求，請更新本文件的軟體清單，並重新製作黃金映像檔。
