<?php

require_once __DIR__.'/../app/app.php';
require_once __DIR__.'/inc/auth.inc.php';

if ($csrf->verify($_POST['_csrf'], 'frmPassword') && isset($_POST['password']) && ('' != $_POST['password'])) {
    $out = '<?php $login="admin"; $password="'.md5($_POST['password']).'"; ?>';
    file_put_contents(__DIR__.'/inc/pwd.inc.php', $out);
    die("Password changed. <a href='administration.php'>Login</a>");
} else {
    die('Can not change password');
}
