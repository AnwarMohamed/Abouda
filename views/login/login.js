'use strict';

angular.module('AboudaApp.login', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
	$routeProvider.when('/login', {
		templateUrl: 'views/login/login.html',
		controller: 'LoginCtrl'
	});
}])

.controller('LoginCtrl', [function() {

}]);

