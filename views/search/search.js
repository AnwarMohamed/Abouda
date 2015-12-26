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
		['$cookies','$location','$scope', 
		function($cookies,$location,$scope) {

    if (!$cookies.get("session_token")) {
        return $location.path('/');
    }  

    $scope.searchSpinnerLabel = 'Search'; 

    $scope.search = function() {
    	$scope.searchSpinner = true;
    	$scope.searchSpinnerLabel = 'Searching';
    }
}]);