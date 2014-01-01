$(document).ready(function () {
    $('.help-popover').popover({
            placement: 'top',
            container: 'body'
        }
    );
    $('.help-popover').click(function () {
        $('#element').popover('show');
    })


});