'use strict';

angular.module('AboudaApp.signin', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/', {
        templateUrl: 'views/signin/signin.html',
        controller: 'SigninCtrl'
    });
}])

.controller('SigninCtrl', [
        '$scope','growl','$cookies','$location','$rootScope','RestClient',
        function($scope,growl,$cookies,$location,$rootScope,RestClient) {
    
    if ($cookies.get("session_token")) {
        return $location.path('/home');
    }

    $scope.signupSpinnerLabel = 'Sign up';
    $scope.signinSpinnerLabel = 'Sign in';

    $scope.signin = function() {

        $scope.signinSpinner = true;
        $scope.signinSpinnerLabel = 'Signing in';

        RestClient.signinMe(
            $scope.signinEmail, $scope.signinPassword, 
            function (error, result) {

            $scope.signinSpinner = false;
            $scope.signinSpinnerLabel = 'Sign in';

            if (!error) {
                $location.path('/home');
            }
        });
    };

    $scope.signup = function() { 

        var data = { 
            fname: $scope.signupFname,
            lname: $scope.signupLname,
            gender: 
                ($scope.signupGender ? 'female':'male'),
            birthdate: $('#signupBirthdate').data('date'),
            email: $scope.signupEmail, 
            password: $scope.signupPassword 
        };        

        $scope.signupSpinner = true;
        $scope.signupSpinnerLabel = 'Signing up';

        RestClient.signupMe(data, 
            function (error, result) {
            
            $scope.signupSpinner = false;     
            $scope.signupSpinnerLabel = 'Sign up';

            if (!error) {
                $('#abouda-signup-modal').modal('hide');
                $location.path('/home');
            }
        });
    };  

}]);