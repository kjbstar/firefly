// load google
google.load('visualization', '1.1', {'packages': ['corechart', 'table']});
google.setOnLoadCallback(drawCharts);

// charts:
var categoryChart, beneficiaryChart, budgetChart, accountChart;
var categoryData, beneficiaryData, budgetCurrentData, budgetBudgetData, accountData;
var chartObject = [];
var chartData = [];
var limitData = [];

var pieChartOpt = {
    height: 200,
    legend: {position: 'none'},
    chartArea: {
        width: 180,
        height: 180
    },
    diff: {innerCircle: { radiusFactor: 0.4 }},
    pieSliceText: 'none'
};

function drawComponentChart(index, type) {
    $.getJSON('/home/charts/' + type + '/' + year + '/' + month).success(function (data) {
        // parse the data from JSON
        var gdata = new google.visualization.DataTable(data);

        // make the money look good.
        var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
        money.format(gdata, 1);
        money.format(gdata, 2);

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

    // BENEFICIARIES
//    $.getJSON('/home/charts/beneficiaries/' + year + '/' + month).success(function (data) {
//        beneficiaryData = new google.visualization.DataTable(data.current);
//        var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
//        money.format(beneficiaryData, 1);
//        beneficiaryChart = new google.visualization.PieChart(document.getElementById('home-beneficiaries-piechart'));
//        beneficiaryChart.draw(beneficiaryData, pieOpt);
//        google.visualization.events.addListener(beneficiaryChart, 'select', selectBeneficiarySlice);
//    }).fail(function () {
//            $('#home-beneficiaries-piechart').addClass('load-error');
//        });
//    // CATEGORIES
//    $.getJSON('/home/charts/categories/' + year + '/' + month).success(function (data) {
//        categoryData = new google.visualization.DataTable(data.current);
//        var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
//        money.format(categoryData, 1);
//        categoryChart = new google.visualization.PieChart(document.getElementById('home-categories-piechart'));
//        categoryChart.draw(categoryData, pieOpt);
//        google.visualization.events.addListener(categoryChart, 'select', selectCategorySlice);
//
//    }).fail(function () {
//            $('#home-categories-piechart').addClass('load-error');
//        });
//
//    // BUDGETS WITH DIFF
//    $.getJSON('/home/charts/budgets/' + year + '/' + month).success(function (data) {
//
//        budgetCurrentData = new google.visualization.DataTable(data.current);
//        budgetBudgetData = new google.visualization.DataTable(data.budget);
//
//        var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
//
//        money.format(budgetCurrentData, 1);
//        money.format(budgetBudgetData, 1);
//
//        budgetChart = new google.visualization.PieChart(document.getElementById('home-budgets-piechart'));
//
//        var diffData = budgetChart.computeDiff(budgetBudgetData, budgetCurrentData);
//
//        budgetChart.draw(diffData, pieOpt);
//
//        //google.visualization.events.addListener(budgetChart, 'select', selectBudgetSlice);
//    }).fail(function () {
//            $('#home-budgets-piechart').addClass('load-error');
//        });
//
//    var oldData = google.visualization.arrayToDataTable([
//        ['Major', 'Degrees'],
//        ['Business', 256070],
//        ['Education', 108034],
//        ['Social Sciences & History', 127101],
//        ['Health', 81863],
//        ['Psychology', 74194]
//    ]);
//
//    var newData = google.visualization.arrayToDataTable([
//        ['Major', 'Degrees'],
//        ['Business', 358293],
//        ['Education', 101265],
//        ['Social Sciences & History', 172780],
//        ['Health', 129634],
//        ['Psychology', 97216]
//    ]);
//
//    var chartDiff = new google.visualization.PieChart(document.getElementById('home-budgets-piechart_diff'));
//
//    //chartBefore.draw(oldData, options);
//    //chartAfter.draw(newData, options);
//
//
////    chartDiff.draw(diffData, pieOpt);
}


$(function () {
});

function drawAccountChart() {
    $.getJSON('/home/charts/accounts/' + year + '/' + month).success(function (data) {
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
        money.format(gdata, 1);
        chart = new google.visualization.AreaChart(document.getElementById('home-accounts-chart'));
        chart.draw(gdata, opt);
    }).fail(function () {
            $('#home-accounts-chart').addClass('load-error');
        });
}

function selectBeneficiarySlice() {
    var selection = beneficiaryChart.getSelection();
    if (selection[0]) {
        var row = selection[0].row;
        var value = beneficiaryData.getValue(row, 0);
        return selectSlice('beneficiary', value);
    }
    return null;

}

function selectCategorySlice() {
    var selection = categoryChart.getSelection();
    if (selection[0]) {
        var row = selection[0].row;
        var value = categoryData.getValue(row, 0);
        return selectSlice('category', value);
    }
    return null;
}

function selectBudgetSlice() {
    var selection = budgetChart.getSelection();
    if (selection[0]) {
        var row = selection[0].row;
        var value = budgetData.getValue(row, 0);
        return selectSlice('budget', value);
    }
    return null;
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

