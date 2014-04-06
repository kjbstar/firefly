// load google
google.load('visualization', '1', {'packages': ['corechart']});
google.setOnLoadCallback(drawCharts);


function drawCharts() {
    drawAccountChart();
}

$('#PopupModal').on('hidden.bs.modal', function () {
    $(this).removeData();
})


function drawAccountChart() {

    $.getJSON('home/account/'+fpAccount+'/overview/chart/' + year + '/' + month).success(function (data) {
        gdata = new google.visualization.DataTable(data);
        var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
        for (i = 1; i < gdata.getNumberOfColumns(); i++) {
            money.format(gdata, i);
        }
        chart = new google.visualization.LineChart(document.getElementById('home-accounts-chart'));

        // tooltip for prediction info:
        chart.setAction({
            id: 'prediction',                  // An id is mandatory for all actions.
            text: 'More information',       // The text displayed in the tooltip.
            action: function() {           // When clicked, the following runs.
                selection = chart.getSelection()[0];
                // build some sort of modal dialog?
                var date = gdata.getValue(selection.row,0);
                console.log('Before add day: ' + date);
                //date.setDate(date.getDate()+1);
                console.log('after add day: ' + date);
                var dateString = date.getFullYear()+'/'+ (date.getMonth()+1) + '/' +date.getDate();
                var URL = '/home/predict/' + dateString;
                $('#PopupModal').modal(
                    {
                        remote: URL

                    }
                )



            }
        });



        chart.draw(gdata, accountChartOptions);



    }).fail(function () {
        $('#home-accounts-chart').addClass('load-error');
    });
}