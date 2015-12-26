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
    

    $("body").vegas({
        timer: false,
        delay: 9000,
        transitionDuration: 5000,
        transition: [ 'fade', 'zoomOut', 'swirlLeft' ],
        overlay: '/abouda/bower_components/vegas/dist/overlays/01.png',
        slides: [        
            { src: "/abouda/img/back2.jpeg" },
            { src: "/abouda/img/back3.jpg" },        
            { src: "/abouda/img/back5.jpg" },
            { src: "/abouda/img/back6.jpg" },
            { src: "/abouda/img/back7.jpg" },
            { src: "/abouda/img/back8.jpg" },
            { src: "/abouda/img/back9.jpg" }
        ]
    });

    $scope.signin = function() {
        var data = { 
            email: $scope.signinEmail, 
            password: $scope.signinPassword 
        };        

        $scope.signingin = true;

        $http.post($scope.baseUrl + "user/me", data)
        .success(function (data, status, headers, config) { 
            $scope.signingin = false;

            var id = data['result']['id'];
            var token = data['result']['token'];
            var session_token = $base64.encode(id + ':' + token);

            $cookies.put("session_token", session_token);
            $("body").vegas('destroy');
            $location.path('/home');
        })
        .error(function (data, status, headers, config) {                
            $scope.signingin = false; 
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

        $scope.signingup = true;

        $http.post($scope.baseUrl + "user/new", data)
        .success(function (data, status, headers, config) { 
            $scope.signingup = false;

            var id = data['result']['id'];
            var token = data['result']['token'];
            var session_token = $base64.encode(id + ':' + token);

            $cookies.put("session_token", session_token);
            $("body").vegas('destroy');
            $location.path('/home');
        })
        .error(function (data, status, headers, config) {                
            $scope.signingup = false; 
            growl.error(data['msg'].capitalizeFirstLetter());        
        }); 

    };  

}]);