// load google
google.load('visualization', '1.0', {'packages': ['corechart']});
google.setOnLoadCallback(drawCharts);

function drawCharts() {
    drawIEchart();
}

var ieChartSettings = {
  colors: ['#01DF01','#DF0101'],
};


function drawIEchart() {
    if ($('#ie').length == 1) {
        var URL = '/home/reports/year/' + year + '/ie';
        $.getJSON(URL).success(function (data) {
            var gdata = new google.visualization.DataTable(data);
            var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
            for (i = 1; i < gdata.getNumberOfColumns(); i++) {
                money.format(gdata, i);
            }
            var chart = new google.visualization.ColumnChart(document.getElementById('ie'));
            chart.draw(gdata, ieChartSettings);
        }).fail(function () {
            $('#ie').addClass('load-error');
        });
    }
}