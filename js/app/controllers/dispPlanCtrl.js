/**
 * Created by elex on 25.12.2016.
 */

var sc = [];

/* Contact Controller */
dspApp.controller('dispPlanCtrl',[
    '$rootScope','$scope','$http', '$location', '$routeParams',
    function($rootScope, $scope, $http, $location, $routeParam ) {

        sc = $scope;
        /*загружаем инфу об отмеченных*/


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
        };

        $scope.radiop = function(value, row, index, p){
            if (row['status'] == false) {
                return '<span class="rd_' + row.enp + '" style="display:none"><input  class="radio1" name="patient_part_' + row.enp + '" type="radio" ' +
                    ' onchange="onSelectPart(' + row.enp + ','+p+')" value="'+p+'"><span class="radio_text">'+p+'</span></span>';
            }
            else {
                if(row['status'].status == '0') {
                    return '<span class="rd_' + row.enp + '" style="display:none"><input  class="radio1" name="patient_part_' + row.enp + '" type="radio" ' +
                        ' onchange="onSelectPart(' + row.enp + ','+p+')" value="'+p+'"><span class="radio_text">'+p+'</span></span>';
                }
                else {
                    var ch = '';
                    if(row['status'].disp_quarter==p) ch = ' checked';
                    return '<span class="rd_' + row.enp + '"><input style="" class="radio1 rd_' + row.enp + '" ' +
                        'name="patient_part_' + row.enp + '" ' +
                        'id="patient_part_' + row.enp + '" ' +
                        'type="radio" ' + ch +
                        ' onchange="onSelectPart(' + row.enp + ','+p+')" value="'+p+'"><span class="radio_text">'+p+'</span></span>'
                }

            }
        };



        $scope.radioFormat1 = function(value, row, index) {
            return $scope.radiop(value, row, index,'1')
        };
        $scope.radioFormat2 = function(value, row, index) {
            return $scope.radiop(value, row, index,'2')
        };
        $scope.radioFormat3 = function(value, row, index) {
            return $scope.radiop(value, row, index,'3')
        };
        $scope.radioFormat4 = function(value, row, index) {
            return $scope.radiop(value, row, index,'4')
        };


        $scope.chkFormat = function(value, row, index){
            if (row['status'] == false) {
                return '<input onchange="onCheckPatient('+"'"+row.enp+"'"+')"' +
                    'style="margin-left: 32px !important;" ' +
                    'class="radio1" ' +
                    'name="patient_chk_'+ row.enp +'" ' +
                    'id="patient_chk_'+ row.enp +'" ' +
                    'type="checkbox" value="1">'
            }
            else if (row['status'].status == '0') {
                return '<input onchange="onCheckPatient('+"'"+row.enp+"'"+')"' +
                    'style="margin-left: 32px !important;" ' +
                    'class="radio1" ' +
                    'name="patient_chk_'+ row.enp +'" ' +
                    'id="patient_chk_'+ row.enp +'" ' +
                    'type="checkbox" value="1">'
            }
            else {
                return '<input onchange="onCheckPatient('+"'"+row.enp+"'"+')"' +
                    'style="margin-left: 32px !important;" ' +
                    'class="radio1" checked ' +
                    'name="patient_chk_'+ row.enp +'" ' +
                    'id="patient_chk_'+ row.enp +'" ' +
                    'type="checkbox" value="1">'
            }

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

        /*отображени в таблице*/
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

        /*событие кнопки взять всх из фильтра*/
        $scope.CheckAllFromFilter = function() {
            var send_data = {
                patient:$scope.patient
                ,disp_year:2017
                ,status:1
            };
            $("#Loader").modal('show');

            $http.post(
                api_url + "dsp_patients/CheckAllFromFilter",
                $.param(send_data)
            )
                .success(function (response) {
                    console.info(response);
                    if(response.auth==0) {
                        $location.path(base_url + 'login');
                    }
                    else {
                       //
                        $scope.RefreshFilter();
                        $("#Loader").modal('hide');
                    }
                });
        };


        /*событие кнопки взять всх из фильтра*/
        $scope.UnCheckAllFromFilter = function() {
            var send_data = {
                patient:$scope.patient
                ,disp_year:2017
                ,status:0
            };
            $("#Loader").modal('show');
            $http.post(
                api_url + "dsp_patients/CheckAllFromFilter",
                $.param(send_data)
            )
                .success(function (response) {
                    console.info(response);
                    if(response.auth==0) {
                        $location.path(base_url + 'login');
                    }
                    else {
                        $scope.RefreshFilter();
                        $("#Loader").modal('hide');
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
                    $scope.total = response.data.total;
                    GetQuaterCount();


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


/*выдает квартал по мсяцу*/
function GeQuarterByDate(date) {
    var d = moment(date);
    var month = d.month()+1;
    if((month>0)&&(month<4)) return 1;
    if((month>3)&&(month<7)) return 2;
    if((month>6)&&(month<10)) return 3;
    if(month>9) return 4;


}


/*вставляем статус*/
function SetStatus(patient_enp,disp_year,disp_quarter,status) {
    /*получаем пациента по enp*/
    $.get(
        api_url + "dsp_patients/get_patient/"+patient_enp,
        {},
        function (response) {
            console.info(response);
            if(response.patient!='false') {
                /*если есть такой пациент*/

                /*проверяем статус*/
                if(disp_quarter == -1) {
                    disp_quarter = GeQuarterByDate(response.patient.BIRTHDAY);
                };

                /*чекаем*/

                if(status==1) {
                    //$('input:radio[name="patient_part_'+patient_enp+'"]').prop('checked', false);
                    $('input:radio[name="patient_part_'+patient_enp+'"][value="'+disp_quarter+'"]').prop('checked', true);
                }
                else {

                }

                $.post(
                    api_url + "dsp_patients/setstatus",
                    {
                        patient_enp:patient_enp,
                        disp_year:disp_year,
                        disp_quarter:disp_quarter,
                        status:status
                    },
                    function (response) {
                        GetQuaterCount();
                    },'json'
                );
            }
        },'json'
    );
}

function onCheckPatient(patient_enp) {
    console.info($("#patient_chk_"+patient_enp).prop('checked'));
    if($("#patient_chk_"+patient_enp).prop('checked')) {
        $('.rd_'+patient_enp).show();
        SetStatus(patient_enp,2017,-1,1);
    } else {
        $('.rd_'+patient_enp).hide();
        SetStatus(patient_enp,2017,-1,0);
    }

}

function onSelectPart(patient_enp,part) {
    console.info(patient_enp,part)
    SetStatus(patient_enp,2017,part,1);
}


function GetQuaterCount(){
    /*проценты по кварталам*/
    var send_data = {
        disp_year:2017
    };

    $.post(
        api_url + "dsp_patients/GetCountPatientsInPlan",
        send_data,
        function (response) {
            $('#pl_1').html(response.plan.kol1);
            $('#pl_2').html(response.plan.kol2);
            $('#pl_3').html(response.plan.kol3);
            $('#pl_4').html(response.plan.kol4);
            $('#pl_all').html(response.plan.kol);
        },'json'
    );


}