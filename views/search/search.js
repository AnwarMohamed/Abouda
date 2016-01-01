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
        ['$cookies', '$location', '$scope', 'growl', 'RestClient',
        function($cookies, $location, $scope, growl, RestClient) {

    $scope.client = RestClient;
    $scope.client.sessionToken = $cookies.get("session_token");
    
    if (!$scope.client.sessionToken) {
        return $location.path('/');
    } 

    $scope.client.getMyInfo(null);
    $scope.client.getMyRequests(null);
    $scope.client.getMyFriends(null);

    $scope.searchTextInput = '';
    $scope.client.searchResults = [];

    $scope.searchSpinnerLabel = 'Search';
    $scope.searchSpinner = false;

    $scope.searchTypes = [{
        title: ' Full Name ',
        flag: 'fullname'
    }, {
        title: ' Email Address ',
        flag: 'email'
    }, {
        title: ' Mobile Phone ',
        flag: 'mobile'
    }, {
        title: ' Post ',
        flag: 'post'
    }, {
        title: ' Hometown ',
        flag: 'hometown'        
    }];

    $scope.selectedSearchType = $scope.searchTypes[0];

    $scope.switchSearchType = function(item) {
        $scope.selectedSearchType = item;
    }    

    $scope.search = function() {

        if ($scope.searchTextInput.trim().length == 0) {
            return growl.error('Search content is empty!');
        }

        $scope.searchSpinner = true;
        $scope.searchSpinnerLabel = 'Searching';

        $scope.client.searchResults = [];

        $scope.client.search(
            $scope.searchTextInput,
            $scope.selectedSearchType['flag'], 
            function(error, data) {
                if (error) {
                    growl.error('Error searching for your query');
                    //return $location.path('/home');
                } else {

                    if (!$scope.client.searchResults.length) {
                        growl.error('No results found');
                    }
                }

                $scope.searchSpinner = false;
                $scope.searchSpinnerLabel = 'Search';                
        });        
    }
}]);