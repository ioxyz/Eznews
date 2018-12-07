<?php if(!isset($admin_access)) return; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  
    <meta charset="utf-8">
    <title>

<?php
    echo _g('简阅') . '后台管理 ';
   
?>
    </title>
    <link rel="stylesheet" media="screen" type="text/css" href="default.css">
  
<!--[if lte IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

    <?=@$header_extra ?: ''; ?>
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />  
</head>

<body id="<?=@$page_id ?: ''; ?>">
    <div id="page">
        <header>
            <h1>简阅</h1>
            <p><a href="../"><?=_g('返回简阅首页')?></a></p>
        </header>

        <?php if($admin_access == 1) : ?>

        <p class="logout"><a href="logout.php"><?=_g('退出登录')?></a></p>
        <nav>
            <ul>
                <li id="nav-feed"><a href="index.php"><?=_g('信息管理')?></a></li>
                <li id="nav-admin"><a href="administration.php"><?=_g('系统管理')?></a></li>
            </ul>
        </nav>

        <?php endif; ?>



        <div id="content">

        <?=@$page_content ?: ''; ?>

        </div>
    </div>

<?=@$footer_extra ?: ''; ?>

</body>
</html>
