<?php
/* Написать скрипт закачивания страницы mirinstrumenta.ua, из страницы извлечь телефоны,
 * заголовки, ссылки в блоке"Бренды", сохранить в таблицу
 * loot, имеющую такую структуру:
 * * id — целое, автоинкрементарное
 * * phone  — строковое
 * * title — строковое, не более 230 символов
 * * url — строковое, не более 240 символов, уникальное
 */

// phones
$html = file_get_contents('https://mirinstrumenta.ua/');
$dom = new DOMDocument();
@$dom->loadHTML($html);
$xpath = new DOMXPath($dom);
$nodes = $xpath->evaluate('//div[@class="phones"]//a');
$allPhones = [];
for($i=0; $i <=3; $i++) {
    $htmlBlock = $dom->saveXML($nodes->item($i));
    $value = explode('<a', $htmlBlock);
    $value = explode('tel:', $value[1]);
    $value = explode(' style', $value[1]);
    $value = explode('"', $value[0]);
    $allPhones[] = $value[0];
}

// menus
$elements = $xpath->query('//*[@id="top_links"]');
$menus = [];
if (!is_null($elements)) {
    foreach ($elements as $element) {
        $nodes = $element->childNodes;
        foreach ($nodes as $node) {
            $menu = explode('innerHTML="', $node->nodeValue);
            $menu = explode('<\/a>', $menu[1]);
            foreach ($menu as $item) {
                $menu = explode('<a', $item);
                if(isset( $menu[1])) {
                    $menu = explode('>', $menu[1]);
                    $menus[] = $menu[1];
                }
            }
        }
    }
}

//links on brands
$nodes = $xpath->evaluate('//div[@id="brands_carousel"]//a');
$links = [];
for($i=0; $i<= count($nodes); $i++) {
  $htmlBlock = $dom->saveXML($nodes->item($i));
  $value = explode('href="', $htmlBlock);
  $value = explode('">', $value[1]);
     if(strripos($value[0], '.html') !== false) {
         $links[] = $value[0];
     }
}
$array = [];
foreach ($links as $key => $link) {
    $array[] = [
        'url' => $link ?? '',
        'title' => $menus[$key] ?? '',
        'phone' => $allPhones[$key] ?? ''
    ];
}

$host = 'localhost';
$dbName = 'tasks';
$tableName = 'loot';
$user = 'root';
$pass = '';

try {
    $dbh = new PDO("mysql:host=$host", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->exec(
        "CREATE DATABASE IF NOT EXISTS $dbName
        DEFAULT CHARACTER SET utf8
        DEFAULT COLLATE utf8_general_ci;"
    );
    $dbh->exec("use $dbName;");
    $dbh->exec(
        "CREATE TABLE IF NOT EXISTS $tableName (
            id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            phone VARCHAR(50) ,
            title VARCHAR(230) ,
            url VARCHAR(240),
            UNIQUE (url)) ;"
    );

    foreach ($array as $item ) {
        $sth = $dbh->prepare(
            "INSERT IGNORE INTO $tableName
            (phone, title, url)
            VALUES (:title,:phone,:url);"
        );
        
        $sth->bindParam(':phone', $item["phone"]);
        $sth->bindParam(':title', $item["title"]);
        $sth->bindParam(':url',  $item["url"]);
        $sth->execute();
    }

    $sth = $dbh->query("SELECT * FROM $tableName;");
    $sth->setFetchMode(PDO::FETCH_ASSOC);
    $result = $sth->fetchAll();

    $dbh = null;
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>