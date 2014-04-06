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
        'i10': {style: 'line', color: '#01DF01'}, // optimistic!
        'i11': {style: 'line', color: 'red'}, // pessimistic
        'i12': {style: 'line', color: '#31B404'}, // alt 1
        'i13': {style: 'line', color: '#298A08'} // alt 2

    }



};
