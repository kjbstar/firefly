
$(function () {
    $('#slider').slider({
        max:100,
        min:0,
        step: 1,
        value:pct,
        slide: updateAmounts
    });
    $('#inputAmount').on('change',updateAmountFromInput)
    updateAmountFromInput();
});

function updateAmountFromInput(ev) {
    var amount = $('#inputAmount').val();
    var pct = $("#slider").slider("value");

    var low = amount * (1-(pct/100));
    var high = amount * (1+(pct/100));
    console.log(pct + '%');

    var lowText = '&euro; ' +  Math.round(low*100)/100;
    var highText = '&euro; ' +  Math.round(high*100)/100;

    $('#lowAmount').html(lowText);
    $('#highAmount').html(highText);

}

function updateAmounts(event,ui) {
    // get the current value from amount:
    var amount = $('#inputAmount').val();

    if(!amount) {
        amount = 0;
    }
    var low = amount * (1-(ui.value/100));
    var high = amount * (1+(ui.value/100));

    $('#inputLeeway').val(ui.value);
    console.log(ui.value + '%');

    var lowText = '&euro; ' +  Math.round(low*100)/100;
    var highText = '&euro; ' +  Math.round(high*100)/100;

    $('#lowAmount').html(lowText);
    $('#highAmount').html(highText);

//    console.log(ui.value);
}


$('input[name="beneficiary"]').typeahead({
    name: 'beneficiary',
    prefetch: 'home/beneficiary/typeahead',
    limit: 10
});
$('input[name="category"]').typeahead({
    name: 'category',
    prefetch: 'home/category/typeahead',
    limit: 10
});

$('input[name="budget"]').typeahead({
    name: 'budget',
    prefetch: 'home/budget/typeahead',
    limit: 10
});

//});