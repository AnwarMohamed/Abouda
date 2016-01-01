'use strict';

angular.module('AboudaApp.profile', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
    $routeProvider        
        .when('/profile/:id', {
            templateUrl: 'views/profile/profile.html',
            controller: 'ProfileCtrl'
        })
        .when('/profile/me', {
            templateUrl: 'views/profile/profile.html',
            controller: 'ProfileCtrl'
        });
}])

.controller('ProfileCtrl', ['$cookies', '$location', '$scope', 'growl', 'RestClient', '$routeParams',
    function($cookies, $location, $scope, growl, RestClient, $routeParams) {

        if (!$cookies.get("session_token")) {
            return $location.path('/');
        }

        $scope.profilePictureInput = null;

        $scope.client = RestClient;
        $scope.client.sessionToken = $cookies.get("session_token");

        $scope.client.profileInfo = {};

        $scope.client.getMyInfo(
            function(error, data) {
                if (!error) {        
                    if ($routeParams.id == $scope.client.myInfo['user_id']) {
                        return $location.path('/profile/me');
                    }
                }
        });
    
        $scope.client.getMyRequests(null);
        $scope.client.getMyFriends(null);

        if ($scope.isActive('/profile/me')) {
            $scope.client.profileInfo = $scope.client.myInfo;
            $scope.client.getMyPosts(null);    
        } else {
            $scope.client.getProfileInfo($routeParams.id, 
                function(error, data) {
                    if (error) {
                        growl.error('Error fetching profile');
                        return $location.path('/home');
                    }
            });

            $scope.client.getProfilePosts($routeParams.id, 
                function(error, data) {
                    if (error) {
                        growl.error('Error fetching profile');
                        return $location.path('/home');
                    }
            });
        }

        $scope.postProfilePicture = function() {
            $scope.client.postProfilePicture(
                $scope.profilePictureInput, 
                function(error, data) {
                    if (!error) {
                        return $location.path('/profile/' + $scope.client.myInfo['user_id']);
                    }
            });            
        }

        $scope.embedPostOptions = {
            code: {
                highlight: false,
            },
            tweetEmbed: false,
        }

        $scope.add = function(profileId) {
            $scope.client.addFriend(profileId, 
                function(error, result) {

                if (!error) {
                    $scope.client.getProfileInfo($routeParams.id);
                }
            });
        }

        $scope.accept = function(profileId) {
            $scope.client.acceptFriend(profileId, 
                function(error, result) {

                if (!error) {
                    $scope.client.getMyRequests(null);
                    $scope.client.getProfileInfo($routeParams.id);
                }
            });
        }

        $scope.delete = function(profileId) {
            $scope.client.deleteFriend(profileId, 
                function(error, result) {

                if (!error) {
                    $scope.client.getMyRequests(null);
                    $scope.client.getProfileInfo($routeParams.id);
                }
            });
        }

        $scope.like = function(postId, index) {
            if ($scope.client.profilePosts[index]['liked']) {

                $scope.client.dislikePost(postId, 
                    function(error, result) {

                    if (!error) {
                        $scope.client.profilePosts[index]['liked'] = 0;
                        $scope.client.profilePosts[index]['likes_count'] -= 1;
                    }
                });

            } else {

                $scope.client.likePost(postId, 
                    function(error, result) {

                    if (!error) {
                        $scope.client.profilePosts[index]['liked'] = 1;
                        $scope.client.profilePosts[index]['likes_count'] += 1;
                    }
                });
            }
        }

        $scope.likes = function(postId) {
            $scope.client.likes(postId, 
                function(error, result) {

                if (!error) {                    
                    $('#aboudaPostLikesModal').modal('show');
                } else {
                    growl.error('Error fetching post likes');
                }
           });
        }        
    }
]);
