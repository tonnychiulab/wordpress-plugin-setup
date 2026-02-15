<?php
/**
 * 通用輸入驗證工具類別
 *
 * 想像這個類別是你外掛的「安檢人員」。
 * 每一筆使用者輸入的資料，都要先經過這個安檢人員的檢查，確認安全後才能放行。
 *
 * 使用方式：直接呼叫靜態方法，例如：
 *   {{class-prefix}}_Validator::is_valid_ipv4( '192.168.1.1' )  → true
 *   {{class-prefix}}_Validator::is_valid_ipv4( '999.1.1.1' )    → false
 *
 * @package {{plugin-name}}
 */

// 防止直接存取這個檔案（安全措施，就像鎖上後門）
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class {{class-prefix}}_Validator {

	/**
	 * 檢查是否為合法的 IPv4 位址
	 *
	 * IPv4 就是最常見的 IP 格式，像 192.168.1.1
	 * 四組數字，每組 0-255，用點分隔
	 *
	 * @param string $ip 要檢查的 IP 位址
	 * @return bool 合法回傳 true，不合法回傳 false
	 *
	 * 範例：
	 *   is_valid_ipv4( '192.168.1.1' )   → true
	 *   is_valid_ipv4( '10.0.0.255' )    → true
	 *   is_valid_ipv4( '256.1.1.1' )     → false（256 超出範圍）
	 *   is_valid_ipv4( 'abc.def.ghi' )   → false
	 */
	public static function is_valid_ipv4( $ip ) {
		// filter_var 是 PHP 內建的驗證函式
		// FILTER_VALIDATE_IP 檢查 IP 格式
		// FILTER_FLAG_IPV4 限定只接受 IPv4
		return false !== filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
	}

	/**
	 * 檢查是否為合法的 IP 位址（支援 IPv4 和 IPv6）
	 *
	 * @param string $ip 要檢查的 IP 位址
	 * @return bool 合法回傳 true，不合法回傳 false
	 */
	public static function is_valid_ip( $ip ) {
		return false !== filter_var( $ip, FILTER_VALIDATE_IP );
	}

	/**
	 * 檢查是否為合法的 CIDR 格式
	 *
	 * CIDR 是一種表示「一整段 IP 範圍」的方式，常用於防火牆規則。
	 * 例如 192.168.1.0/24 表示 192.168.1.0 到 192.168.1.255 這整段。
	 *
	 * @param string $cidr 要檢查的 CIDR（例如 '192.168.1.0/24'）
	 * @return bool 合法回傳 true，不合法回傳 false
	 *
	 * 範例：
	 *   is_valid_cidr( '192.168.1.0/24' ) → true
	 *   is_valid_cidr( '10.0.0.0/8' )     → true
	 *   is_valid_cidr( '192.168.1.0/33' ) → false（遮罩超過 32）
	 *   is_valid_cidr( '192.168.1.0' )    → false（缺少 /遮罩）
	 */
	public static function is_valid_cidr( $cidr ) {
		// 用 / 把 CIDR 拆成 IP 和遮罩兩部分
		$parts = explode( '/', $cidr );

		// 必須剛好有兩部分（IP / 遮罩）
		if ( count( $parts ) !== 2 ) {
			return false;
		}

		$ip   = $parts[0];
		$mask = $parts[1];

		// 遮罩必須是純數字
		if ( ! ctype_digit( $mask ) ) {
			return false;
		}

		$mask = (int) $mask;

		// IP 要合法，遮罩要在 0-32 之間
		return self::is_valid_ipv4( $ip ) && $mask >= 0 && $mask <= 32;
	}

	/**
	 * 檢查是否為合法的 Port 號碼
	 *
	 * Port 號碼就像「門牌號碼」，電腦用它來區分不同的服務。
	 * 合法範圍是 0 到 65535。
	 *
	 * @param mixed $port 要檢查的 port 號碼
	 * @return bool 合法回傳 true，不合法回傳 false
	 */
	public static function is_valid_port( $port ) {
		return false !== filter_var( $port, FILTER_VALIDATE_INT, array(
			'options' => array(
				'min_range' => 0,
				'max_range' => 65535,
			),
		) );
	}

	/**
	 * 檢查是否只包含數字 0-9
	 *
	 * 比 is_numeric() 更嚴格——不接受負號、小數點、科學記號。
	 * 只有純粹的 0123456789 才會通過。
	 *
	 * @param string $value 要檢查的值
	 * @return bool 只有數字回傳 true，否則回傳 false
	 *
	 * 範例：
	 *   is_digits_only( '12345' )   → true
	 *   is_digits_only( '007' )     → true
	 *   is_digits_only( '-5' )      → false（有負號）
	 *   is_digits_only( '3.14' )    → false（有小數點）
	 *   is_digits_only( '12 34' )   → false（有空格）
	 */
	public static function is_digits_only( $value ) {
		// 確保不是空值，然後用正規表示式檢查只有 0-9
		return is_string( $value ) && strlen( $value ) > 0 && preg_match( '/^[0-9]+$/', $value );
	}

	/**
	 * 檢查是否只包含英文字母 A-Z 和 a-z
	 *
	 * @param string $value 要檢查的值
	 * @return bool 只有英文字母回傳 true，否則回傳 false
	 *
	 * 範例：
	 *   is_alpha_only( 'Hello' )     → true
	 *   is_alpha_only( 'ABC' )       → true
	 *   is_alpha_only( 'Hello123' )  → false（有數字）
	 *   is_alpha_only( 'Hello!' )    → false（有驚嘆號）
	 */
	public static function is_alpha_only( $value ) {
		return is_string( $value ) && strlen( $value ) > 0 && preg_match( '/^[A-Za-z]+$/', $value );
	}

	/**
	 * 清理整數並確保在指定範圍內
	 *
	 * 就像一個「夾子」，把數值夾在你指定的最小值和最大值之間。
	 *
	 * @param mixed    $value 要清理的值
	 * @param int|null $min   最小值（不指定則不限制）
	 * @param int|null $max   最大值（不指定則不限制）
	 * @return int|false 合法的整數，或 false（如果不是合法整數）
	 */
	public static function sanitize_int( $value, $min = null, $max = null ) {
		$options = array();

		if ( null !== $min ) {
			$options['min_range'] = $min;
		}
		if ( null !== $max ) {
			$options['max_range'] = $max;
		}

		return filter_var( $value, FILTER_VALIDATE_INT, array( 'options' => $options ) );
	}

	/**
	 * 清理字串並限制長度
	 *
	 * 先用 WordPress 的 sanitize_text_field 清理（移除 HTML 標籤和多餘空白），
	 * 然後把超過長度的部分截掉。
	 *
	 * @param string $value      要清理的字串
	 * @param int    $max_length 最大允許長度
	 * @return string 清理後的字串
	 */
	public static function sanitize_string( $value, $max_length = 255 ) {
		// 先用 WordPress 內建函式清理
		$clean = sanitize_text_field( $value );
		// 如果超過長度就截斷（mb_substr 支援中文等多位元組字元）
		if ( mb_strlen( $clean ) > $max_length ) {
			$clean = mb_substr( $clean, 0, $max_length );
		}
		return $clean;
	}

	/**
	 * 檢查是否為合法的 URL
	 *
	 * @param string $url 要檢查的 URL
	 * @return bool 合法回傳 true，不合法回傳 false
	 */
	public static function is_valid_url( $url ) {
		return false !== filter_var( $url, FILTER_VALIDATE_URL );
	}

	/**
	 * 檢查是否為合法的 Email
	 *
	 * 使用 WordPress 內建的 is_email() 函式，
	 * 它比 PHP 原生的 filter_var 更適合 WordPress 環境。
	 *
	 * @param string $email 要檢查的 Email
	 * @return bool 合法回傳 true，不合法回傳 false
	 */
	public static function is_valid_email( $email ) {
		return false !== is_email( $email );
	}

	/**
	 * 檢查檔案副檔名是否在白名單中
	 *
	 * 「白名單」的意思是：只有名單上的才允許，其他一律拒絕。
	 * 這比「黑名單」（列出不允許的）更安全，因為你不可能列出所有危險的類型。
	 *
	 * @param string $filename 檔案名稱
	 * @param array  $allowed  允許的副檔名列表，例如 array( 'jpg', 'png', 'pdf' )
	 * @return bool 在白名單中回傳 true，否則回傳 false
	 */
	public static function is_allowed_file_type( $filename, $allowed = array( 'jpg', 'jpeg', 'png', 'gif', 'pdf' ) ) {
		// 取得副檔名並轉小寫（避免 .JPG 和 .jpg 被當成不同東西）
		$ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
		return in_array( $ext, $allowed, true );
	}
}
