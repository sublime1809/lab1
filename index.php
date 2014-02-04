<?php
    require_once("FoursquareAPI.class.php");
    
    $foursquare = new FoursquareAPI();
    if(array_key_exists("code",$_GET) && array_key_exists("userId", $_COOKIE)){
        require_once("userAjax.php");
        
        $redirect_uri = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $token = $foursquare->GetToken($_GET['code'],$redirect_uri);
        setToken($_COOKIE['userId'], $token);
        $_COOKIE['access_token'] = $token;
    }
?><!DOCTYPE html>
<html ng-app="LabApp">
    <head>
        <meta charset="UTF-8">
        <title></title>
        <script src="scripts/lib/angular/angular.js"></script>
        <script src="scripts/lib/angular/angular-route.js"></script>
        <script src="scripts/app.js"></script>
        <script src="scripts/controllers.js"></script>
    </head>
    <body>
        
        <a href="#/all">View All Users</a>
        <login></login>
        <div ng-view></div>
        
    </body>
</html>
