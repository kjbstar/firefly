// load google
google.load('visualization', '1.0', {'packages': ['corechart']});
google.setOnLoadCallback(drawAllCharts);


function drawAllCharts() {
    if ($('#overview-chart').length === 1) {

        if (month === null && year === null) {
            var URL = '/home/' + object + '/' + id + '/overview/chart';
        } else {
            var URL = '/home/' + object + '/' + id + '/overview/chart/' + year + '/' + month;
        }


        $.getJSON(URL).success(function (data) {
            var opt = {
                legend: {position: 'none'},
                series: {
                    0: {type: "bars", targetAxisIndex: 1},
                    1: {type: "line", targetAxisIndex: 1},
                    2: {type: "bars"},
                    3: {type: "line", targetAxisIndex: 1},
                    4: {type: "bars"},
                },
                interpolateNulls: true

            };
            var gdata = new google.visualization.DataTable(data);
            var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
            money.format(gdata, 2);
            money.format(gdata, 3);
            money.format(gdata, 4);
            money.format(gdata, 5);
            var chart = new google.visualization.ComboChart(document.getElementById('overview-chart'));
            chart.draw(gdata, opt);
        }).fail(function () {
                $('#overview-chart').addClass('load-error');
            });
    }

    // compare charts (probably two):
    var list = $('.compare-piechart');
    $.each(list, function (index, value) {
        var holder = $(value);
        var obj = holder.data('object');
        var compare = holder.data('compare');



        $.getJSON('/home/meta/piechart', {
            object: obj,
            compare: compare,
            id: id,
            month: month,
            year: year
        }).success(function (data) {
                var opt = {
                    height: 300,
                    legend: {position: 'none'}
                };
                var gdata = new google.visualization.DataTable(data);
                var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
                money.format(gdata, 1);
                var chart = new google.visualization.PieChart(document.getElementById(holder.attr('id')));
                chart.draw(gdata, opt);

            }).fail(function () {
                holder.addClass('load-error');
            });
    });


}