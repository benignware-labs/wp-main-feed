<?php
/*
Plugin Name: Main Feed
Plugin URI:  https://github.com/benignware-labs/wp-main-feed
Description: Apply filters on your main feed
Version:     1.5
Author:      Rafael Nowrotek
Author URI:  https://github.com/benignware
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
*/


defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

add_action('admin_menu', 'main_feed_menu');
function main_feed_menu() {
  add_menu_page('Main Feed Settings', 'Main Feed', 'administrator', 'main-feed-settings', 'main_feed_settings_page', 'dashicons-rss');
}

add_action( 'admin_init', 'main_feed_settings' );
function main_feed_settings() {
  register_setting( 'main-feed-settings-group', 'main_feed_year' );
  register_setting( 'main-feed-settings-group', 'main_feed_month' );
  register_setting( 'main-feed-settings-group', 'main_feed_cat_id' );
  register_setting( 'main-feed-settings-group', 'main_feed_term_id' );
  
} 

function main_feed_settings_page() {
//
$range = main_feed_get_post_range();

$min_year = date("Y", strtotime($range[0]));
$max_year = date("Y", strtotime($range[1]));

$categories = get_categories();

$tags = get_tags();
  
?>
  <div class="wrap wrap-main-feed">
<h2>Main Feed</h2>
 
<form method="post" action="options.php">
<?php settings_fields( 'main-feed-settings-group' ); ?>
<?php do_settings_sections( 'main-feed-settings-group' ); ?>
<table class="form-table">

<tr valign="top">
<th scope="row">Feed Year</th>
<td>
  <?php $selected_year = esc_attr( get_option('main_feed_year') ); ?>
  <select name="main_feed_year">
    <option value="">All Years</option>
    <?php for ($year = $min_year; $year <= $max_year; $year++) : ?>
      <option value="<?= $year; ?>"<?php if ($selected_year == $year) : ?> selected="selected"<?php endif; ?>><?= $year; ?></option>
    <?php endfor; ?>
  </select>
</td>
</tr>

<tr valign="top">
<th scope="row">Feed Month</th>
<td>
  <?php $selected_month = esc_attr( get_option('main_feed_month') ); ?>
  <select name="main_feed_month">
    <option value="">All Months</option>
    <?php for ($month = 1; $month <= 12; $month++) : ?>
      <option value="<?= $month; ?>"<?php if ($selected_month == $month) : ?> selected="selected"<?php endif; ?>><?= date_i18n("F", mktime(0, 0, 0, $month, 1, 2000)); ?></option>
    <?php endfor; ?>
  </select>
</td>
</tr>

<tr valign="top">
<th scope="row">Feed Category</th>
<td>
  <?php $selected_cat_id = esc_attr( get_option('main_feed_cat_id') ); ?>
  <select name="main_feed_cat_id">
    <option value="">All Categories</option>
    <?php foreach ($categories as $category) : ?>
      <option value="<?= $category->cat_ID; ?>"<?php if ($selected_cat_id == $category->cat_ID) : ?> selected="selected"<?php endif; ?>><?= $category->name ?></option>
    <?php endforeach; ?>
  </select>
</td>
</tr>

<tr valign="top">
<th scope="row">Feed Tag</th>
<td>
  <?php $selected_term_id = esc_attr( get_option('main_feed_term_id') ); ?>
  <select name="main_feed_term_id">
    <option value="">All Tags</option>
    <?php foreach ($tags as $tag) : ?>
      <option value="<?= $tag->term_id; ?>"<?php if ($selected_term_id == $tag->term_id) : ?> selected="selected"<?php endif; ?>><?= $tag->name ?></option>
    <?php endforeach; ?>
  </select>
</td>
</tr>

</table>
<?php submit_button(); ?>
 
</form>
</div> 
<?php
}


function main_feed_get_post_range() {
  

  // get posts from WP
  $first_post = get_posts(array(
    'numberposts' => 1,
    'orderby' => 'post_date',
    'order' => 'ASC',
    'post_type' => 'post',
    'post_status' => 'publish'
  ));
  $first_post = isset($first_post[0]) ? $first_post[0] : null;
  
  $last_post = get_posts(array(
    'numberposts' => 1,
    'orderby' => 'post_date',
    'order' => 'DESC',
    'post_type' => 'post',
    'post_status' => 'publish'
  ));
  
  $last_post = isset($last_post[0]) ? $last_post[0] : null;
  
  
  return array($first_post->post_date, $last_post->post_date);
}

function main_feed_request($qv) {
  if (count(array_keys($qv)) == 1 && isset($qv['feed'])) {
    $qv['year'] = esc_attr( get_option('main_feed_year') );
    $qv['month'] = esc_attr( get_option('main_feed_month') );
    $qv['cat'] = esc_attr( get_option('main_feed_cat_id') );
    $term = get_term_by( 'id', esc_attr( get_option('main_feed_term_id') ), 'post_tag');
    $qv['tag'] = isset($term) && $term ? $term->name : "";
  }
  return $qv;
}
add_filter('request', 'main_feed_request');
?>