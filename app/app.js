'use strict';

// Declare app level module which depends on views, and components
angular.module('AboudaApp', [
  'ngRoute',
  'ngCookies',
  'myApp.view1',
  'myApp.view2',
  'myApp.version'
])
.config(['$routeProvider', function($routeProvider) {
  $routeProvider.otherwise({redirectTo: '/view1'});
}])

.controller("MainController", ['$cookies', function($cookies) {
	console.log($cookies.get("session_token"));

	if ($cookies.get("session_token")) {
		console.log("logged in");

	} else {
		console.log("not logged");
	}
}]);
