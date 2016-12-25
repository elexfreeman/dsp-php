
/*форматирование даты*/
dspApp.filter('dateFormat', function() {
    return function(input_date) {
        var d = moment(input_date);
        return d.format("DD.MM.YYYY");
    };
});