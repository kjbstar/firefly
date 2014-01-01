// load google
google.load('visualization', '1.0', {'packages': ['corechart']});
google.setOnLoadCallback(drawOverviewChart);


function drawOverviewChart() {
    if ($('#account-overview-chart').length == 1) {
        var URL = '/home/account/' + id + '/overview/chart';
        if (year != null && month != null) {
            URL += '/' + year + '/' + month;
        }

        $.getJSON(URL).success(function (data) {
            var opt = {legend: {position: 'none'},
                intervals: { 'style': 'area' }

            };
            var gdata = new google.visualization.DataTable(data);
            var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
            money.format(gdata, 1);
            money.format(gdata, 5);
            money.format(gdata, 6);
            var chart = new google.visualization.ComboChart(document.getElementById('account-overview-chart'));
            chart.draw(gdata, opt);
        }).fail(function () {
                $('#account-overview-chart').addClass('load-error');
            });
    }
}