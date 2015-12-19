'use strict';

angular.module('AboudaApp.profile', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider
  .when('/profile/', {
    templateUrl: 'views/profile/profile.html',
    controller: 'ProfileCtrl'
  })
  .when('/profile/:id', {
    templateUrl: 'views/profile/profile.html',
    controller: 'ProfileCtrl'
  });  
}])

.controller('ProfileCtrl', ['$cookies', function($cookies) {
	//$cookies.put("session_token", "abouda");
	//$cookies.remove("session_token");
	// /console.log("log view2");
}]);