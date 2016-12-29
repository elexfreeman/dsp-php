/**
 * Created by elex on 25.12.2016.
 */


/* Contact Controller */
dspApp.controller('dispPlanCtrl',[
    '$rootScope','$scope','$http', '$location', '$routeParams',
    function($rootScope, $scope, $http, $location, $routeParam ) {


        $scope.toLink = function(link) {
            $location.path(base_url + link);
        }

        $scope.age_list = [
            21,24,27,30,33,36,39,42,45,48,51,54,57,60,63,66,69,76,72,75,78,81,84,87,90,93,96,99
        ];



        $scope.month_list = [
            {m:'01',month:'Январь'},
            {m:'02',month:'Февраль'},
            {m:'03',month:'Март'},
            {m:'04',month:'Апрель'},
            {m:'05',month:'Май'},
            {m:'06',month:'Июнь'},
            {m:'07',month:'Июль'},
            {m:'08',month:'Август'},
            {m:'09',month:'Сентябрь'},
            {m:'10',month:'Октябрь'},
            {m:'11',month:'Ноябрь'},
            {m:'12',month:'Декабрь'}
        ];

        $scope.patient = {
            month:'',
            age:'',
            TMODOC:'',
            DOCTOR:''
        }



        $("html, body").animate({scrollTop: 0}, 100);

        $scope.Logout = function() {
            $http.get(api_url + "dsp_auth/logout")
                .then(function(response) {
                        $location.path(base_url + 'login');
                });
        };

        /*событие чека элемента в таблице*/
        $scope.onSelectRow = function(e, row, $el) {
            console.info(e.SURNAME);
            console.info(row);
            console.info($el);
        };
        $scope.onUnSelectRow = function(e, row, $el) {
            console.info('unselect: '+e.SURNAME);
        };

        $scope.selectPatientPart = function(enp,part) {
            console.info(enp,part);
        };

        $scope.onclickRow = function(row, $element, field) {
            console.info(row, $element, field);
        }

        $scope.radioFormat1 = function(value, row, index){

            return '<input class="radio1" name="patient_part_'+row.enp+'" type="radio"  onchange="onCheckPatient('+row.enp+',1)" value="2"><span class="radio_text">1</span>'
        };
        $scope.radioFormat2 = function(value, row, index){
            return '<input class="radio1" name="patient_part_'+row.enp+'" type="radio"  onchange="onCheckPatient('+row.enp+',2)" value="2"><span class="radio_text">2</span>'
        };
        $scope.radioFormat3 = function(value, row, index){
            return '<input class="radio1" name="patient_part_'+row.enp+'" type="radio"  onchange="onCheckPatient('+row.enp+',3)" value="3"><span class="radio_text">3</span>'
        };
        $scope.radioFormat4 = function(value, row, index){
            return '<input class="radio1" name="patient_part_'+row.enp+'" type="radio" onchange="onCheckPatient('+row.enp+',4)" value="4"><span class="radio_text">4</span>'
        };
        $scope.chkFormat = function(value, row, index){
            return '<input style="margin-left: 32px !important;" class="radio1" name="patient_part_'+ row.ENP +'" type="checkbox" value="4">'
        };

        $scope.rclick = function(a){
            console.info(a)
        };

        $scope.dateFormat = function(input_date) {
                var d = moment(input_date);
                return d.format("DD.MM.YYYY");
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
                   /* height: 700,*/
                    /*striped: true,*/
                    pagination: true,
                    pageSize: 30,
                    pageList: [5, 10, 25, 50, 100, 200],
                    search: false,
                    sidePagination:'server',
                    showColumns: false,
                    /*showRefresh: false,*/
                    minimumCountColumns: 2,
                    clickToSelect: true,
                    /*showToggle: true,*/
                    locale: 'ru-RU',
                    onCheck: $scope.onSelectRow,
                    onUncheck: $scope.onUnSelectRow,
                   // onClickRow: $scope.onclickRow,

                    /*maintainSelected: true,*/
                    columns: [
                        {
                            title: 'В плане',
                            formatter: $scope.chkFormat,
                            align: 'center'
                            ,width:60

                        },
                        {
                            field: 'part1',
                            title: 'I кв.',
                            formatter: $scope.radioFormat1,
                            valign: 'middle'
                            ,width:60
                        },
                        {
                            field: 'part1',
                            title: 'II  кв.',
                            formatter: $scope.radioFormat2,
                            valign: 'middle'
                            ,width:60
                        },
                        {
                            field: 'part1',
                            title: 'III  кв.',
                            formatter: $scope.radioFormat3,
                            valign: 'middle'
                            ,width:60
                        },
                        {
                            field: 'part',
                            title: 'IV  кв.',
                            formatter: $scope.radioFormat4,
                            valign: 'middle'
                            ,width:60
                        },


                        {
                            field: 'surname1',
                            title: 'Фамилия',
                            align: 'center',
                            valign: 'middle',
                            sortable: true
                        },
                        {
                            field: 'name1',
                            title: 'Имя',
                            align: 'center',
                            valign: 'middle',
                            sortable: true
                       },
                        {
                        field: 'secname1',
                        title: 'Отчество',
                        align: 'left',
                        valign: 'middle',
                        sortable: true
                    }, {
                        field: 'birthday1',
                        title: 'Дата рождения',
                        align: 'left',
                        valign: 'middle',
                        sortable: true,
                        formatter:$scope.dateFormat,
                        align: 'center'
                    },
                        {
                            field: 'age',
                            title: 'Возраст',
                            align: 'center',
                            valign: 'middle',
                            sortable: true
                        }

                    ]
                },

            };

            function flagFormatter(value, row, index) {
                return '<img src="' + row.flagImage + '"/>'
            }
        }

        /*обновление всей таблицы*/
        $scope.RefreshFilter = function() {
            console.info($scope.bsTable);
            /*это не правельно но работатет ткт не на angular*/
            $("#b1").bootstrapTable('refresh');
            //$scope.bsTableControl.bootstrapTable('refresh');
        }

        $scope.GetPatients = function(params) {
            console.log(params.data);
            /*проверяем на авторизацию*/
            var send_data = {
                patient:$scope.patient,
                data:params.data
            }

            $http.post(
                api_url + "dsp_patients/GetPatients",
                $.param(send_data)
            )
                .success(function (response) {
                    console.info(response);
                    if(response.auth==0) {
                        $location.path(base_url + 'login');
                    }
                    else {
                        params.success(response.patients);
                    }

                });


        };

        $scope.initBt();

        /*Доктора*/
        $http.get(api_url + "dsp_patients/GetRegLPUDoctors")
            .then(function(response) {
                if(response.auth==1) {
                    $location.path(base_url);
                } else {
                    $scope.doctors = response.data.doctors
                }
            });
        /*Участки*/
        $http.get(api_url + "dsp_patients/GetRegLPUuch")
            .then(function(response) {
                if(response.auth==1) {
                    $location.path(base_url);
                } else {
                    $scope.uch = response.data.uch
                }
            });

            /*Общее кол-во дисп*/
        $http.get(api_url + "dsp_patients/GetDspTotalByLPU")
            .then(function(response) {
                if(response.auth==1) {
                    $location.path(base_url);
                } else {
                    $scope.total = response.data.total
                }
            });

        /*план на год*/
        $http.get(api_url + "dsp_patients/GetDspPlanForYear/2017")
            .then(function(response) {
                if(response.auth==1) {
                    $location.path(base_url);
                } else {
                    $scope.DspPlanForYear = response.data.DspPlanForYear
                }
            });



    }
]);

function onCheckPatient(patient_enp,part) {
    console.info(patient_enp,part)
    /*$.post(
        "/products/UpdateProductnfoAjax/"+data[index].id,
        {
            articul:value
        },
        function (response) {

        },'json'
    );*/
}

