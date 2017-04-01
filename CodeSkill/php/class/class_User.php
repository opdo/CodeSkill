<?php
if (!defined("_WEB_NAME")) die();
// CLASS PROBLEM
// Mỗi bài được gọi là 1 user
class User
{
    // Sử dụng biến global tại class
    protected $glob;
    public function __construct() {
        global $GLOBALS;
        $this->glob =& $GLOBALS;
    }

    // CÁC HÀM KIỂM TRA ---------------------------------------------------------------------------

    // Hàm kiểm tra một $id của user có tồn tại hay không.
    // Trả về true nếu tồn tại, trả về false nếu không tồn tại
    function is_ID_Exist($id) {
        // Chống mysql injection
        $id = addslashes($id);
        // Query MySQL với điều kiện Where để tìm ID
        $query = mysqli_query($this->glob["__CONN"],"SELECT * FROM db_account WHERE ID_acc='$id'");
        if (!$query || mysqli_num_rows($query) == 0) return false;
        return true;
    }
    


    // CÁC HÀM GET ---------------------------------------------------------------------------

    // Lấy thông tin của một user
    // Trả về một array với các thông tin của user đó. Trả về false nếu không tìm thấy
    function get_Info($id) {
        // Chống mysql injection
        $id = addslashes($id);
        // Query MySQL với điều kiện Where để tìm ID
        $query = mysqli_query($this->glob["__CONN"],"SELECT * FROM db_account WHERE ID_acc='$id'");
        if (!$query || mysqli_num_rows($query) == 0) return false;
        // Lưu dưới dạng array các thông tin query được
        $info = mysqli_fetch_array($query, MYSQLI_ASSOC);
        return $info;
    }

    // Hàm lấy list user
    // trả về list user
    function get_List() {
        $data = array();
        $cmd = "SELECT * FROM db_account";
        $query = mysqli_query($this->glob["__CONN"],$cmd);
        if (!$query || mysqli_num_rows($query) == 0) return $data;
        while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

    // Hàm lấy list user có order theo Point
    // trả về list user có order theo Point
    function get_Rank() {
        $data = array();
        $cmd = "SELECT * FROM db_account ORDER BY Point DESC";
        $query = mysqli_query($this->glob["__CONN"],$cmd);
        if (!$query || mysqli_num_rows($query) == 0) return $data;
        while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }
    // CÁC HÀM SET ---------------------------------------------------------------------------

    // Hàm save một user
    // Trả về true nếu thành công và ngược lại
    // Các tham biến lưu tương tự vào mysql
    function save($name, $avatar, $id) {
        // Chống mysql injection
        $id = addslashes($id);
        $name = addslashes($name);
        $avatar = addslashes($avatar);
        // Phân biệt 2 lệnh tạo mới hoặc save
        if (!$this->is_ID_Exist($id)) {
            $cmd = "INSERT INTO db_account (Name, Ava, Date, ID_acc) VALUES ('".$name."','".$avatar."', now(), '".$id."')";
        } else {
            $cmd = "UPDATE db_account SET Name='". $name . "', Ava='". $avatar . "' WHERE ID_acc='". $id  ."'";
        }
        // Lưu vào mysql
        if (mysqli_query($this->glob["__CONN"],$cmd)) return true;
        return false;
    }

    // Hàm cộng trừ point
    function add_point($number,$id) {
        // Chống mysql injection
        $id = addslashes($id);
        $number = addslashes($number);
        $number = intval($number);
        $info = $this->get_Info($id);
        if ($info === false) return false;
        $info["Point"] += $number;
        $cmd = "UPDATE db_account SET Point='". $info["Point"] . "' WHERE ID_acc='". $id  ."'";
        if (mysqli_query($this->glob["__CONN"],$cmd)) return true;
        return false;
    }

    // CÁC HÀM UNSET, DELETE ---------------------------------------------------------------------------

    // Hàm delete một user
    // Trả về true nếu thành công và ngược lại
    function del($id) {
        // Chống mysql injection
        $id = addslashes($id);
        if (mysqli_query($this->glob["__CONN"],"DELETE FROM db_account WHERE ID_acc='". $id  ."'")) return true;
        return false;
    }

}
?>