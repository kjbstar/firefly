$(function () {
    $("#sortable").sortable(
        {
            handle: 'h4',
            stop: function (event, ui) {
                position = 1;
                $.each($('#sortable').children(),function(i,v) {
                    var ID = $(v).attr('id').substring(4);
                    // send to server:
                    var URL = '/home/piggy/drop';
                    $.post(URL, {id: ID, position: position});
                    position++;
                });


            }
        }
    );
    $("#sortable").disableSelection();
});