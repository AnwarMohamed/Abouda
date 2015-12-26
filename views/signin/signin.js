'use strict';

angular.module('AboudaApp.signin', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/', {
        templateUrl: 'views/signin/signin.html',
        controller: 'SigninCtrl'
    });
}])

.controller('SigninCtrl', [
        '$scope', '$http', 'growl', '$base64', '$cookies', '$location',
        function($scope, $http, growl, $base64, $cookies, $location) {
    
    if ($cookies.get("session_token")) {
        return $location.path('/home');
    }

    $scope.signupSpinnerLabel = 'Sign up';
    $scope.signinSpinnerLabel = 'Sign in';

    $scope.signin = function() {
        var data = { 
            email: $scope.signinEmail, 
            password: $scope.signinPassword 
        };        

        $scope.signinSpinner = true;
        $scope.signinSpinnerLabel = 'Signing in';

        $http.post($scope.baseUrl + "user/me", data)
        .success(function (data, status, headers, config) { 
            
            $scope.signinSpinner = false;
            $scope.signinSpinnerLabel = 'Sign in';

            var id = data['result']['id'];
            var token = data['result']['token'];
            var session_token = $base64.encode(id + ':' + token);

            $cookies.put("session_token", session_token);            
            $location.path('/home');
        })
        .error(function (data, status, headers, config) {                
            
            $scope.signinSpinner = false; 
            $scope.signinSpinnerLabel = 'Sign in';

            growl.error(data['msg'].capitalizeFirstLetter());        
        }); 
    };

    $scope.signup = function() {        
        var data = { 
            fname: $scope.signupFname,
            lname: $scope.signupLname,
            gender: ($scope.signupGender ? 'female':'male'),
            birthdate: $scope.signupByear + '-' + ("0" + $scope.signupBmonth).slice(-2) + '-' + ("0" + $scope.signupBday).slice(-2),
            email: $scope.signupEmail, 
            password: $scope.signupPassword 
        };        

        $scope.signupSpinner = true;
        $scope.signupSpinnerLabel = 'Signing up';

        $http.post($scope.baseUrl + "user/new", data)
        .success(function (data, status, headers, config) { 
            
            $scope.signupSpinnerLabel = 'Sign up';
            $scope.signupSpinner = false;

            var id = data['result']['id'];
            var token = data['result']['token'];
            var session_token = $base64.encode(id + ':' + token);

            $cookies.put("session_token", session_token);            
            $location.path('/home');
        })
        .error(function (data, status, headers, config) {                
            
            $scope.signupSpinner = false; 
            $scope.signupSpinnerLabel = 'Sign up';
            
            growl.error(data['msg'].capitalizeFirstLetter());        
        }); 

    };  

}]);