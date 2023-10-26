// Toggle field layouts of row
function toggleFieldLayouts(el) {
    $(el).closest('tr').find('.toggle').click();
}

// Delete a specified field
function deleteField(el) {
    var id = $(el).data('id');
    var name = $(el).data('name');
    var warning = Craft.t('app', 'Are you sure you want to delete “{name}”?', {name: name});
    var success = Craft.t('app', 'Successfully deleted “{name}”', {name: name});
    var error   = Craft.t('app', 'Unable to delete “{name}”', {name: name});
    if (confirm(warning)) {
        Craft.postActionRequest('fields/delete-field', {id:id}, $.proxy(function(response, textStatus) {
            if (textStatus === 'success') {
                $('tr#field-'+id).remove();
                $('tr#field-details-'+id).remove();
                Craft.cp.displaySuccess(success);
            } else {
                Craft.cp.displayError(error);
            }
        }));
    }
}

// On ready
$(function () {

    // Switch field group when a new group is selected
    $('#content').on('change', 'select#fieldGroup', function () {
        // Compile target URL
        let url = window.inventoryUrl;
        // If value was specified, append it
        if (this.value) {
            url += `/${this.value}`;
        }
        // If query string exists, append it
        if (window.inventoryQuery) {
            url += `?${window.inventoryQuery}`;
        }
        // Go to target URL
        window.location.href = url;
    });

    // Show/hide the field layout results
    $('.tableview').on('click', '.fieldtoggle', function () {
        var id = $(this).data('id');
        var $tr = $('tr#field-details-'+id);
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
