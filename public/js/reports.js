var google = google ? google : false;

if (google) {
    google.load('visualization', '1.0', {'packages': ['corechart']});
    google.setOnLoadCallback(drawCharts)
}
$(function () {
    compareButtons();
});

function drawCharts() {
    drawIEchart();
    drawMonthAllAccountsChart();
    drawMonthAccountCompareChart();

    drawCompYearChart();

}

var ieChartSettings = {
    isStacked: true,
    seriesType: "bars",
    series: {0: {type: "line"}},
    colors: ['#0a0', '#500', '#a00'],
    legend: {position: 'none'}
};
var componentChartSettings = {
    isStacked: true
};

function drawCompYearChart() {
    if ($('#components-year').length == 1) {
        var URL = '/home/reports/year/' + year + '/components';
        $.getJSON(URL).success(function (data) {
            var gdata = new google.visualization.DataTable(data);
            var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
            for (i = 1; i < gdata.getNumberOfColumns(); i++) {
                money.format(gdata, i);
            }
            var chart = new google.visualization.ColumnChart(document.getElementById('components-year'));
            chart.draw(gdata, componentChartSettings);
        }).fail(function () {
            $('#components-year').addClass('load-error');
        });
    }
}

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

function drawMonthAccountCompareChart() {
    if ($('#all-accounts-compare-chart-month').length == 1) {
        var URL = window.location + '/account';
        $.getJSON(URL).success(function (data) {
            var gdata = new google.visualization.DataTable(data);
            var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
            for (i = 1; i < gdata.getNumberOfColumns(); i++) {
                money.format(gdata, i);
            }
            var chart = new google.visualization.LineChart(document.getElementById('all-accounts-compare-chart-month'));
            chart.draw(gdata, {});
        }).fail(function () {
            $('#all-accounts-compare-chart-month').addClass('load-error');
        });
    }
}

function compareButtons() {
    $('#year-compare').on('click', compareYear);
    $('#month-compare').on('click', compareMonth);
}

function compareYear(ev) {
    var left = $('#year_left').val();
    var right = $('#year_right').val();
    if (left != right) {
        var URL = '/home/reports/compare/' + left + '/' + right;
        window.location = URL;
    }

    return false;
}
function compareMonth(ev) {
    var left = $('#month_left').val();
    var right = $('#month_right').val();
    if (left != right) {
        var URL = '/home/reports/compare/' + left + '/' + right;
        window.location = URL;
    }
    return false;
}