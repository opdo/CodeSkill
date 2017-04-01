<?php
    // HEADER CỦA WEB
    if (!defined("_WEB_NAME")) die();
?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<title><?= _WEB_NAME ?></title>
<!-- deffault -->
<meta name="description" content="<?= _WEB_DESCRIPTION ?>"/>
<meta name="keywords" content="<?= _WEB_KEYWORD ?>">
<!-- Facebook -->
<meta property="og:title" content="<?= _WEB_NAME ?>">
<meta content='website' property='og:type'/>
<meta content="<?= _WEB_DESCRIPTION ?>" property='og:description'/>
<meta content="<?= _WEB_NAME ?>" property='og:site_name'/>
<meta property="og:image" content="<?= _WEB_IMAGE ?>" />
<meta name="author" content="Vinh Phạm">
<!-- CSS & ICON -->
<link href='<?= _WEB_ICON ?>' rel='icon' type='image/x-icon'/>
<link rel="stylesheet" href="/assets/css/bootstrap.min.css">
<link rel="stylesheet" href="/assets/css/font-awesome.css">
<link rel="stylesheet" href="/assets/css/index.css">
<script src="/assets/plugin/ckeditor/ckeditor.js"></script>
<script src="/assets/js/jquery-3.1.1.min.js" type="text/javascript"></script>
<script src="/assets/js/tether.min.js" type="text/javascript"></script>
<script src="/assets/js/bootstrap.min.js" type="text/javascript"></script>

<div id="fb-root"></div>
<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v2.8&appId=<?= _FB_APP_ID ?>";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
