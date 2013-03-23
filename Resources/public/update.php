<?php
if (isset($_POST['update'])) {
    set_time_limit(0);
    header('Content-Type: text/plain');
    if ($_POST['key']!='qwerty') die('Access denied');
    $path = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
    if (!file_exists("$path/composer.phar")) system("cd $path;php -r \"eval('?>'.file_get_contents('https://getcomposer.org/installer'));\" > /dev/null");
    $cmd = "cd $path; php composer.phar update --no-ansi --no-interaction";
    passthru($cmd);
    $cmd = "cd $path; app/console doctrine:schema:update --force";
    passthru($cmd);
}
else
{
    echo '<html><body><form method="post"><label for="key">Secret</label><br/><textarea name="key" style="width:500px; height:90px;"></textarea><br/><input type="submit" name="update" value="Automatic update"/></form></body></html>';
}