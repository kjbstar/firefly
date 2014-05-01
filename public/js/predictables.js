
$(function () {
    $('#inputAmount').on('change',updateAmountFromInput);
    $('#inputLeeway').on('change',updateAmountFromInput);
    updateAmountFromInput();
});

function updateAmountFromInput(ev) {
    var amount = $('#inputAmount').val();
    var pct = $("#inputLeeway").val();

    var low = amount * (1-(pct/100));
    var high = amount * (1+(pct/100));

    var lowText = '&euro; ' +  Math.round(low*100)/100;
    var highText = '&euro; ' +  Math.round(high*100)/100;

    $('#lowAmount').html(lowText);
    $('#highAmount').html(highText);

}