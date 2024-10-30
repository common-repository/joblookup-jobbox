jQuery(document).ready(function ()
{
	colorPicker();

	jQuery(document).ajaxSuccess(function()
	{
		colorPicker();
	});

	jQuery('select').change(function ()
	{
		if(this.value !== 'compact')
		{
			jQuery('.pagination-customise').show();
		}
		else
		{
			jQuery('.pagination-customise').hide()
		}
	});
});

function colorPicker()
{
	jQuery('.cw-color-picker').each(function ()
	{
		var $this = jQuery(this);
		var id = $this.attr('rel');

		$this.farbtastic('#' + id);
	});

	jQuery('.input_color').focus(function ()
	{
		jQuery(this).parent().next('.cw-color-picker').slideDown('slow');
	}).focusout(function ()
	{
		jQuery(this).parent().next('.cw-color-picker').slideUp('slow');
	});
}
