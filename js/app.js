'use strict';

function toTitleCase(str) {
    if (!str) 
        return '';

    return str.replace(/\w\S*/g, function(txt) {
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
}

angular.module('AboudaApp', [
    'ngRoute',
    'ngCookies',    
    'toggle-switch',
    'ngSanitize',
    'angular-ladda',
    'pusher-angular',
    'ngEmbed',
    'ngScrollbars',
    'angularMoment',
    'angular-growl',
    'base64',    
    'ng-file-model',
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
        scrollInertia: 200,
        axis: 'y',
        theme: 'minimal',
        autoHideScrollbar: true,
        scrollbarPosition: "outside"
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
    this.profileInfo = {};

    this.searchResults  = [];

    this.homePosts = [];
    this.myPosts = [];
    this.profilePosts = [];

    this.unreadRequests = [];
    this.myRequests = [];
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
            if (data) {                  
                growl.error(toTitleCase(data['msg']));
            }            

            if (callback) {
                callback(true, data);
            }
        }); 
    }

    this.signupMe = function(postData, callback) {

        $http.post(baseUrl + "user/new", postData)
        .success(function (data, status, headers, config) {        

            if (callback) {
                callback(false, data);  
            }
        })
        .error(function (data, status, headers, config) {                
            if (data) {                     
                growl.error(toTitleCase(data['msg']));
            }            

            if (callback) {
                callback(true, data);       
            }
        });   
    }

    this.addFriend = function(profileId, callback) {

        $http.post(baseUrl + "user/"+ profileId +"/request", null, {
            headers: { 'Abouda-Token': instance.sessionToken }
        })
        .success(function (data, status, headers, config) {        

            if (callback) {
                callback(false, data);  
            }
        })
        .error(function (data, status, headers, config) {                
            if (data) {                     
                growl.error(toTitleCase(data['msg']));
            }            

            if (callback) {
                callback(true, data);       
            }
        });   
    }

    this.acceptFriend = function(profileId, callback) {

        $http.post(baseUrl + "user/"+ profileId +"/accept", null, {
            headers: { 'Abouda-Token': instance.sessionToken }
        })
        .success(function (data, status, headers, config) {        

            if (callback) {
                callback(false, data);  
            }
        })
        .error(function (data, status, headers, config) {                
            if (data) {                     
                growl.error(toTitleCase(data['msg']));
            }            

            if (callback) {
                callback(true, data);       
            }
        });   
    }

    this.deleteFriend = function(profileId, callback) {

        $http.delete(baseUrl + "user/"+ profileId +"/request", {
            headers: { 'Abouda-Token': instance.sessionToken }
        })
        .success(function (data, status, headers, config) {        

            if (callback) {
                callback(false, data);  
            }
        })
        .error(function (data, status, headers, config) {                
            if (data) {                     
                growl.error(toTitleCase(data['msg']));
            }            

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

            if (callback) {
                callback(false, instance.myInfo); 
            }
        })
        .error(function (data, status, headers, config) {
            if (data) {                   
                growl.error(toTitleCase(data['msg']));
            }            

            if (callback) {
                callback(true, data);       
            }
        });  
    }

    this.getProfileInfo = function (profileId, callback) {

        $http.get(baseUrl + "user/"+ profileId +"/info", {
            headers: { 'Abouda-Token': instance.sessionToken }
        })
        .success(function (data, status, headers, config) {
            instance.profileInfo = data['info'];               

            if (instance.profileInfo['gender'])
                instance.profileInfo['gender'] = 'male';
            else
                instance.profileInfo['gender'] = 'female';

            if (callback) {
                callback(false, instance.profileInfo); 
            }
        })
        .error(function (data, status, headers, config) {
            if (data) {                   
                growl.error(toTitleCase(data['msg']));
            }            

            if (callback) {
                callback(true, data);       
            }
        });  
    }

    this.postMe = function(postData, callback) {

        $http.post(baseUrl + "post/", postData, {
            headers: { 'Abouda-Token': instance.sessionToken }
        })
        .success(function (data, status, headers, config) {        

            if (callback) {
                callback(false, data);  
            }
        })
        .error(function (data, status, headers, config) {                
            if (data) {                    
                growl.error(toTitleCase(data['msg']));
            }            

            if (callback) {
                callback(true, data);       
            }
        });   
    }   

    this.postProfilePicture = function(postData, callback) {

        $http.post(baseUrl + "user/me/picture", postData, {
            headers: { 'Abouda-Token': instance.sessionToken }
        })
        .success(function (data, status, headers, config) {        

            if (callback) {
                callback(false, data);  
            }
        })
        .error(function (data, status, headers, config) {                
            if (data) {                    
                growl.error(toTitleCase(data['msg']));
            }            

            if (callback) {
                callback(true, data);       
            }
        });   
    }   


    this.getHome = function(callback) {

        $http.get(baseUrl + "post/", {
            headers: { 'Abouda-Token': instance.sessionToken }
        })
        .success(function (data, status, headers, config) {        

            instance.homePosts = data['posts'];            

            if (callback) {
                callback(false, instance.homePosts);  
            }
        })
        .error(function (data, status, headers, config) {
            
            if (data) {                
                growl.error(toTitleCase(data['msg']));
            }

            if (callback) {
                callback(true, data);       
            }
        });   
    }        

    this.likePost = function(postId, callback) {

        $http.put(baseUrl + "post/" + postId + "/like", null, {
            headers: { 'Abouda-Token': instance.sessionToken }
        })
        .success(function (data, status, headers, config) {        
            if (callback) {
                callback(false, data);  
            }
        })
        .error(function (data, status, headers, config) {
            
            if (data) {                
                growl.error(toTitleCase(data['msg']));
            }

            if (callback) {
                callback(true, data);       
            }
        });   
    }   

    this.likes = function (postId, callback) {

        $http.get(baseUrl + "post/" + postId + "/like", {
            headers: { 'Abouda-Token': instance.sessionToken }
        })
        .success(function (data, status, headers, config) {
            instance.postLikes = data['likes'];               

            if (callback) {
                callback(false, instance.myInfo); 
            }
        })
        .error(function (data, status, headers, config) {
            if (data) {                   
                growl.error(toTitleCase(data['msg']));
            }            

            if (callback) {
                callback(true, data);       
            }
        });  
    }

    this.dislikePost = function(postId, callback) {

        $http.delete(baseUrl + "post/" + postId + "/like", {
            headers: { 'Abouda-Token': instance.sessionToken }
        })
        .success(function (data, status, headers, config) {        
            if (callback) {
                callback(false, data);  
            }
        })
        .error(function (data, status, headers, config) {
            
            if (data) {                
                growl.error(toTitleCase(data['msg']));
            }

            if (callback) {
                callback(true, data);       
            }
        });   
    }     


    this.getMyPosts = function(callback) {

        $http.get(baseUrl + "user/me/posts", {
            headers: { 'Abouda-Token': instance.sessionToken }
        })
        .success(function (data, status, headers, config) {        

            instance.myPosts = data['posts'];   
            instance.profilePosts = data['posts'];         

            if (callback) {
                callback(false, instance.homePosts);  
            }
        })
        .error(function (data, status, headers, config) {
            
            if (data) {                
                growl.error(toTitleCase(data['msg']));
            }

            if (callback) {
                callback(true, data);       
            }
        });   
    }     

    this.getProfilePosts = function(profileId, callback) {

        $http.get(baseUrl + "user/"+profileId+"/posts", {
            headers: { 'Abouda-Token': instance.sessionToken }
        })
        .success(function (data, status, headers, config) {        
            
            instance.profilePosts = data['posts'];

            if (callback) {
                callback(false, instance.homePosts);  
            }
        })
        .error(function (data, status, headers, config) {
            
            if (data) {                
                growl.error(toTitleCase(data['msg']));
            }

            if (callback) {
                callback(true, data);       
            }
        });   
    } 

    this.getMyFriends = function(callback) {

        $http.get(baseUrl + "user/me/friends", {
            headers: { 'Abouda-Token': instance.sessionToken }
        })
        .success(function (data, status, headers, config) {        
            
            instance.myInfo['friends'] = data['friends'];

            if (callback) {
                callback(false, instance.homePosts);  
            }
        })
        .error(function (data, status, headers, config) {
            
            if (data) {                
                growl.error(toTitleCase(data['msg']));
            }

            if (callback) {
                callback(true, data);       
            }
        });   
    }     

    this.checkRequest = function(profileId) {   
            var i;
            for (i=0; i<instance.myRequests.length; i++) {
                if (instance.myRequests[i]['id'] == profileId) {                    
                    return true;
                }
            }

            return false;
        }

    this.getMyRequests = function(callback) {

        $http.get(baseUrl + "/user/me/friends/requests", {
            headers: { 'Abouda-Token': instance.sessionToken }
        })
        .success(function (data, status, headers, config) {        
            
            instance.myRequests = data['requests'];
            instance.unreadRequests = data['requests'];

            if (callback) {
                callback(false, instance.homePosts);  
            }
        })
        .error(function (data, status, headers, config) {
            
            if (data) {                
                growl.error(toTitleCase(data['msg']));
            }

            if (callback) {
                callback(true, data);       
            }
        });   
    } 

    this.search = function(input, type, callback) {
        
        $http.post(baseUrl + "search", {
            input: input,
            type: type
        },
        {
            headers: { 'Abouda-Token': instance.sessionToken }
        })
        .success(function (data, status, headers, config) {        
            
            instance.searchResults = data['results'];            

            if (callback) {
                callback(false, instance.homePosts);  
            }
        })
        .error(function (data, status, headers, config) {
            
            if (data) {                
                growl.error(toTitleCase(data['msg']));
            }

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
        src: "/abouda/img/back10.jpg"        
    }, {
        src: "/abouda/img/back11.jpg"        
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
$("[data-dismiss=modal]").trigger({ type: "click" });
