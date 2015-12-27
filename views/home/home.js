'use strict';

angular.module('AboudaApp.home', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
    $routeProvider
    .when('/home', {
        templateUrl: 'views/home/home.html',
        controller: 'HomeCtrl'
    });
}])

.controller('HomeCtrl', 
        ['$cookies','$location','$scope','growl','RestClient',
        function($cookies,$location,$scope,growl,RestClient) {

    if (!$cookies.get("session_token")) {
        return $location.path('/');
    } 

    $scope.client = RestClient;
    $scope.client.sessionToken = $cookies.get("session_token");

    $scope.postSpinnerLabel = 'Post';
    $scope.postSpinner = false;   


    $scope.postPrivacy = [
        {title:' Public ', icon:'glyphicon glyphicon-globe', value:0},
        {title:' Private ', icon:'glyphicon glyphicon-lock', value:1}
    ];

    $scope.selectedPostPrivacy = $scope.postPrivacy[0];
    $scope.client.getMyInfo(null);

    $scope.switchPostPrivacy = function(item) {
        $scope.selectedPostPrivacy = item;
    }

}]);