'use strict';

angular.module('myApp.view2', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/view2', {
    templateUrl: 'view2/view2.html',
    controller: 'View2Ctrl'
  });
}])

.controller('View2Ctrl', ['$cookies', function($cookies) {
	//$cookies.put("session_token", "abouda");
	//$cookies.remove("session_token");
	console.log("log view2");
}]);