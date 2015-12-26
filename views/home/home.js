'use strict';

angular.module('AboudaApp.home', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
    $routeProvider
    .when('/home', {
        templateUrl: 'views/home/home.html',
        controller: 'HomeCtrl'
    });
}])

.controller('HomeCtrl', ['$cookies', '$location', function($cookies, $location) {
    if (!$cookies.get("session_token")) {
        return $location.path('/');
    }    

}]);