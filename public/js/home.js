// load google
google.load('visualization', '1.1', {'packages': ['corechart', 'table']});
google.setOnLoadCallback(drawCharts);

// charts:
var chartObject = [];

var pieChartOpt = {
    height: 200,
    legend: {position: 'none'},
    chartArea: {
        width: 180,
        height: 180
    },
    //diff: {innerCircle: { radiusFactor: 0.4 }},
    pieSliceText: 'value'
};

function drawComponentChart(index, type) {
    $.getJSON('home/charts/' + type + '/' + year + '/' + month).success(function (data) {
        // parse the data from JSON
        var gdata = new google.visualization.DataTable(data);

        // make the money look good.
        var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
        money.format(gdata, 1);
        //money.format(gdata, 2);

        // make a chart.
        chartObject[index] = new google.visualization.PieChart(document.getElementById('home-' + type + '-piechart'));

        // draw it.
        chartObject[index].draw(gdata, pieChartOpt);
//        google.visualization.events.addListener(chartObject[index], 'select', function () {
//            selectSlice(index);
//        });
    }).fail(function () {
            $('#home-' + type + '-piechart').addClass('load-error');
        });
}

function drawCharts() {

    drawAccountChart();

    drawComponentChart(0, 'beneficiary');
    drawComponentChart(1, 'budget');
    drawComponentChart(2, 'category');
}


$(function () {
});

function drawAccountChart() {
    $.getJSON('home/charts/accounts/' + year + '/' + month).success(function (data) {
        // 260x150
        var opt = {
            height: 150,
            legend: {position: 'none'},
            lineWidth: 1,
            curveType: 'function',
            axisTitlesPosition: 'none',
            chartArea: {
                left: 5,
                top: 5,
                width: 260,
                height: 150
            },
            hAxis: {
                textPosition: 'none'
            },
            vAxes: {
                0: {textPosition: 'none'},
                1: {textPosition: 'none'}
            },
            vaXis: {textPosition: 'none'}
        };

        gdata = new google.visualization.DataTable(data);
        var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
        for (i = 1; i < gdata.getNumberOfColumns(); i++) {
            money.format(gdata, i);
        }

        chart = new google.visualization.AreaChart(document.getElementById('home-accounts-chart'));
        chart.draw(gdata, opt);
    }).fail(function () {
            $('#home-accounts-chart').addClass('load-error');
        });
}

function selectSlice(index) {

//    //console.log(id);
//    var URL = '/home/list/' + id + '/' + year + '/' + month + '/' + type;
//    //$('#PopupModal');
//    $('#PopupModal').removeData('modal').modal({
//        remote: URL
//    });
//    $('#PopupModal').removeData();
}

