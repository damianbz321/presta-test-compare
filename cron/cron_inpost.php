<?php
//system('wget https://hifood.pl/module/pminpostpaczkomaty/baselinker?token=9c0b948700f3');
//system('wget https://studiomocy.com/modules/pminpostpaczkomaty/cron.php?token=e81e35dff5bf');
//system('wget https://hifood.pl/modules/x13googlemerchant/xml.php?id_shop=1&id_lang=1&id_currency=1&token=ZpX1DwUQ');
//system('wget https://studiomocy.com/modules/x13googlemerchant/xml.php?id_shop=1&id_lang=1&id_currency=1&token=26EOwqTi');


$baselinker_hifood = file_get_contents('https://hifood.pl/module/pminpostpaczkomaty/baselinker?token=9c0b948700f3');
$baselinker_studio = file_get_contents('https://studiomocy.com/modules/pminpostpaczkomaty/cronbaselinker.php?token=779e2ca819f9');
$googlemerc_hifood = file_get_contents('https://hifood.pl/modules/x13googlemerchant/xml.php?id_shop=1&id_lang=1&id_currency=1&token=ZpX1DwUQ');
$googlemerc_studio = file_get_contents('https://studiomocy.com/modules/x13googlemerchant/xml.php?id_shop=1&id_lang=1&id_currency=1&token=26EOwqTi');
//$freshmail__hifood = file_get_contents('https://hifood.pl/modules/freshmail/cron/abandoned_cart.php?token=f686e39f0d');

$kodyrabatowe__hifood = file_get_contents('https://hifood.pl/modules/comvou/cronjob.php?key=1f543c304ca179919e100538410bb66a');
$kodyrabatowe__studio = file_get_contents('https://studiomocy.com/modules/comvou/cronjob.php?key=0ff7b225ea3ca5b4811bca48f3592676');

$ga4 = file_get_contents('https://hifood.pl/modules/seigitagmanager/cron/ga4.php');

$sitemap = file_get_contents('https://hifood.pl/modules/gsitemap/gsitemap-cron.php?token=295b8fecb4&id_shop=1');


echo '<h2>GA4 hifood</h2>';
echo '<br><br>';
var_dump($ga4);
echo '<br><br>';
echo '<h2>Baselinker hifood</h2>';
echo '<br><br>';
var_dump($baselinker_hifood);
echo '<br><br>';
echo '<h2>Baselinker studiomocy</h2>';
echo '<br><br>';
var_dump($baselinker_studio);
echo '<br><br>';
echo '<h2>merchant hifood</h2>';
echo '<br><br>';
var_dump($googlemerc_hifood);
echo '<br><br>';
echo '<h2>merchant studiomocy</h2>';
echo '<br><br>';
var_dump($googlemerc_studio);
echo '<br><br>';
//echo '<h2>Freshmail hifood</h2>';
//echo '<br><br>';
//var_dump($freshmail__hifood);
echo '<br><br>';
echo '<h2>Kody rabatowe hifood</h2>';
echo '<br><br>';
var_dump($kodyrabatowe__hifood);
echo '<br><br>';
echo '<h2>Kody rabatowe studiomocy</h2>';
echo '<br><br>';
var_dump($kodyrabatowe__studio);
echo '<br><br>';
echo '<h2>Sitemap</h2>';
echo '<br><br>';
var_dump($sitemap);
echo '<br><br>';
echo '<br><br>';
echo '<br><br>';
echo '<br><br>';

die('OK');
