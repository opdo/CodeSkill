<?php
if (!defined("_WEB_NAME")) die();
$u = new User;
$_list = $u->get_Rank();
?>
<h4 class="display-4" style="font-size: 2em"><i class="fa fa-rocket" aria-hidden="true"></i> Ranking</h4>
<br>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>Thành viên</th>
            <th>Joinned</th>
            <th>Số bài</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 0;
        foreach ($_list as $value) {
            $_idu = $value["ID_acc"];
            $_avatar = $value["Ava"];
            $_name = htmlentities($value["Name"]);
            $_number = '<span class="badge badge-pill badge-danger">'. $value["Point"] .'</span>';
            $_joinned = _Exchange_Time($value["Date"]);
            $i++;
            echo "
            <tr>
                <td>$i</td>
                <td><img src='$_avatar' class='rounded-circle' alt='$_name' style='height: 30px;width: 30px;'> <a href='http://fb.com/$_idu' target='_blank'><b>$_name</b></a></td>
                <td>$_joinned</td>
                <td>$_number</td>
            </tr>
                ";
        }
        if (empty($i)) {
            echo '
            <tr class="text-center">
                <td colspan="4"><i class="fa fa-frown-o fa-2x" aria-hidden="true"></i> <b>Không có nội dung hiển thị</b></td>
            </tr>
                ';
        }
        ?>
        </tbody>
    </table>
</div>