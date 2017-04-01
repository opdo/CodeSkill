<?php
if (!defined("_WEB_NAME")) die();
$p = new Problem;
$_list = $p->get_List();
?>
<h4 class="display-4" style="font-size: 2em"><i class="fa fa-list" aria-hidden="true"></i> Problem list <small><sup class="badge badge-default"><?= count($_list) ?> <i class="fa fa-bug" aria-hidden="true"></i></sup></small>
    <div class="pull-right">
        <?php
            if (is_Admin()) {
                echo '<a href="/admin" role="button" class="btn btn-outline-danger"><i class="fa fa-external-link" aria-hidden="true"></i> <b>Tạo mới</b></a>';
            }
        ?>
    </div>
</h4>
<br>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>Mã</th>
            <th>Tên bài</th>
            <th>Loại</th>
            <th><i class="fa fa-users" aria-hidden="true"></i></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
            $i = 0;
            foreach ($_list as $value) {
                $type_split = explode(",", $value["ID_tag"]);
                $value["ID_tag"] = '';
                $btn_admin = "<td></td>";
                if (is_Admin()) {
                    $btn_admin = '<td><a href="/admin/'. $value["ID_prob"] .'" type="role" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="Chỉnh sửa"><i class="fa fa-pencil" aria-hidden="true"></i></a> <a href="/admin/delete/'. $value["ID_prob"] .'" type="role" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Xóa"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                }
                foreach ($type_split as $item) {
                    $value["ID_tag"] .= '<a class="badge badge-default" style="color: white;"><i class="fa fa-tag" aria-hidden="true"></i> '.$item.'</a> ';
                }
                $_title = $value["Name"];
                $_type = $value["ID_tag"];
                $_id = $value["ID_prob"];
                $_user_count = count(json_decode($value["AC_Users"], true));
                $i++;
                echo "
            <tr>
                <td>$i</td>
                <td><a href='/problem/$_id'><b>$_id</b></a></td>
                <td><a href='/problem/$_id'><b>$_title</b></a></td>
                <td>$_type</td>
                <td>$_user_count</td>
                $btn_admin
            </tr>
                ";
            }
            if (empty($i)) {
                echo '
            <tr class="text-center">
                <td colspan="6"><i class="fa fa-frown-o fa-2x" aria-hidden="true"></i> <b>Không có nội dung hiển thị</b></td>
            </tr>
                ';
            }
        ?>
        </tbody>
    </table>
</div>