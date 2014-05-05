// load google
google.load('visualization', '1', {'packages': ['corechart']});
google.setOnLoadCallback(drawCharts);

var chart;

function drawCharts() {
    drawAccountChart();
}

function updateSelectedAccount() {
    fpAccount = $('#accountChartSelector').val();
    drawAccountChart();
}


function drawAccountChart() {

    if (fpAccount != 0) {
        var URL = 'home/account/' + fpAccount + '/overview/chart/' + year + '/' + month;

        $.getJSON(URL).success(function (data) {
            gdata = new google.visualization.DataTable(data);
            var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
            for (i = 1; i < gdata.getNumberOfColumns(); i++) {
                money.format(gdata, i);
            }
            if (chart == undefined) {
                chart = new google.visualization.LineChart(document.getElementById('home-accounts-chart'));
            }

            // tooltip for prediction info:
            chart.setAction({
                id: 'prediction',                  // An id is mandatory for all actions.
                text: 'More information',       // The text displayed in the tooltip.
                action: function () {           // When clicked, the following runs.
                    var selection = chart.getSelection()[0];
                    // build some sort of modal dialog?
                    var date = gdata.getValue(selection.row, 0);

                    var dateString = date.getFullYear() + '/' + (date.getMonth() + 1) + '/' + date.getDate();
                    var URL = '/home/account/'+fpAccount+'/predict/' + dateString;


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
    } else {
        $('#home-accounts-chart').append(
            $('<a>').addClass('text-info').attr('href',addAccountURL).text('Create an account to continue.')
        );
    }
}