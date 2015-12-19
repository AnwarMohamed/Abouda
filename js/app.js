'use strict';


angular.module('AboudaApp', [
  'ngRoute',
  'ngCookies',
  'AboudaApp.login',
  'AboudaApp.profile',
  'AboudaApp.version'
])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.otherwise({redirectTo: '/login'});
}])

.controller("MainCtrl", ['$cookies', function($cookies) {
	console.log($cookies.get("session_token"));

	if ($cookies.get("session_token")) {
		console.log("logged in");

	} else {
		console.log("not logged");
	}
}]);
