'use strict';

var redirect = location.origin + location.pathname;
var LabCtrl = angular.module('LabCtrl', []);

LabCtrl.directive('login', function($http) {
    var cookiesTemp = document.cookie.split(";");
    var cookies = Array();
    for (var index = 0; index < cookiesTemp.length; ++index) {
        var cookie = cookiesTemp[index].split("=");
        var name = cookie[0].trim();
        var value = cookie[1];
        cookies[name] = value;
    }
    $http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
    // Override $http service's default transformRequest
    $http.defaults.transformRequest = [function(data) {
            /**
             * The workhorse; converts an object to x-www-form-urlencoded serialization.
             * @param {Object} obj
             * @return {String}
             */
            var param = function(obj)
            {
                var query = '';
                var name, value, fullSubName, subName, subValue, innerObj, i;

                for (name in obj)
                {
                    value = obj[name];

                    if (value instanceof Array)
                    {
                        for (i = 0; i < value.length; ++i)
                        {
                            subValue = value[i];
                            fullSubName = name + '[' + i + ']';
                            innerObj = {};
                            innerObj[fullSubName] = subValue;
                            query += param(innerObj) + '&';
                        }
                    }
                    else if (value instanceof Object)
                    {
                        for (subName in value)
                        {
                            subValue = value[subName];
                            fullSubName = name + '[' + subName + ']';
                            innerObj = {};
                            innerObj[fullSubName] = subValue;
                            query += param(innerObj) + '&';
                        }
                    }
                    else if (value !== undefined && value !== null)
                    {
                        query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
                    }
                }

                return query.length ? query.substr(0, query.length - 1) : query;
            };

            return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
        }];
    return {
        restrict: 'E',
        templateUrl: 'tpls/login.tpl.html',
        scope: {
            ratingValue: '='
        },
        link: function(scope, element, attrs) {
            element.find('form').css('display', 'none');
            scope.username = cookies['username'];
            scope.loginBtnVal = (cookies['username']) ? "Log Out" : "Log In";
            scope.login = function() {
                $http.post('userAjax.php', {method: "login", username: this.username, password: this.password})
                    .success(function(data) {
                        if(data.valid)
                            scope.loginBtnVal = "Log Out";
                        else
                            scope.loginBtnVal = "Log In";
                        location.reload();
                    });
            };
            
            scope.clickLogin = function($event) {
                var loggedIn = (cookies['username']) ? true : false;
                console.log();
                if(loggedIn) {
                    $http.post('userAjax.php', {method: "logout"})
                        .success(function(data) {
                            scope.loginBtnVal = "Log In";
                            location.reload();
                        });
                } else {
                    angular.element($event.target).css('display', 'none');
                    element.find('form').css('display', 'inline');
                }
            };

            scope.createAccount = function() {
                console.log("Create: " + this.username + " : " + this.password);
                $http.post('userAjax.php', {method: "createAccount", username: this.username, password: this.password})
                    .success(function(data) {
                        document.location.href = "https://foursquare.com/oauth2/authenticate?client_id=EZEI4PDG0CJEMSPOKGIHC412UDBS521YZD52X3EJ0LH5RRQR&response_type=code&redirect_uri=" + redirect;
                    });
            };
        }
    };
});

function CheckInCtrl($scope, $http, $routeParams) {
    var cookiesTemp = document.cookie.split(";");
    var cookies = Array();
    for (var index = 0; index < cookiesTemp.length; ++index) {
        var cookie = cookiesTemp[index].split("=");
        var name = cookie[0].trim();
        var value = cookie[1];
        cookies[name] = value;
    }
    $scope.userid = $routeParams.userid;
    $scope.checkins = Array();
    if($routeParams.userid && cookies['userId'] !== $routeParams.userid) {
        $http.post('userAjax.php', {method: "getToken", userId: $routeParams.userid})
            .success(function(data) {
                var token = data.access_token;
                $http.get('https://api.foursquare.com/v2/users/self/checkins?limit=1&oauth_token=' + token + '&v=20140203')
                    .success(function(data) {
                        var response = data.response;
                        $scope.checkins = response.checkins.items;
                        if($scope.checkins.length < 1) {
                            $scope.loadedMsg = "No Checkins.";
                        }
                    });
            });
    } else {
        $scope.checkins = Array();
        $http.post('userAjax.php', {method: "getToken", userId: cookies['userId']})
            .success(function(data) {
                var token = data.access_token;
                $http.get('https://api.foursquare.com/v2/users/self/checkins?oauth_token=' + token + '&v=20140203')
                    .success(function(data) {
                        var response = data.response;
                        $scope.checkins = response.checkins.items;
                        if($scope.checkins.length < 1) {
                            $scope.loadedMsg = "No Checkins.";
                        }
                    });
            });
    }
}
LabCtrl.controller('CheckInCtrl', ['$scope', '$http', '$routeParams', CheckInCtrl]);

function UsersCtrl($scope, $http) {
    
}
LabCtrl.controller('UsersCtrl', ['$scope', '$http', UsersCtrl]);