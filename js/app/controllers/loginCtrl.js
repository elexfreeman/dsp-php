/**
 * Created by elex on 25.12.2016.
 */

/* Contact Controller */
dspApp.controller('loginCtrl',[
    '$scope','$http', '$location', '$routeParams',
    function($scope, $http, $timeout, $routeParams) {

        $("html, body").animate({scrollTop: 0}, 100);
        /*проверяем на авторизацию*/
        $http.get(api_url + "dsp_auth/isAuth")
            .then(function(response) {


            });

    }
]);