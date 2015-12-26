'use strict';


angular.module('AboudaApp', [
  'ngRoute',
  'ngCookies',
  'toggle-switch',
  'ngSanitize',
  'angular-ladda',
  'angular-growl',
  'base64',
  'AboudaApp.signin',
  'AboudaApp.profile',
  'AboudaApp.home',
  'AboudaApp.version'
])

.config(['$routeProvider', function($routeProvider) {
    $routeProvider.otherwise({redirectTo: '/'});
}])

.config(['growlProvider', function(growlProvider) {
    growlProvider.globalTimeToLive(2000);
    growlProvider.globalDisableCountDown(true);
    growlProvider.globalDisableCloseButton(true);
}])

.controller("MainCtrl", ['$scope', '$cookies', '$location', 
        function($scope, $cookies, $location) {
        
    $scope.baseUrl = '/abouda/api/v1/';

    $scope.isActive = function(viewLocation) {
        return viewLocation === $location.path();
    };

    $scope.range = function(min, max, step) {
        step = step || 1;
        var input = [];
        for (var i = min; i <= max; i += step) {
            input.push(i);
        }
        return input;
    };
}]);

String.prototype.capitalizeFirstLetter = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}