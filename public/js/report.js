// load google
google.load('visualization', '1.1', {'packages': ['corechart', 'table']});
google.setOnLoadCallback(drawCharts);

function drawCharts() {
    drawNetWorthChart();
}

function drawNetWorthChart() {
    $.getJSON('/home/report/' + year + '/networth').success(function (data) {
        var opt = {
            series:{
                0: {type:'bars',color:'#3c763d',targetAxisIndex: 0},
                1: {type:'bars',color:'#a94442',targetAxisIndex: 0},
                2: {type:'line',color:'#31708f',targetAxisIndex: 1,lineWidth: 2,curveType:'function'}
            },
            height:300

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