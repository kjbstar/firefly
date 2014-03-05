// load google
google.load('visualization', '1.0', {'packages': ['corechart']});
google.setOnLoadCallback(drawCharts)

function drawCharts() {
    drawIEchart();
    drawMonthAllAccountsChart();
}

var ieChartSettings = {
    isStacked: true,
    seriesType: "bars",
    series: {0: {type: "line"}},
    colors: ['#0a0', '#500', '#a00'],
    legend: {position: 'none'}
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

function drawMonthAllAccountsChart() {
    if ($('#all-accounts-chart-month').length == 1) {
        var URL = 'home/account/overview/chart/' + year + '/' + month;
        $.getJSON(URL).success(function (data) {
            var gdata = new google.visualization.DataTable(data);
            var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
            for (i = 1; i < gdata.getNumberOfColumns(); i++) {
                money.format(gdata, i);
            }
            var chart = new google.visualization.LineChart(document.getElementById('all-accounts-chart-month'));
            chart.draw(gdata, {});
        }).fail(function () {
            $('#all-accounts-chart-month').addClass('load-error');
        });
    }
}