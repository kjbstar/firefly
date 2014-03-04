// load google
google.load('visualization', '1.0', {'packages': ['corechart']});
google.setOnLoadCallback(drawCharts)

function drawCharts() {
    drawIEchart();
    drawMonthAllAccountsChart();
    pieChartsYear();
}

var ieChartSettings = {
    isStacked: true,
    seriesType: "bars",
    series: {0: {type: "line"}},
    colors: ['#0a0', '#500', '#a00'],
    legend: {position: 'none'}
};

var monthAccounts = {
    legend: {position: 'none'},
    height: 250,
    width: 250
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

function pieChartsYear() {
    //piechart-year
    if ($('.piechart-year').length > 0) {
        $.each($('.piechart-year'), function (i, v) {
            var holder = $(v);
            var ID = holder.attr('id');
            var URL = '/home/reports/year/' + year + '/' + ID;
            $.getJSON(URL, {predictables: predictables}).success(function (data) {
                var gdata = new google.visualization.DataTable(data);
                var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
                for (i = 1; i < gdata.getNumberOfColumns(); i++) {
                    money.format(gdata, i);
                }
                var chart = new google.visualization.PieChart(document.getElementById(ID));
                chart.draw(gdata, monthAccounts);
            }).fail(function () {
                holder.addClass('load-error');
            });

        });
    }
}