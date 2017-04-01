<?php
	// Apply các config
	require_once $_SERVER['DOCUMENT_ROOT']."/php/config.php";
	// Load các class
	require_once $_SERVER['DOCUMENT_ROOT'] . "/php/class/class_Problem.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/php/class/class_User.php";


	// Các function


    // Đổi thời gian thành dạng x [ngày/giờ/tháng/năm,...] trước
    function _Exchange_Time($time) {
    $datetime1 = strtotime($time);
    $datetime2 = strtotime(date("Y-m-d H:i:s"));
    $_sec = $datetime2 - $datetime1;
    $_dv = "giây";
    if ($_sec > 86400*365) {
        $_sec = floor($_sec/(86400*365));
        $_dv = "năm";
    } elseif ($_sec > 86400*30) {
        $_sec = floor($_sec/(86400*30));
        $_dv = "tháng";
    } elseif ($_sec > 86400) {
        $_sec = floor($_sec/86400);
        $_dv = "ngày";
    } elseif ($_sec > 60*60) {
        $_sec = floor($_sec/(60*60));
        $_dv = "giờ";
    } elseif ($_sec > 60) {
        $_sec = floor($_sec/60);
        $_dv = "phút";
    }
    return $_sec. " " . $_dv . " trước";
}

    // Thực thi code online bằng codechef
    function _ExecuteOnline($code, $input = "", $lng = 'c') {
        $lng_to_number = array('cpp14' => 44, 'c' => 1, 'cpp' => 1, 'csharp' => 27, 'pascal' => 22, 'java' => 10); // đổi ra số
        $lng = $lng_to_number[$lng];
        $url = 'https://www.codechef.com/api/ide/run/all';
        $data = array('language' => $lng, 'sourceCode' => $code, 'input' => $input);
        //open connection
        $ch = curl_init();
        $cookie = tempnam ("/tmp", "CURLCOOKIE");
        $result = get_fcontent($url, $ch, $cookie, $data);
        if (!is_array($result)) { return "Không kết nối được với máy chủ :( bạn thông cảm thử lại sau :("; }
        $info = json_decode($result[0],true);
        //var_dump($info);
        if (isset($info["status"])) {
            if ($info["status"] == "OK") {
                $timestamp = $info["timestamp"];
                $url = "https://www.codechef.com/api/ide/run/all?timestamp=".$timestamp;
                for ($i = 1; $i < 6; $i++) {
                    sleep(1);
                    $result = get_fcontent($url, $ch, $cookie);
                    $info = json_decode($result[0],true);

                    if ($info["status"] == "OK") {
                        // làm "Sạch" output
                        // 1. Trên mỗi dòng, xóa các ký tự khoảng cách thừa ở cuối chuỗi
                        // 2. Xóa tất cả các dòng trống bên dưới
                        $str = explode("\n",$info["output"]);
                        $info["output"] = "";
                        foreach ($str as $value) {
                            $info["output"] .= rtrim($value, " ") . "\n";
                        }
                        $info["output"] = rtrim($info["output"]);

                        $return = array('executeTime' => $info["time"], 'output' => $info["output"], 'memory' => $info["memory"], 'cmpinfo' => $info["cmpinfo"]);
                        curl_close($ch );
                        return $return;
                    } elseif ($info["status"] == "Error") {
                        curl_close($ch );
                        return $info["message"];
                    }
                }
                curl_close($ch);
                return "Lấy kết quả thất bại :( bạn thông cảm thử lại sau :(";
            }
        }

        curl_close($ch);
        return "Lấy dữ liệu thất bại :( bạn thông cảm thử lại sau :(";
    }

    // In danh sách AC
    function _HTML_PROBLEM_RANK($id) {
        $_lng_list = $GLOBALS["__LNG"];
        $p = new Problem;
        $u = new User;
        $_list = $p->get_AC($id);

        $html = '<table class="table table-striped">
                        <thead class="thead-default">
                        <tr>
                            <th>#</th>
                            <th>Họ tên</th>
                            <th>Thông tin</th>
                            <th>Ngôn ngữ</th>
                        </tr>
                        </thead>
                        <tbody>';
        $i = 0;
        if (count($_list) > 0) {
            foreach ($_list as $key=>$value) {
                $_user_info = $u->get_Info($key);
                if (!isset($value["l"])) $value["l"] = 'c';
                if (!isset($value["m"])) $value["m"] = "NaN";
                if (!isset($value["s"])) $value["s"] = "NaN";
                $i++;
                $html .= '
                                    <tr>
                                        <th scope="row">'. $i .'</th>
                                        <th scope="row"><img src="'. $_user_info['Ava'] .'" class="rounded-circle" alt="'. htmlentities($_user_info['Name']) .'" style="height: 30px;width: 30px;"> <a href="http://fb.com/'. $_user_info["ID_acc"] .'" target="_blank">'. htmlentities($_user_info['Name']) .'</a></th>
                                        <td><span class="badge badge-default"><i class="fa fa-clock-o" aria-hidden="true"></i> '. round(floatval($value["t"])*1000,2) .' ms</span> <span class="badge badge-default"><i class="fa fa-microchip" aria-hidden="true"></i> '. round(floatval($value["m"])/1024,2) .' kb</span> <span class="badge badge-default"><i class="fa fa-calendar" aria-hidden="true"></i> '. _Exchange_Time($value["data"]) .'</span></td>
                                        <td><b><a target="_blank">'. array_search($value["l"],$_lng_list) .'</a></b></td>
                                  </tr>
                ';
            }
        } else {
            $html .= '
                        <tr class="text-center">
                            <td colspan="4">Không có nội dung hiển thị</td>
                        </tr>';
        }
        $html .= '</tbody>
        </table>';
        echo $html;
    }
    // Kiểm tra đã login hay chưa
    function is_Logged() {
        if (isset($_SESSION['LOGIN']['FB_ACCESSTOKEN'])) return true;
        return false;
    }
    // Kiểm tra có phải admin ko
    function is_Admin() {
        if (!is_Logged()) return false;
        $u = new User;
        if (!empty($u->get_Info($_SESSION["LOGIN"]["ID"])["admin"])) return true;
        return false;
    }
    // Lấy URL đăng nhập facebook
    function _Facebook_URL_Login($url = "") {
        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]?login=1";
        if (empty($url)) $url = $actual_link;
        $helper = $GLOBALS["FB"]->getRedirectLoginHelper();
        $permissions = ['email']; // Optional permissions
        $loginUrl = $helper->getLoginUrl($url, $permissions);
        return $loginUrl;
    }

    // Kiểm tra đăng nhập Facebook
    function _Facebook_Check_Login() {
        $fb = $GLOBALS["FB"];
        $helper = $fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            return false;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            return false;
        }

        if (! isset($accessToken)) {
            return false;
        }

        $_SESSION['LOGIN']['FB_ACCESSTOKEN'] = $accessToken->getValue();
        return true;

    }

    // Lấy info facebook
    function _Facebook_Get_Info() {
        $fb = $GLOBALS["FB"];
        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get('/me?fields=id,name', $_SESSION['LOGIN']['FB_ACCESSTOKEN']);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        $user = $response->getGraphUser();
        return $user;
    }

    // Đăng nhập
    function _Login() {
        if (!is_Logged()) {
            if (_Facebook_Check_Login()) {
                $info = _Facebook_Get_Info();
                $u = new User;
                $u->save($info['name'],"https://graph.facebook.com/". $info['id'] ."/picture?type=large", $info['id']);
                $_SESSION["LOGIN"]["ID"] = $info['id'];
                $_SESSION["LOGIN"]["NAME"] = $info['name'];
                $_SESSION["LOGIN"]["AVATAR"] = "https://graph.facebook.com/". $info['id'] ."/picture?type=large";
            } else {
                unset($_SESSION["LOGIN"]["ID"]);
            }
        }
        if (isset($_GET["logout"])) {
            header('Location: /');
            die();
        }
        header('Location: ' . parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));
        die();
    }

    function get_fcontent( $url, $ch, $ckfile = "", $data_post = "", $javascript_loop = 0, $timeout = 5 ) {
    curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
    curl_setopt( $ch, CURLOPT_URL, $url );
    if (is_array($data_post)) {
        if (!empty($ckfile)) curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile );
        curl_setopt($ch,CURLOPT_POST, count($data_post));
        curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($data_post));
        curl_setopt($ch, CURLOPT_POST, true);
    } else {
        curl_setopt($ch, CURLOPT_POST, false);
        if (!empty($ckfile)) curl_setopt ($ch, CURLOPT_COOKIEFILE, $ckfile);
    }
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt( $ch, CURLOPT_AUTOREFERER, true );

    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
    curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
    //curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );



    $content = curl_exec( $ch );
    $response = curl_getinfo( $ch );


    if ($response['http_code'] == 301 || $response['http_code'] == 302) {
        ini_set("user_agent", "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");

        if ( $headers = get_headers($response['url']) ) {
            foreach( $headers as $value ) {
                if ( substr( strtolower($value), 0, 9 ) == "location:" )
                    return get_url( trim( substr( $value, 9, strlen($value) ) ) );
            }
        }
    }

    if (    ( preg_match("/>[[:space:]]+window\.location\.replace\('(.*)'\)/i", $content, $value) || preg_match("/>[[:space:]]+window\.location\=\"(.*)\"/i", $content, $value) ) && $javascript_loop < 5) {
        return get_url( $value[1], $javascript_loop+1 );
    } else {
        return array( $content, $response );
    }
}
?>