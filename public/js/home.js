// load google
google.load('visualization', '1.1', {'packages': ['corechart', 'table', 'gauge']});
google.setOnLoadCallback(drawCharts);


function drawCharts() {
    drawAccountChart();
    drawGaugeForTomorrow();
    drawGaugeForEOM();
}


function drawAccountChart() {
    $.getJSON('home/chart/accounts/' + year + '/' + month).success(function (data) {
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

function drawGaugeForTomorrow() {
    drawGaugeForDay(tomorrow, 'gauge-predict-tomorrow');
}
function drawGaugeForEOM() {
    drawGaugeForDay(eom, 'gauge-predict-eom');
}

function drawGaugeForDay(date, holder) {
    $.getJSON('home/gauge/' + date).success(function (data) {
        gdata = new google.visualization.DataTable(data);
        var chart = new google.visualization.Gauge(document.getElementById(holder));
        chart.draw(gdata, gaugeOptions);
    }).fail(function () {
        $('#' + holder).addClass('load-error');
    });
}

$(function () {
    // click trigger for all X blocks
    $('a[rel="collapse-objects"]').click(collapseList);
});

function collapseList(ev) {
    var ID = $(ev.target).attr('href');
    var holder = $(ID);
    $(holder).on('show.bs.collapse', respondToCollapse);
    holder.collapse('toggle');
    return false;
}

function respondToCollapse(ev) {
    var type = $(ev.target).attr('id').split('-')[1];
    var holder = $(ev.target);

    if (holder.hasClass('loaded')) {
        console.log('Already loaded ' + type + ' so nothing will happen.');
    } else {
        $.get('home/table/' + type + '/' + year + '/' + month)
            .success(
            function (data) {
                holder.empty();
                holder.append($(data));
                holder.addClass('loaded');
            }).fail(function () {
                holder.empty();
                holder.addClass('load-error');
            });
    }
}


//if(holder.hasClass('loaded')) {
//    console.log('Loaded!');
//    holder.collapse('toggle');
//} else {
//    // get the stuff from json bla bla. then toggle
//    $.getJSON('home/tables/beneficiaries/' + year + '/' + month).
//        success(function(data) {
//
//        }).fail(function() {
//            holder.empty();
//            holder.addClass('load-error');
//            holder.collapse('toggle');
//        });
//    console.log('Not yet loaded');
//
//}


// charts:
//var chartObject = [];

//var pieChartOpt = {
//    height: 200,
//    legend: {position: 'none'},
//    chartArea: {
//        width: 180,
//        height: 180
//    },
//    //diff: {innerCircle: { radiusFactor: 0.4 }},
//    pieSliceText: 'value'
//};
//var gaugeFinalValue = 0;

//var gaugePredictionOpt = {
//    width: 400,
//    height: 150,
//    redFrom: 50,
//    redTo: 300,
//    yellowFrom: -50,
//    yellowTo: 50,
//    greenFrom: -300,
//    greenTo: -50,
//    max:300,
//    min:-300,
//    minorTicks: 5};

//function drawComponentChart(index, type) {
//    $.getJSON('home/charts/' + type + '/' + year + '/' + month).success(function (data) {
//        // parse the data from JSON
//        var gdata = new google.visualization.DataTable(data);
//
//        // make the money look good.
//        var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
//        money.format(gdata, 1);
//        //money.format(gdata, 2);
//
//        // make a chart.
//        chartObject[index] = new google.visualization.PieChart(document.getElementById('home-' + type + '-piechart'));
//
//        // draw it.
//        chartObject[index].draw(gdata, pieChartOpt);
////        google.visualization.events.addListener(chartObject[index], 'select', function () {
////            selectSlice(index);
////        });
//    }).fail(function () {
//        $('#home-' + type + '-piechart').addClass('load-error');
//    });
//}

//function drawCharts() {
//
//    drawAccountChart();
//
//    //drawComponentChart(0, 'beneficiary');
//    //drawComponentChart(1, 'budget');
//    //drawComponentChart(2, 'category');
//
//
//}


//function drawGauges() {
//    var data = google.visualization.arrayToDataTable([
//        ['Label', 'Value'],
//        ['Current', -40],
//        ['Expected', {'v': gaugeFinalValue,'f': gaugeFinalValue*-1}]
//    ]);
//
//    var chart = new google.visualization.Gauge(document.getElementById('gauge_prediction'));
//    chart.draw(data, gaugePredictionOpt);
//}

//function selectSlice(index) {
//
////    //console.log(id);
////    var URL = '/home/list/' + id + '/' + year + '/' + month + '/' + type;
////    //$('#PopupModal');
////    $('#PopupModal').removeData('modal').modal({
////        remote: URL
////    });
////    $('#PopupModal').removeData();
//}



