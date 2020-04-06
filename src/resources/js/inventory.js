// Toggle field layouts of row
function toggleFieldLayouts(el) {
    $(el).closest('tr').find('.toggle').click();
}

// On ready
$(function () {
    $('.tableview').on('click', '.fieldtoggle', function () {
        var id = $(this).data('id');
        var $tr = $('tr#field-'+id);
        if ($(this).hasClass('expanded')) {
            $tr.hide();
            $(this).removeClass('expanded');
            $(this).addClass('collapsed');
        } else {
            $tr.show();
            $(this).removeClass('collapsed');
            $(this).addClass('expanded');
        }
    });
});
