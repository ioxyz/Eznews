<?php

require_once __DIR__ . '/../app/app.php';
require_once __DIR__ . '/inc/auth.inc.php';

function removeSlashes(&$item, $key){
    $item = stripslashes($item);
}

if (!$csrf->verify($_POST['_csrf'], 'feedmanage')) {
    die('Invalid CSRF token!');
}

if (isset($_POST['opml']) || isset($_POST['add'])) {

    // Load old OPML
    $oldOpml = OpmlManager::load(__DIR__.'/../custom/people.opml');
    if ($PlanetConfig->getName() === '') {
        $PlanetConfig->setName($oldOpml->getTitle());
    }
    $newOpml = new Opml();
    $newOpml->title = $PlanetConfig->getName();

    // Remove slashes if needed
    if (get_magic_quotes_gpc() && isset($_POST['opml'])) {
        array_walk_recursive($_POST['opml'], 'removeSlashes');
    }
    // Delete/Save feeds
    if (isset($_POST['delete']) || isset($_POST['save'])){
        foreach ($_POST['opml'] as $person){
            if (isset($_POST['delete'])) {
                //delete mode, check if to be deleted
                if (!isset($person['delete'])){
                    $newOpml->entries[] = $person;
                }
            } else {
                $newOpml->entries[] = $person;
            }
        }
    }

    // Add feed
    if (isset($_POST['add'])){
        if ('http://' != $_POST['url']) {
            //autodiscover feed
            $feed = new SimplePie();
            $feed->enable_cache(false);
            $feed->set_feed_url($_POST['url']);
            if ($conf['checkcerts'] === false) {
                $feed->set_curl_options([
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSL_VERIFYPEER => false
                ]);
            }
            $feed->init();
            $feed->handle_content_type();
            $person['name'] = html_entity_decode($feed->get_title());
            $person['website'] = $feed->get_permalink();
            $person['feed'] = $feed->feed_url;
            $person['isDown'] = '0';

            $oldOpml->entries[] = $person;
        }
        $newOpml->entries = $oldOpml->entries;
    }

    // Backup old OPML
    OpmlManager::backup(__DIR__.'/../custom/people.opml');

    // Save new OPML
    OpmlManager::save($newOpml, __DIR__.'/../custom/people.opml');
}
header("Location: index.php");
die();
