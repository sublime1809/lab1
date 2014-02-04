<?php
error_reporting(E_ALL);
    ini_set('display_errors', 'On');
require_once('../userAjax.php');

$users = getUsers();
echo "<ul>";
foreach($users as  $user) {
    ?>
    <li><a href="#/user/<?=$user['id']?>"><?=$user['username']?></a></li>
    <?php
}
echo "</ul>";

