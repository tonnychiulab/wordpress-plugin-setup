# WordPress 外掛輸入驗證規範

> 每一筆使用者輸入都像「門口的訪客」—— 你必須先檢查身份，才能讓他進門。  
> 這份文件就是你的「安檢清單」。

## 📌 本文件的優先級原則

> [!IMPORTANT]
> **WordPress 官方函式是第一優先！**  
> 官方文件：[developer.wordpress.org/apis/security/](https://developer.wordpress.org/apis/security/)  
>
> 只有在 WordPress **沒有**提供對應函式時，才使用 PHP 內建函式或 OWASP 正規表示式。

決策流程圖：

```
使用者輸入進來了
    │
    ▼
WordPress 有內建函式嗎？ ──是──▶ 用 WordPress 的（如 sanitize_email、is_email）
    │
    否
    ▼
PHP 有內建函式嗎？ ──是──▶ 用 PHP 的（如 filter_var、ctype_digit）
    │
    否
    ▼
用 OWASP 正規表示式 ──▶（如密碼複雜度、信用卡號、MAC 位址）
```

---

## 第一部分：WordPress 官方安全體系

> 以下內容來自 WordPress 官方安全文件，連結皆指向 [developer.wordpress.org](https://developer.wordpress.org/apis/security/)

### 1. 五條黃金法則

WordPress 官方定義了開發安全外掛的 5 條核心原則：

| # | 原則 | 白話解釋 |
|---|------|---------|
| 1 | **永遠不要信任使用者輸入** | 所有資料都可能被偽造，不管是管理員還是訪客 |
| 2 | **跳脫（Escape）要越晚越好** | 在 `echo` 的最後一秒才做跳脫，不要提前 |
| 3 | **所有不信任的來源都要跳脫** | 資料庫、第三方 API、使用者——通通不信任 |
| 4 | **永遠不要假設任何事** | 不要假設資料是乾淨的、不要假設使用者會乖乖填表 |
| 5 | **驗證（Validation）優於清理（Sanitization）** | 能「拒絕不合法的」比「把髒資料洗乾淨」更安全 |

---

### 2. WordPress 內建清理函式（Sanitization）

> 官方文件：[Sanitizing Data](https://developer.wordpress.org/apis/security/sanitizing/)
>
> 清理 = 把「危險的部分洗掉」，讓資料變安全。就像把蔬菜洗乾淨再下鍋。

| 函式名稱 | 用途 | 使用時機 |
|----------|------|----------|
| [`sanitize_text_field()`](https://developer.wordpress.org/reference/functions/sanitize_text_field/) | 移除 HTML 標籤、多餘空白、無效 UTF-8 | 一般文字輸入 |
| [`sanitize_textarea_field()`](https://developer.wordpress.org/reference/functions/sanitize_textarea_field/) | 同上，但保留換行 | 多行文字框 |
| [`sanitize_email()`](https://developer.wordpress.org/reference/functions/sanitize_email/) | 移除 Email 中不合法的字元 | Email 欄位 |
| [`sanitize_url()`](https://developer.wordpress.org/reference/functions/sanitize_url/) | 清理 URL，移除不合法字元 | 網址欄位 |
| [`sanitize_file_name()`](https://developer.wordpress.org/reference/functions/sanitize_file_name/) | 清理檔案名稱中的特殊字元 | 檔案上傳 |
| [`sanitize_title()`](https://developer.wordpress.org/reference/functions/sanitize_title/) | 移除特殊字元，轉小寫（Slug 友善） | 標題 / URL slug |
| [`sanitize_title_with_dashes()`](https://developer.wordpress.org/reference/functions/sanitize_title_with_dashes/) | 同上，保留連字號 | Slug 產生 |
| [`sanitize_key()`](https://developer.wordpress.org/reference/functions/sanitize_key/) | 只保留小寫字母、數字、底線、連字號 | 設定名稱、Meta key |
| [`sanitize_hex_color()`](https://developer.wordpress.org/reference/functions/sanitize_hex_color/) | 驗證並清理 CSS 色碼（#RRGGBB） | 色彩選擇器 |
| [`sanitize_hex_color_no_hash()`](https://developer.wordpress.org/reference/functions/sanitize_hex_color_no_hash/) | 同上但不含 # 號 | 色碼值 |
| [`sanitize_html_class()`](https://developer.wordpress.org/reference/functions/sanitize_html_class/) | 只保留 A-Z、a-z、0-9、連字號 | HTML class 屬性 |
| [`sanitize_meta()`](https://developer.wordpress.org/reference/functions/sanitize_meta/) | 清理 Meta 資料 | Post/User/Term meta |
| [`sanitize_mime_type()`](https://developer.wordpress.org/reference/functions/sanitize_mime_type/) | 清理 MIME 類型字串 | 檔案類型判斷 |
| [`sanitize_option()`](https://developer.wordpress.org/reference/functions/sanitize_option/) | 根據設定名稱自動清理 | Options API |
| [`sanitize_sql_orderby()`](https://developer.wordpress.org/reference/functions/sanitize_sql_orderby/) | 清理 SQL ORDER BY 子句 | 資料庫排序 |
| [`sanitize_user()`](https://developer.wordpress.org/reference/functions/sanitize_user/) | 清理使用者名稱 | 使用者註冊/登入 |
| [`wp_kses()`](https://developer.wordpress.org/reference/functions/wp_kses/) | 只允許指定的 HTML 標籤和屬性 | 允許部分 HTML 的內容 |
| [`wp_kses_post()`](https://developer.wordpress.org/reference/functions/wp_kses_post/) | 允許文章中常見的 HTML 標籤 | 文章內容 |
| `absint()` | 轉為正整數（absolute integer） | 數字 ID |
| `intval()` | 轉為整數 | 數值計算 |

```php
// ✅ 範例：清理表單中的各種欄位
$title = sanitize_text_field( wp_unslash( $_POST['title'] ) );
$email = sanitize_email( wp_unslash( $_POST['email'] ) );
$url   = sanitize_url( wp_unslash( $_POST['website'] ) );
$slug  = sanitize_title( wp_unslash( $_POST['slug'] ) );
$color = sanitize_hex_color( wp_unslash( $_POST['color'] ) );
$id    = absint( $_POST['post_id'] );
```

---

### 3. WordPress 內建驗證函式（Validation）

> 官方文件：[Validating Data](https://developer.wordpress.org/apis/security/data-validation/)
>
> 驗證 = 檢查資料「格式對不對」，不對就**直接拒絕**。比清理更嚴格，就像門口寫「穿拖鞋不得入內」。

| 函式名稱 | 用途 | 回傳值 |
|----------|------|--------|
| [`is_email()`](https://developer.wordpress.org/reference/functions/is_email/) | 驗證 Email 格式 | Email 字串 或 `false` |
| [`term_exists()`](https://developer.wordpress.org/reference/functions/term_exists/) | 檢查分類/標籤是否存在 | 陣列 或 `null` |
| [`username_exists()`](https://developer.wordpress.org/reference/functions/username_exists/) | 檢查使用者名稱是否已被使用 | 使用者 ID 或 `false` |
| [`validate_file()`](https://developer.wordpress.org/reference/functions/validate_file/) | 檢查檔案路徑是否合法（防目錄遍歷攻擊） | `0` = 合法 |
| [`wp_verify_nonce()`](https://developer.wordpress.org/reference/functions/wp_verify_nonce/) | 驗證 Nonce 安全令牌 | `1` / `2` / `false` |
| `in_array( ..., true )` | 白名單比對（嚴格型別） | `true` / `false` |

#### 官方推薦的四種驗證策略

```php
// 策略一：白名單（Safelist）—— 最安全！
// 只接受你明確允許的值
$allowed = array( 'author', 'date', 'title' );
$orderby = sanitize_key( wp_unslash( $_POST['orderby'] ) );
if ( in_array( $orderby, $allowed, true ) ) {
    // ✅ 合法，繼續處理
}

// 策略二：格式檢測（Format Detection）—— 檢查格式對不對
if ( ! ctype_alnum( $data ) ) {
    wp_die( '格式不正確' );
}

// 策略三：格式修正（Format Correction）—— 偏向清理
$trusted_integer = (int) $untrusted_integer;
$trusted_alpha   = preg_replace( '/[^a-z]/i', '', $untrusted_alpha );
$trusted_slug    = sanitize_title( $untrusted_slug );

// 策略四：黑名單（Blocklist）—— 不推薦！
// WordPress 官方說：「這很少是個好主意」
```

---

### 4. WordPress 內建跳脫函式（Escaping）

> 官方文件：[Escaping Data](https://developer.wordpress.org/apis/security/escaping/)
>
> 跳脫 = 在**輸出**的最後一刻，把資料中的特殊字元轉成無害的格式。
> 就像郵寄包裹時，在外面包一層氣泡紙——防止運送途中被破壞。

| 函式名稱 | 用途 | 使用場景 |
|----------|------|----------|
| [`esc_html()`](https://developer.wordpress.org/reference/functions/esc_html/) | 跳脫 HTML 特殊字元（`<`, `>`, `&`, `"`, `'`） | 顯示在 HTML 中的文字 |
| [`esc_attr()`](https://developer.wordpress.org/reference/functions/esc_attr/) | 跳脫 HTML 屬性值 | HTML 標籤屬性（如 `value=""`） |
| [`esc_url()`](https://developer.wordpress.org/reference/functions/esc_url/) | 跳脫並驗證 URL（移除危險協定） | `href`, `src` 等 URL |
| [`esc_js()`](https://developer.wordpress.org/reference/functions/esc_js/) | 跳脫 JavaScript 字串 | 行內 JavaScript |
| [`esc_textarea()`](https://developer.wordpress.org/reference/functions/esc_textarea/) | 跳脫 textarea 內容 | textarea 標籤 |
| [`wp_kses()`](https://developer.wordpress.org/reference/functions/wp_kses/) | 白名單式 HTML 過濾 | 允許有限 HTML 的區域 |

```php
// ✅ 輸出時必須跳脫！
echo '<p>' . esc_html( $user_name ) . '</p>';
echo '<input value="' . esc_attr( $value ) . '">';
echo '<a href="' . esc_url( $url ) . '">連結</a>';
```

---

### 5. 安全三道鎖（官方強制要求）

#### 第一道鎖：Nonce 驗證

> 官方文件：[Nonces](https://developer.wordpress.org/apis/security/nonces/)

```php
// 📤 產生 nonce（放在表單裡）
wp_nonce_field( 'my_plugin_save', 'my_plugin_nonce' );

// 📥 驗證 nonce（處理表單時）
if ( ! isset( $_POST['my_plugin_nonce'] )
     || ! wp_verify_nonce(
            sanitize_text_field( wp_unslash( $_POST['my_plugin_nonce'] ) ),
            'my_plugin_save'
     )
) {
    wp_die( '安全驗證失敗，請重新整理頁面再試一次' );
}
```

#### 第二道鎖：權限檢查

> 官方文件：[User Roles and Capabilities](https://developer.wordpress.org/apis/security/user-roles-and-capabilities/)

```php
// ✅ 確認使用者有權限執行這個操作
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( '你沒有權限執行此操作' );
}
```

#### 第三道鎖：SQL 防注入

```php
// ❌ 極度危險：直接把使用者輸入塞進 SQL
$wpdb->query( "DELETE FROM $table WHERE id = " . $_POST['id'] );

// ✅ 正確做法：使用 $wpdb->prepare()
$wpdb->query(
    $wpdb->prepare( "DELETE FROM $table WHERE id = %d", absint( $_POST['id'] ) )
);
```

---

## 第二部分：依欄位類型的具體驗證

> 這裡列出各種常見欄位的驗證方式。  
> 每種都標注是 🔵 WordPress 函式、🐘 PHP 內建、還是 🟠 OWASP 正規表示式。

### 6. Email 驗證 🔵

**WordPress 有內建函式，所以用 WordPress 的！**

```php
// ✅ 正確做法（WordPress 官方推薦）
$email = sanitize_email( wp_unslash( $_POST['email'] ) );  // 🔵 清理
if ( ! is_email( $email ) ) {                                // 🔵 驗證
    wp_die( '請輸入有效的 Email 地址' );
}
```

```php
// ❌ 不需要這樣做（多此一舉）
if ( ! preg_match( '/^[\w\.\-]+@[\w\.\-]+\.\w+$/', $email ) ) { ... }
// ↑ WordPress 的 is_email() 已經幫你做了更完整的檢查
```

---

### 7. URL 驗證 🔵

**WordPress 有內建函式！**

```php
// ✅ WordPress 官方做法
$url = sanitize_url( wp_unslash( $_POST['url'] ) );        // 🔵 清理
if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {           // 🐘 PHP 驗證
    wp_die( '請輸入有效的網址' );
}

// ✅ 輸出時只允許 HTTP/HTTPS（防止 javascript: 等攻擊）
echo '<a href="' . esc_url( $url, array( 'http', 'https' ) ) . '">連結</a>';
```

---

### 8. 色碼驗證 🔵

**WordPress 有 `sanitize_hex_color()`！**

```php
// ✅ 直接用 WordPress 的
$color = sanitize_hex_color( wp_unslash( $_POST['color'] ) );  // 🔵 清理 + 驗證
if ( null === $color ) {
    wp_die( '請輸入有效的色碼（如 #FF5733）' );
}
```

```php
// ❌ 不需要自己寫 regex（除非你需要支援不帶 # 的格式）
if ( ! preg_match( '/^#?([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/', $color ) ) { ... }
```

---

### 9. Slug / Title 驗證 🔵

**WordPress 有 `sanitize_title()`！**

```php
// ✅ WordPress 自動處理 slug
$slug = sanitize_title( wp_unslash( $_POST['slug'] ) );       // 🔵 自動轉小寫、移除特殊字元

// 也可以保留連字號的版本
$slug = sanitize_title_with_dashes( wp_unslash( $_POST['slug'] ) );
```

---

### 10. 檔案上傳驗證 🔵

```php
// ✅ 檢查檔案類型（白名單方式）
$allowed_types = array( 'image/jpeg', 'image/png', 'image/gif', 'application/pdf' );
$file_type     = wp_check_filetype( $filename );               // 🔵 WordPress 函式

if ( ! in_array( $file_type['type'], $allowed_types, true ) ) {
    wp_die( '不允許的檔案類型' );
}

// ✅ 檢查檔案路徑是否合法（防止目錄遍歷攻擊）
if ( 0 !== validate_file( $file_path ) ) {                     // 🔵 WordPress 函式
    wp_die( '無效的檔案路徑' );
}

// ✅ 檢查檔案大小（例如最大 5MB）
$max_size = 5 * 1024 * 1024;
if ( $_FILES['upload']['size'] > $max_size ) {
    wp_die( '檔案大小不得超過 5MB' );
}
```

---

### 11. IP 位址驗證 🐘

**WordPress 沒有 IP 驗證函式，所以用 PHP 內建的 `filter_var()`。**

```php
// ✅ IPv4 驗證
$ip = sanitize_text_field( wp_unslash( $_POST['ip_address'] ) );   // 🔵 先清理
if ( ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) { // 🐘 PHP 驗證
    wp_die( '無效的 IPv4 位址' );
}

// ✅ IPv6 驗證
if ( ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) { // 🐘
    wp_die( '無效的 IPv6 位址' );
}
```

#### CIDR 範圍（如 `192.168.1.0/24`）

```php
// 🐘 WordPress 和 PHP 都沒有內建 CIDR 驗證，需要自己寫
function validate_cidr( $cidr ) {
    $parts = explode( '/', $cidr );
    if ( count( $parts ) !== 2 ) {
        return false;
    }
    $ip   = $parts[0];
    $mask = (int) $parts[1];

    return filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )
           && $mask >= 0
           && $mask <= 32;
}
```

---

### 12. 數字欄位驗證 🐘

**WordPress 有 `absint()` 和 `intval()`，但更精細的數字驗證需要用 PHP。**

| 場景 | 使用的函式 | 來源 |
|------|-----------|------|
| 正整數 ID | `absint( $value )` | 🔵 WordPress |
| 轉為整數 | `intval( $value )` | 🐘 PHP |
| 純數字 0-9 | `ctype_digit( $value )` 或 `preg_match('/^[0-9]+$/', $value)` | 🐘 PHP |
| 正整數（大於 0） | `preg_match('/^[1-9]\d*$/', $value)` | 🐘 PHP |
| 整數（含負數） | `filter_var($value, FILTER_VALIDATE_INT)` | 🐘 PHP |
| 浮點數 | `filter_var($value, FILTER_VALIDATE_FLOAT)` | 🐘 PHP |
| 範圍限制 | `filter_var($value, FILTER_VALIDATE_INT, ['options' => [...]])` | 🐘 PHP |

```php
// ✅ 純數字驗證（0-9）
$value = sanitize_text_field( wp_unslash( $_POST['number_field'] ) );  // 🔵 先清理
if ( ! preg_match( '/^[0-9]+$/', $value ) ) {                          // 🐘 PHP 驗證
    wp_die( '此欄位僅允許輸入數字 0-9' );
}

// ✅ Port 號碼範圍驗證
$port = sanitize_text_field( wp_unslash( $_POST['port'] ) );          // 🔵 先清理
$port = filter_var( $port, FILTER_VALIDATE_INT, [                      // 🐘 PHP 驗證
    'options' => [ 'min_range' => 0, 'max_range' => 65535 ]
] );
if ( false === $port ) {
    wp_die( 'Port 必須是 0 到 65535 之間的整數' );
}
```

---

### 13. 字串欄位驗證 🐘

**WordPress 的 `sanitize_text_field()` 處理大部分情況，但特定格式需要 PHP。**

```php
// ✅ 長度限制
$username = sanitize_text_field( wp_unslash( $_POST['username'] ) );   // 🔵 先清理
if ( mb_strlen( $username ) > 50 ) {
    wp_die( '使用者名稱不得超過 50 個字元' );
}

// ✅ 只允許英文字母（A-Z, a-z）
// 正規表示式：^[A-Za-z]+$
if ( ! preg_match( '/^[A-Za-z]+$/', $value ) ) {                       // 🐘 PHP
    wp_die( '此欄位僅允許輸入英文字母' );
}

// ✅ 英數字組合（Alphanumeric）
// 正規表示式：^[A-Za-z0-9]+$
if ( ! ctype_alnum( $value ) ) {                                       // 🐘 PHP
    wp_die( '此欄位僅允許輸入英文字母和數字' );
}

// ✅ 移除危險字元（WordPress 函式優先）
$safe_text = wp_strip_all_tags( $value );                              // 🔵 WordPress
```

---

## 第三部分：OWASP 補充驗證（WordPress 沒有提供的）

> 以下正規表示式來自 [OWASP Validation Regex Repository](https://owasp.org/www-community/OWASP_Validation_Regex_Repository)
> —— 資安界公認的「黃金標準」。
>
> **只在 WordPress 和 PHP 都沒有對應函式時才使用！**

### 14. 密碼複雜度 🟠

WordPress 沒有密碼格式驗證函式（只有 `wp_hash_password()` 用於儲存），所以需要 OWASP。

#### 基本密碼（4-8 字元）

適用場景：內部管理系統、低風險功能

```
正規表示式：^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{4,8}$
```

| 符號 | 意思 |
|------|------|
| `(?=.*\d)` | 「前方探測」，確保至少包含一個數字 |
| `(?=.*[a-z])` | 確保至少包含一個小寫字母 |
| `(?=.*[A-Z])` | 確保至少包含一個大寫字母 |
| `.{4,8}` | 任意字元，長度 4 到 8 |

```php
// ✅ 基本密碼驗證
// ⚠️ 密碼不要 sanitize！因為會破壞使用者故意輸入的特殊字元
$password = $_POST['password'];
if ( ! preg_match( '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{4,8}$/', $password ) ) {
    wp_die( '密碼需要 4-8 字元，且包含大寫、小寫字母和數字' );
}
```

#### 複雜密碼（12-128 字元，高安全性）

適用場景：管理員帳號、金融相關、對外服務

```
正規表示式：
^(?:(?=.*\d)(?=.*[A-Z])(?=.*[a-z])|(?=.*\d)(?=.*[^A-Za-z0-9])(?=.*[a-z])|(?=.*[^A-Za-z0-9])(?=.*[A-Z])(?=.*[a-z])|(?=.*\d)(?=.*[A-Z])(?=.*[^A-Za-z0-9]))(?!.*(.)\1{2,}).{12,128}$
```

**規則說明（白話文版）：**
- 至少 12 字元，最多 128 字元
- 必須滿足以下 4 項中的至少 3 項：大寫字母、小寫字母、數字、特殊字元
- 不能有連續 3 個以上相同的字元（例如 `aaa` 不行）

```php
// ✅ 複雜密碼驗證
$pattern = '/^(?:(?=.*\d)(?=.*[A-Z])(?=.*[a-z])|(?=.*\d)(?=.*[^A-Za-z0-9])(?=.*[a-z])|(?=.*[^A-Za-z0-9])(?=.*[A-Z])(?=.*[a-z])|(?=.*\d)(?=.*[A-Z])(?=.*[^A-Za-z0-9]))(?!.*(.)\1{2,}).{12,128}$/';

if ( ! preg_match( $pattern, $password ) ) {
    wp_die( '密碼需要 12-128 字元，且至少包含大寫、小寫、數字、特殊字元中的三種' );
}
```

---

### 15. 信用卡號 🟠

#### 格式驗證

支援 Visa、MasterCard、Discover、American Express

```
正規表示式：^((4\d{3})|(5[1-5]\d{2})|(6011)|(7\d{3}))-?\d{4}-?\d{4}-?\d{4}|3[4,7]\d{13}$
```

| 開頭 | 發卡組織 |
|------|----------|
| `4xxx` | Visa |
| `51xx` - `55xx` | MasterCard |
| `6011` | Discover |
| `34xx` / `37xx` | American Express |

```php
// ✅ 信用卡號格式驗證
$card = preg_replace( '/[\s\-]/', '', sanitize_text_field( wp_unslash( $_POST['card'] ) ) );
$pattern = '/^((4\d{3})|(5[1-5]\d{2})|(6011)|(7\d{3}))\d{4}\d{4}\d{4}|3[47]\d{13}$/';

if ( ! preg_match( $pattern, $card ) ) {
    wp_die( '請輸入有效的信用卡號碼' );
}
```

#### Luhn 演算法（進階：檢查卡號數學邏輯）

> 光看格式還不夠！真正的信用卡號還需要通過 Luhn 演算法的檢驗碼驗證。
> 這個演算法就像「身分證字號的檢查碼」——最後一碼是由前面的數字計算出來的。

```php
/**
 * Luhn 演算法 — 驗證信用卡號的檢查碼
 *
 * 原理：從右邊算起，偶數位的數字乘以 2（超過 9 就減 9），
 *       全部數字加總後，如果能被 10 整除就是合法的。
 *
 * @param string $number 信用卡號（純數字）
 * @return bool 合法回傳 true
 */
function validate_luhn( $number ) {
    $sum    = 0;
    $length = strlen( $number );
    $parity = $length % 2;

    for ( $i = 0; $i < $length; $i++ ) {
        $digit = (int) $number[ $i ];

        if ( $i % 2 === $parity ) {
            $digit *= 2;
            if ( $digit > 9 ) {
                $digit -= 9;
            }
        }
        $sum += $digit;
    }

    return ( $sum % 10 ) === 0;
}
```

---

### 16. 網路與系統識別碼 🟠

#### MAC 位址

MAC 位址是網路設備的「身分證號碼」，每台電腦、路由器都有一個獨一無二的 MAC。

```
正規表示式：^([0-9a-fA-F]{2}:){5}[0-9a-fA-F]{2}$
```

```php
// ✅ MAC 位址驗證
$mac = sanitize_text_field( wp_unslash( $_POST['mac_address'] ) );     // 🔵 先清理
if ( ! preg_match( '/^([0-9a-fA-F]{2}:){5}[0-9a-fA-F]{2}$/', $mac ) ) {  // 🟠 OWASP
    wp_die( '請輸入有效的 MAC 位址（格式：XX:XX:XX:XX:XX:XX）' );
}
```

#### 網域名稱（Domain Name）

```
正規表示式：^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$
```

```php
// ✅ 網域名稱驗證
$domain = sanitize_text_field( wp_unslash( $_POST['domain'] ) );       // 🔵 先清理
if ( ! preg_match( '/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/', $domain ) ) {
    wp_die( '請輸入有效的網域名稱（例如 example.com）' );
}
```

#### 浮點數（含正負號和科學記號）

```
正規表示式：^[-+]?[0-9]+[.]?[0-9]*([eE][-+]?[0-9]+)?$
```

```php
// ✅ 浮點數驗證（支援 3.14、-2.5、1.5e10 等格式）
$float = sanitize_text_field( wp_unslash( $_POST['float_value'] ) );   // 🔵 先清理
if ( ! preg_match( '/^[-+]?[0-9]+[.]?[0-9]*([eE][-+]?[0-9]+)?$/', $float ) ) {
    wp_die( '請輸入有效的數字' );
}
```

#### GUID / UUID

```
正規表示式：^[A-Fa-f0-9]{8}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{12}$
```

```php
// ✅ GUID/UUID 驗證
$guid = sanitize_text_field( wp_unslash( $_POST['guid'] ) );           // 🔵 先清理
if ( ! preg_match( '/^[A-Fa-f0-9]{8}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{12}$/', $guid ) ) {
    wp_die( '請輸入有效的 GUID/UUID' );
}
```

---

### 17. 台灣在地格式 🐘

WordPress 和 OWASP 都沒有台灣格式，所以自己寫。

#### 台灣手機號碼

```
正規表示式：^09[0-9]{8}$
規則：09 開頭 + 8 位數字 = 共 10 碼
```

```php
$phone = sanitize_text_field( wp_unslash( $_POST['phone'] ) );         // 🔵 先清理
if ( ! preg_match( '/^09[0-9]{8}$/', $phone ) ) {
    wp_die( '請輸入有效的台灣手機號碼（09 開頭、共 10 碼）' );
}
```

#### 台灣身分證字號

```
正規表示式：^[A-Z][12][0-9]{8}$
規則：1 個大寫英文 + 1（男）或 2（女） + 8 位數字
```

```php
$tw_id = strtoupper( sanitize_text_field( wp_unslash( $_POST['tw_id'] ) ) );  // 🔵 先清理
if ( ! preg_match( '/^[A-Z][12][0-9]{8}$/', $tw_id ) ) {
    wp_die( '請輸入有效的身分證字號' );
}
```

#### 日期格式（YYYY-MM-DD）

```
正規表示式：^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$
```

```php
$date = sanitize_text_field( wp_unslash( $_POST['date'] ) );           // 🔵 先清理
if ( ! preg_match( '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/', $date ) ) {
    wp_die( '日期格式必須是 YYYY-MM-DD' );
}

// ⚡ 進階：用 PHP 的 checkdate() 確認日期真的存在
// （例如 2024-02-30 格式對但日期不存在）
$parts = explode( '-', $date );
if ( ! checkdate( (int) $parts[1], (int) $parts[2], (int) $parts[0] ) ) {  // 🐘 PHP
    wp_die( '日期不存在' );
}
```

---

## 附錄：正規表示式速查表

| 欄位類型 | 正規表示式 | 來源 |
|----------|-----------|------|
| 純數字 | `^[0-9]+$` | 🐘 PHP |
| 正整數 | `^[1-9]\d*$` | 🐘 PHP |
| 純英文字母 | `^[A-Za-z]+$` | 🐘 PHP |
| 英數字組合 | `^[A-Za-z0-9]+$` | 🐘 PHP |
| IPv4 | `^((25[0-5]\|2[0-4][0-9]\|[01]?[0-9][0-9]?)\.){3}(...)$` | 🐘 PHP `filter_var` |
| Email | 用 `is_email()` | 🔵 WordPress |
| URL | 用 `sanitize_url()` + `esc_url()` | 🔵 WordPress |
| 色碼 | 用 `sanitize_hex_color()` | 🔵 WordPress |
| Slug | 用 `sanitize_title()` | 🔵 WordPress |
| 台灣手機 | `^09[0-9]{8}$` | 🐘 自訂 |
| 身分證字號 | `^[A-Z][12][0-9]{8}$` | 🐘 自訂 |
| 日期 YYYY-MM-DD | `^\d{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[12]\d\|3[01])$` | 🐘 自訂 |
| 基本密碼 | `^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{4,8}$` | 🟠 OWASP |
| 複雜密碼 | `^(?:...).{12,128}$` | 🟠 OWASP |
| 信用卡號 | `^((4\d{3})\|(5[1-5]\d{2})\|...)...` | 🟠 OWASP |
| MAC 位址 | `^([0-9a-fA-F]{2}:){5}[0-9a-fA-F]{2}$` | 🟠 OWASP |
| 網域名稱 | `^([a-zA-Z0-9](...)?\.)+[a-zA-Z]{2,6}$` | 🟠 OWASP |
| 浮點數 | `^[-+]?[0-9]+[.]?[0-9]*([eE]...)?$` | 🟠 OWASP |
| GUID/UUID | `^[A-Fa-f0-9]{8}-...-[A-Fa-f0-9]{12}$` | 🟠 OWASP |
| 安全文字 | `^[a-zA-Z0-9 .\-]+$` | 🟠 OWASP |
