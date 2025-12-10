<?php
//system('wget https://hifood.pl/modules/ph_instagram/cronjob.php?secure=8eca33071595c0e7e70140af1ea8fbe4');
//system('wget https://hifood.pl/?insta-token=g43h7vn437vwcw7');

$baselinker_hifood = file_get_contents('https://hifood.pl/modules/ph_instagram/cronjob.php?secure=8eca33071595c0e7e70140af1ea8fbe4');
$baselinker_studio = file_get_contents('https://hifood.pl/?insta-token=g43h7vn437vwcw7');