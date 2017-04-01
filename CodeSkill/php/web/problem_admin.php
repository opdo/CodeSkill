<?php
if (!defined("_WEB_NAME")) die();
if (!is_Admin()) die();
$html = "";
$_lock_key = "";

if (isset($_GET["save"]) == 1) {
    // Lưu problem lại
    $_ERROR_MSG = "<div class=\"alert alert-danger\" role=\"alert\">
                     <strong>Oh snap!</strong> Thông tin của bạn nhập không đầy đủ
                   </div>";
    if (isset($_POST["bug-title"], $_POST["bug-tag"], $_POST["bug-time"], $_POST["bug-memory"], $_POST["bug-key"], $_POST["bug-content"])) {
        // Bắt các bộ testCase
        $testCase = array();
        $i = 1;
        while (1) {
            if (isset($_POST["io_data_".$i])) {
                $testCase[$i] = $_POST["io_data_".$i];
            } else break;
            $i++;
        }
        $p = new Problem;
        if ($p->save($_POST["bug-title"],$_POST["bug-tag"],$_POST["bug-content"],json_encode($testCase),$_POST["bug-time"],$_POST["bug-memory"],$_POST["bug-key"])) {
            $_ERROR_MSG = "<div class=\"alert alert-success\" role=\"alert\">
                     <strong>Thành công!</strong> Dữ liệu đã được lưu lại thành công
                   </div>";
        } else {
            if ($p->is_ID_Exist($_POST["bug-key"])) {
                $_lock_key = false;
                $_ERROR_MSG = "<div class=\"alert alert-danger\" role=\"alert\">
                     <strong>Oh snap!</strong> Mã problem này đã tồn tại, xin hãy đổi một mã khác
                   </div>";
            } else {
                $_ERROR_MSG = "<div class=\"alert alert-danger\" role=\"alert\">
                     <strong>Oh snap!</strong> Có lỗi trong quá trình lưu dữ liệu
                   </div>";
            }
        }
    }
}
elseif (isset($_GET["admin"])) {
    $p = new Problem;
    if ($p->is_ID_Exist($_GET["admin"])) {
        $html = '';
        $i = 1;
        $info = $p->get_Info($_GET["admin"]);
        if (!isset($_POST["bug-title"])) $_POST["bug-title"] = $info["Name"];
        if (!isset($_POST["bug-tag"])) $_POST["bug-tag"] = $info["ID_tag"];
        if (!isset($_POST["bug-time"])) $_POST["bug-time"] = $info["LimitTime"];
        if (!isset($_POST["bug-memory"])) $_POST["bug-memory"] = $info["LimitMem"];
        if (!isset($_POST["bug-key"])) $_POST["bug-key"] = $info["ID_prob"];
        if (!isset($_POST["bug-content"])) $_POST["bug-content"] = $info["Decription"];
        $testCase = json_decode($info['TestCase'],true);
        $i = 1;
        while (1) {
            if (isset($testCase[$i])) {
                $_POST["io_data_".$i] = $testCase[$i];
            } else break;
            $i++;
        }
    }
}

if (isset($_POST["bug-key"])) {
    if ($p->is_ID_Exist($_POST["bug-key"])) {
        if ($_lock_key !== false) $_lock_key = true;
    }
}
if ($_lock_key !== true) $_lock_key = false;
?>
<h4 class="display-4" style="font-size: 2em"><i class="fa fa-bug" aria-hidden="true"></i> Problem Editor</h4>
<hr>
<div id="AlertDiv">
    <?php if (isset($_ERROR_MSG)) echo $_ERROR_MSG; ?>
</div>
<form id="formPost" method="post" action="/adminsave">
    <div class="row">
        <div class="col-6">
            <div class="form-group row">
                <label class="col-6 col-form-label"><i class="fa fa-github-alt" aria-hidden="true"></i> Tiêu đề: </label>
                <div class="col-6">
                    <input name="bug-title" type="text" class="form-control" placeholder="Tiêu đề Problem" value="<?php if (isset($_POST['bug-title'])) echo $_POST['bug-title']; ?>" required>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group row">
                <label for="example-text-input" class="col-6 col-form-label"><i class="fa fa-tags" aria-hidden="true"></i> Tag: </label>
                <div class="col-6">
                    <input name="bug-tag" type="text" class="form-control" placeholder="Tag Problem" value="<?php if (isset($_POST['bug-tag'])) echo $_POST['bug-tag']; ?>" required>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group row">
                <label for="example-text-input" class="col-6 col-form-label"><i class="fa fa-clock-o" aria-hidden="true"></i> Limit time execute: </label>
                <div class="col-6">
                    <input name="bug-time" type="text" class="form-control" placeholder="Count by sec" value="<?php if (isset($_POST['bug-time'])) echo $_POST['bug-time']; ?>" required>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group row">
                <label for="example-text-input" class="col-6 col-form-label"><i class="fa fa-microchip" aria-hidden="true"></i> Limit memory usage: </label>
                <div class="col-6">
                    <input name="bug-memory" type="text" class="form-control" placeholder="Count by byte" value="<?php if (isset($_POST['bug-memory'])) echo $_POST['bug-memory']; ?>" required>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group row">
                <label class="col-6 col-form-label"><i class="fa fa-key" aria-hidden="true"></i> Mã problem </label>
                <div class="col-6">
                    <input <?php if (!$_lock_key) echo 'name="bug-key"'; ?> type="text" class="form-control" placeholder="Mã Problem" value="<?php if (isset($_POST['bug-key'])) echo $_POST['bug-key']; ?>" required <?php if ($_lock_key) echo "disabled"; ?>>
                    <?php
                        if ($_lock_key) {
                            echo '<input name="bug-key" type="text" value="'. $_POST['bug-key'] .'" hidden>';
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <textarea class="form-control" name="bug-content" id="bug-content"><?php if (isset($_POST['bug-content'])) echo htmlentities($_POST['bug-content']); ?></textarea>
    <br>

    <div role="tablist" aria-multiselectable="true">
        <div class="card">
            <div class="card-header" role="tab" id="headingOne">
                <h5 class="mb-0">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Thiết lập input / output
                    </a>
                </h5>
            </div>

            <div id="collapseOne" class="collapse show" role="tabpanel" aria-labelledby="headingOne">
                <div class="card-block">
                    <div class="custom-controls-stacked">
                        <?php
                        if (isset($_POST["io_type"])) {
                            echo '<label>Loại input / output:</label>
                    <label class="custom-control custom-radio">
                        <input id="radiotype1" name="io_type" type="radio" class="custom-control-input" value="1" '. ($_POST["io_type"] == 1? "checked" : "") .'>
                        <span class="custom-control-indicator"></span>
                        <span class="custom-control-description">AutoIT Language</span>
                    </label>
                    <label class="custom-control custom-radio">
                        <input id="radiotype2" name="io_type" type="radio" class="custom-control-input" value="2"  '. ($_POST["io_type"] == 2? "checked" : "") .'>
                        <span class="custom-control-indicator"></span>
                        <span class="custom-control-description">C/C++ Language</span>
                    </label>
                        ';
                        }
                        ?>
                        <label>Các bộ test</label>
                        <div class="form-inline">
                            <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                                <div class="input-group-addon">Input</div>
                                <input type="text" class="form-control" id="io_input">
                            </div>

                            <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                                <div class="input-group-addon">Output</div>
                                <input type="text" class="form-control" id="io_output">
                            </div>

                            <button type="button" class="btn btn-primary" onclick="Add_InOut('io_input','io_output')">Thêm</button>

                        </div>
                        <small class="text-muted">Đối với ký tự xuống dòng, vui lòng điền "\n", không chấp nhận ký tự "|"</small>
                        <br>
                        <table class="table table-hover" id="BoTest">
                            <thead class="thead-default">
                            <tr>
                                <th>Input</th>
                                <th>Output</th>
                                <th>Tác vụ</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i = 1;
                            while (1) {
                                if (isset($_POST["io_data_".$i])) {
                                    $in = explode('|',$_POST["io_data_".$i])[0];
                                    $out = explode('|',$_POST["io_data_".$i])[1];
                                    echo '<input type="text" class="form-control" name="io_data_'.$i.'" value="'.$in.'|'.$out.'" style="display:none">';
                                    $html = $html . '<tr class="input-output" name="io_data_'.$i.'"><td>'.$in.'</td><td>'.$out.'</td><td><button type="button" name="btn-delete-inoutput" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Xóa bộ test"><i class="fa fa-trash" aria-hidden="true"></i></button></td></tr>';
                                } else break;
                                $i++;
                            }
                            if (empty($html)) $html = '
                            <tr class="text-center" name="none">
                                <td colspan="3">Không có nội dung hiển thị</td>
                            </tr>
                            ';
                            echo $html;
                            ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="pull-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
        <button type="submit" class="btn btn-primary"><i class="fa fa-cloud" aria-hidden="true"></i> Lưu lại</button>
    </div>
</form>
<br>
<br>
<br>
<script>

    $(document).delegate("button[name='btn-delete-inoutput']",'click',function() {
        var _name_id = $(this).parent().parent().attr("name");
        $(this).parent().parent().remove();
        $("#formPost").find("input[name='"+_name_id+"']").remove();
        //window.location.replace("?hashtag=" + _hashtag_id);
    });
    function Add_InOut($cin, $cout) {
        $in = $("#"+$cin).val();
        $out = $("#"+$cout).val();

        if ($in == "" || $out == "") {
            alert("Vui lòng không để rỗng thông tin bộ test");
        } else {
            $("#BoTest>tbody").find("[name='none']").each(function () {
                $(this).remove();
            });
            var num = $("#BoTest>tbody").find(".input-output").length + 1;
            $("#BoTest>tbody").prepend('<tr class="input-output" name="io_data_'+num+'"><td>'+$in+'</td><td>'+$out+'</td><td><button type="button" name="btn-delete-inoutput" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Xóa bộ test"><i class="fa fa-trash" aria-hidden="true"></i></button></td></tr>');
            $("#formPost").append('<input type="text" class="form-control" name="io_data_'+num+'" value="'+$in+'|'+$out+'" style="display:none">');
            $("#"+$cin).val("");
            $("#"+$cout).val("");
        }
    }

    $( "input[name='bug-key']" ).change(function() {
        ChangeToSlug();
    });
    function ChangeToSlug()
    {
        // tks to freetuts.net
        var title, slug;
        title = $( "input[name='bug-key']" ).val();

        slug = title.toLowerCase();

        //Đổi ký tự có dấu thành không dấu
        slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a');
        slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e');
        slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i');
        slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o');
        slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u');
        slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y');
        slug = slug.replace(/đ/gi, 'd');
        //Xóa các ký tự đặt biệt
        slug = slug.replace(/\`|\~|\!|\@|\#|\||\$|\%|\^|\&|\*|\(|\)|\+|\=|\,|\.|\/|\]|\[|\{|\}|\?|\>|\<|\'|\"|\:|\;|_/gi, '');
        //Đổi khoảng trắng thành ký tự gạch ngang
        slug = slug.replace(/ /gi, "-");
        //Đổi nhiều ký tự gạch ngang liên tiếp thành 1 ký tự gạch ngang
        //Phòng trường hợp người nhập vào quá nhiều ký tự trắng
        slug = slug.replace(/\-\-\-\-\-/gi, '-');
        slug = slug.replace(/\-\-\-\-/gi, '-');
        slug = slug.replace(/\-\-\-/gi, '-');
        slug = slug.replace(/\-\-/gi, '-');
        //Xóa các ký tự gạch ngang ở đầu và cuối
        slug = '@' + slug + '@';
        slug = slug.replace(/\@\-|\-\@|\@/gi, '');
        slug = slug.toUpperCase();
        //In slug ra textbox có id “slug”
        $( "input[name='bug-key']" ).val(slug);
    }
</script>
<script>
    CKEDITOR.replace( 'bug-content' );
</script>