<?php
	// THIẾT LẬP CÁC TÍNH NĂNG CỦA WEB
	// set-timezone
    date_default_timezone_set("Asia/Ho_Chi_Minh");
    // start session
    session_start();


	// THIẾT LẬP THÔNG TIN WEB
	// Tên của web, hiển thị tại meta tag title, brand name ở thanh nav menu
	define("_WEB_NAME", "Code Skill");
	// Mô tả của web, hiển thị tại meta tag
	define("_WEB_DESCRIPTION", "Trang luyện tư duy giải thuật lập trình trực tuyến bằng nhiều ngôn ngữ miễn phí, tiện lợi");
	// Keyword của web, dành cho bộ tìm kiếm
	define("_WEB_KEYWORD", "giải thuật, tư duy thuật toán, tư duy code, học code, luyen code, giai thuat toan, code skill, giai bai truc tuyen");
	// Icon của web, hiển thị icon
	define("_WEB_ICON", "/assets/img/icon.png");
	// Img của web, hiển thị tại thẻ meta tag
	define("_WEB_IMAGE", "/assets/img/icon.png");
	// Phiên bản, hiển thị tại footer của wev
	define("_WEB_VERSION", "1.0");


	// THIẾT LẬP FACEBOOK APP
	// Phục vụ cho plugin comment, like, share và login
	// Dữ liệu sẽ được đọc chung từ file config bên dưới cùng với database
    define('_FB_APP_ID','');
    define('_FB_APP_SECRET','');
    // Autoload facebook SDK
    require_once $_SERVER['DOCUMENT_ROOT']."/php/Facebook/autoload.php";
    $GLOBALS["FB"] = new Facebook\Facebook([
        'app_id' => _FB_APP_ID,
        'app_secret' => _FB_APP_SECRET,
        'default_graph_version' => 'v2.8',
    ]);

	// THIẾT LẬP CƠ SỞ DỮ LIỆU MYSQL
    define("_DB_SETTING_HOST", "localhost");
    define("_DB_SETTING_ACCOUNT", "root");
    define("_DB_SETTING_PASSWORD", "");
    define("_DB_SETTING_DB", "tathienhao");

	// THIẾT LẬP BỘ COMPLIER ONLINE
	// Sử dụng tại codechef.com/ide
	// Là một array với "tên hiển thị" => "giá trị post lên ide"
	$GLOBALS['__LNG'] = array(	'C (gcc 4.9.2)' => 'c', 
							'C++ (gcc 4.9.2)' => 'cpp', 
							'C++ 14 (g++ 4.9.2)' => 'cpp14', 
							'C# (gmcs 3.10)'=>'csharp', 
							'Pascal (fpc 2.6.4)' => 'pascal', 
							'Java (Javac 8)' => 'java');


	// KẾT NỐI CSDL
	$GLOBALS['__CONN'] = mysqli_connect(_DB_SETTING_HOST,_DB_SETTING_ACCOUNT,_DB_SETTING_PASSWORD,_DB_SETTING_DB);
	if (!$GLOBALS['__CONN']) die("[LỖI] Không thể kết nối với Database<br>Account: "._DB_SETTING_ACCOUNT . " - Password: [" . _DB_SETTING_PASSWORD . "] - Host: ". _DB_SETTING_HOST);
	// Thiết lập trạng thái kết nối cho mysql
	// Sử dụng utf8 cho db và wait_timeout
	// wait_timeout để cho db có thể giữ 1 khoảng thời gian kết nối đủ lâu để chờ bộ complier online hoàn tất kết quả trả về save vào mysql
	if (!mysqli_query($GLOBALS['__CONN'],"SET SESSION wait_timeout=300;") || !mysqli_set_charset($GLOBALS['__CONN'], 'utf8')) {
        die("[LỖI] Không thể thiết lập database");
    }
    
?>
