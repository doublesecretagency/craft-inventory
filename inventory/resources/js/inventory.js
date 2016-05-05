
// Toggle field layouts of row
function toggleFieldLayouts(el) {
	$(el).closest('tr').find('.toggle').click();
}

// On ready
$(function () {
	$('.tableview').on('click', '.toggle', function () {
		var id = $(this).data('id');
		var $tr = $('tr#field-'+id);
		if ($(this).hasClass('expanded')) {
			$tr.hide();
			$(this).removeClass('expanded');
		} else {
			$tr.show();
			$(this).addClass('expanded');
		}
	});
});