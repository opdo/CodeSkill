<?php
    // NAVBAR CỦA WEB
    if (!defined("_WEB_NAME")) die();

?>

<nav class="col-sm-3 col-md-2 hidden-xs-down bg-faded sidebar">
    <p class="text-center">
        <b class="navbar-brand" style="color: #0275d8"><i class="fa fa-code fa-lg" aria-hidden="true"></i> CodeSkill</b>
        <?php
            if (is_Logged()) {
                echo "<br><br>";
                echo '<img src="'. $_SESSION["LOGIN"]["AVATAR"] .'" class="rounded-circle" alt="'. htmlentities($_SESSION["LOGIN"]["NAME"]) .'" style="height: 80px;width: 80px;">';
                echo '<br><b style="color: #0275d8">'. htmlentities($_SESSION["LOGIN"]["NAME"]) .'</b><br>';
                echo '<a href="/logout" class="btn btn-outline-primary btn-sm" role="button" aria-disabled="true"><i class="fa fa-sign-out" aria-hidden="true"></i> Thoát</a>';
            }
        ?>
    </p>

    <ul class="nav nav-pills flex-column">
        <li class="nav-item">
            <a class="nav-link <?php if (!isset($_GET["recent"]) && !isset($_GET["rank"])) echo "active"; ?>" href="/"><i class="fa fa-bug" aria-hidden="true"></i> Problem</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if (isset($_GET["recent"])) echo "active"; ?>" href="/recent"><i class="fa fa-random" aria-hidden="true"></i> Recent</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if (isset($_GET["rank"])) echo "active"; ?>" href="/rank"><i class="fa fa-rocket" aria-hidden="true"></i> Rank</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#"><i class="fa fa-book" aria-hidden="true"></i> How to use?</a>
        </li>
    </ul>
    <?php
        if (!is_Logged()) {
            echo '
            <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="'. _Facebook_URL_Login() .'"><i class="fa fa-facebook-square" aria-hidden="true"></i> Login by Facebook</a>
                </li>
            </ul>
            ';
        }
    ?>

</nav>
