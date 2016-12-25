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

        $scope.initBt = function(params) {
            $scope.bsTable = {
                options: {
                    //data: rows,
                    ajax:$scope.GetPatients,
                    rowStyle: function (row, index) {
                        return { classes: 'none' };
                    },
                    cache: false,
                    height: 600,
                    striped: true,
                    pagination: true,
                    pageSize: 30,
                    pageList: [5, 10, 25, 50, 100, 200],
                    search: true,
                    sidePagination:'server',
                    showColumns: true,
                    showRefresh: false,
                    minimumCountColumns: 2,
                    clickToSelect: false,
                    showToggle: true,
                    locale: 'ru-RU',
                    maintainSelected: true,
                    columns: [
                        {
                        field: 'SURNAME',
                        title: 'SURNAME',
                        align: 'center',
                        valign: 'bottom',
                        sortable: true
                    }, {
                        field: 'NAME',
                        title: 'NAME',
                        align: 'center',
                        valign: 'middle',
                        sortable: true
                    }, {
                        field: 'SECNAME',
                        title: 'SECNAME',
                        align: 'left',
                        valign: 'top',
                        sortable: true
                    }, {
                        field: 'BIRTHDAY',
                        title: 'BIRTHDAY',
                        align: 'left',
                        valign: 'top',
                        sortable: true
                    }, {
                        field: 'LPUBASE',
                        title: 'LPUBASE',
                        align: 'left',
                        valign: 'top',
                        sortable: true
                    }]
                }
            };
            function flagFormatter(value, row, index) {
                return '<img src="' + row.flagImage + '"/>'
            }
        }

        $scope.GetPatients = function(params) {
            console.log(params.data);
            /*проверяем на авторизацию*/
            $http.get(api_url + "dsp_patients/GetPatients")
                .then(function(response) {

                    if(response.data.auth==0) {
                        $location.path(base_url + 'login');
                    }
                    else {
                        params.success(response.data.patients);
                    }

                });
        }

        $scope.initBt();



    }
]);