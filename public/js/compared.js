//chart

// load google
google.load('visualization', '1.0', {'packages': ['corechart']});
google.setOnLoadCallback(drawCompareCharts);

var opt = {
vAxis: {baseline: 0}
};

function drawCompareCharts() {
    $.each($('.chart'),function(i,v) {
        var holder = $(v);
        var ID = holder.data('id');

        var URL = '/home/reports/compared/chart/'+ID+'/'+first+'/' + second;

        $.getJSON(URL).success(function (data) {
            var gdata = new google.visualization.DataTable(data);
            var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
            money.format(gdata, 1);
            money.format(gdata, 2);
            var chart = new google.visualization.ColumnChart(document.getElementById('component-'+ID));
            chart.draw(gdata, opt);
        }).fail(function () {
            $('#account-overview-chart').addClass('load-error');
        });


    });
}