<?php

/**
 * Plugin Name:       Validation Demo
 * Plugin URI:        https://example.com
 * Description:       示範輸入驗證功能 — 以 WordPress 官方函式為第一優先，PHP 為第二，OWASP 為補充
 * Version:           3.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.2
 * Author:            Tonny
 * License:           GPL v2 or later
 * Text Domain:       validation-demo
 *
 * 🔵 = WordPress 內建函式
 * 🐘 = PHP 內建函式
 * 🟠 = OWASP 正規表示式
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * 主外掛類別
 *
 * 架構原則（對應 validation-rules.md）：
 * 1. WordPress 有的 → 用 WordPress 的（如 is_email、sanitize_hex_color）
 * 2. WordPress 沒有但 PHP 有的 → 用 PHP 的（如 filter_var、ctype_digit）
 * 3. 兩者都沒有 → 用 OWASP 正規表示式（如密碼複雜度）
 */
class Validation_Demo_Plugin
{

    /**
     * 驗證類型配置
     * 每個 type 都標注使用的函式來源
     */
    private function get_validation_types()
    {
        return array(
            // ===== Tab 1：🔵 WordPress 有內建函式的 =====
            'email' => array(
                'label'       => 'Email 電子郵件',
                'icon'        => 'dashicons-email-alt',
                'icon_class'  => 'email',
                'source'      => '🔵 WordPress',
                'source_fn'   => 'sanitize_email() + is_email()',
                'desc'        => '使用 WordPress 官方函式驗證，不需要自己寫 regex',
                'placeholder' => '例如：user@example.com',
                'hint'        => '函式：sanitize_email() → is_email()',
                'tab'         => 'wordpress',
                'maxlength'   => 254,
            ),
            'hex_color' => array(
                'label'       => 'CSS 色碼',
                'icon'        => 'dashicons-art',
                'icon_class'  => 'color',
                'source'      => '🔵 WordPress',
                'source_fn'   => 'sanitize_hex_color()',
                'desc'        => 'WordPress 內建色碼驗證，支援 #RGB 和 #RRGGBB',
                'placeholder' => '例如：#FF5733 或 #F00',
                'hint'        => '函式：sanitize_hex_color()',
                'tab'         => 'wordpress',
                'maxlength'   => 7,
            ),
            'slug' => array(
                'label'       => 'URL Slug',
                'icon'        => 'dashicons-admin-links',
                'icon_class'  => 'slug',
                'source'      => '🔵 WordPress',
                'source_fn'   => 'sanitize_title()',
                'desc'        => 'WordPress 自動轉換成 URL 友善格式（小寫、連字號）',
                'placeholder' => '例如：My Blog Post!',
                'hint'        => '函式：sanitize_title()（輸入任何文字，WP 幫你轉）',
                'tab'         => 'wordpress',
                'maxlength'   => 200,
            ),

            // ===== Tab 2：🐘 PHP 內建函式 =====
            'ipv4' => array(
                'label'       => 'IPv4 位址',
                'icon'        => 'dashicons-admin-site-alt3',
                'icon_class'  => 'ip',
                'source'      => '🐘 PHP',
                'source_fn'   => 'filter_var( FILTER_VALIDATE_IP )',
                'desc'        => 'WordPress 沒有 IP 驗證，所以用 PHP 的 filter_var()',
                'placeholder' => '例如：192.168.1.100',
                'hint'        => '函式：filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )',
                'tab'         => 'php',
                'maxlength'   => 15,
            ),
            'numeric' => array(
                'label'       => '純數字（0-9）',
                'icon'        => 'dashicons-editor-ol',
                'icon_class'  => 'num',
                'source'      => '🐘 PHP',
                'source_fn'   => 'ctype_digit() 或 preg_match',
                'desc'        => 'WordPress 沒有「只允許 0-9」的函式，用 PHP 的',
                'placeholder' => '例如：12345',
                'hint'        => '正規表示式：^[0-9]+$',
                'tab'         => 'php',
                'maxlength'   => 50,
            ),
            'positive_int' => array(
                'label'       => '正整數',
                'icon'        => 'dashicons-plus-alt2',
                'icon_class'  => 'posint',
                'source'      => '🐘 PHP',
                'source_fn'   => 'preg_match',
                'desc'        => '大於 0 的整數，第一位不能是 0',
                'placeholder' => '例如：42',
                'hint'        => '正規表示式：^[1-9]\d*$',
                'tab'         => 'php',
                'maxlength'   => 50,
            ),
            'alpha' => array(
                'label'       => '純英文字母',
                'icon'        => 'dashicons-editor-spellcheck',
                'icon_class'  => 'alpha',
                'source'      => '🐘 PHP',
                'source_fn'   => 'ctype_alpha() 或 preg_match',
                'desc'        => 'WordPress 沒有字母驗證函式，用 PHP 的',
                'placeholder' => '例如：HelloWorld',
                'hint'        => '正規表示式：^[A-Za-z]+$',
                'tab'         => 'php',
                'maxlength'   => 50,
            ),
            'alphanumeric' => array(
                'label'       => '英數字組合',
                'icon'        => 'dashicons-editor-paste-text',
                'icon_class'  => 'alnum',
                'source'      => '🐘 PHP',
                'source_fn'   => 'ctype_alnum()',
                'desc'        => '只允許英文字母和數字',
                'placeholder' => '例如：Hello123',
                'hint'        => '函式：ctype_alnum()',
                'tab'         => 'php',
                'maxlength'   => 50,
            ),

            // ===== Tab 3：🐘 台灣在地 + 日期 =====
            'tw_phone' => array(
                'label'       => '台灣手機號碼',
                'icon'        => 'dashicons-smartphone',
                'icon_class'  => 'phone',
                'source'      => '🐘 自訂 regex',
                'source_fn'   => 'preg_match',
                'desc'        => 'WordPress 和 OWASP 都沒有，自己寫的台灣格式',
                'placeholder' => '例如：0912345678',
                'hint'        => '正規表示式：^09[0-9]{8}$',
                'tab'         => 'local',
                'maxlength'   => 10,
            ),
            'tw_id' => array(
                'label'       => '身分證字號',
                'icon'        => 'dashicons-id-alt',
                'icon_class'  => 'twid',
                'source'      => '🐘 自訂 regex',
                'source_fn'   => 'preg_match',
                'desc'        => '台灣身分證：英文 + 1/2 + 8 位數字',
                'placeholder' => '例如：A123456789',
                'hint'        => '正規表示式：^[A-Z][12][0-9]{8}$',
                'tab'         => 'local',
                'maxlength'   => 10,
            ),
            'date_ymd' => array(
                'label'       => '日期（YYYY-MM-DD）',
                'icon'        => 'dashicons-calendar-alt',
                'icon_class'  => 'date',
                'source'      => '🐘 PHP regex + checkdate()',
                'source_fn'   => 'preg_match + checkdate()',
                'desc'        => '格式驗證用 regex，日期是否存在用 PHP 的 checkdate()',
                'placeholder' => '例如：2024-01-15',
                'hint'        => '正規表示式 + checkdate() 雙重驗證',
                'tab'         => 'local',
                'maxlength'   => 10,
            ),

            // ===== Tab 4：🟠 OWASP =====
            'password' => array(
                'label'       => '密碼複雜度',
                'icon'        => 'dashicons-lock',
                'icon_class'  => 'pass',
                'source'      => '🟠 OWASP',
                'source_fn'   => 'OWASP regex',
                'desc'        => 'WordPress 沒有密碼格式驗證，使用 OWASP 的正規表示式',
                'placeholder' => '例如：MyPass123',
                'hint'        => '需含大寫 + 小寫 + 數字，4-8 字元',
                'tab'         => 'owasp',
                'maxlength'   => 128,
            ),
            'mac_address' => array(
                'label'       => 'MAC 位址',
                'icon'        => 'dashicons-desktop',
                'icon_class'  => 'mac',
                'source'      => '🟠 OWASP',
                'source_fn'   => 'OWASP regex',
                'desc'        => '網路設備的身分證號碼，WordPress 和 PHP 都沒有內建',
                'placeholder' => '例如：00:1A:2B:3C:4D:5E',
                'hint'        => '正規表示式：^([0-9a-fA-F]{2}:){5}[0-9a-fA-F]{2}$',
                'tab'         => 'owasp',
                'maxlength'   => 17,
            ),
        );
    }

    private function get_tabs()
    {
        return array(
            'wordpress' => array('label' => '🔵 WordPress 官方', 'desc' => 'WordPress 有內建函式，直接用'),
            'php'       => array('label' => '🐘 PHP 內建',       'desc' => 'WP 沒有，用 PHP 的'),
            'local'     => array('label' => '🇹🇼 台灣在地',       'desc' => 'WP 和 OWASP 都沒有，自己寫'),
            'owasp'     => array('label' => '🟠 OWASP 補充',     'desc' => 'WP 和 PHP 都沒有，用 OWASP'),
        );
    }

    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('admin_menu', array($this, 'add_menu'));
        add_action('wp_ajax_validation_demo_check', array($this, 'ajax_validate'));
    }

    public function enqueue_assets($hook)
    {
        if ('toplevel_page_validation-demo' !== $hook) {
            return;
        }
        wp_enqueue_script('jquery');
    }

    public function add_menu()
    {
        add_menu_page('Validation Demo', 'Validation Demo', 'manage_options', 'validation-demo', array($this, 'render_page'), 'dashicons-shield', 80);
    }

    /**
     * AJAX 驗證 — 核心邏輯
     * 每個 case 都標注使用的函式來源
     */
    public function ajax_validate()
    {
        // 🔵 WordPress 安全三道鎖
        if (! isset($_POST['nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'vd_nonce')) {
            wp_send_json_error(array('message' => '安全驗證失敗'));
        }
        if (! current_user_can('manage_options')) {
            wp_send_json_error(array('message' => '權限不足'));
        }

        $type = isset($_POST['field_type']) ? sanitize_key(wp_unslash($_POST['field_type'])) : '';

        // 密碼特殊處理：不做 sanitize（會破壞特殊字元）
        if ('password' === $type) {
            $value = isset($_POST['value']) ? wp_unslash($_POST['value']) : ''; // phpcs:ignore
        } else {
            $value = isset($_POST['value']) ? sanitize_text_field(wp_unslash($_POST['value'])) : '';
        }

        if ('' === $value) {
            wp_send_json_error(array('message' => '請輸入要驗證的內容'));
        }

        $r = array();

        switch ($type) {

            // ============================================================
            // 🔵 WordPress 內建函式
            // ============================================================

            case 'email':
                // 🔵 sanitize_email() 清理 → is_email() 驗證
                $clean = sanitize_email($value);
                if (is_email($clean)) {
                    $parts = explode('@', $clean);
                    $r = array('valid' => true, 'message' => '✅ 合法 Email', 'source' => '🔵 sanitize_email() + is_email()', 'details' => sprintf('使用者名稱：%s | 網域：%s', esc_html($parts[0]), esc_html($parts[1])));
                } else {
                    $r = array('valid' => false, 'message' => '❌ 無效 Email', 'source' => '🔵 is_email() 回傳 false', 'details' => 'WordPress 的 is_email() 判定不合法');
                }
                break;

            case 'hex_color':
                // 🔵 sanitize_hex_color() 一個函式搞定
                $clean = sanitize_hex_color($value);
                if (null !== $clean && '' !== $clean) {
                    $r = array('valid' => true, 'message' => '✅ 合法色碼', 'source' => '🔵 sanitize_hex_color()', 'details' => sprintf('清理後：%s', esc_html($clean)));
                } else {
                    // 使用者可能忘了加 #
                    $try = sanitize_hex_color('#' . ltrim($value, '#'));
                    $hint = (null !== $try) ? sprintf('你是不是忘了加 #？試試 %s', esc_html($try)) : '格式需為 #RGB 或 #RRGGBB';
                    $r = array('valid' => false, 'message' => '❌ 無效色碼', 'source' => '🔵 sanitize_hex_color() 回傳 null', 'details' => $hint);
                }
                break;

            case 'slug':
                // 🔵 sanitize_title() 不是「驗證」而是「轉換」
                $clean = sanitize_title($value);
                $r = array('valid' => true, 'message' => '✅ WordPress 自動轉換完成', 'source' => '🔵 sanitize_title()', 'details' => sprintf('輸入：%s → 輸出：%s', esc_html($value), esc_html($clean)));
                break;

            // ============================================================
            // 🐘 PHP 內建函式
            // ============================================================

            case 'ipv4':
                // 🐘 filter_var()（WordPress 沒有 IP 驗證）
                if (false !== filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    $r = array('valid' => true, 'message' => '✅ 合法 IPv4', 'source' => '🐘 filter_var( FILTER_VALIDATE_IP )', 'details' => $this->get_ip_details($value));
                } else {
                    $r = array('valid' => false, 'message' => '❌ 無效 IPv4', 'source' => '🐘 filter_var() 回傳 false', 'details' => '格式：X.X.X.X，每段 0-255');
                }
                break;

            case 'numeric':
                // 🐘 preg_match（WordPress 只有 absint/intval，沒有「純 0-9」驗證）
                if (preg_match('/^[0-9]+$/', $value)) {
                    $r = array('valid' => true, 'message' => '✅ 合法純數字', 'source' => '🐘 preg_match( /^[0-9]+$/ )', 'details' => sprintf('數值：%s | %d 位數', esc_html($value), strlen($value)));
                } else {
                    $invalid = preg_replace('/[0-9]/', '', $value);
                    $r = array('valid' => false, 'message' => '❌ 包含非數字', 'source' => '🐘 preg_match 失敗', 'details' => sprintf('不合法字元：「%s」', esc_html($invalid)));
                }
                break;

            case 'positive_int':
                // 🐘 preg_match
                if (preg_match('/^[1-9]\d*$/', $value)) {
                    $r = array('valid' => true, 'message' => '✅ 合法正整數', 'source' => '🐘 preg_match( /^[1-9]\d*$/ )', 'details' => sprintf('數值：%s', esc_html($value)));
                } else {
                    $reason = '正整數 = 大於 0 的整數，第一位不能是 0';
                    if ('0' === $value) {
                        $reason = '0 不是正整數';
                    } elseif ('0' === substr($value, 0, 1)) {
                        $reason = '不能以 0 開頭（如 007）';
                    } elseif (false !== strpos($value, '.')) {
                        $reason = '不能有小數點';
                    } elseif (false !== strpos($value, '-')) {
                        $reason = '不能是負數';
                    }
                    $r = array('valid' => false, 'message' => '❌ 不是正整數', 'source' => '🐘 preg_match 失敗', 'details' => $reason);
                }
                break;

            case 'alpha':
                // 🐘 ctype_alpha()
                if (ctype_alpha($value)) {
                    $r = array('valid' => true, 'message' => '✅ 合法純英文', 'source' => '🐘 ctype_alpha()', 'details' => sprintf('大寫：%d | 小寫：%d', preg_match_all('/[A-Z]/', $value), preg_match_all('/[a-z]/', $value)));
                } else {
                    $invalid = preg_replace('/[A-Za-z]/', '', $value);
                    $r = array('valid' => false, 'message' => '❌ 包含非字母', 'source' => '🐘 ctype_alpha() 回傳 false', 'details' => sprintf('不合法字元：「%s」', esc_html($invalid)));
                }
                break;

            case 'alphanumeric':
                // 🐘 ctype_alnum()
                if (ctype_alnum($value)) {
                    $r = array('valid' => true, 'message' => '✅ 合法英數字', 'source' => '🐘 ctype_alnum()', 'details' => sprintf('字母：%d | 數字：%d', preg_match_all('/[A-Za-z]/', $value), preg_match_all('/[0-9]/', $value)));
                } else {
                    $invalid = preg_replace('/[A-Za-z0-9]/', '', $value);
                    $r = array('valid' => false, 'message' => '❌ 含非法字元', 'source' => '🐘 ctype_alnum() 回傳 false', 'details' => sprintf('不合法字元：「%s」', esc_html($invalid)));
                }
                break;

            // ============================================================
            // 🐘 台灣在地（自訂 regex）
            // ============================================================

            case 'tw_phone':
                if (preg_match('/^09[0-9]{8}$/', $value)) {
                    $fmt = substr($value, 0, 4) . '-' . substr($value, 4, 3) . '-' . substr($value, 7, 3);
                    $r = array('valid' => true, 'message' => '✅ 合法台灣手機', 'source' => '🐘 自訂 regex：^09[0-9]{8}$', 'details' => sprintf('格式化：%s', $fmt));
                } else {
                    $reason = '需 09 開頭、共 10 碼';
                    if (strlen($value) !== 10) {
                        $reason = sprintf('你輸入 %d 碼，需要 10 碼', strlen($value));
                    } elseif ('09' !== substr($value, 0, 2)) {
                        $reason = '必須以 09 開頭';
                    }
                    $r = array('valid' => false, 'message' => '❌ 無效手機號', 'source' => '🐘 preg_match 失敗', 'details' => $reason);
                }
                break;

            case 'tw_id':
                $upper = strtoupper($value);
                if (preg_match('/^[A-Z][12][0-9]{8}$/', $upper)) {
                    $city_map = array('A' => '台北市', 'B' => '台中市', 'C' => '基隆市', 'D' => '台南市', 'E' => '高雄市', 'F' => '新北市', 'G' => '宜蘭縣', 'H' => '桃園市', 'I' => '嘉義市', 'J' => '新竹縣', 'K' => '苗栗縣', 'L' => '台中縣', 'M' => '南投縣', 'N' => '彰化縣', 'O' => '新竹市', 'P' => '雲林縣', 'Q' => '嘉義縣', 'T' => '屏東縣', 'U' => '花蓮縣', 'V' => '台東縣', 'W' => '金門縣', 'X' => '澎湖縣', 'Z' => '連江縣');
                    $gender = ('1' === $upper[1]) ? '男性' : '女性';
                    $city   = isset($city_map[$upper[0]]) ? $city_map[$upper[0]] : '未知';
                    $r = array('valid' => true, 'message' => '✅ 格式合法', 'source' => '🐘 自訂 regex：^[A-Z][12][0-9]{8}$', 'details' => sprintf('%s（%s） | %s', $upper[0], $city, $gender));
                } else {
                    $reason = '格式：1 英文 + 1或2 + 8 位數字';
                    if (strlen($value) !== 10) {
                        $reason = sprintf('你輸入 %d 碼，需要 10 碼', strlen($value));
                    }
                    $r = array('valid' => false, 'message' => '❌ 格式不合', 'source' => '🐘 preg_match 失敗', 'details' => $reason);
                }
                break;

            case 'date_ymd':
                $fmt_ok = (bool) preg_match('/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/', $value);
                if ($fmt_ok) {
                    $p = explode('-', $value);
                    if (checkdate((int) $p[1], (int) $p[2], (int) $p[0])) {
                        $wd = array('日', '一', '二', '三', '四', '五', '六');
                        $w  = $wd[(int) gmdate('w', strtotime($value))];
                        $today = new DateTime('now', new DateTimeZone('Asia/Taipei'));
                        $input = new DateTime($value, new DateTimeZone('Asia/Taipei'));
                        $diff  = $today->diff($input);
                        $d_txt = (0 === $diff->days) ? '就是今天！' : (($input < $today) ? $diff->days . ' 天前' : $diff->days . ' 天後');
                        $r = array('valid' => true, 'message' => '✅ 合法日期', 'source' => '🐘 regex + checkdate()', 'details' => sprintf('星期%s | %s', $w, $d_txt));
                    } else {
                        $r = array('valid' => false, 'message' => '❌ 日期不存在', 'source' => '🐘 checkdate() 回傳 false', 'details' => sprintf('%d 年 %d 月沒有 %d 號', (int) $p[0], (int) $p[1], (int) $p[2]));
                    }
                } else {
                    $r = array('valid' => false, 'message' => '❌ 格式錯誤', 'source' => '🐘 preg_match 失敗', 'details' => '格式：YYYY-MM-DD，例如 2024-01-15');
                }
                break;

            // ============================================================
            // 🟠 OWASP 正規表示式（WP 和 PHP 都沒有）
            // ============================================================

            case 'password':
                // 🟠 OWASP 基本密碼 regex
                if (preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{4,8}$/', $value)) {
                    $has = array();
                    if (preg_match('/[A-Z]/', $value)) {
                        $has[] = '大寫✓';
                    }
                    if (preg_match('/[a-z]/', $value)) {
                        $has[] = '小寫✓';
                    }
                    if (preg_match('/\d/', $value)) {
                        $has[] = '數字✓';
                    }
                    $r = array('valid' => true, 'message' => '✅ 密碼強度合格', 'source' => '🟠 OWASP regex', 'details' => implode(' | ', $has) . ' | 長度：' . strlen($value));
                } else {
                    $missing = array();
                    if (! preg_match('/[A-Z]/', $value)) {
                        $missing[] = '大寫字母';
                    }
                    if (! preg_match('/[a-z]/', $value)) {
                        $missing[] = '小寫字母';
                    }
                    if (! preg_match('/\d/', $value)) {
                        $missing[] = '數字';
                    }
                    $len = strlen($value);
                    if ($len < 4) {
                        $missing[] = sprintf('至少 4 字元（目前 %d）', $len);
                    }
                    if ($len > 8) {
                        $missing[] = sprintf('最多 8 字元（目前 %d）', $len);
                    }
                    $r = array('valid' => false, 'message' => '❌ 密碼強度不足', 'source' => '🟠 OWASP regex', 'details' => '缺少：' . implode('、', $missing));
                }
                break;

            case 'mac_address':
                // 🟠 OWASP MAC regex
                if (preg_match('/^([0-9a-fA-F]{2}:){5}[0-9a-fA-F]{2}$/', $value)) {
                    $vendor = strtoupper(substr(str_replace(':', '', $value), 0, 6));
                    $r = array('valid' => true, 'message' => '✅ 合法 MAC', 'source' => '🟠 OWASP regex', 'details' => sprintf('廠商代碼（OUI）：%s', $vendor));
                } else {
                    $r = array('valid' => false, 'message' => '❌ 無效 MAC', 'source' => '🟠 OWASP regex', 'details' => '格式：XX:XX:XX:XX:XX:XX（X = 0-9 或 A-F）');
                }
                break;

            default:
                $r = array('valid' => false, 'message' => '未知驗證類型', 'source' => '', 'details' => '');
        }

        wp_send_json_success($r);
    }

    private function get_ip_details($ip)
    {
        $p = explode('.', $ip);
        $f = (int) $p[0];
        $d = array();
        if ($f >= 1 && $f <= 126) {
            $d[] = 'Class A';
        } elseif ($f >= 128 && $f <= 191) {
            $d[] = 'Class B';
        } elseif ($f >= 192 && $f <= 223) {
            $d[] = 'Class C';
        }
        if ('10' === $p[0] || ('172' === $p[0] && (int) $p[1] >= 16 && (int) $p[1] <= 31) || ('192' === $p[0] && '168' === $p[1])) {
            $d[] = '私有 IP';
        } else {
            $d[] = '公開 IP';
        }
        if ('127' === $p[0]) {
            $d[] = 'Loopback';
        }
        return implode(' | ', $d);
    }

    /**
     * 渲染頁面
     */
    public function render_page()
    {
        $nonce = wp_create_nonce('vd_nonce');
        $types = $this->get_validation_types();
        $tabs  = $this->get_tabs();
?>
        <style>
            :root {
                --vd-p: #6366f1;
                --vd-pd: #4f46e5;
                --vd-ok: #10b981;
                --vd-ok-bg: #ecfdf5;
                --vd-err: #ef4444;
                --vd-err-bg: #fef2f2;
                --vd-bg: #fff;
                --vd-bdr: #e5e7eb;
                --vd-txt: #1f2937;
                --vd-txt2: #6b7280;
                --vd-r: 12px
            }

            .vd-w {
                max-width: 1000px;
                margin: 20px auto;
                padding: 0 20px
            }

            .vd-hdr {
                background: linear-gradient(135deg, var(--vd-p), #8b5cf6);
                color: #fff;
                padding: 28px 36px;
                border-radius: var(--vd-r);
                margin-bottom: 20px;
                box-shadow: 0 4px 20px rgba(99, 102, 241, .3)
            }

            .vd-hdr h1 {
                font-size: 24px;
                margin: 0 0 4px;
                font-weight: 700;
                color: #fff
            }

            .vd-hdr p {
                font-size: 13px;
                opacity: .9;
                margin: 0
            }

            .vd-priority {
                background: #fff;
                border: 1px solid var(--vd-bdr);
                border-radius: var(--vd-r);
                padding: 16px 20px;
                margin-bottom: 20px;
                display: flex;
                gap: 8px;
                align-items: center;
                flex-wrap: wrap
            }

            .vd-priority-label {
                font-size: 13px;
                font-weight: 600;
                color: var(--vd-txt);
                margin-right: 4px
            }

            .vd-badge {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 5px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600
            }

            .vd-badge.wp {
                background: #dbeafe;
                color: #1d4ed8
            }

            .vd-badge.php {
                background: #d1fae5;
                color: #065f46
            }

            .vd-badge.ow {
                background: #fef3c7;
                color: #92400e
            }

            .vd-badge .arr {
                margin: 0 4px;
                color: #9ca3af
            }

            .vd-tabs {
                display: flex;
                gap: 4px;
                background: #f3f4f6;
                padding: 5px;
                border-radius: 10px;
                margin-bottom: 20px
            }

            .vd-tab {
                flex: 1;
                padding: 10px 14px;
                border: none;
                border-radius: 8px;
                background: 0;
                color: var(--vd-txt2);
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                transition: .2s;
                text-align: center
            }

            .vd-tab:hover {
                color: var(--vd-txt);
                background: rgba(255, 255, 255, .6)
            }

            .vd-tab.on {
                background: #fff;
                color: var(--vd-p);
                box-shadow: 0 1px 4px rgba(0, 0, 0, .08)
            }

            .vd-pnl {
                display: none;
                animation: vdIn .3s
            }

            .vd-pnl.on {
                display: block
            }

            @keyframes vdIn {
                from {
                    opacity: 0;
                    transform: translateY(6px)
                }

                to {
                    opacity: 1;
                    transform: translateY(0)
                }
            }

            .vd-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 16px
            }

            @media(max-width:782px) {
                .vd-grid {
                    grid-template-columns: 1fr
                }
            }

            .vd-card {
                background: var(--vd-bg);
                border: 1px solid var(--vd-bdr);
                border-radius: var(--vd-r);
                padding: 20px;
                box-shadow: 0 1px 4px rgba(0, 0, 0, .04);
                transition: .2s
            }

            .vd-card:hover {
                box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
                transform: translateY(-2px)
            }

            .vd-ch {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 6px
            }

            .vd-ci {
                width: 38px;
                height: 38px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                flex-shrink: 0
            }

            .vd-ci.email {
                background: #fce7f3;
                color: #db2777
            }

            .vd-ci.color {
                background: #e0e7ff;
                color: #4338ca
            }

            .vd-ci.slug {
                background: #dbeafe;
                color: #2563eb
            }

            .vd-ci.ip {
                background: #ede9fe;
                color: #7c3aed
            }

            .vd-ci.num {
                background: #dbeafe;
                color: #2563eb
            }

            .vd-ci.posint {
                background: #cffafe;
                color: #0891b2
            }

            .vd-ci.alpha {
                background: #d1fae5;
                color: #059669
            }

            .vd-ci.alnum {
                background: #fef9c3;
                color: #ca8a04
            }

            .vd-ci.phone {
                background: #ffe4e6;
                color: #e11d48
            }

            .vd-ci.twid {
                background: #e0e7ff;
                color: #4338ca
            }

            .vd-ci.date {
                background: #fed7aa;
                color: #ea580c
            }

            .vd-ci.pass {
                background: #fef3c7;
                color: #d97706
            }

            .vd-ci.mac {
                background: #f3e8ff;
                color: #7c3aed
            }

            .vd-ct {
                font-size: 15px;
                font-weight: 600;
                color: var(--vd-txt);
                margin: 0
            }

            .vd-src {
                display: inline-block;
                font-size: 11px;
                padding: 2px 8px;
                border-radius: 10px;
                margin-top: 2px;
                font-weight: 600
            }

            .vd-src.wp {
                background: #dbeafe;
                color: #1d4ed8
            }

            .vd-src.php {
                background: #d1fae5;
                color: #065f46
            }

            .vd-src.ow {
                background: #fef3c7;
                color: #92400e
            }

            .vd-cd {
                font-size: 12px;
                color: var(--vd-txt2);
                margin: 6px 0 12px
            }

            .vd-ir {
                display: flex;
                gap: 6px
            }

            .vd-inp {
                flex: 1;
                padding: 9px 12px;
                border: 2px solid var(--vd-bdr);
                border-radius: 8px;
                font-size: 13px;
                transition: .2s;
                outline: 0
            }

            .vd-inp:focus {
                border-color: var(--vd-p)
            }

            .vd-btn {
                padding: 9px 16px;
                background: var(--vd-p);
                color: #fff;
                border: 0;
                border-radius: 8px;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                transition: .2s;
                white-space: nowrap
            }

            .vd-btn:hover {
                background: var(--vd-pd)
            }

            .vd-hint {
                font-size: 11px;
                color: var(--vd-txt2);
                margin-top: 6px;
                font-family: monospace
            }

            .vd-res {
                margin-top: 10px;
                padding: 10px 12px;
                border-radius: 8px;
                font-size: 13px;
                line-height: 1.6;
                display: none
            }

            .vd-res.ok {
                display: block;
                background: var(--vd-ok-bg);
                border: 1px solid var(--vd-ok);
                color: #065f46
            }

            .vd-res.err {
                display: block;
                background: var(--vd-err-bg);
                border: 1px solid var(--vd-err);
                color: #991b1b
            }

            .vd-rm {
                font-weight: 600;
                margin-bottom: 2px
            }

            .vd-rs {
                font-size: 12px;
                opacity: .7;
                font-style: italic
            }

            .vd-rd {
                font-size: 12px;
                opacity: .85
            }
        </style>
        <div class="vd-w">
            <div class="vd-hdr">
                <h1>🛡️ Validation Demo v3 — 以 WordPress 官方為優先</h1>
                <p>每種驗證都標注使用的函式來源：🔵 WordPress → 🐘 PHP → 🟠 OWASP</p>
            </div>
            <div class="vd-priority">
                <span class="vd-priority-label">優先順序：</span>
                <span class="vd-badge wp">🔵 WordPress 內建</span>
                <span class="arr">→</span>
                <span class="vd-badge php">🐘 PHP 內建</span>
                <span class="arr">→</span>
                <span class="vd-badge ow">🟠 OWASP 補充</span>
            </div>
            <div class="vd-tabs">
                <?php $f = true;
                foreach ($tabs as $k => $t) : ?>
                    <button class="vd-tab <?php echo $f ? 'on' : ''; ?>" data-tab="<?php echo esc_attr($k); ?>"><?php echo esc_html($t['label']); ?></button>
                <?php $f = false;
                endforeach; ?>
            </div>
            <?php $fp = true;
            foreach ($tabs as $tk => $t) : ?>
                <div class="vd-pnl <?php echo $fp ? 'on' : ''; ?>" id="vd-p-<?php echo esc_attr($tk); ?>">
                    <div class="vd-grid">
                        <?php foreach ($types as $vk => $v) : if ($v['tab'] !== $tk) {
                                continue;
                            }
                            $src_cls = 'wordpress' === $tk ? 'wp' : ('owasp' === $tk ? 'ow' : 'php');
                        ?>
                            <div class="vd-card">
                                <div class="vd-ch">
                                    <div class="vd-ci <?php echo esc_attr($v['icon_class']); ?>"><span class="dashicons <?php echo esc_attr($v['icon']); ?>"></span></div>
                                    <div>
                                        <h3 class="vd-ct"><?php echo esc_html($v['label']); ?></h3>
                                        <span class="vd-src <?php echo esc_attr($src_cls); ?>"><?php echo esc_html($v['source']); ?></span>
                                    </div>
                                </div>
                                <p class="vd-cd"><?php echo esc_html($v['desc']); ?></p>
                                <div class="vd-ir">
                                    <input type="text" id="vi-<?php echo esc_attr($vk); ?>" class="vd-inp" placeholder="<?php echo esc_attr($v['placeholder']); ?>" maxlength="<?php echo esc_attr($v['maxlength']); ?>">
                                    <button class="vd-btn" data-type="<?php echo esc_attr($vk); ?>" data-input="vi-<?php echo esc_attr($vk); ?>">驗證</button>
                                </div>
                                <div class="vd-hint"><?php echo esc_html($v['hint']); ?></div>
                                <div id="vr-<?php echo esc_attr($vk); ?>" class="vd-res"></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php $fp = false;
            endforeach; ?>
        </div>
        <script>
            jQuery(function($) {
                $('.vd-tab').on('click', function() {
                    var t = $(this).data('tab');
                    $('.vd-tab').removeClass('on');
                    $(this).addClass('on');
                    $('.vd-pnl').removeClass('on');
                    $('#vd-p-' + t).addClass('on');
                });
                $('.vd-btn').on('click', function() {
                    var b = $(this),
                        t = b.data('type'),
                        v = $('#' + b.data('input')).val(),
                        rd = $('#vr-' + t);
                    b.text('...').prop('disabled', 1);
                    $.post(ajaxurl, {
                        action: 'validation_demo_check',
                        nonce: '<?php echo esc_js($nonce); ?>',
                        field_type: t,
                        value: v
                    }, function(res) {
                        rd.removeClass('ok err');
                        if (res.success && res.data) {
                            var d = res.data;
                            rd.addClass(d.valid ? 'ok' : 'err');
                            var h = '<div class="vd-rm">' + d.message + '</div>';
                            if (d.source) h += '<div class="vd-rs">使用：' + d.source + '</div>';
                            if (d.details) h += '<div class="vd-rd">' + d.details + '</div>';
                            rd.html(h);
                        } else {
                            rd.addClass('err').html('<div class="vd-rm">❌ ' + (res.data ? res.data.message : '錯誤') + '</div>');
                        }
                    }).fail(function() {
                        rd.removeClass('ok').addClass('err').html('<div class="vd-rm">❌ 連線錯誤</div>');
                    }).always(function() {
                        b.text('驗證').prop('disabled', 0);
                    });
                });
                $('.vd-inp').on('keypress', function(e) {
                    if (e.which === 13) $(this).siblings('.vd-btn').click();
                });
            });
        </script>
<?php
    }
}

add_action('plugins_loaded', function () {
    new Validation_Demo_Plugin();
});
