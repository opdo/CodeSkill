<?php
if (!defined("_WEB_NAME")) die();
$p = new Problem;
$u = new User;
$_list = $p->get_recent();
?>
<h4 class="display-4" style="font-size: 2em"><i class="fa fa-history" aria-hidden="true"></i> Recent list</h4>
<br>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Ngày</th>
            <th>Mã bài</th>
            <th>Tên bài</th>
            <th>Người nộp</th>
            <th>Trạng thái</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 0;
        foreach ($_list as $value) {
            $_idp = $value["ID_prob"];
            $_title = $p->get_Info($_idp)["Name"];
            $_idu = $value["ID_acc"];
            $_avatar = $u->get_Info($_idu)["Ava"];
            $_name = htmlentities($u->get_Info($_idu)["Name"]);
            $_status = '<span class="badge badge-pill badge-danger">CHƯA HOÀN THÀNH</span>';
            if (!empty($value["Status"])) $_status = '<span class="badge badge-pill badge-primary">HOÀN THÀNH</span>';
            $_date = _Exchange_Time($value["Date"]);
            $i++;
            echo "
            <tr>
                <td>$_date</td>
                <td><a href='/problem/$_idp'><b>$_idp</b></a></td>
                <td><a href='/problem/$_idp'><b>$_title</b></a></td>
                <td><img src='$_avatar' class='rounded-circle' alt='$_name' style='height: 30px;width: 30px;'> <a href='http://fb.com/$_idu' target='_blank'><b>$_name</b></a></td>
                <td>$_status</td>
            </tr>
                ";
        }
        if (empty($i)) {
            echo '
            <tr class="text-center">
                <td colspan="5"><i class="fa fa-frown-o fa-2x" aria-hidden="true"></i> <b>Không có nội dung hiển thị</b></td>
            </tr>
                ';
        }
        ?>
        </tbody>
    </table>
</div>