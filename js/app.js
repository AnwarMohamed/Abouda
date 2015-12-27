'use strict';

String.prototype.capitalizeFirstLetter = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}

angular.module('AboudaApp', [
    'ngRoute',
    'ngCookies',
    'toggle-switch',
    'ngSanitize',
    'angular-ladda',
    'ngScrollbars',
    'angular-growl',
    'base64',    
    'AboudaApp.signin',
    'AboudaApp.profile',
    'AboudaApp.home',
    'AboudaApp.search',
    'AboudaApp.version'
])

.config(['$routeProvider', function($routeProvider) {
    $routeProvider.otherwise({
        redirectTo: '/'
    });
}])

.config(function(ScrollBarsProvider) {
    ScrollBarsProvider.defaults = {
        scrollButtons: {
            scrollAmount: 'auto',
            enable: false
        },
        scrollInertia: 400,
        axis: 'y',
        theme: 'light',
        autoHideScrollbar: true
    };
})

.config(['growlProvider', function(growlProvider) {
    growlProvider.globalTimeToLive(2000);
    growlProvider.globalDisableCountDown(true);
    growlProvider.globalDisableCloseButton(true);
}])

.run(function($rootScope, $location) {
    $rootScope.location = $location;
})

.service('RestClient', 
        ['$http','$cookies','$base64','growl',
        function($http,$cookies,$base64,growl){

    var baseUrl = '/abouda/api/v1/';    
    var instance = this;

    this.sessionToken = null;    
    this.myInfo = {};

    this.unreadRequests = [];
    this.unreadNotifications = [];


    this.signinMe = function(email, password, callback) {
        
        $http.post(baseUrl + "user/me", {
            email: email, password: password
        })
        .success(function (data, status, headers, config) {             
            var id = data['result']['user_id'];
            var token = data['result']['token'];
            
            instance.sessionToken = $base64.encode(id + ':' + token);            
            $cookies.put("session_token", instance.sessionToken);            
            
            if (callback) {
                callback(false, data);            
            }
        })        
        .error(function (data, status, headers, config) {                            
            growl.error(data['msg'].capitalizeFirstLetter());

            if (callback) {
                callback(true, data);
            }
        }); 
    }

    this.signupMe = function(postData, callback) {

        $http.post(baseUrl + "user/new", postData)
        .success(function (data, status, headers, config) {        

            var id = data['result']['user_id'];
            var token = data['result']['token'];

            instance.sessionToken = $base64.encode(id + ':' + token);            
            $cookies.put("session_token", instance.sessionToken);            
            
            if (callback) {
                callback(false, data);  
            }
        })
        .error(function (data, status, headers, config) {                
            growl.error(data['msg'].capitalizeFirstLetter());

            if (callback) {
                callback(true, data);       
            }
        });   
    }

    this.getMyInfo = function (callback) {

        $http.get(baseUrl + "user/me/info", {
            headers: { 'Abouda-Token': instance.sessionToken }
        })
        .success(function (data, status, headers, config) {
            instance.myInfo = data['info'];               

            if (instance.myInfo['gender'])
                instance.myInfo['gender'] = 'male';
            else
                instance.myInfo['gender'] = 'female';

            if(!instance.myInfo['thumbnail']) {
                if (instance.myInfo['gender'] == 'male')
                    instance.myInfo['thumbnail'] = 'img/male.jpg'
                else if (instance.myInfo['gender'] == 'female')
                    instance.myInfo['thumbnail'] = 'img/female.jpg'
            }

            if (callback) {
                callback(false, instance.myInfo); 
            }
        })
        .error(function (data, status, headers, config) {
            growl.error(data['msg'].capitalizeFirstLetter());  

            if (callback) {
                callback(true, data);       
            }
        });  
    }
}])

.controller("MainCtrl", 
        ['$scope','$cookies','$location','RestClient',
        function($scope,$cookies,$location,RestClient) {    

        $scope.client = RestClient;
        $scope.client.sessionToken = $cookies.get("session_token");

        $scope.signout = function() {            
            $scope.session_token = null;
            $cookies.remove("session_token");
            $location.path('/');
        }

        $scope.isActive = function(viewLocation) {
            return viewLocation === $location.path();
        };
    }
]);

$("body").vegas({
    timer: false,
    delay: 9000,
    transitionDuration: 5000,
    transition: ['fade', 'zoomOut', 'swirlLeft'],
    overlay: '/abouda/bower_components/vegas/dist/overlays/01.png',
    slides: [{
        src: "/abouda/img/back2.jpeg"
    }, {
        src: "/abouda/img/back3.jpg"
    }, {
        src: "/abouda/img/back5.jpg"
    }, {
        src: "/abouda/img/back7.jpg"
    }, {
        src: "/abouda/img/back8.jpg"
    }, {
        src: "/abouda/img/back9.jpg"
    }]
});

$(".jaybar-menu-button").on("click", function(s) {
    $(".jaybar-menu-button").toggleClass("jaybar-menu-button-active"),
        $(".jaybar-menu").toggleClass("jaybar-menu-open")
});

$(".jaybar-menu-item")
    .add(".jaybar-buttons").on("click", function(s) {
        $(".jaybar-menu-button").removeClass("jaybar-menu-button-active"),
            $(".jaybar-menu").removeClass("jaybar-menu-open")
    });

$('[data-toggle="tooltip"]').tooltip();
