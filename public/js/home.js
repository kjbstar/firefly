// load google
google.load('visualization', '1.0', {'packages': ['corechart', 'table']});
google.setOnLoadCallback(drawCharts);

// charts:
var categoryChart, beneficiaryChart, budgetChart, accountChart;
var categoryData, beneficiaryData, budgetData, accountData;

var pieOpt = {
    height: 200,
    legend: {position: 'none'},
    chartArea: {
        width: 160,
        height: 160

    }
};

function drawCharts() {

    // ACCOUNTS
    $.getJSON('/home/charts/accounts/' + year + '/' + month).success(function (data) {
        // 260x150
        var opt = {
            height: 150,
            legend: {position: 'none'},
            lineWidth: 1,
            curveType: 'function',
            axisTitlesPosition: 'none',
            chartArea: {
                left:5,
                top:5,
                width:260,
                height:150
            },
            hAxis: {
                textPosition:'none'
            },
            vAxes: {
                0: {textPosition: 'none'},
                1: {textPosition: 'none'}
            },
            vaXis: {textPosition:'none'}
        };
        accountData = new google.visualization.DataTable(data);
        var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
        money.format(accountData, 1);
        accountChart = new google.visualization.AreaChart(document.getElementById('home-accounts-chart'));
        accountChart.draw(accountData, opt);
    }).fail(function () {
            $('#home-accounts-chart').addClass('load-error');
        });

    // BENEFICIARIES
    $.getJSON('/home/charts/beneficiaries/' + year + '/' + month).success(function (data) {
        beneficiaryData = new google.visualization.DataTable(data);
        var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
        money.format(beneficiaryData, 1);
        beneficiaryChart = new google.visualization.PieChart(document.getElementById('home-beneficiaries-piechart'));
        beneficiaryChart.draw(beneficiaryData, pieOpt);
        google.visualization.events.addListener(beneficiaryChart, 'select', selectBeneficiarySlice);
    }).fail(function () {
            $('#home-beneficiaries-piechart').addClass('load-error');
        });
    // CATEGORIES
    $.getJSON('/home/charts/categories/' + year + '/' + month).success(function (data) {
        categoryData = new google.visualization.DataTable(data);
        var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
        money.format(categoryData, 1);
        categoryChart = new google.visualization.PieChart(document.getElementById('home-categories-piechart'));
        categoryChart.draw(categoryData, pieOpt);
        google.visualization.events.addListener(categoryChart, 'select', selectCategorySlice);

    }).fail(function () {
            $('#home-categories-piechart').addClass('load-error');
        });

    // BUDGETS
    $.getJSON('/home/charts/budgets/' + year + '/' + month).success(function (data) {

        budgetData = new google.visualization.DataTable(data);
        var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
        money.format(budgetData, 1);
        budgetChart = new google.visualization.PieChart(document.getElementById('home-budgets-piechart'));
        budgetChart.draw(budgetData, pieOpt);
        google.visualization.events.addListener(budgetChart, 'select', selectBudgetSlice);
    }).fail(function () {
            $('#home-budgets-piechart').addClass('load-error');
        });
}


$(function () {
});

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

function selectSlice(type, id) {
    //console.log(id);
    var URL = '/home/list/' + id + '/' + year + '/' + month + '/' + type;
    //$('#PopupModal');
    $('#PopupModal').removeData('modal').modal({
        remote: URL
    });
    $('#PopupModal').removeData();
}

