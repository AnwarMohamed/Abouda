'use strict';

angular.module('AboudaApp.home', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
    $routeProvider
        .when('/home', {
            templateUrl: 'views/home/home.html',
            controller: 'HomeCtrl'
        });
}])

.controller('HomeCtrl', ['$cookies', '$location', '$scope', 'growl', 'RestClient',
    function($cookies, $location, $scope, growl, RestClient) {

        if (!$cookies.get("session_token")) {
            return $location.path('/');
        }

        $scope.client = RestClient;
        $scope.client.sessionToken = $cookies.get("session_token");

        $scope.postSpinnerLabel = 'Post';
        $scope.postSpinner = false;

        $scope.postTextInput = '';
        $scope.postPictureInput = null;

        $scope.postPrivacy = [{
            title: ' Public ',
            icon: 'glyphicon glyphicon-globe',
            value: 1
        }, {
            title: ' Private ',
            icon: 'glyphicon glyphicon-lock',
            value: 0
        }];

        $scope.selectedPostPrivacy = $scope.postPrivacy[0];

        $scope.client.getMyInfo(null);
        $scope.client.getMyRequests(null);
        $scope.client.getMyFriends(null);
        $scope.client.getHome(null);

        $scope.embedPostOptions = {
            code: {
                highlight: false,                
            },
            tweetEmbed: false,
        }

        $scope.switchPostPrivacy = function(item) {
            $scope.selectedPostPrivacy = item;
        }

        $scope.like = function(postId, index) {
            if ($scope.client.homePosts[index]['liked']) {

                $scope.client.dislikePost(postId, 
                    function(error, result) {

                    if (!error) {
                        $scope.client.homePosts[index]['liked'] = 0;
                        $scope.client.homePosts[index]['likes_count'] -= 1;
                    }
                });

            } else {

                $scope.client.likePost(postId, 
                    function(error, result) {

                    if (!error) {
                        $scope.client.homePosts[index]['liked'] = 1;
                        $scope.client.homePosts[index]['likes_count'] += 1;
                    }
                });
            }
        }

        $scope.post = function() {
            if ($scope.postTextInput.trim().length == 0) {
                return growl.error('Post content is empty!');
            }

            if (!$scope.postPictureInput) {
                $scope.postPictureInput = {};
            }

            $scope.postSpinnerLabel = 'Posting';
            $scope.postSpinner = true;

            $scope.client.postMe({
                text: $scope.postTextInput,
                privacy: $scope.selectedPostPrivacy['title'].toLowerCase().trim(),
                picture: $scope.postPictureInput
            }, function(error, result) {

                $scope.postSpinnerLabel = 'Post';
                $scope.postSpinner = false;

                if (!error) {
                    $scope.client.getHome(null);
                    $scope.postTextInput = '';
                    $scope.postPictureInput = null;
                }
            });
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
