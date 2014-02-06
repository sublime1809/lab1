<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if(isset($_POST['method'])) {
    $method = $_POST['method'];
    $result = array();
    if($method == "createAccount") {   
        $username = $_POST['username'];
        $password = $_POST['password'];

        $userId = saveUser($username, $password);

        setcookie('username', $username);
        setcookie('userId', $userId);
    } elseif($method == "addToken") {
        if(!isset($_COOKIE['userId'])) {
            die('No userId.');
        }
        $token = $_POST['access_token'];
        setToken($_COOKIE['userId'], $token);
    } elseif($method == "login") {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $row = verifyUser($username, $password);
        if($row) {
            setcookie('access_token', $row['access_token']);
            setcookie('username', $row['username']);
            setcookie('userId', $row['id']);

            $result['valid'] = true;
        } else {
            $result['valid'] = false;
            unsetUser();
        }
    } elseif($method == "logout") {
        unsetUser();
    } elseif($method == "getToken") {
        $userId = $_POST['userId'];
        $result['access_token'] = getToken($userId);
    } elseif($method == "getTokenAndUser") {
        $userId = $_POST['userId'];
        $result['access_token'] = getToken($userId);
        $users = getUsers($userId);
        $result['user'] = (isset($users[0])) ? $users[0]['username'] : '';
    } elseif($method == "getUsers") {
        $result['users'] = getUsers();
    }
    echo json_encode($result);
}

function getUsers($userId = "") {
    $link = connectDB();
    $query;
    if($userId === "") {
        $query = sprintf("SELECT id, username FROM `foursquare`.`Users`;");
    } else {
        $query = sprintf("SELECT id, username FROM `foursquare`.`Users` WHERE id = '%s';",
                $link->real_escape_string($userId));
    }
    $result = $link->query($query);
    $return = array();
    while( $row = $result->fetch_assoc() ) {
        $return[] = $row;
    }
    return $return;
}
function unsetUser() {
    setcookie('access_token', '', time()-3600);
    setcookie('username', '', time()-3600);
    setcookie('userId', '', time()-3600);
}
function getToken($userId) {
    $link = connectDB();
    $query = sprintf("SELECT COUNT(*) as count, access_token FROM `foursquare`.`Users` "
            . "WHERE id = %s;", 
            $link->real_escape_string($userId));
    
    $result = $link->query($query);
    $row = $result->fetch_assoc();    
    if ( mysqli_num_rows($result) > 1 || $row['count'] == 0 ) {
        return false;
    } else {
        return $row['access_token'];
    }
}
function verifyUser($user, $password) {
    $link = connectDB();
    $query = sprintf("SELECT COUNT(*) as count, access_token, id, username FROM `foursquare`.`Users` "
            . "WHERE username = '%s' AND password = '%s';", 
            $link->real_escape_string($user), $link->real_escape_string($password));
    $result = $link->query($query);
    $row = $result->fetch_assoc();
    if ( mysqli_num_rows($result) > 1 || $row['count'] == 0 ) {
        return false;
    } else {
        return $row;
    }
}
function saveUser($user, $password, $access_token = null) {
    $link = connectDB();
    $query = sprintf("INSERT INTO  `foursquare`.`Users` (
            `username` ,
            `password` ,
            `access_token`
        )
        VALUES ('%s', '%s', '%s');", 
        $link->real_escape_string($user), $link->real_escape_string($password), $link->real_escape_string($access_token));
    $link->query($query);
    $id = $link->insert_id;
    closeDB($link);
    return $id;
}
function setToken($userId, $access_token) {
    $link = connectDB();
    $query = sprintf("UPDATE  `foursquare`.`Users` SET  `access_token` =  '%s' WHERE  `Users`.`id` = %s;", 
        $link->real_escape_string($access_token), $link->real_escape_string($userId));
    $link->query($query);
    closeDB($link);
}
function connectDB() {
    $server = "localhost";
    $username = "foursquareAdmin";
    $password = "fourSQ16";
    
    $link = mysqli_connect($server, $username, $password);
    if(!$link) {
        die('Cannot create link.');
    }
    return $link;
}
function closeDB($link) {
    mysqli_close($link);
}
