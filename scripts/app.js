'use strict';

var LabApp = angular.module('LabApp', [
    'ngRoute', 
    'LabCtrl'
]);

LabApp.config(['$routeProvider', '$locationProvider', function($routeProvider, $locationProvider) {
//    $locationProvider.html5Mode(true);
    $routeProvider.when('/user', {
        templateUrl: 'tpls/user.tpl.html',
        controller: 'CheckInCtrl'
    })
    .when('/user/:userid', {
        templateUrl: 'tpls/user.tpl.html',
        controller: 'CheckInCtrl'
    })
    .when('/all', {
        templateUrl: 'tpls/user.tpl.php'
    })
    .otherwise({
        redirectTo: '/user'
    });
}]);