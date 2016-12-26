/**
 * Created by elex on 25.12.2016.
 */


/* Contact Controller */
dspApp.controller('mainCtrl',[
    '$rootScope','$scope','$http', '$location', '$routeParams',
    function($rootScope, $scope, $http, $location, $routeParam ) {



        $("html, body").animate({scrollTop: 0}, 100);

        $scope.Logout = function() {
            $http.get(api_url + "dsp_auth/logout")
                .then(function(response) {
                        $location.path(base_url + 'login');
                });
        };

        $scope.toLink = function(link) {
            $location.path(base_url + link);
        }



    }
]);