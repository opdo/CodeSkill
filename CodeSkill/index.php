<?php
    ob_start();
	require_once $_SERVER['DOCUMENT_ROOT']."/php/function.php";
	// Xử lý trạng thái login
	if (isset($_GET["login"])) {
        _Login();
    }
	elseif (isset($_GET["logout"])) {
        session_unset();
        header('Location: /');
        die();
    }

?>

<!DOCTYPE html>
<html lang="vi">
	<head>
		<?php require_once $_SERVER['DOCUMENT_ROOT']."/php/web/header.php"; ?>
	</head>

	<body>
	<div class="container-fluid">
		<div class="row">
            <?php require_once $_SERVER['DOCUMENT_ROOT']."/php/web/navbar.php"; ?>
			<main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3">
                <?php
                    if (isset($_GET["recent"])) {
                        require_once $_SERVER['DOCUMENT_ROOT']."/php/web/recent.php";
                    } elseif (isset($_GET["rank"])) {
                        require_once $_SERVER['DOCUMENT_ROOT']."/php/web/rank.php";
                    } else {
                        if (isset($_GET["problem"])) require_once $_SERVER['DOCUMENT_ROOT']."/php/web/problem_view.php";
                        elseif (isset($_GET["admin"])) {
                            if (is_Admin()) {
                                if (isset($_GET["delete"])) {
                                    $p = new Problem;
                                    $idp = $_GET["admin"];
                                    $p->del($idp);
                                    header('Location: /');
                                    die();
                                } else {
                                    require_once $_SERVER['DOCUMENT_ROOT']."/php/web/problem_admin.php";
                                }
                            }

                        }
                        else require_once $_SERVER['DOCUMENT_ROOT']."/php/web/problem.php";
                    }
                ?>
			</main>
		</div>
	</div>
	</body>
</html>
