'use strict';

angular.module('AboudaApp.search', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
    $routeProvider
    .when('/search', {
        templateUrl: 'views/search/search.html',
        controller: 'SearchCtrl'
    });
}])

.controller('SearchCtrl', 
		['$cookies','$location','$scope','$rootScope', 
		function($cookies,$location,$scope,$rootScope) {

    $scope.session_token = $cookies.get("session_token");
    
    if (!$scope.session_token) {
        return $location.path('/');
    } 

    $scope.searchSpinnerLabel = 'Search';
    $scope.searchSpinner = false;

    $scope.search = function() {
    	$scope.searchSpinner = true;
    	$scope.searchSpinnerLabel = 'Searching';
    }
}]);