var accountChartOptions = {
    height: 250,
    legend: {position: 'none'},
    lineWidth: 2,
    curveType: 'function',
    axisTitlesPosition: 'none',
    chartArea: {
        left: 60,
        top: 10,
        width: 1060,
        height: 200
    },

    tooltip: {isHtml: true,
        trigger: 'selection'
    },
    interval: {
        'i13': {style: 'line', color: '#8B0707'}, // pessimistic
        'i12': {style: 'line', color: '#66AA00'}, // least optimistic
        'i11': {style: 'line', color: '#329262'}, // more optimistic
        'i10': {style: 'line', color: '#16D620'}, // very optimistic!




    }



};
