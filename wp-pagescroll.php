<?php

/*
Plugin Name: WP-PageScroll
Version: 0.3
Plugin URI: http://neo22s.com/wp-pagescroll
Description: Infinite scroll with pagination and auto scroll. It is a plugin combo from <a href="http://wordpress.org/extend/plugins/infinite-scroll/">Infinite Scroll</a>, <a href="http://wordpress.org/extend/plugins/wp-pagenavi/">WP-PageNavi</a> and <a href="http://neo22s.com/jsscroll/">jsScroll</a>
Author: Chema Garrido
Author URI: http://garridodiaz.com
License : GPL v3
*/


//*************************************************************************************
//Infinite Scroll:
//*************************************************************************************

// constants for enables/disabled
define('infscr_enabled'		, 'enabled');
define('infscr_disabled'	, 'disabled');
define('infscr_maint'	, 'disabledforadmins');
define('infscr_config'	, 'enabledforadmins');


// options keys constants
define('key_infscr_state'			, 'infscr_state');
define('key_infscr_js_calls'			, 'infscr_js_calls');
define('key_infscr_image'			, 'infscr_image');
define('key_infscr_text'			, 'infscr_text');
define('key_infscr_donetext'			, 'infscr_donetext');
define('key_infscr_content_selector'		, 'infscr_content_selector');
define('key_infscr_nav_selector'		, 'infscr_nav_selector');
define('key_infscr_post_selector'		, 'infscr_post_selector');
define('key_infscr_next_selector'		, 'infscr_next_selector');


// defaults
define('infscr_state_default'			, infscr_config);
define('infscr_js_calls_default'		, '');

$image_path = plugins_url('wp-pagescroll/ajax-loader.gif');
define('infscr_image_default'			, $image_path);
define('infscr_text_default'			, '<em>Loading the next set of posts...</em>');
define('infscr_donetext_default'			, '<em>Congratulations, you\'ve reached the end of the internet.</em>');
define('infscr_content_selector_default'	, '#content');
define('infscr_post_selector_default'		, '#content > div.post');
define('infscr_nav_selector_default'		, 'div.navigation');
define('infscr_next_selector_default', 'div.navigation a:first');


// add options
add_option(key_infscr_state		, infscr_state_default			, 'If InfiniteScroll is turned on, off, or in maintenance');
add_option(key_infscr_js_calls		, infscr_js_calls_default		, 'Javascript to execute when new content loads in');
add_option(key_infscr_image		, infscr_image_default			, 'Loading image');
add_option(key_infscr_text		, infscr_text_default			, 'Loading text');
add_option(key_infscr_donetext		, infscr_donetext_default			, 'Completed text');
add_option(key_infscr_content_selector	, infscr_content_selector_default	, 'Content Div css selector');
add_option(key_infscr_nav_selector 	, infscr_nav_selector_default		, 'Navigation Div css selector');
add_option(key_infscr_post_selector 	, infscr_post_selector_default		, 'Post Div css selector');
add_option(key_infscr_next_selector 	, infscr_next_selector_default		, 'Next page Anchor css selector');


// adding actions
add_action('init'		, 'wp_inf_scoll_init');
add_action('wp_footer'		, 'wp_inf_scroll_add');
add_action('admin_menu'		, 'add_wp_inf_scroll_options_page');
add_action('activate_wp-pagescroll/wp-pagescroll.php', 'pagenavi_init');
add_action('wp_print_styles', 'pagenavi_stylesheets');


if ( get_option(key_infscr_state) == infscr_state_default && !isset($_POST['submit']) ) {
	function setup_warning() {
		echo "
		<div id='infinitescroll-warning' class='updated fade'><p>Now you have installed a modification version of WP-PageNavi, Inifinite Scroll and jsScroll<strong>".__('Infinite Scroll is almost ready.')."</strong> ".sprintf(__('Please <a href="%1$s">review the configuration and set the state to enabled for all users</a>. Also you need to ad some code in the footer <a href=http://neo22s.com/wp-pagescroll>check the doc</a>'), "options-general.php?page=wp-pagescroll.php")."</p></div>
		";
	}
	add_action('admin_notices', 'setup_warning');
	return;
}


function add_wp_inf_scroll_options_page()
{
	global $wpdb;
	add_options_page('Infinite Scroll Options', 'Infinite Scroll', 8, basename(__FILE__), 'wp_inf_scroll_options_page');
	add_options_page(__('PageNavi', 'wp-pagenavi'), __('PageNavi', 'wp-pagenavi'), 'manage_options', 'wp-pagescroll/pagenavi-options.php') ;
}

function wp_inf_scroll_options_page()
{
	// if postback, store options
	if (isset($_POST['info_update']))
	{
		check_admin_referer();

		// update state
		$infscr_state = $_POST[key_infscr_state];
		if ($infscr_state != infscr_enabled && $infscr_state != infscr_disabled && $infscr_state != infscr_maint && $infscr_state != infscr_config)
			$infscr_state = infscr_state_default;
		update_option(key_infscr_state, $infscr_state);

		// update js calls field
		$infscr_js_calls = $_POST[key_infscr_js_calls];
		update_option(key_infscr_js_calls, $infscr_js_calls);

		// update image
		$infscr_image = $_POST[key_infscr_image];
		update_option(key_infscr_image, $infscr_image);
		
	    // update text 
		$infscr_text = $_POST[key_infscr_text];
		update_option(key_infscr_text, $infscr_text);
		
		// update done text 
		$infscr_donetext = $_POST[key_infscr_donetext];
		update_option(key_infscr_donetext, $infscr_donetext);

		// update content selector
		$content_selector = $_POST[key_infscr_content_selector];
		update_option(key_infscr_content_selector, $content_selector);

		// update the navigation selector
		$navigation_selector = $_POST[key_infscr_nav_selector];
		update_option(key_infscr_nav_selector, $navigation_selector);

		// update the post selector
		$post_selector = $_POST[key_infscr_post_selector];
		update_option(key_infscr_post_selector, $post_selector);

		// update the next selector
		$next_selector = $_POST[key_infscr_next_selector];
		update_option(key_infscr_next_selector, $next_selector);


		// update notification
		echo "<div class='updated'><p><strong>Infinite Scroll options updated</strong></p></div>";
	}

	// output the options page

?>
<form method="post" action="options-general.php?page=<?php echo basename(__FILE__); ?>">
	<div class="wrap">
<?php if (get_option(key_infscr_state) == infscr_disabled) { ?>
	<div style="margin:10px auto; border:3px #f00 solid; background-color: #fdd; color: #000; padding: 10px; text-align: center;">
	Infinite Scroll plugin is <strong>disabled</strong>.
	</div>
<?php } ?>
<?php if ( false && get_option(key_infscr_state) != infscr_disabled && get_option(key_infscr_js_calls) == '') {  // disabled for now?>
	<div style="margin:10px auto; border:1px #f00 solid; background-color:#fdd; color:#000; padding:10px; text-align:center;">
	No Javascript calls will be made after the content is added. This might cause errors in newly added content.
	</div>
<?php } ?>
  <style type="text/css">
    table.infscroll-opttable { width: 100%;}
    table.infscroll-opttable td, table.infscroll-opttable th { vertical-align: top; padding: 9px 4px; }
    table.infscroll-opttable th { padding-top: 13px; text-align: right;}
    table.infscroll-opttable td p { margin: 0;}
    table.infscroll-opttable dl { font-size: 90%; color: #666; margin-top: 5px; }
    table.infscroll-opttable dd { margin-bottom: 0 }
  </style>

	<h2>Infinite Scroll Options</h2>

	  <p>All CSS selectors are found with the jQuery javascript library. See the <a href="http://docs.jquery.com/Selectors">jQuery CSS Selector documentation</a> for an overview of all possibilities. Single-quotes are not allowed&mdash;only double-quotes may be used.

		<table class="editform infscroll-opttable" cellspacing="0" >
		  <tbody>
			<tr>
				<th width="30%" >
					<label for="<?php echo key_infscr_state; ?>">Infinite Scroll state is:</label>
				</th>
				<td>
					<?php
						echo "<select name='".key_infscr_state."' id='".key_infscr_state."'>\n";
						echo "<option value='".infscr_enabled."'";
						if (get_option(key_infscr_state) == infscr_enabled)
							echo "selected='selected'";
						echo ">Enabled for all users</option>\n";
						
						echo "<option value='".infscr_disabled."'";
						if (get_option(key_infscr_state) == infscr_disabled)
							echo "selected='selected'";
						echo ">Disabled for all users</option>\n";
						
						echo "<option value='".infscr_config."'";
						if (get_option(key_infscr_state) == infscr_config)
							echo "selected='selected'";
						echo ">Enabled for admins only</option>\n";
						
            echo "<option value='".infscr_maint."'";
						if (get_option(key_infscr_state) == infscr_maint)
							echo "selected='selected'";
						echo ">Disabled for admins only</option>\n";
						echo "</select>";
					?>
				</td>
	      <td width="50%">
	        "Enabled for admins only" will enable the plugin code only for logged-in administrators&mdash;visitors will not be affected while you configure the plugin. "Disabled for admins only" is useful for administrators when customizing the blog&mdash;infinite scroll will be disabled for them, but still enabled for any visitors. 
        </td>
			</tr>

		
			<tr>
				<th>
					<label for="<?php echo key_infscr_content_selector; ?>">Content CSS Selector:</label>
				</th>
				<td>
					<?php
						echo "<input name='".key_infscr_content_selector."' id='".key_infscr_content_selector."' value='".stripslashes(get_option(key_infscr_content_selector))."' size='30' type='text'>\n";
					?>
  			</td>
  			<td>
  			  <p>The selector of the content div on the main page.</p>
			  </td>
			</tr>
			  
			<tr>
				<th >
					<label for="<?php echo key_infscr_post_selector; ?>">Post CSS Selector:</label>
				</th>
				<td>
					<?php
						echo "<input name='".key_infscr_post_selector."' id='".key_infscr_post_selector."' value='".stripslashes(get_option(key_infscr_post_selector))."' size='30' type='text'>\n";
					?>
				</td>
				<td>
				  <p>The selector of the post block.</p>
				  <dl>
				    <dt>Examples:</dt>
				    <dd>#content &gt; *</dd>
				    <dd>#content div.post</dd>
				    <dd>div.primary div.entry</dd>
			    </dl>
			  </td>
			</tr>
			  
			<tr>
				<th>
					<label for="<?php echo key_infscr_nav_selector; ?>">Navigation Links CSS Selector:</label>
				</th>
				<td>
					<?php
						echo "<input name='".key_infscr_nav_selector."' id='".key_infscr_nav_selector."' value='".stripslashes(get_option(key_infscr_nav_selector))."' size='30' type='text'>\n";
					?>
			
				</td>
				<td>
			  	<p>The selector of the navigation div (the one that includes the next and previous links).</p>
			  </td>
			</tr>			

			<tr>
				<th>
					<label for="<?php echo key_infscr_next_selector; ?>">Previous posts CSS Selector:</label>
				</th>
				<td>
					<?php
						echo "<input name='".key_infscr_next_selector."' id='".key_infscr_next_selector."' value='".stripslashes(get_option(key_infscr_next_selector))."' size='30' type='text'>\n";
					?>
				</td>
				<td>
				  <p>The selector of the previous posts (next page) A tag.</p>
				  <dl>
				    <dt>Examples:</dt>
				    <dd>div.navigation a:first</dd>
				    <dd>div.navigation a:contains(Previous)</dd>
			    </dl>
			  </td>
			</tr>			
			  
			  
			<tr>
				<th>
					<label for="<?php echo key_infscr_js_calls; ?>">Javascript to be called after the next posts are fetched:</label>
				</th>
				<td>
					<?php
						echo "<textarea name='".key_infscr_js_calls."' rows='2'  style='width: 95%;'>\n";
						echo stripslashes(get_option(key_infscr_js_calls));
						echo "</textarea>\n";
					?>
				</td>
				<td>
				  <p>Any functions that are applied to the post contents on page load will need to be executed when the new content comes in.</p>
		    </td>
			</tr>

			<tr>
				<th>
					<label for="<?php echo key_infscr_image; ?>">Loading image:</label>
				</th>
				<td>
					<?php
						echo "<input name='".key_infscr_image."' id='".key_infscr_image."' value='".stripslashes(get_option(key_infscr_image))."' size='30' type='text'>\n";
					?>
				</td>
                <td>
              	  <p>URL of image that will be displayed while content is being loaded. Visit <a href="http://www.ajaxload.info" target="_blank">www.ajaxload.info</a> to customize your own loading spinner.</p>
              	</td>
  	          </tr>
  	  
  	  			<tr>
				<th>
					<label for="<?php echo key_infscr_text; ?>">Loading text:</label>
				</th>
				<td>
					<?php
						echo "<input name='".key_infscr_text."' id='".key_infscr_text."' value='".stripslashes(get_option(key_infscr_text))."' size='30' type='text'>\n";
					?>
				</td>
                <td>
              	  <p>Text will be displayed while content is being loaded. <small><acronym>HTML</acrynom> allowed.</small></p>
              	</td>
  	          </tr>

	<tr>
				<th>
					<label for="<?php echo key_infscr_donetext; ?>">"You've reached the end" text:</label>
				</th>
				<td>
					<?php
						echo '<input name="'.key_infscr_donetext.'" id="'.key_infscr_donetext.'" value="'.stripslashes(get_option(key_infscr_donetext)).'" size="30" type="text">';
					?>
				</td>
                <td>
              	  <p>Text will be displayed when all entries have already been retrieved. The plugin will show this message, fade it out, and cease working. <small><acronym>HTML</acrynom> allowed.</small></p>
              	</td>
  	          </tr>

			</tbody>
		</table>
			
	<p class="submit">
		<input type='submit' name='info_update' value='Update Options' />
	</p>
	</div>
</form>

<?php
}

function wp_inf_scoll_init(){
	pagenavi_textdomain();
	wp_enqueue_script('jsScroll',plugins_url('wp-pagescroll/jsScroll.js'));
	wp_enqueue_script('jquery-infinitescroll',plugins_url('wp-pagescroll/jquery.infinitescroll.min.js'),array('jquery'),'1.3');
}

function wp_inf_scroll_add()
{

if(function_exists('wp_pagescroll')) wp_pagescroll(); //ads the pagination

	global $user_level;
	
	if (get_option(key_infscr_state) == infscr_disabled)
		return;

	if (is_page() || is_single() ) /* single posts/pages dont get it */
	{
		echo '<!-- Infinite-Scroll not added for this page (single post/page) -->';
		return;
	}
	

  if (get_option(key_infscr_state) == infscr_maint && $user_level >= 8)
  {
    echo '<!-- Infinite-Scroll not added for administrator (maintenance state) -->';
    return;
  }

  if (get_option(key_infscr_state) == infscr_config && $user_level <= 8)
  {
    echo '<!-- Infinite-Scroll not added for visitors (configuration state) -->';
    return;
  }

	$js_calls		= stripslashes(get_option(key_infscr_js_calls));
	$loading_image		= stripslashes(get_option(key_infscr_image));
	$loading_text		= stripslashes(get_option(key_infscr_text));
	$donetext		= stripslashes(get_option(key_infscr_donetext));
	$content_selector	= stripslashes(get_option(key_infscr_content_selector));
	$navigation_selector	= stripslashes(get_option(key_infscr_nav_selector));
	$post_selector		= stripslashes(get_option(key_infscr_post_selector));
	$next_selector		= stripslashes(get_option(key_infscr_next_selector));
	if ($user_level >= 8) {$isAdmin = "true"; }else {$isAdmin = "false";}

$plugin_scroll=plugins_url('wp-pagescroll/wp-pagescroll.js');

$js_string = <<<EOT
<script type="text/javascript" src="$plugin_scroll"></script>
<script type="text/javascript" >
jQuery(document).ready(function($){
  // Infinite Scroll jQuery+Wordpress plugin
  $('$content_selector').infinitescroll({
    debug           : $isAdmin,
    nextSelector    : "$next_selector",
    loadingImg      : "$loading_image",
    text            : "$loading_text",
    donetext        : "$donetext",
    navSelector     : "$navigation_selector",
    contentSelector : "$content_selector",
    itemSelector    : "$post_selector"
    },function(){
$js_calls
    });
});
</script>

EOT;

	echo $js_string;
	return;
}

//*************************************************************************************
//wp-pagenavi
//*************************************************************************************

### Create Text Domain For Translations, the init is with the infinite scroll init
function pagenavi_textdomain() {
	load_plugin_textdomain('wp-pagenavi', false, 'wp-pagenavi');
}

### Function: Enqueue PageNavi Stylesheets
function pagenavi_stylesheets() {
	if(@file_exists(TEMPLATEPATH.'/wp-pagescroll.css')) {
		wp_enqueue_style('wp-pagenavi', get_stylesheet_directory_uri().'/wp-pagescroll.css', false, '2.50', 'all');
	} else {
		wp_enqueue_style('wp-pagenavi', plugins_url('wp-pagescroll/wp-pagescroll.css'), false, '2.50', 'all');
	}	
}


### Function: Page Navigation: Boxed Style Paging
function wp_pagescroll($before = '', $after = '') {
	global $wpdb, $wp_query;
	echo $before.'<div class="wp-pagenavi">'."\n";
	if (!is_single()) {
		$request = $wp_query->request;
		$posts_per_page = intval(get_query_var('posts_per_page'));
		$paged = intval(get_query_var('paged'));
		$pagenavi_options = get_option('pagenavi_options');
		$numposts = $wp_query->found_posts;
		$max_page = $wp_query->max_num_pages;
		if(empty($paged) || $paged == 0) {
			$paged = 1;
		}
		$pages_to_show = intval($pagenavi_options['num_pages']);
		$larger_page_to_show = intval($pagenavi_options['num_larger_page_numbers']);
		$larger_page_multiple = intval($pagenavi_options['larger_page_numbers_multiple']);
		$pages_to_show_minus_1 = $pages_to_show - 1;
		$half_page_start = floor($pages_to_show_minus_1/2);
		$half_page_end = ceil($pages_to_show_minus_1/2);
		$start_page = $paged - $half_page_start;
		if($start_page <= 0) {
			$start_page = 1;
		}
		$end_page = $paged + $half_page_end;
		if(($end_page - $start_page) != $pages_to_show_minus_1) {
			$end_page = $start_page + $pages_to_show_minus_1;
		}
		if($end_page > $max_page) {
			$start_page = $max_page - $pages_to_show_minus_1;
			$end_page = $max_page;
		}
		if($start_page <= 0) {
			$start_page = 1;
		}
		$larger_per_page = $larger_page_to_show*$larger_page_multiple;
		$larger_start_page_start = (n_round($start_page, 10) + $larger_page_multiple) - $larger_per_page;
		$larger_start_page_end = n_round($start_page, 10) + $larger_page_multiple;
		$larger_end_page_start = n_round($end_page, 10) + $larger_page_multiple;
		$larger_end_page_end = n_round($end_page, 10) + ($larger_per_page);
		if($larger_start_page_end - $larger_page_multiple == $start_page) {
			$larger_start_page_start = $larger_start_page_start - $larger_page_multiple;
			$larger_start_page_end = $larger_start_page_end - $larger_page_multiple;
		}
		if($larger_start_page_start <= 0) {
			$larger_start_page_start = $larger_page_multiple;
		}
		if($larger_start_page_end > $max_page) {
			$larger_start_page_end = $max_page;
		}
		if($larger_end_page_end > $max_page) {
			$larger_end_page_end = $max_page;
		}
		if($max_page > 1 || intval($pagenavi_options['always_show']) == 1) {
			$pages_text = str_replace("%CURRENT_PAGE%", '<span id="currentpage">'.number_format_i18n($paged).'</span>', $pagenavi_options['pages_text']);
			$pages_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pages_text);
			switch(intval($pagenavi_options['style'])) {
				case 1:
					if(!empty($pages_text)) {
						echo '<span class="pages">'.$pages_text.'</span>';
					}
					if ($start_page >= 2 && $pages_to_show < $max_page) {
						$first_page_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pagenavi_options['first_text']);
						echo '<a href="'.clean_url(get_pagenum_link()).'" class="first" title="'.$first_page_text.'">'.$first_page_text.'</a>';
						if(!empty($pagenavi_options['dotleft_text'])) {
							echo '<span class="extend">'.$pagenavi_options['dotleft_text'].'</span>';
						}
					}
					if($larger_page_to_show > 0 && $larger_start_page_start > 0 && $larger_start_page_end <= $max_page) {
						for($i = $larger_start_page_start; $i < $larger_start_page_end; $i+=$larger_page_multiple) {
							$page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
							echo '<a href="'.clean_url(get_pagenum_link($i)).'" class="page" title="'.$page_text.'">'.$page_text.'</a>';
						}
					}
					previous_posts_link($pagenavi_options['prev_text']);
					for($i = $start_page; $i  <= $end_page; $i++) {						
						if($i == $paged) {
							//$current_page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['current_text']);
							//echo '<span class="current">'.$current_page_text.'</span>';
							$page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
							echo '<a onclick="GoToPage('.$i.');return false;" id="page'.$i.'"  href="'.clean_url(get_pagenum_link($i)).'" class="current_page" title="'.$page_text.'">'.$page_text.'</a>';
						} else {
							$page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
							echo '<a onclick="GoToPage('.$i.');return false;" id="page'.$i.'"  href="'.clean_url(get_pagenum_link($i)).'" class="page" title="'.$page_text.'">'.$page_text.'</a>';
						}
					}
					next_posts_link($pagenavi_options['next_text'], $max_page);
					if($larger_page_to_show > 0 && $larger_end_page_start < $max_page) {
						for($i = $larger_end_page_start; $i <= $larger_end_page_end; $i+=$larger_page_multiple) {
							$page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
							echo '<a href="'.clean_url(get_pagenum_link($i)).'" class="page" title="'.$page_text.'">'.$page_text.'</a>';
						}
					}
					if ($end_page < $max_page) {
						if(!empty($pagenavi_options['dotright_text'])) {
							echo '<span class="extend">'.$pagenavi_options['dotright_text'].'</span>';
						}
						$last_page_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pagenavi_options['last_text']);
						echo '<a href="'.clean_url(get_pagenum_link($max_page)).'" class="last" title="'.$last_page_text.'">'.$last_page_text.'</a>';
					}
					break;
				case 2;
					echo '<form action="'.htmlspecialchars($_SERVER['PHP_SELF']).'" method="get">'."\n";
					echo '<select size="1" onchange="document.location.href = this.options[this.selectedIndex].value;">'."\n";
					for($i = 1; $i  <= $max_page; $i++) {
						$page_num = $i;
						if($page_num == 1) {
							$page_num = 0;
						}
						if($i == $paged) {
							$current_page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['current_text']);
							echo '<option value="'.clean_url(get_pagenum_link($page_num)).'" selected="selected" class="current">'.$current_page_text."</option>\n";
						} else {
							$page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
							echo '<option value="'.clean_url(get_pagenum_link($page_num)).'">'.$page_text."</option>\n";
						}
					}
					echo "</select>\n";
					echo "</form>\n";
					break;
			}
			
		}
	}
	echo '<img onclick="scrollUp(0,1);return false;" alt="Top" title="Top" src="'.plugins_url('wp-pagescroll/uparrow.png').'"  />
		<img onclick="scrollDown(null,1);return false;" alt="Footer" title="Footer" src="'.plugins_url('wp-pagescroll/downarrow.png').'"  />';
	echo '</div>'.$after."\n";
}



### Function: Round To The Nearest Value
function n_round($num, $tonearest) {
   return floor($num/$tonearest)*$tonearest;
}


### Function: Page Navigation Options
function pagenavi_init() {
	pagenavi_textdomain();
	// Add Options
	$pagenavi_options = array();
	$pagenavi_options['pages_text'] = __('Page %CURRENT_PAGE% of %TOTAL_PAGES%','wp-pagenavi');
	$pagenavi_options['current_text'] = '%PAGE_NUMBER%';
	$pagenavi_options['page_text'] = '%PAGE_NUMBER%';
	$pagenavi_options['first_text'] = __('&laquo; First','wp-pagenavi');
	$pagenavi_options['last_text'] = __('Last &raquo;','wp-pagenavi');
	$pagenavi_options['next_text'] = __('&raquo;','wp-pagenavi');
	$pagenavi_options['prev_text'] = __('&laquo;','wp-pagenavi');
	$pagenavi_options['dotright_text'] = __('...','wp-pagenavi');
	$pagenavi_options['dotleft_text'] = __('...','wp-pagenavi');
	$pagenavi_options['style'] = 1;
	$pagenavi_options['num_pages'] = 5;
	$pagenavi_options['always_show'] = 0;
	$pagenavi_options['num_larger_page_numbers'] = 3;
	$pagenavi_options['larger_page_numbers_multiple'] = 10;
	add_option('pagenavi_options', $pagenavi_options, 'PageNavi Options');
}
?>
