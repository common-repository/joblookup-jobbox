<?php
/*
Plugin Name: JobLookup Jobbox
Plugin URI: https://joblookup.com/publisher/info/jobbox#wordpress
Description: JobLookup's Jobbox is a small, lightweight plugin that's simple to use. It integrates the latest UK job adverts into your site with a widget.
Version: 1.2.0
Author: JobLookup
Author URI: https://joblookup.com
Text Domain: joblookup-jobbox
License: GPL2
*/

### Class: JobLookup Jobbox Widget
class JobLookupJobbox extends WP_Widget {

	var $joblookup_url = 'https://joblookup.com';
	var $total_page = 1;
	var $jobs_exist = true;

	public function __construct()
	{
		$widget_ops = [
			'classname'   => 'joblookup_jobbox',
			'description' => __('Get the latest UK job listings on your site.', 'joblookup-jobbox'),
		];

		parent::__construct('JobLookupJobbox', 'JobLookup Jobbox', $widget_ops);

		add_action('wp_ajax_jobbox_pagination', [$this, 'widgetAjax']);
		add_action('wp_ajax_nopriv_jobbox_pagination', [$this, 'widgetAjax']);

		add_action('wp_ajax_jobbox_searchbox', [$this, 'widgetAjax']);
		add_action('wp_ajax_nopriv_jobbox_searchbox', [$this, 'widgetAjax']);
	}

	public function widgetAjax()
	{
		$inputs = $_POST;

		if (empty($inputs['publisher_id']))
		{
			echo 'Publisher ID is invalid!';
		}
		else
		{
			echo $this->ajaxData($inputs);
		}

		die();
	}

	public function widget($args, $instance)
	{
		$keyword_variable = sanitize_text_field($instance['keyword_var']) ?: 'keyword';
		$location_variable = sanitize_text_field($instance['location_var']) ?: 'location';

		$input = [
			'publisher_id' => intval($instance['publisher_id']) ?: (intval(@$_REQUEST['publisher_id']) ?: ''),
			'country'      => sanitize_text_field(@$instance['country']) ?: (sanitize_text_field(@$_REQUEST['country']) ?: 'ip'),
			'limit'        => intval($instance['limit']) ?: (intval(@$_REQUEST['limit']) ?: 20),
			'channel'      => sanitize_text_field($instance['channel']) ?: (sanitize_text_field(@$_REQUEST['channel']) ?: ''),
			'keyword'      => (sanitize_text_field(@$_REQUEST[$keyword_variable]) ?: '') ?: sanitize_text_field($instance['keyword']),
			'location'     => (sanitize_text_field(@$_REQUEST[$location_variable]) ?: '') ?: sanitize_text_field($instance['location']),
			'text_color'   => sanitize_text_field($instance['text_color']) ?: (sanitize_text_field(@$_REQUEST['text_color']) ?: ''),
			'url_color'    => sanitize_text_field($instance['url_color']) ?: (sanitize_text_field(@$_REQUEST['url_color']) ?: ''),
			'density'      => sanitize_text_field($instance['density']) ?: (sanitize_text_field(@$_REQUEST['density']) ?: ''),
			'jobbox_page'  => intval(@$_GET['jobbox_page']) ?: '',
			'total_pages'  => intval(@$_GET['total_pages']) ?: 1,
		];

		if (! isset($input['publisher_id']) || ($input['publisher_id'] == 0))
		{
			echo 'Publisher ID is invalid!';

			return false;
		}

		echo $this->data($input, $args, $instance);

		return true;
	}

	public function form($instance)
	{ ?>
		<p>
			<label for="<?php echo $this->get_field_id('publisher_id'); ?>"><?php _e('Publisher ID:', 'joblookup-jobbox') ?></label>
			<input
					class="widefat"
					type="number"
					id="<?php echo $this->get_field_id('publisher_id'); ?>"
					name="<?php echo $this->get_field_name('publisher_id'); ?>"
					value="<?php echo ! empty($instance['publisher_id']) ? intval($instance['publisher_id']) : 0 ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('country') ?>"><?php _e('Country:', 'joblookup-jobbox') ?></label>
			<select name="<?php echo $this->get_field_name('country'); ?>" id="<?php echo $this->get_field_id('country'); ?>" class="widefat">
				<?php
				$options = [
					'ip' => __('User Location', 'User Location'),
					'uk' => __('UK', 'UK'),
					'us' => __('US', 'US'),
				];
				foreach ($options as $key => $name)
				{
					echo '<option value="' . esc_attr($key) . '" id="' . esc_attr($key) . '" ' . selected($instance['country'], $key, false) . '>' . $name . '</option>';
				} ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Limit:', 'joblookup-jobbox') ?></label>
			<input
					class="widefat"
					type="number"
					id="<?php echo $this->get_field_id('limit'); ?>"
					name="<?php echo $this->get_field_name('limit'); ?>"
					value="<?php echo ! empty($instance['limit']) ? intval($instance['limit']) : 20 ?>"
					min="1"
					max="50"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('keyword') ?>"><?php _e('Keyword:', 'joblookup-jobbox') ?></label>
			<input
					class="widefat"
					type="text"
					id="<?php echo $this->get_field_id('keyword') ?>"
					name="<?php echo $this->get_field_name('keyword') ?>"
					placeholder="e.g. Account Manager"
					value="<?php echo esc_attr(@$instance['keyword']) ?: null; ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('location') ?>"><?php _e('Area:', 'joblookup-jobbox') ?></label>
			<input
					class="widefat"
					type="text"
					id="<?php echo $this->get_field_id('location') ?>"
					name="<?php echo $this->get_field_name('location') ?>"
					placeholder="e.g. London"
					value="<?php echo esc_attr(@$instance['location']) ?: null; ?>"/>
			<span class="joblookup_description"><?php _e('Enter town or postcode', 'joblookup-jobbox') ?></span>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('channel') ?>"><?php _e('Channel:', 'joblookup-jobbox') ?></label>
			<input
					class="widefat"
					type="text"
					id="<?php echo $this->get_field_id('channel') ?>"
					name="<?php echo $this->get_field_name('channel') ?>"
					value="<?php echo esc_attr(@$instance['channel']) ?: null; ?>"/>
			<span class="joblookup_description"><?php _e('Channel\'s name should be valid, see more <a target="_blank" href="' . $this->joblookup_url . '/publisher/faq#wordpress-settings">here</a>', 'joblookup-jobbox') ?></span>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('keyword_var') ?>"><?php _e('Keyword variable name in the URL:', 'joblookup-jobbox') ?></label>
			<input
					class="widefat"
					type="text"
					id="<?php echo $this->get_field_id('keyword_var') ?>"
					name="<?php echo $this->get_field_name('keyword_var') ?>"
					placeholder="e.g. key_word"
					value="<?php echo esc_attr(@$instance['keyword_var']) ?: null; ?>"/>
			<span class="joblookup_description"><?php _e('Enter specified variable name for the Keyword, leaving it blank will pick the <strong>$keyword</strong> variable, see more <a target="_blank" href="' . $this->joblookup_url . '/publisher/faq#wordpress-settings">here</a>', 'joblookup-jobbox') ?></span>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('location_var') ?>"><?php _e('Area variable name in the URL:', 'joblookup-jobbox') ?></label>
			<input
					class="widefat"
					type="text"
					id="<?php echo $this->get_field_id('location_var') ?>"
					name="<?php echo $this->get_field_name('location_var') ?>"
					placeholder="e.g. place"
					value="<?php echo esc_attr(@$instance['location_var']) ?: null; ?>"/>
			<span class="joblookup_description"><?php _e('Enter specified variable name for the Location, leaving it blank will pick the <strong>$location</strong> variable, see more <a target="_blank" href="' . $this->joblookup_url . '/publisher/faq#wordpress-settings">here</a>', 'joblookup-jobbox') ?></span>

		</p>
		<p>
			<label for="<?php echo $this->get_field_id('density') ?>"><?php _e('Density:', 'joblookup-jobbox') ?></label>
			<select name="<?php echo $this->get_field_name('density'); ?>" id="<?php echo $this->get_field_id('density'); ?>" class="widefat">
				<?php
				$options = [
					'comfortable' => __('Comfortable', 'Comfortable'),
					'compact'     => __('Compact', 'Compact'),
				];
				foreach ($options as $key => $name)
				{
					echo '<option value="' . esc_attr($key) . '" id="' . esc_attr($key) . '" ' . selected($instance['density'], $key, false) . '>' . $name . '</option>';
				} ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('border_color') ?>"><?php _e('Border colour:', 'joblookup-jobbox') ?></label>
			<input
					class="widefat input_color"
					type="text"
					id="<?php echo $this->get_field_id('border_color') ?>"
					name="<?php echo $this->get_field_name('border_color') ?>"
					value="<?php echo esc_attr(@$instance['border_color']) ?: '#dddddd'; ?>"/>
		</p>
		<div class="cw-color-picker" style="display: none" rel="<?php echo $this->get_field_id('border_color'); ?>"></div>
		<p>
			<label for="<?php echo $this->get_field_id('text_color') ?>"><?php _e('Text colour:', 'joblookup-jobbox') ?></label>
			<input
					class="widefat input_color"
					type="text"
					id="<?php echo $this->get_field_id('text_color') ?>"
					name="<?php echo $this->get_field_name('text_color') ?>"
					value="<?php echo esc_attr(@$instance['text_color']) ?: '#000000'; ?>"/>
		</p>
		<div class="cw-color-picker" style="display: none" rel="<?php echo $this->get_field_id('text_color'); ?>"></div>
		<p>
			<label for="<?php echo $this->get_field_id('title_color') ?>"><?php _e('Title colour:', 'joblookup-jobbox') ?></label>
			<input
					class="widefat input_color"
					type="text"
					id="<?php echo $this->get_field_id('title_color') ?>"
					name="<?php echo $this->get_field_name('title_color') ?>"
					value="<?php echo esc_attr(@$instance['title_color']) ?: '#000000'; ?>"/>
		</p>
		<div class="cw-color-picker" style="display: none" rel="<?php echo $this->get_field_id('title_color'); ?>"></div>
		<p>
			<label for="<?php echo $this->get_field_id('url_color') ?>"><?php _e('Url colour:', 'joblookup-jobbox') ?></label>
			<input
					class="widefat input_color"
					type="text"
					id="<?php echo $this->get_field_id('url_color') ?>"
					name="<?php echo $this->get_field_name('url_color') ?>"
					value="<?php echo esc_attr(@$instance['url_color']) ?: '#0000cc'; ?>"/>
		</p>
		<div class="cw-color-picker" style="display: none" rel="<?php echo $this->get_field_id('url_color'); ?>"></div>
		<p>
			<label for="<?php echo $this->get_field_id('background_color') ?>"><?php _e('Background colour:', 'joblookup-jobbox') ?></label>
			<input
					class="widefat input_color"
					type="text"
					id="<?php echo $this->get_field_id('background_color') ?>"
					name="<?php echo $this->get_field_name('background_color') ?>"
					value="<?php echo esc_attr(@$instance['background_color']) ?: '#ffffff'; ?>"/>
		</p>
		<div class="cw-color-picker" style="display: none" rel="<?php echo $this->get_field_id('background_color'); ?>"></div>
		<p>
			<label for="<?php echo $this->get_field_id('pagination') ?>"><?php _e('Pagination:', 'joblookup-jobbox') ?></label>
			<select name="<?php echo $this->get_field_name('pagination'); ?>" id="<?php echo $this->get_field_id('pagination'); ?>" class="widefat">
				<?php
				$options = [
					'compact'  => __('Compact', 'Compact'),
					'standard' => __('Standard', 'Standard'),
					'curved'   => __('Curved', 'Curved'),
				];
				foreach ($options as $key => $name)
				{
					echo '<option value="' . esc_attr($key) . '" id="' . esc_attr($key) . '" ' . selected($instance['pagination'], $key, false) . '>' . $name . '</option>';
				} ?>
			</select>
		</p>
		<div class="pagination-customise" style="<?php if (@$instance['pagination'] === 'compact') { ?>display: none; <?php } ?>">
			<h4 style="margin-bottom: 0;font-weight: bolder;">Pagination Customisation</h4>
			<hr>
			<p>
				<label for="<?php echo $this->get_field_id('pagination_border') ?>"><?php _e('Border colour:', 'joblookup-jobbox') ?></label>
				<input
						class="widefat input_color"
						type="text"
						id="<?php echo $this->get_field_id('pagination_border') ?>"
						name="<?php echo $this->get_field_name('pagination_border') ?>"
						value="<?php echo esc_attr(@$instance['pagination_border']) ?: '#cccccc'; ?>"/>
			</p>
			<div class="cw-color-picker" style="display: none" rel="<?php echo $this->get_field_id('pagination_border'); ?>"></div>
			<p>
				<label for="<?php echo $this->get_field_id('pagination_text') ?>"><?php _e('Text colour:', 'joblookup-jobbox') ?></label>
				<input class="widefat input_color"
				       type="text"
				       id="<?php echo $this->get_field_id('pagination_text') ?>"
				       name="<?php echo $this->get_field_name('pagination_text') ?>"
				       value="<?php echo esc_attr(@$instance['pagination_text']) ?: '#000000'; ?>"/>
			</p>
			<div class="cw-color-picker" style="display: none" rel="<?php echo $this->get_field_id('pagination_text'); ?>"></div>
			<p>
				<label for="<?php echo $this->get_field_id('pagination_background') ?>"><?php _e('Background colour:', 'joblookup-jobbox') ?></label>
				<input class="widefat input_color"
				       type="text"
				       id="<?php echo $this->get_field_id('pagination_background') ?>"
				       name="<?php echo $this->get_field_name('pagination_background') ?>"
				       value="<?php echo esc_attr(@$instance['pagination_background']) ?: '#ffffff'; ?>"/>
			</p>
			<div class="cw-color-picker" style="display: none" rel="<?php echo $this->get_field_id('pagination_background'); ?>"></div>
			<p>
				<label for="<?php echo $this->get_field_id('pagination_number') ?>"><?php _e('Number colour:', 'joblookup-jobbox') ?></label>
				<input class="widefat input_color"
				       type="text"
				       id="<?php echo $this->get_field_id('pagination_number') ?>"
				       name="<?php echo $this->get_field_name('pagination_number') ?>"
				       value="<?php echo esc_attr(@$instance['pagination_number']) ?: '#000000'; ?>"/>
			</p>
			<div class="cw-color-picker" style="display: none" rel="<?php echo $this->get_field_id('pagination_number'); ?>"></div>
			<p>
				<label for="<?php echo $this->get_field_id('pagination_number_bg') ?>"><?php _e('Number background colour:', 'joblookup-jobbox') ?></label>
				<input class="widefat input_color"
				       type="text"
				       id="<?php echo $this->get_field_id('pagination_number_bg') ?>"
				       name="<?php echo $this->get_field_name('pagination_number_bg') ?>"
				       value="<?php echo esc_attr(@$instance['pagination_number_bg']) ?: '#cccccc'; ?>"/>
			</p>
			<div class="cw-color-picker" style="display: none" rel="<?php echo $this->get_field_id('pagination_number_bg'); ?>"></div>
			<hr>
		</div>
		<p>
			<input class="checkbox"
			       type="checkbox" <?php checked(@$instance['searchbox_checkbox'], 'on'); ?>
			       id="<?php echo $this->get_field_id('searchbox_checkbox'); ?>"
			       name="<?php echo $this->get_field_name('searchbox_checkbox'); ?>"/>
			<label for="<?php echo $this->get_field_id('searchbox_checkbox'); ?>"><?php _e('Show search box', 'joblookup-jobbox') ?></label>
		</p>
		<p>
			<input class="checkbox"
			       type="checkbox" <?php checked(@$instance['logo_checkbox'], 'on'); ?>
			       id="<?php echo $this->get_field_id('logo_checkbox'); ?>"
			       name="<?php echo $this->get_field_name('logo_checkbox'); ?>"/>
			<label for="<?php echo $this->get_field_id('logo_checkbox'); ?>"><?php _e('Light logo', 'joblookup-jobbox') ?></label>
		</p>
		<?php
	}

	public function update($new_instance, $old_instance)
	{
		$instance = $new_instance;

		$instance['country'] = (! empty($new_instance['country']) ? esc_attr($new_instance['country']) : 'ip');

		return $instance;
	}

	private function ajaxData($input)
	{
		$ajax_query = $this->jobs($input);

		$ajax_query = '<input type="hidden" class="total-pages" value="' . $this->total_page . '"><div class="pnp-job' . (esc_attr(@$input['density']) !== 'compact' ? ' comfortable">' : '">') . implode('</div><div class="pnp-job' . (esc_attr(@$input['density']) !== 'compact' ? ' comfortable">' : '">'), $ajax_query) . '</div>';

		return $ajax_query;
	}

	private function data($input, $args, $instance)
	{
		echo '<section id="' . $args['widget_id'] . '" class="joblookup_jobbox">';

		if ($query = $this->jobs($input))
		{
			$instance['keyword'] = $input['keyword'];
			$instance['location'] = $input['location'];

			$args['php_url_path'] = parse_url(esc_url(@$_SERVER['REQUEST_URI']), PHP_URL_PATH);
			$args['ajaxurl'] = admin_url('admin-ajax.php');

			$page = intval(@$_GET['jobbox_page']) ?: 1;
			?>

			<div id="pnp" class="pnp-jobswidget-wrapper" style="background: <?php echo $instance['background_color'] ?>; border-color: <?php echo $instance['border_color']; ?>">
				<?php
				if (empty($instance['keyword']) && empty($instance['location']))
				{
					$jobbox_title = 'Popular Jobs';
				}
				else
				{
					$title_keyword = ! empty($instance['keyword']) ? esc_attr($instance['keyword']) : '';
					$title_location = ! empty($instance['location']) ? ' in ' . esc_attr($instance['location']) : '';

					$jobbox_title = trim($title_keyword . ' Jobs' . $title_location);
				}
				?>
				<input type="hidden" name="widget_inputs" id="input_<?php echo esc_attr($args['widget_id']) ?>" value='<?php echo json_encode(array_merge($instance, $args), JSON_HEX_APOS) ?>'>
				<div class="pnp-widget-header" style="color: <?php echo $instance['title_color'] ?>;"><?php echo ($jobbox_title); ?></div>

				<div class="pnp-searchbox" style="<?php echo(@$instance['searchbox_checkbox'] ? '' : 'display:none;') ?>">
					<form method="post">
						<input type="text" placeholder="What: keyword" name="keyword" class="pnp-q">
						<input type="text" placeholder="Where: town" name="location" class="pnp-l">
						<input type="button" value="Find Jobs" class="pnp-find-jobs">
					</form>
					<hr>
				</div>

				<div id="pnp-jobs-content" class="pnp-jobs-content">
					<div class="pnp-jobs-load">
						<?php echo '<div class="pnp-job' . (esc_attr(@$instance['density']) !== 'compact' ? ' comfortable">' : '">') . implode('</div><div class="pnp-job' . (esc_attr(@$instance['density']) !== 'compact' ? ' comfortable">' : '">'), $query) . '</div>'; ?>
					</div>
					<?php
					if (@$instance['pagination'] === 'compact')
					{ ?>
						<div class="pnp-pagination pnp-page">
							<a class="prev_page" href="<?php get_permalink() ?>?jobbox_page=<?php echo $page && $this->jobs_exist ? $page - 1 : 1 ?>" style="<?php echo ($page == 1) ? 'display: none;' : '' ?> color: <?php echo $instance['url_color'] ?>;">&lt;&lt;</a>
							<?php if ($page < $this->total_page || $this->jobs_exist) { ?>
								<a class="next_page" href="<?php get_permalink() ?>?jobbox_page=<?php echo $page >= $this->total_page ? '2' : ($page + 1) ?>" style="color: <?php echo $instance['url_color'] ?>; ">&gt;&gt;</a>
							<?php } ?>
						</div>
					<?php } else { ?>
						<div id="pnp-jobs-content-pagination">
							<div class="pnp-page pnp-pagination-standard <?php if (@$instance['pagination'] === 'curved') { ?> pnp-pagination-curved <?php } ?>">
								<ul>
									<li><span class="cover-disabled <?php echo ($page <= 1) ? 'is-disabled' : '' ?>"></span>
										<a class="prev_page <?php echo ($page <= 1) ? 'is-disabled' : '' ?>" style="border: 1px solid <?php echo $instance['pagination_border']; ?>; background: <?php echo $instance['pagination_background']; ?>;color: <?php echo $instance['pagination_text'] ?>;" href="<?php get_permalink() ?>?jobbox_page=<?php echo $page && $this->jobs_exist ? $page - 1 : 1 ?>"><?php if (@$instance['pagination'] === 'curved') { ?> &lt;&lt; <?php } else { ?> Previous <?php } ?></a>
									</li>
									<li>
										<span class="pnp-disabled-link" style="border: 1px solid <?php echo $instance['pagination_border']; ?>; background: <?php echo $instance['pagination_number_bg']; ?>;color: <?php echo $instance['pagination_number'] ?>;" href="#"><?php echo '<span class="page-number-fix">' . $page . '</span>'; ?>
											<span><?php if (@$instance['pagination'] === 'curved') {
													echo ' / ';
												} else {
													echo ' of ';
												} ?></span>
											<span class="total-pages"><?php echo $this->total_page; ?></span>
										</span>
									</li>
									<li><span class="cover-disabled <?php echo ($page >= $this->total_page || ! $this->jobs_exist) ? 'is-disabled' : '' ?>"></span>
										<a class="next_page <?php echo ($page >= $this->total_page || ! $this->jobs_exist) ? 'is-disabled' : '' ?>" style="border: 1px solid <?php echo $instance['pagination_border']; ?>; background: <?php echo $instance['pagination_background']; ?>;color: <?php echo $instance['pagination_text'] ?>;" href="<?php get_permalink() ?>?jobbox_page=<?php echo $this->jobs_exist && $page ? ($page >= $this->total_page ? '2' : ($page + 1)) : '2' ?>">
											<?php if (@$instance['pagination'] === 'curved') { ?> &gt;&gt; <?php } else { ?> Next <?php } ?></a>
									</li>
								</ul>
							</div>
						</div>
					<?php } ?>
				</div>

				<div class="pnp-link ">
					<a target="_blank" href="<?php echo $this->joblookup_url; ?>" title="Job Search" style="<?php echo(@$instance['logo_checkbox'] ? 'color:#ddd;' : '') ?>">jobs by</a>
					<a target="_blank" title="Job Search" href="<?php echo $this->joblookup_url; ?>"><img alt="JobLookup job search" style="border: 0; vertical-align: middle;" src="<?php echo $this->joblookup_url; ?>/job-search<?php echo(esc_attr(@$instance['logo_checkbox']) ? '-l' : '') ?>.png"></a>
				</div>
			</div>
			<?php
		}
		echo '</section>';
	}

	private function jobs($input)
	{
		$result = [];
		$jobs = $this->fetchData($input);

		$this->total_page =  preg_replace('/[^0-9.]/', '', $jobs->last_page_url);

		if (! $jobs)
		{
			$result[] = '<span style="color:' . $input['url_color'] . '">There is connection error. Please try later.</span>';

			$this->jobs_exist = false;
			$this->total_page =  preg_replace('/[^0-9.]/', '', $jobs->last_first_url);

			return $result;
		}

		if (! $jobs->data)
		{
			$result[] = '<span style="color:' . $input['url_color'] . '">There are no' . ' ' . $input['keyword'] . ' ' . 'jobs.</span>';

			$this->jobs_exist = false;

			return $result;
		}

		foreach ($jobs->data as $job)
		{
			$result[] = '<a rel="nofollow" target="_blank" style="color:' . $input['url_color'] . '" href="' . $job->url . '">' . $job->title . '</a><br><span style="color: ' . $input['text_color'] . '">' . substr(strip_tags($job->snippet), 0, 80) . '...</span>';
		}

		return $result;
	}

	private function fetchData($input)
	{
		$json = $this->request($input);
		$json = json_decode($json);

		return $json;
	}

	private function request($input)
	{
		// Set parameters
		$parameters = [
			'publisher'  => $input['publisher_id'],
			'country'    => $input['country'],
			'channel'    => $input['channel'],
			'user_ip'    => $this->getRealIpAddress(),
			'user_agent' => @$_SERVER['HTTP_USER_AGENT'],
			'keyword'    => $input['keyword'],
			'location'   => $input['location'],
			'limit'      => $input['limit'],
			'page'       => $input['jobbox_page'],
			'addon'      => 1,
		];

		// Create curl resource
		$curl = curl_init();

		// Set URL
		curl_setopt($curl, CURLOPT_URL, $this->joblookup_url . '/api/v1/jobs.json' . '?' . http_build_query($parameters));

		// Return the transfer as a string
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Disable SSL verification
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		// Drop connection after 10 seconds
		curl_setopt($curl, CURLOPT_TIMEOUT, 5);

		// Exec and return the response
		$response = curl_exec($curl);

		// Close curl resource
		curl_close($curl);

		// Dump the response
		return $response;
	}

	private function getRealIpAddress()
	{
		if (! empty($_SERVER['HTTP_CLIENT_IP'])) //check ip from share internet
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) //to check ip is pass from proxy
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}
}

### Load scripts
add_action('init', 'my_script_enqueuer');
function my_script_enqueuer()
{
	wp_register_script("my_voter_script", plugins_url('js/jobbox.js', __FILE__), ['jquery'], null, true);
	wp_localize_script('my_voter_script', 'myAjax', ['ajaxurl' => admin_url('admin-ajax.php')]);

	wp_enqueue_script('jquery');
	wp_enqueue_script('my_voter_script');
}

### Load Admin scripts
add_action('admin_enqueue_scripts', 'joblookup_jobbox_admin_script');
function joblookup_jobbox_admin_script()
{
	wp_enqueue_script('joblookupAdminJobboxScripts', plugins_url('js/adminJobbox.js', __FILE__), ['jquery'], false, true);
}

### Load Admin styles
add_action('admin_print_styles', 'joblookup_jobbox_admin_css');
function joblookup_jobbox_admin_css()
{
	wp_register_style('joblookupAdminJobbox', plugins_url('css/adminJobbox.css', __FILE__));
	wp_enqueue_style('joblookupAdminJobbox');
}

### Load styles
add_action('wp_enqueue_scripts', 'joblookup_jobbox_css');
function joblookup_jobbox_css()
{
	wp_register_style('joblookupJobboxCss', plugins_url('css/jobbox.css', __FILE__));
	wp_enqueue_style('joblookupJobboxCss');
}

### Load color picker scripts
function load_color_picker_script()
{
	wp_enqueue_script('farbtastic');
}

add_action('admin_print_scripts-widgets.php', 'load_color_picker_script');

### Load color picker styles
function load_color_picker_style()
{
	wp_enqueue_style('farbtastic');
}

add_action('admin_print_styles-widgets.php', 'load_color_picker_style');

### JobLookup jobbox widgets init
add_action('widgets_init', function ()
{
	register_widget('JobLookupJobbox');
});
