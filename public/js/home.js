// load google
google.load('visualization', '1.1', {'packages': ['corechart', 'table', 'gauge']});
google.setOnLoadCallback(drawCharts);


function drawCharts() {
    drawAccountChart();
}


function drawAccountChart() {

    $.getJSON('/home/account/'+fpAccount+'/overview/chart/' + year + '/' + month).success(function (data) {
        gdata = new google.visualization.DataTable(data);
        var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
        for (i = 1; i < gdata.getNumberOfColumns(); i++) {
            money.format(gdata, i);
        }
        chart = new google.visualization.LineChart(document.getElementById('home-accounts-chart'));
        chart.draw(gdata, accountChartOptions);
    }).fail(function () {
        $('#home-accounts-chart').addClass('load-error');
    });
}