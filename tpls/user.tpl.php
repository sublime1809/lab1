<?php
error_reporting(E_ALL);
    ini_set('display_errors', 'On');
require_once('../userAjax.php');

$users = getUsers();
echo "<ul>";
foreach($users as  $user) {
    ?>
<li><a href="#/user/<?=$user['id']?>"><?=$user['username']?></a><span><?php if(isset($_COOKIE['userId']) && $user['id'] == $_COOKIE['userId']) echo " - (Myself)"; ?></span></li>
    <?php
}
echo "</ul>";

