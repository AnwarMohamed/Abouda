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

    $scope.client = RestClient;
    $scope.signupSpinnerLabel = 'Sign up';
    $scope.signinSpinnerLabel = 'Sign in';

    $scope.verifyEmail = function(email) {        
        var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i; 
        return email.match(emailRegEx);        
    }

    $scope.signin = function() {
        
        if (!$scope.verifyEmail($scope.signinEmail)) {
            return growl.error('Invalid or empty email');
        } else if ($scope.signinPassword.trim().length == 0) {
            return growl.error('Invalid or empty password');
        }

        $scope.signinSpinner = true;
        $scope.signinSpinnerLabel = 'Signing in';

        $scope.client.signinMe(
            $scope.signinEmail, $scope.signinPassword, 
            function (error, result) {

            $scope.signinSpinner = false;
            $scope.signinSpinnerLabel = 'Sign in';

            if (!error) {
                $location.path('/home');
            }
        });
    };

    $scope.signup = function(modal) { 

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

        $scope.client.signupMe(data, 
            function (error, result) {
            
            $scope.signupSpinner = false;     
            $scope.signupSpinnerLabel = 'Sign up';

            if (!error) {
                $('#aboudaSignupModal').modal('hide');
                growl.success('Sign up completed! Sign in now');
            }
        });
    };  

}]);