'use strict';

angular.module('AboudaApp', [
    'ngRoute',
    'ngCookies',
    'toggle-switch',
    'ngSanitize',
    'angular-ladda',
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
.config(['growlProvider', function(growlProvider) {
    growlProvider.globalTimeToLive(2000);
    growlProvider.globalDisableCountDown(true);
    growlProvider.globalDisableCloseButton(true);
}])
.run(function($rootScope, $location) {
    $rootScope.location = $location;
})
.controller("MainCtrl", ['$scope', '$cookies', '$location',
    function($scope, $cookies, $location) {

        $scope.baseUrl = '/abouda/api/v1/';

        $scope.signout = function() {
            $cookies.remove("session_token");
            $location.path('/');
        }

        $scope.isActive = function(viewLocation) {
            return viewLocation === $location.path();
        };

        $scope.range = function(min, max, step) {
            step = step || 1;
            var input = [];
            for (var i = min; i <= max; i += step) {
                input.push(i);
            }
            return input;
        };
    }
]);

String.prototype.capitalizeFirstLetter = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}


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
