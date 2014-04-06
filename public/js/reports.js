google.load('visualization', '1', {'packages': ['corechart']});
google.setOnLoadCallback(drawCharts)


function drawCharts() {

    if ($('#report-month').length == 1) {

        $.getJSON(document.URL + '/chart').success(function (data) {
            gdata = new google.visualization.DataTable(data);
            var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
            for (i = 1; i < gdata.getNumberOfColumns(); i++) {
                money.format(gdata, i);
            }
            chart = new google.visualization.LineChart(document.getElementById('report-month'));


            chart.draw(gdata, {});
        }).fail(function () {
            $('#report-month').addClass('load-error');
        });
    }

    if ($('#report-year').length == 1) {

        $.getJSON(document.URL + '/chart').success(function (data) {
            gdata = new google.visualization.DataTable(data);
            var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
            for (i = 1; i < gdata.getNumberOfColumns(); i++) {
                money.format(gdata, i);
            }
            chart = new google.visualization.LineChart(document.getElementById('report-year'));


            chart.draw(gdata, {});
        }).fail(function () {
            $('#report-year').addClass('load-error');
        });
    }
}

