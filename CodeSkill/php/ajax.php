<?php
    require_once $_SERVER['DOCUMENT_ROOT']."/php/function.php";
    // giá trị trả về
    $return = array('success' => 0, 'msg' => 'Lệnh nhập chưa chính xác');

    // lấy các giá trị post lên
    $_var_cmd = "";
    $_var_task = "";
    $_var_data_1 = "";$_var_data_2 = "";$_var_data_3 = "";$_var_data_4 = "";
    if (isset($_POST["cmd"])) $_var_cmd = $_POST["cmd"];
    if (isset($_POST["task"])) $_var_task = $_POST["task"];
    if (isset($_POST["data_1"])) $_var_data_1 = $_POST["data_1"];
    if (isset($_POST["data_2"])) $_var_data_2 = $_POST["data_2"];
    if (isset($_POST["data_3"])) $_var_data_3 = $_POST["data_3"];
    if (isset($_POST["data_4"])) $_var_data_4 = $_POST["data_4"];
    if ($_var_cmd == "problem") {
        if ($_var_task == "post") {
            $return["msg"] = "Vui lòng đăng nhập để tiếp tục";
            if (is_Logged()) {
                $return["msg"] = "Vui lòng nhập đầy đủ các thông tin bên trên";
                if (!empty($_var_data_1) && !empty($_var_data_2) && !empty($_var_data_3) && !empty($_var_data_4)) {
                    if ($_var_data_4 != "public") $_var_data_4 = 1;
                    else $_var_data_4 = 0;
                    $_list_lng = $GLOBALS['__LNG'];
                    $return["msg"] = "Không hỗ trợ mã nguồn này, vui lòng kiểm tra lại hoặc F5 lại trang";
                    if (array_search($_var_data_3, $_list_lng) !== false) {
                        $return["msg"] = 'Hic hic không tìm thấy bài toán này @@ bạn vui lòng F5 lại trang nha :(';
                        $p = new Problem;
                        if ($p->is_ID_Exist($_var_data_1)) {
                            $return["success"] = 1;
                            $return["time_exe"] = 0;
                            $return["memory"] = 0;
                            $return["msg"] = "Chúc mừng bạn đã giải quyết được problem này <i aria-hidden=\"true\" class=\"fa fa-heart\" style=\"color: #ff005e;\"></i>";
                            $_info = $p->get_Info($_var_data_1);
                            $io = json_decode($_info["TestCase"],true);
                            $i = 1;
                            while (1) {
                                if (isset($io[$i])) {
                                    $in = explode('|',$io[$i])[0];
                                    $out = explode('|',$io[$i])[1];
                                    $in = str_replace("\\n","\n", $in);
                                    $in = str_replace("\\t","\t", $in);
                                    $out = str_replace("\\n","\n", $out);
                                    $out = str_replace("\\t","\t", $out);
                                    $exe = _ExecuteOnline($_var_data_2, $in, $_var_data_3);
                                    if (!isset($exe["output"], $exe["executeTime"], $exe["memory"])) {
                                        $return["success"] = 0;
                                        $return["msg"] = " Không lấy được thông tin, có thể do lỗi biên dịch @@ bạn vui lòng trở lại sau nha :(";
                                        break;
                                    }
                                    $return["time_exe"] += floatval($exe["executeTime"]);
                                    $return["memory"] += floatval($exe["memory"]);
                                    if ($exe === false) {
                                        $return["success"] = 0;
                                        $return["msg"] = " Server hiện đang trục trặc @@ bạn vui lòng trở lại sau nha :(";
                                        break;
                                    }
                                    if ($exe["output"] != $out) {
                                        if (empty($exe["cmpinfo"])) {
                                            $exe["output"] = str_replace("\n","<br>",$exe["output"]);
                                            $exe["output"] = str_replace(" "," ",$exe["output"]);
                                            $return["msg"] = " Sai kết quả ở bộ test thứ " . $i . '<br><i class="fa fa-sign-in" aria-hidden="true"></i> Input: <kbd style="display: block;">'. str_replace("\n","<br>",$in) .'</kbd><br><i class="fa fa-sign-out" aria-hidden="true"></i> Output của bạn: <kbd style="display: block;">'. $exe["output"] . '</kbd>';
                                        } else {
                                            $exe["cmpinfo"] = str_replace("\n","<br>",$exe["cmpinfo"]);
                                            $exe["cmpinfo"] = str_replace("\r","<br>",$exe["cmpinfo"]);
                                            $return["msg"] = " Lỗi biên dịch<br><kbd style=\"display: block;\">" . $exe["cmpinfo"].'</kbd>';
                                        }
                                        $return["success"] = 0;
                                        break;
                                    }
                                    if (floatval($exe["executeTime"]) > floatval($_info["LimitTime"])) {
                                        $return["msg"] = " Thời gian chạy quá lâu so với quy định (<i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> ". floatval($exe["executeTime"])*1000 ." ms)";
                                        $return["success"] = 0;
                                        break;
                                    }
                                    if (floatval($exe["memory"]) > floatval($_info["LimitMem"])) {
                                        $return["msg"] = " Bộ nhớ chạy vượt quy định (<i class=\"fa fa-microchip\" aria-hidden=\"true\"></i> ". floatval($exe["memory"])/1024 ." kb)";
                                        $return["success"] = 0;
                                        break;
                                    }
                                } else {
                                    $i--;
                                    if ($i == 0) break;
                                    $return["time_exe"] /= $i;
                                    $return["memory"] /= $i;
                                    break;
                                }
                                $i++;
                            }

                            if ($return["success"] == 1) {
                                $return["msg"] .= "<br><i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> Thời gian thực thi: " . floatval($return["time_exe"])*1000 . " ms";
                                $return["msg"] .= "<br><i class=\"fa fa-microchip\" aria-hidden=\"true\"></i> Bộ nhớ thực thi: " . round(floatval($return["memory"])/1024,2) . " kb";
                                $p->set_AC($_SESSION["LOGIN"]["ID"],$_var_data_1);
                                $p->add_recent($_var_data_1,$_SESSION["LOGIN"]["ID"],1);
                            } else {
                                $p->add_recent($_var_data_1,$_SESSION["LOGIN"]["ID"],0);
                            }
                        }
                    }
                }
            }
        }
    }

    // trả giá trị về
    echo json_encode($return);
    exit();
?>

