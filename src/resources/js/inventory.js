// Toggle field layouts of row
function toggleFieldLayouts(el) {
    $(el).closest('tr').find('.toggle').click();
}

// Delete a specified field
function deleteField(el) {
    var id = $(el).data('id');
    var name = $(el).data('name');
    var warning = Craft.t('app', 'Are you sure you want to delete “{name}”?', {name: name});
    if (confirm(warning)) {
        Craft.postActionRequest('fields/delete-field', {id:id}, $.proxy(function(response, textStatus) {
            if (textStatus === 'success') {
                if (response.success) {
                    location.reload();
                } else {
                    Craft.cp.displayError();
                }
            }
        }));
    }
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
