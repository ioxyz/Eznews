<?php

require_once __DIR__ . '/../app/app.php';
require_once __DIR__ . '/inc/auth.inc.php';

//Load configuration
$config_file = __DIR__ . '/../custom/config.yml';

if (is_file($config_file)){
    $conf = Spyc::YAMLLoad($config_file);
    $PlanetConfig = new PlanetConfig($conf);
} else {
    die('Config file (custom/config.yml) is missing.');
}

//Instantiate app
$Planet = new Planet($PlanetConfig);

//Load
if (0 < $Planet->loadOpml(__DIR__ . '/../custom/people.opml')) {
    $Planet->loadFeeds();
    $items = $Planet->getItems();
}

$everyone     = $Planet->getPeople();
$count_feeds  = count($everyone);
$page_id      = 'admin-feed';
$footer_extra = <<<FRAGMENT
    <script>
    var allCheckboxes = function(status){
        var form = document.getElementById('feedmanage');
        var selectboxes = form.getElementsByTagName('input');
        for (var i=0; i<selectboxes.length; i++){
            if ('checkbox' == selectboxes[i].type){
                selectboxes[i].checked = status;
            }
        }
    }

    window.onload = function(){
        //Select/unselect rows
        var form = document.getElementById('feedmanage');
        var selectboxes = form.getElementsByTagName('input');
        for (var i=0; i<selectboxes.length; i++){
            if ('checkbox' == selectboxes[i].type) {
                selectboxes[i].onchange = function() {
                    var tr = this.parentNode.parentNode;
                    if (this.checked) {
                        tr.className += ' selected';
                    } else {
                        tr.className = tr.className.replace('selected','');
                    }
                }
            }
        }

        var btSelectall = document.getElementById('selectall');
        btSelectall.onclick = function(){
            allCheckboxes('checked');
        }

        var btSelectnone = document.getElementById('selectnone');
        btSelectnone.onclick = function(){
            allCheckboxes('');
        }
    }
    </script>
FRAGMENT;

ob_start();
?>

            <div class="widget">
                <h3><?=_g('添加信息源')?></h3>
                <form action="subscriptions.php" method="post" id="feedimport">
                    <fieldset>
                        <label for="url"><?=_g('源地址：')?></label>
                        <input type="text" class="text" name="url" id="url" placeholder="http://news.walterzhang.cn" class="text" size="50" />
                        <input type="submit" class="submit add" name="add" value="<?=_g('确定')?>" />
                    </fieldset>
                    <p class="help"><?=_g('确保将信息源转为RSS或ATOM格式，简阅不提供自动转码服务')?></p>
                <input type="hidden" value="<?php echo $csrf->generate('feedmanage'); ?>" name="_csrf">
                </form>
            </div>

            <div class="widget">
                <h3><?=_g('管理现有源')?></h3>
                <form action="subscriptions.php" method="post" id="feedmanage">
                <p class="action">
                <span class="count">
                  <?php echo sprintf(_g('当前源数量: %s（过多源将影响加载速度）'), $count_feeds)?> 
             
                  </span>
                  
                <input type="hidden" value="<?php echo $csrf->generate('feedmanage'); ?>" name="_csrf">
                <input type="submit" class="submit save" name="save" id="save" value="<?=_g('保存更改')?>" />
                <input type="submit" class="submit delete" name="delete" id="delete" value="<?=_g('删除所选源')?>" />
                </p>
                <p class="select"><?=_g('操作')?> <a href="javascript:void(0);" id="selectall"><?=_g('全选')?></a>, <a href="javascript:void(0);" id="selectnone"><?=_g('全不选')?></a></p>
                <table>
                    <thead>
                        <tr>
                            <th><span><?=_g('Selection')?></span></th>
                            <th><?=_g('信息源')?></th>
                            <th><?=_g('操作时间')?></th>
                            <th><?=_g('网页地址')?></th>
                            <th><?=_g('RSS地址')?></th>
                            <th><?=_g('暂停服务')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($everyone as $opml_person){
                        $i++;
                        ?>
                        <tr class="<?=($i%2)?'odd':'even'; ?>">
                            <td><input type="checkbox" class="checkbox" name="opml[<?=$i; ?>][delete]" /></td>
                            <td><input type="text" size="10" class="text" name="opml[<?=$i; ?>][name]" value="<?=$opml_person->getName(); ?>" /></td>
                            <td>
                                <?php
                                $items = $opml_person->get_items();
                                if (count($items) > 0) {
                                    echo $items[0]->get_date();
                                } else {
                                    echo _g('Not in cache');
                                }
                                $check_is_down = $opml_person->getIsDown() === '1' ? 'checked="checked"' : '';
                                ?>
                            </td>
                            <td><input type="text" size="30" class="text" name="opml[<?=$i; ?>][website]" value="<?=$opml_person->getWebsite(); ?>" /></td>
                            <td><input type="text" size="30" class="text" name="opml[<?=$i; ?>][feed]" value="<?=$opml_person->getFeed(); ?>" /></td>
                            <td><input type="checkbox" readonly="readonly" name="opml[<?=$i; ?>][isDown]" <?=$check_is_down?> value="1" /></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                </form>
            </div>
<?php
$page_content = ob_get_contents();
ob_end_clean();

$admin_access = 1;
require_once __DIR__ . '/template.php';
