<?php
if (!defined("_WEB_NAME")) die();
if (!isset($_GET["problem"])) die();
$P_ID = $_GET["problem"];
$p = new Problem;
if (!$p->is_ID_Exist($P_ID)) die();
$INFO = $p->get_Info($P_ID);
?>

<h1 style="color: #0089e0"><button type="button" style="border-radius: 50%;width: 50px;height: 50px;" onclick="location.href = '/'" class="btn btn-outline-primary"><i class="fa fa-chevron-left fa-lg" aria-hidden="true"></i></button> <?= $INFO["Name"] ?>
</h1>
<br>

<div class="container">
    <div class="row" style="padding-bottom: 30px;margin-bottom: 30px">

        <div class="col-sm-9 blog-content">
            <div class="card">
                <div class="card-header">
                    <div style="float:right" class="fb-like" data-href="<?php echo parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH); ?>" data-layout="button_count" data-action="like" data-size="large" data-show-faces="false" data-share="true"></div>

                    <ul class="nav nav-tabs card-header-tabs pull-left">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#Menu1"><i class="fa fa-question-circle" aria-hidden="true"></i> Nội dung</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#Menu2"><i class="fa fa-upload" aria-hidden="true"></i>  Nộp bài</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#Menu3"><i class="fa fa-rocket" aria-hidden="true"></i>  Danh sách đã nộp</a></li>
                    </ul>
                </div>
                <div class="card-block tab-content">
                    <div id="Menu1" class="tab-pane fade active show">
                        <?php
                        if (is_Logged()) {
                            if ($p->is_AC($_SESSION["LOGIN"]["ID"], $P_ID)) {
                                echo '
                            <div class="alert alert-info" role="alert">
                                <i class="fa fa-check fa-2x" aria-hidden="true" style="color: seagreen"></i> Bạn đã giải quyết hoàn tất problem này trước đó.
                            </div>
                                ';
                            }
                        }
                        echo $INFO["Decription"];
                        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
                        ?>
                        <br>
                        <div class="fb-comments" data-href="<?= $actual_link ?>" data-width="100%" data-numposts="5"></div>
                    </div>
                    <div id="Menu2" class="tab-pane fade">
                        <div id="Problem-Alert">
                            <?php
                            if (is_Logged()) {
                                echo '
                            <div class="alert alert-info" role="alert">
                                <i class="fa fa-warning fa-2x" style="color: orangered" aria-hidden="true"></i> <strong style="color: orangered; font-size: 1.3em">Chú ý,</strong> không sử dụng các hàm system("pause"), fopen, freopen, fstream, getch, kbhit. Mã thoát chương trình là 0. Không in ra các câu nhắc như "Nhap n", "Ket qua la".
                            </div>
                            ';
                            } else {
                                echo '
                            <div class="alert alert-warning" role="alert">
                              <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Bạn vui lòng <a href="'. _Facebook_URL_Login() .'" class="alert-link">đăng nhập</a> để thực hiện chức năng nộp bài<br>
                              <small>Quá trình đăng nhập bằng Facebook diễn ra rất nhanh và chúng tôi không thu thập bất kỳ thông tin nào của bạn trừ ID Facebook.</small>
                            </div>
                                ';
                            }

                            ?>
                        </div>

                        <div id="Problem-Post">
                            <?php
                            if (is_Logged()) {
                                $select_html = "";
                                $_list = $GLOBALS['__LNG'];
                                foreach ($_list as $key => $value) {
                                    $select_html .= '<option value="' . $value . '">' . $key . '</option>';
                                }

                                echo '
                        <div class="row">
                            <div class="col">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"><i class="fa fa-github-alt" aria-hidden="true"></i> Ngôn ngữ: </label>
                                    <div class="col-sm-8">
                                        <select class="custom-select form-control" id="code-type" name="code-type" required>
                                            ' .
                                    $select_html
                                    . '
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group row">
                                    <label class="col-sm-5 col-form-label"><i class="fa fa-share-alt-square" aria-hidden="true"></i> Chế độ chia sẻ:</label>
                                    <div class="col-sm-7">
                                        <select class="custom-select form-control" id="code-share" name="code-share" required>
                                            <option value="public" checked>Công khai</option>
                                            <option value="private">Riêng tư</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-code" aria-hidden="true"></i> Mã nguồn: </label>
                            <textarea class="form-control" id="code-source" rows="10" name="code-source"></textarea>
                        </div>
                        <center><button type="button" class="btn btn-primary text-center" onclick="_Problem_Submit()"><i class="fa fa-check" aria-hidden="true"></i> Nộp bài</button></center>

                                ';
                            }
                            ?>

                        </div>
                    </div>
                    <div id="Menu3" class="tab-pane fade">
                        <?php
                            _HTML_PROBLEM_RANK($_GET["problem"]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3 text-center hidden-sm-down">
            <div style="color: white; background-color: #0089e0; padding: 20px; border-radius: 20px">
                <i class="fa fa-users" aria-hidden="true"></i></i> <b>Số người hoàn tất: </b> <?php echo count($p->get_AC($_GET["problem"])); ?>
                <br>
                <i class="fa fa-clock-o" aria-hidden="true"></i> <b>Tạo lúc: </b> <?php echo _Exchange_Time($INFO["Date"]); ?>
                <br>
                <i class="fa fa-window-maximize" aria-hidden="true"></i> <b>Time limit: </b> <?php echo intval($INFO["LimitTime"]*1000); ?> ms
                <br>
                <i class="fa fa-microchip" aria-hidden="true"></i> <b>Memory limit: </b> <?php echo round(floatval($INFO["LimitMem"]/1024), 2); ?> kb
            </div>
            <br>
            <a role="button" class="btn btn-outline-primary btn-block" href="https://youtu.be/oLXG84cKZOU" target="_blank"><i class="fa fa-info" aria-hidden="true"></i> Hướng dẫn nộp bài</a>

        </div>

    </div>

</div>


<script>
    function _Problem_Submit() {
        $("#Problem-Post").hide();
        $("#Problem-Alert").html('<div class="text-center"><i class="fa fa-refresh fa-spin fa-2x fa-fw"></i><br>Đang biên dịch và chạy thử<br>Đợi tí nha bạn <i aria-hidden="true" class="fa fa-heart" style="color: #ff005e;"></i></div>');
        $code = $("#code-source").val();
        $code_type = $("#code-type").val();
        $code_share = $("#code-share").val();
        $.ajax({
            url : "/php/ajax.php",
            type : "post",
            dateType:"text",
            data : {
                cmd : 'problem',
                task : 'post',
                data_1 : <?php echo "'".$_GET["problem"]."'";?>,
                data_2 : $code,
                data_3 : $code_type,
                data_4 : $code_share
            },
            success : function (result){
                var data = JSON.parse(result);
                if (data.success == 1) {
                    $("#Problem-Alert").html('<div class="alert alert-success" role="alert"> <strong>Hoàn tất!</strong> '+ data.msg +'</div>');
                    $("#Problem-Post").remove();
                } else {
                    $("#Problem-Alert").html('<div class="alert alert-danger" role="alert"> <strong>Có lỗi!</strong> '+ data.msg +'</div>');
                    $("#Problem-Post").show();
                }
            }
        });
    }
</script>