// load google
google.load('visualization', '1.1', {'packages': ['corechart', 'table']});
google.setOnLoadCallback(drawCharts);

function drawCharts() {
    drawNetWorthChart();
    drawBenefactorChart();
    drawFanChart();
    drawCatChart();
    drawBudgetCharts();
}

function drawBudgetCharts() {
    var budgets = $('.report-budget-year-chart');

    var opt = {
        colors: ['#000099','#006699','#00CC99'],
        legend: {position: 'none'}
    }

    $.each(budgets, function (index, value) {
        var holder = $(value);
        var ID = holder.data('id');
        var URL = 'home/report/' + year + '/chart/overview/' + ID;
        $.getJSON(URL).success(function (data) {
            gdata = new google.visualization.DataTable(data);
            var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
            money.format(gdata, 1);
            money.format(gdata, 2);
            money.format(gdata, 3);
            chart = new google.visualization.ColumnChart(document.getElementById(holder.attr('id')));
            chart.draw(gdata, opt);

        }).fail(function () {
                holder.addClass('load-error');
            });

    });
}

function drawNetWorthChart() {
    $.getJSON('home/report/' + year + '/networth').success(function (data) {
        var opt = {
            series: {
                0: {type: 'bars', color: '#3c763d', targetAxisIndex: 0},
                1: {type: 'bars', color: '#a94442', targetAxisIndex: 0},
                2: {type: 'line', color: '#31708f', targetAxisIndex: 1, lineWidth: 2, curveType: 'function'}
            },
            height: 300

        };

        gdata = new google.visualization.DataTable(data);
        var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
        money.format(gdata, 1);
        money.format(gdata, 2);
        money.format(gdata, 3);
        chart = new google.visualization.ComboChart(document.getElementById('netWorth'));
        chart.draw(gdata, opt);
    }).fail(function () {
            $('#netWorth').addClass('load-error');
        });
}

function drawBenefactorChart() {
    $.getJSON('home/report/' + year + '/chart/beneficiary/desc').success(function (data) {
        var opt = {
            height: 200,
            legend: {position: 'none'},
            chartArea: {
                width: 160,
                height: 160
            }
        };

        gdata = new google.visualization.DataTable(data);
        var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
        money.format(gdata, 1);
        chart = new google.visualization.PieChart(document.getElementById('report-benefactor-chart'));
        chart.draw(gdata, opt);
    }).fail(function () {
            $('#report-benefactor-chart').addClass('load-error');
        });
}

function drawFanChart() {
    $.getJSON('home/report/' + year + '/chart/beneficiary/asc').success(function (data) {
        var opt = {
            height: 200,
            legend: {position: 'none'},
            chartArea: {
                width: 160,
                height: 160
            }
        };

        gdata = new google.visualization.DataTable(data);
        var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
        money.format(gdata, 1);
        chart = new google.visualization.PieChart(document.getElementById('report-fan-chart'));
        chart.draw(gdata, opt);
    }).fail(function () {
            $('#report-fan-chart').addClass('load-error');
        });
}

function drawCatChart() {
    $.getJSON('home/report/' + year + '/chart/category/asc').success(function (data) {
        var opt = {
            height: 200,
            legend: {position: 'none'},
            chartArea: {
                width: 160,
                height: 160
            }
        };

        gdata = new google.visualization.DataTable(data);
        var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
        money.format(gdata, 1);
        chart = new google.visualization.PieChart(document.getElementById('report-cat-chart'));
        chart.draw(gdata, opt);
    }).fail(function () {
            $('#report-cat-chart').addClass('load-error');
        });
}