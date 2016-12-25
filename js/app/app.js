'use strict';
var api_url = '/dsp/api/';
var views_url = '/dsp/js/app/views/';
var base_url = '/dsp/';
var controllers_url = '/dsp/js/app/controllers/';
/* App Module */

var dspApp = angular.module('dspApp', ['ngAnimate','ngResource','ngSanitize','ngRoute', 'httpPostFix']);

dspApp.config([
    '$routeProvider', '$locationProvider',
    function($routeProvide, $locationProvider){

        /*вклучаем урлы без #*/
        $locationProvider.html5Mode({
            enabled: true,
            requireBase: false
        });

        /*маршруты с контролеррами и view*/
        $routeProvide
            /*главная страница*/
            .when('/dsp/',{
                templateUrl:views_url + 'main/index.html',
                controller:'mainCtrl'
            })
            /*личный кабинет*/
            .when('/dsp/login',{
                templateUrl:views_url + 'login/index.html',
                controller:'loginCtrl'
            })
            /*другая страница*/
            /*todo 303 redirect*/
            .otherwise({
                redirectTo: '/dsp/'
            });
    }
]);


/*загружаем в память шаблоны*/
dspApp.run(function($rootScope,$templateCache, $http) {
    /*Заголовок страницы*/

    /*загружаем шаблоны в память чтобы потом летало*/
    $http.get(views_url + 'main/index.html')
        .then(function(response) {
            console.info(response);
            $templateCache.put(views_url + 'main/index.html', response.data);
        });

    $http.get(views_url + 'login/index.html')
        .then(function(response) {
            console.info(response);
            $templateCache.put(views_url + 'login/index.html', response.data);
        });

    $rootScope.exit_link = base_url + 'api/dsp_auth/logout';

});

