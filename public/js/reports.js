google.load('visualization', '1', {'packages': ['corechart']});
google.setOnLoadCallback(drawCharts);

var pieOptions = {
    legend: {
        position: 'none'
    },
    height: 350,
    pieSliceText: 'value',
    sliceVisibilityThreshold: 1/180
};


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

    if ($('.pie-chart').length == 3) {
        $('.pie-chart').each(function(i,v) {
            var type = $(v).attr('rel');
            var ID = $(v).attr('id');
            var URL = document.URL + '/pie/' + type;
            $.getJSON(URL).success(function (data) {
                gdata = new google.visualization.DataTable(data);
                var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
                for (i = 1; i < gdata.getNumberOfColumns(); i++) {
                    money.format(gdata, i);
                }
                chart = new google.visualization.PieChart(document.getElementById(ID));


                chart.draw(gdata, pieOptions);
            }).fail(function () {
                $(v).addClass('load-error');
            });
        });

    }
}

