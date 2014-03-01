var accountChartOptions = {
    height: 250,
    legend: {position: 'none'},
    lineWidth: 2,
    curveType: 'function',
    axisTitlesPosition: 'none',
    chartArea: {
        left: 60,
        top: 5,
        width: 1060,
        height: 200
    },
};

var gaugeOptions = {
    width: 150,
    height: 150,
    redFrom: -300,
    redTo: -50,
    yellowFrom: -50,
    yellowTo: 50,
    greenFrom: 50,
    greenTo: 300,
    max: 300,
    min: -300,
    minorTicks: 5

};