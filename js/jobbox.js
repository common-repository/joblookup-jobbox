jQuery(document).ready(function ()
{
	var jlWidgetOptions = {
		widget_id: 0
	};

	var  $inputs = jQuery('#pnp').find('input[name="widget_inputs"]').val();

	if ($inputs && typeof $inputs !== "undefined")
	{
		jlWidgetOptions = JSON.parse($inputs);
	}

	if (jQuery('#' + jlWidgetOptions.widget_id + ' #pnp').width() <= 600)
	{
		jQuery('#' + jlWidgetOptions.widget_id + ' #pnp .pnp-searchbox').find('input').css('width', '100%');
	}

	jQuery('body').on('click', '#' + jlWidgetOptions.widget_id + ' #pnp .pnp-page a, #' + jlWidgetOptions.widget_id + ' #pnp .pnp-searchbox .pnp-find-jobs', function (event)
	{
		event.preventDefault();

		var el = jQuery(this);
		var input = JSON.parse(jQuery("input#input_" + jlWidgetOptions.widget_id + "").val());

		var keyword = el.parents().find('.pnp-q').val();
		var location = el.parents().find('.pnp-l').val();
		var pnpKeyword = keyword ? keyword : input['keyword'];
		var pnpLocation = location ? location : input['location'];

		var jobbox_page = event.currentTarget.className === 'pnp-find-jobs' ? 1 : currentPage(jlWidgetOptions.widget_id);

		jQuery.ajax({
			type: 'POST',
			url: myAjax.ajaxurl,
			data: {
				action: 'jobbox_searchbox',
				'publisher_id': jlWidgetOptions.publisher_id,
				'country': input['country'],
				'limit': jlWidgetOptions.limit,
				'jobbox_page': jobbox_page,
				'channel': input['channel'],
				'keyword': pnpKeyword,
				'location': pnpLocation,
				'text_color': input['text_color'],
				'url_color': input['url_color'],
				'density': input['density']
			},
			success: function (data)
			{
				var prev = '';
				var page = currentPage(jlWidgetOptions.widget_id)

				if (input.pagination === 'compact')
				{
					compactPagination(event, el, jlWidgetOptions, input, prev, page, data);
				}
				else
				{
					pagination(event, el, jlWidgetOptions, prev, page, data);
				}
			}
		});
	});

	jQuery('body').on('click', '#' + jlWidgetOptions.widget_id + ' #pnp .pnp-page a.is-disabled',function (e)
	{
		e.preventDefault();
		jQuery(this).removeAttr('href');
	});

	jQuery.getScript('https://joblookup.com/js/pub/tracking.js?publisher=' + jlWidgetOptions.publisher_id + '&channel=' + jlWidgetOptions.channel + '&source=addon');
});

function currentPage(id)
{
	var url = jQuery('#' + id + ' .next_page').attr('href');
	return getURLParameter(url, 'jobbox_page');
}

function getURLParameter(url, name)
{
	return (RegExp(name + '=' + '(.+?)(&|$)').exec(url) || [, null])[1];
}

function pagination(event, el, jlWidgetOptions, prev, page, data)
{
	if (el.hasClass("next_page"))
	{
		page =+ page + 1;
	}
	else
	{
		page =+ page - 1;
	}

	if (page <= 1)
	{
		page = 2;
	}
	else
	{
		prev =+ page - 2;
	}

	el.parents().find('.pnp-jobs-load').html(data);

	jQuery('#' + jlWidgetOptions.widget_id + ' .prev_page').attr('href', jlWidgetOptions.php_url_path + '?jobbox_page=' + prev)
	jQuery('#' + jlWidgetOptions.widget_id + ' .next_page').attr('href', jlWidgetOptions.php_url_path + '?jobbox_page=' + page)
	jQuery('#' + jlWidgetOptions.widget_id + ' .page-number-fix').html(page > 2 ? page - 1 : 1);

	var page_total = jQuery('#' + jlWidgetOptions.widget_id + ' input.total-pages').val();
	jQuery('#' + jlWidgetOptions.widget_id + ' span.total-pages').html(page_total);

	if ((page - 1) >= page_total)
	{
		resetPaginationStyle(jlWidgetOptions.widget_id);
		disablePaginationButton(jlWidgetOptions.widget_id, '.next_page');
	}
	else if (page <= 2)
	{
		resetPaginationStyle(jlWidgetOptions.widget_id);
		disablePaginationButton(jlWidgetOptions.widget_id, '.prev_page');
	}
	else
	{
		resetPaginationStyle(jlWidgetOptions.widget_id);
	}

	if (event.currentTarget.className === 'pnp-find-jobs')
	{
		jQuery('#' + jlWidgetOptions.widget_id + ' .next_page').attr('href', jlWidgetOptions.php_url_path + '?jobbox_page=2')
		jQuery('#' + jlWidgetOptions.widget_id + ' .page-number-fix').html('1');

		disablePaginationButton(jlWidgetOptions.widget_id, '.prev_page');
	}
}

function compactPagination(event, el, jlWidgetOptions, input, prev, page, data)
{
	if (el.hasClass("next_page"))
	{
		page = + page + 1;
	}
	else
	{
		page = + page - 1;
	}

	if (page < 0)
	{
		page = 2;
	}
	else
	{
		prev = '<a class="prev_page" href=" ' + jlWidgetOptions.php_url_path + '?jobbox_page=' + (+ page - 2) + '" style="color:' + input.url_color + '">&lt;&lt;</a>';
	}

	if (event.currentTarget.className === 'pnp-find-jobs')
	{
		page = 2;
		prev = "";
		jQuery('#' + jlWidgetOptions.widget_id + ' .prev_page').hide();
	}

	var $pagination_result = data + '<div class="pnp-pagination pnp-page">' + prev + ' <a class="next_page" href="' + jlWidgetOptions.php_url_path + '?jobbox_page=' + page + '" style="color:' + input.url_color + '" >&gt;&gt;</a></div>';

	el.parents().find('#pnp-jobs-content').html($pagination_result);

	if (+ page === 2)
	{
		jQuery('#' + jlWidgetOptions.widget_id + ' .prev_page').hide();
	}
	else
	{
		jQuery('#' + jlWidgetOptions.widget_id + ' .prev_page').show();
	}
}

function disablePaginationButton(id, $class)
{
	jQuery('#' + id + ' .pnp-pagination-standard ' + $class).parent().find('.cover-disabled').addClass('is-disabled');
	jQuery('#' + id + ' .pnp-pagination-standard ' + $class).addClass('is-disabled');
}

function resetPaginationStyle(id)
{
	jQuery('#' + id + ' .pnp-pagination-standard .prev_page').parent().find('.cover-disabled').removeClass('is-disabled');
	jQuery('#' + id + ' .pnp-pagination-standard .prev_page').removeClass('is-disabled');

	jQuery('#' + id + ' .pnp-pagination-standard .next_page').parent().find('.cover-disabled').removeClass('is-disabled');
	jQuery('#' + id + ' .pnp-pagination-standard .next_page').removeClass('is-disabled');
}
