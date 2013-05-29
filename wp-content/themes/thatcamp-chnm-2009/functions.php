<?php
//Adapted from PHP.net: http://us.php.net/manual/en/function.nl2br.php#73479
function nls2p($str)
{
	
  return str_replace('<p></p>', '', '<p>'
        . preg_replace('#([\r\n]\s*?[\r\n]){2,}#', '</p>$0<p>', $str)
        . '</p>');

}

function cw_get_the_category_stuff($cat_ID) {
    global $cache_categories, $wpdb;
    if ( !$cache_categories[$cat_ID] ) {
        $cat_stuff = $wpdb->get_results("SELECT * FROM $wpdb->categories WHERE cat_ID = '$cat_ID'");
    } else {
        $cat_name = $cache_categories[$cat_ID]->cat_name;
    }
    return($cat_name);
}

function cw_get_by_metafield($key,$value) {
	
	global $wpdb;
	$result = $wpdb->get_results("SELECT * FROM $wpdb->posts,$wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND $wpdb->postmeta.meta_value = '$value' AND $wpdb->postmeta.meta_key = '$key' ORDER BY RAND() LIMIT 1");
	
	echo '<h2><a href="'.get_permalink($result[0]->ID).'">'.$result[0]->post_title.'</a></h2>';
	echo '<p>'.get_post_meta($result[0]->ID,'Short Description',true).'</p>';
	echo '<p><a href="'.get_permalink($result[0]->ID).'">Read More</a></p>';
		
}

function cw_get_category_link($category_id) {
	global $wp_rewrite;
	$catlink = $wp_rewrite->get_category_permastruct();
	$catlink = str_replace('/category','', $catlink);
	if ( empty($catlink) ) {
		$file = get_option('home') . '/';
		$catlink = $file . '?cat=' . $category_id;
	} else {
		$category = &get_category($category_id);
		if ( is_wp_error( $category ) )
			return $category;
		$category_nicename = $category->slug;

		if ( $parent = $category->parent )
			$category_nicename = get_category_parents($parent, false, '/', true) . $category_nicename;

		$catlink = str_replace('%category%', $category_nicename, $catlink);
		$catlink = get_option('home') . user_trailingslashit($catlink, '');
	}
	return apply_filters('category_link', $catlink, $category_id);
}


// display all users with a link to their user page
function cw_get_authors() {
	global $wpdb;
	
	$author_query = "SELECT ID 
	FROM $wpdb->users u 
	JOIN $wpdb->usermeta um 
	ON u.ID = um.user_id 
	WHERE u.user_nicename != 'info' 
	AND um.meta_key = 'home_user_level' 
	AND um.meta_value != '' 
	AND um.meta_value IS NOT NULL 
	ORDER BY user_nicename";
	
	return $wpdb->get_results($author_query);
		
}

function cw_twitter_results($keyword = 'thatcamp') {
    include_once(ABSPATH . WPINC . '/rss.php');
    $rss = fetch_rss('http://search.twitter.com/search.atom?q='.$keyword);
    $maxitems = 5;
    $items = array_slice($rss->items, 0, $maxitems);
    ?>
    <ul id="twitter">
    <?php if (empty($items)) echo '<li>No items</li>';
    else
    foreach ( $items as $item ) { ?>
    <li><a href="<?php echo $item['author_uri']; ?>"><img src="<?php echo $item['link_image'] ?>" alt="<?php echo $item['author_name'] ?>" /></a> 
        <p><a href="<?php echo $item['author_uri'] ?>"><?php echo $item['author_name']; ?></a> <?php echo $item['title']; ?></p></li>
<?php }
}

// display users of a certain level with a link to their user page
function cw_show_authors_level($level, $list=false) {
	global $wpdb;
	$author_query = "SELECT $wpdb->users.ID FROM $wpdb->users JOIN wp_cimy_uef_data ON wp_cimy_uef_data.USER_ID = $wpdb->users.ID JOIN wp_usermeta ON $wpdb->users.ID = wp_usermeta.user_id WHERE $wpdb->users.user_nicename != 'admin' AND wp_cimy_uef_data.FIELD_ID = '7' AND wp_usermeta.meta_key = 'last_name' AND wp_cimy_uef_data.VALUE = '$level' ORDER BY wp_usermeta.meta_value, $wpdb->users.user_nicename";
	$users = $wpdb->get_results($author_query);

	foreach($users as $user) {
		$user_info = get_userdata($user->ID);
		if($list==true) {
			echo '<li><a href="' . get_author_posts_url($user_info->ID, $user_info->user_nicename) . '">' . $user_info->first_name .' '.$user_info->last_name. '</a></li>';
		} 
		else { 
			echo '<div class="staff-member">';
			echo '<h2><a href="' . get_author_posts_url($user_info->ID, $user_info->user_nicename) . '">' . $user_info->first_name .' '.$user_info->last_name. '</a></h2>';
			echo '<p>'.get_cimyFieldValue($user->ID, 'JOBTITLE').'</p>';
			echo '</div>'; 
		}  
	}
}


/**
 * Always add our styles when using the proper theme
 *
 * Done inline to reduce overhead
 */
function thatcamp_add_styles_note() {
	if ( bp_is_root_blog() ) {
		return;
	}

	?>
<style type="text/css">
div.generic-button {
  margin: 1rem 0;
}
div.generic-button a {
  background: #edbe04;
  border: 1px solid #edbe04;
  opacity: 1;
  -moz-border-radius: 3px;
  -webkit-border-radius: 3px;
  border-radius: 3px;
  color: #000;
  cursor: pointer;
  font-size: 0.9rem;
  outline: none;
  padding: 4px 10px;
  text-align: center;
  text-decoration: none;
  line-height: 14px;
  text-decoration: -1px -1px 0px #668800;
}
div.generic-button a:hover {
  opacity: 0.9;
}
div.generic-button.disabled-button {
  position: relative;
}
div.generic-button.disabled-button a {
  opacity: 0.7;
}
div.generic-button.disabled-button span {
  margin-left: -999em;
  position: absolute;
}
div.generic-button.disabled-button:hover span {
  border-radius: 5px 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px;
  box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.1); -webkit-box-shadow: 5px 5px rgba(0, 0, 0, 0.1); -moz-box-shadow: 5px 5px rgba(0, 0, 0, 0.1);
  position: absolute; left: 1em; top: 2em; z-index: 99;
  margin-left: 0;
  background: #2f2f2f; border: 1px solid #ccc;
  padding: 4px 8px;
  color: #fff;
  white-space: nowrap;
}
</style>
	<?php
}

remove_action( 'wp_head', 'thatcamp_add_styles' );
add_action( 'wp_head', 'thatcamp_add_styles_note' );



?>