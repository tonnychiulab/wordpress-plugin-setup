# LEARN.md — Validation Demo 學習筆記

> 這份文件記錄了在開發 Validation Demo 外掛過程中學到的東西。
> 寫給未來的自己看，也寫給其他想學 WordPress 開發的人看。

---

## 🧠 這個外掛在幹嘛？

想像你開了一家餐廳，客人在點餐單上寫下各種需求。你不能照單全收——萬一有人寫「給我 1000 碗飯」或是夾帶奇怪符號呢？**輸入驗證就是幫你檢查點餐單的服務生**。

這個外掛展示了 12 種不同的「檢查方式」，從 Email 到身分證字號都有。

---

## 🏗️ 技術架構

### 整體結構（只有一個檔案！）

```
validation-demo/
├── validation-demo.php    ← 全部的程式碼都在這裡
├── README.md              ← GitHub 頁面說明
└── docs/
    ├── USAGE.md           ← 使用說明文件
    └── screenshots/       ← 截圖存放處
```

為什麼只有一個 PHP 檔案？因為這是一個**教學用 Demo**，故意保持簡單，讓你能在一個檔案裡看到所有程式碼的運作方式。正式的大型外掛會拆分成多個檔案。

### 程式碼架構

```
Validation_Demo_Plugin（主類別）
├── get_validation_types()  ← 12 種驗證類型的設定檔（資料驅動）
├── get_tabs()              ← 4 個分頁標籤的設定
├── __construct()           ← 註冊 WordPress 鉤子（hooks）
├── enqueue_assets()        ← 載入 jQuery
├── add_menu()              ← 建立後台選單
├── ajax_validate()         ← ⭐ 核心：接收 AJAX 請求並驗證
├── get_ip_details()        ← IPv4 的輔助函式
└── render_page()           ← 產生整個 HTML 頁面
```

---

## 💡 學到的重要觀念

### 1. 驗證的三層優先順序

這是這個外掛最核心的教學：

| 優先順序 | 來源 | 為什麼？ |
|---------|------|---------|
| 第一 | 🔵 WordPress 官方 | 跟著老大走，穩定、更新、社群維護 |
| 第二 | 🐘 PHP 內建 | WordPress 沒有的，PHP 通常有 |
| 第三 | 🟠 OWASP regex | 前兩者都沒有的，用安全界公認的標準 |

**比喻**：就像看病一樣——先看家庭醫生（WordPress），家庭醫生沒辦法就看專科（PHP），專科也沒辦法才去找國際知名醫院（OWASP）。

### 2. WordPress 安全三道鎖

每次處理使用者的 AJAX 請求，都要做三件事：

```php
// 第一道鎖：Nonce 驗證（防止偽造請求）
wp_verify_nonce( $nonce, 'vd_nonce' );

// 第二道鎖：權限檢查（確認是管理員）
current_user_can( 'manage_options' );

// 第三道鎖：清理輸入（移除危險內容）
sanitize_text_field( wp_unslash( $_POST['value'] ) );
```

**比喻**：就像進銀行金庫——先刷門禁卡（nonce），再確認身分（capability），最後金屬探測器掃一遍（sanitize）。

### 3. 資料驅動設計（Data-Driven Design）

這個外掛用一個大的 PHP 陣列（`get_validation_types()`）來定義所有驗證類型。要新增一種驗證，只需要在陣列裡加一筆資料，不需要改 HTML 或 JavaScript。

**比喻**：就像 Excel 表格一樣——你只需要加一行資料，整個報表就自動更新。

---

## 🐛 學到的經驗教訓

### 密碼欄位不能做 sanitize

```php
// ❌ 錯誤：sanitize_text_field() 會把特殊字元洗掉
$value = sanitize_text_field( $_POST['value'] );

// ✅ 正確：密碼需要保留特殊字元
if ( 'password' === $type ) {
    $value = wp_unslash( $_POST['value'] );  // 只做 unslash
}
```

**為什麼？** 因為密碼的重點就是要有各種特殊字元（`!@#$%`），如果你把它們洗掉了，使用者的密碼就變了。

### 模板檔案 PHP 語法檢查會報錯

用 `php -l` 檢查含有 `{{placeholder}}` 的模板檔案時，PHP 會報語法錯誤。這是**正常的**，因為 `{{` 在 PHP 裡有特殊意義。只要最終產出的檔案通過語法檢查就好。

---

## 🔧 使用的技術

| 技術 | 用途 | 為什麼選它 |
|------|------|-----------|
| PHP 類別 | 程式碼組織 | 避免全域函式污染，更好管理 |
| WordPress AJAX | 前後端溝通 | WordPress 標準做法，內建安全機制 |
| CSS 變數 | 樣式管理 | 改一個變數就能改全站色調 |
| Grid Layout | 卡片排版 | 響應式設計，手機上自動變一欄 |
| Dashicons | 圖示 | WordPress 內建，不需要額外載入 |

---

## 📚 延伸閱讀

- [WordPress Plugin Developer Handbook](https://developer.wordpress.org/plugins/)
- [OWASP Validation Regex Repository](https://owasp.org/www-community/OWASP_Validation_Regex_Repository)
- [validation-rules.md](../../references/validation-rules.md) — 完整的驗證規則參考

---

*最後更新：2026-02-15*
