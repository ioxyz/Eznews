<?php

require_once __DIR__ . '/../app/app.php';
require_once __DIR__ . '/inc/auth.inc.php';


$opml         = OpmlManager::load(__DIR__ . '/../custom/people.opml');
$opml_people  = $opml->getPeople();
$page_id      = 'admin-admin';
$header_extra = <<<"HTML"
    <script>
    window.onload = function(){
        var formManage = document.getElementById('frmPurge');
        formManage.onsubmit = function(){
            return confirm("{$l10n->getString('清除缓存，此操作不可逆，您确定吗？')}");
        }
    }
    </script>

HTML;

$page_content = <<<"FRAGMENT"

            <div class="widget">
                <h3>{$l10n->getString('清除系统缓存')}</h3>
                <form action="purgecache.php" method="post" id="frmPurge">
                    <input type="hidden" value="{$csrf->generate('frmPurge')}" name="_csrf">
                    <p><label>{$l10n->getString('清除缓存：')}</label><input type="submit" class="submit delete" name="purge" id="purge" value="{$l10n->getString('确定')}" /></p>
                    <p class="help">{$l10n->getString('清除缓存将使简阅系统重新加载历史新闻数据')}</p>
                </form>
            </div>

            <div class="widget">
                <h3>{$l10n->getString('更改后台管理员登录密码')}</h3>
                <form action="changepassword.php" method="post" id="frmPassword">
                    <input type="hidden" value="{$csrf->generate('frmPassword')}" name="_csrf">
                    <p><label for="password">{$l10n->getString('新密码：')}</label> <input type="password" class="text" value="" name="password" id="password" size="20" /> <input type="submit" class="submit delete" name="changepwd" id="changepwd" value="{$l10n->getString('确定更改')}" /></p>
                </form>
            </div>

FRAGMENT;

$footer_extra = '';
$admin_access = 1;
require_once __DIR__ . '/template.php';
