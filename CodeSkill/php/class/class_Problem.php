<?php
    if (!defined("_WEB_NAME")) die();
	// CLASS PROBLEM
	// Mỗi bài được gọi là 1 problem
	class Problem
	{
		// Sử dụng biến global tại class
	    protected $glob;
	    public function __construct() {
	        global $GLOBALS;
	        $this->glob =& $GLOBALS;
	    }

	    // CÁC HÀM KIỂM TRA ---------------------------------------------------------------------------

	    // Hàm kiểm tra một $id của problem có tồn tại hay không.
	    // Trả về true nếu tồn tại, trả về false nếu không tồn tại
		function is_ID_Exist($id) {
			// Chống mysql injection
			$id = addslashes($id);
			// Query MySQL với điều kiện Where để tìm ID
	        $query = mysqli_query($this->glob["__CONN"],"SELECT * FROM db_problem WHERE ID_prob='$id'");
	        if (!$query || mysqli_num_rows($query) == 0) return false;
	        return true;
		}

		// Hàm kiểm tra một $user đã được AC một problem có $id hay không
		// Trả về true nếu AC, trả về false nếu chưa AC
		function is_AC($user, $id) {
			// Chống mysql injection
			$id = addslashes($id);
			$user = addslashes($user);
			// Lấy thông tin của problem
			$p_info = $this->get_Info($id);
			if ($p_info === false) return false;
			// Tách kiểu json ra lấy list của các user AC
			$p_list_AC = json_decode($p_info["AC_Users"], true);
			// Kiểm tra user có tồn tại trong list hay không
			if (isset($p_list_AC[$user])) return true;
			return false;
		}


		// CÁC HÀM GET ---------------------------------------------------------------------------

		// Lấy thông tin của một problem
		// Trả về một array với các thông tin của problem đó. Trả về false nếu không tìm thấy
		function get_Info($id) {
			// Chống mysql injection
			$id = addslashes($id);
			// Query MySQL với điều kiện Where để tìm ID
	        $query = mysqli_query($this->glob["__CONN"],"SELECT * FROM db_problem WHERE ID_prob='$id'");
	        if (!$query || mysqli_num_rows($query) == 0) return false;
	        // Lưu dưới dạng array các thông tin query được
	        $info = mysqli_fetch_array($query, MYSQLI_ASSOC);
	        return $info;
		}
		// Lấy danh sách các user đã AC của một problem
		// Trả về một array với các thông tin của user đó, null nếu không có
		// Các tham biến
		// - sort: sắp xếp, 1 (mặc định) nếu giảm dần và 0 nếu tăng dần
		function get_AC($id, $sort = 1) {
			// Chống mysql injection
			$id = addslashes($id);
			$sort = addslashes($sort);
			// Lấy thông tin của problem
			$p_info = $this->get_Info($id);
			if ($p_info === false) return false;
			// Tách kiểu json ra lấy list của các user AC
			$p_list_AC = json_decode($p_info["AC_Users"], true);
			// Sắp xếp theo thứ tự ưu tiên thời gian chạy -> memory -> ngày nộp
            if (count($p_list_AC) > 1) {
                uasort($p_list_AC, function($a, $b) {
                    if (floatval($b['t']) != floatval($a['t'])) return floatval($b['t']) < floatval($a['t']);
                    if (floatval($b['m']) == floatval($a['m'])) return $b['data'] < $a['data'];
                    return floatval($b['m']) < floatval($a['m']);
                });
            }
	        return $p_list_AC;
		}

        // Hàm lấy list problem
        // trả về list problem
        function get_List() {
            $data = array();
            $cmd = "SELECT * FROM db_problem ORDER BY Date DESC";
            $query = mysqli_query($this->glob["__CONN"],$cmd);
            if (!$query || mysqli_num_rows($query) == 0) return $data;
            while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        }

        // Hàm lấy list problem recent
        // trả về list problem recent
        function get_recent() {
            $data = array();
            $cmd = "SELECT * FROM submit_status ORDER BY Date DESC";
            $query = mysqli_query($this->glob["__CONN"],$cmd);
            if (!$query || mysqli_num_rows($query) == 0) return $data;
            while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        }

		// CÁC HÀM SET ---------------------------------------------------------------------------

		// Hàm save một problem
		// Trả về true nếu thành công và ngược lại
		// Các tham biến lưu tương tự vào mysql
		function save($name, $tag, $des, $test, $t_limit = 1, $m_limit = 10240, &$id = 0) {
			// Chống mysql injection
			$id = addslashes($id);
			$name = addslashes($name);
			$tag = addslashes($tag);
			$des = addslashes($des);
			$test = addslashes($test);
			$t_limit = addslashes($t_limit);
			$m_limit = addslashes($m_limit);
			// Chuyển time và memory về giá trị số
			$t_limit = floatval($t_limit);
			$m_limit = floatval($m_limit);
			// Phân biệt 2 lệnh tạo mới hoặc save
			if (!$this->is_ID_Exist($id)) { 
				$cmd = "INSERT INTO db_problem (Name, ID_tag, Decription, TestCase, LimitTime, LimitMem, Date, ID_prob) VALUES ('".$name."','".$tag."','".$des."','".$test."','".$t_limit."','".$m_limit."',now(),'".$id."')";
			} else {
				$cmd = "UPDATE db_problem SET Name='". $name . "', ID_tag='". $tag . "', Decription='". $des . "', TestCase='". $test . "', LimitTime='". $t_limit . "', LimitMem='". $m_limit . "' WHERE ID_prob='". $id  ."'";
			}
			// Lưu vào mysql
			if (mysqli_query($this->glob["__CONN"],$cmd)) return true;
			return false;
		}


		// Hàm set một user AC một problem
		// Trả về true nếu thành công và ngược lại
		// Các tham biến
		// - t_exe: thời gian chạy
		// - m_run: memory sử dụng
		// - lng: ngôn ngữ sử dụng (dưới dạng post)
		// - s_id: source_id
		function set_AC($user, $id, $t_exe = 0, $m_usage = 0, $lng = "c", $s_id = 0) {
			// Chống mysql injection
			$id = addslashes($id);
			$user = addslashes($user);
			$t_exe = addslashes($t_exe);
			$m_usage = addslashes($m_usage);
			$lng = addslashes($lng);
			$s_id = addslashes($s_id);
			// Lấy thông tin của problem
			$p_info = $this->get_Info($id);
			if ($p_info === false) return false;
			// Tách kiểu json ra lấy list của các user AC
			$p_list_AC = json_decode($p_info["AC_Users"], true);
			// Kiểm tra có định nghĩa user hay chưa
            if (!isset($p_list_AC[$user])) {
                $u = new User;
                $u->add_point(1, $_SESSION["LOGIN"]["ID"]);
            }
			// Định nghĩa user vào array trên
			$p_list_AC[$user] = array('data' => date("Y-m-d H:i:s"), 't' => $t_exe, 'm' => $m_usage, 'l' => $lng, 's' => $s_id);
			// Save lại vào mysql
			$cmd = "UPDATE db_problem SET AC_Users='". json_encode($p_list_AC) . "' WHERE ID_prob='$id'";
			if (mysqli_query($this->glob["__CONN"],$cmd)) return true;
			return false;
		}

        // Hàm add một submit của problem vào recent
        function add_recent($id_problem, $id_acc, $status) {
            $cmd = "INSERT INTO submit_status (ID_prob, ID_acc, Status, Date) VALUES ('".$id_problem."','".$id_acc."','".$status."',now())";
            if (mysqli_query($this->glob["__CONN"],$cmd)) return true;
            return false;
        }

		// CÁC HÀM UNSET, DELETE ---------------------------------------------------------------------------

		// Hàm delete một problem
		// Trả về true nếu thành công và ngược lại
		function del($id) {
			// Chống mysql injection
	        $id = addslashes($id);
	        if (mysqli_query($this->glob["__CONN"],"DELETE FROM db_problem WHERE ID_prob='". $id  ."'")) return true;
	        return false;
	    }

		// Hàm unset user AC một problem
		// Trả về true nếu thành công và ngược lại
	    function unset_AC($user, $id) {
			// Chống mysql injection
			$id = addslashes($id);
			$user = addslashes($user);
			// Lấy thông tin của problem
			$p_info = $this->get_Info($id);
			if ($p_info === false) return false;
			// Tách kiểu json ra lấy list của các user AC
			$p_list_AC = json_decode($p_info["AC_Users"]);
			// Xóa user ra khỏi array trên
	        if (isset($p_list_AC[$user])) unset($p_list_AC[$user]);
	        else return false;
	        // Save lại vào mysql
			$cmd = "UPDATE db_problem SET AC_Users='". json_encode($p_list_AC) . "' WHERE ID_prob='$id'";
			if (mysqli_query($this->glob["__CONN"],$cmd)) return true;
			return false;
	    }

	}
?>