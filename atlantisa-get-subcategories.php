<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * Plugin Name: AtlantisA Show Subcategories
 * Plugin URI: https://atlantisa.com
 * Description: This Wordpress plugin shows all subcategories (child categories) of a given post category via shortcode. Go to Settings -> AtlantisA Show Subcategories, setup the plugin according to your requirements and then use shortcode [atlantisa_showsubcat] in Posts -> Categories -> Category -> Description or implement it via: echo do_shortcode('[atlantisa_showsubcat]'); in your page template. It will not show anything if the visitor is not on Category page which has subcategories.
 * Version: 1.0.0
 * Author: www.AtlantisA.com
 * Author URI: https://atlantisa.com
 * License: Apache License 2.0
 */
 
// Prepare admin menu
add_action('admin_menu', 'atlantisa_show_subcategories_menu');
function atlantisa_show_subcategories_menu() {
	add_submenu_page('options-general.php', 'AtlantisA Show Subcategories Settings', 'AtlantisA Show Subcategories', 'administrator', 'atlantisa-show-subcategories-settings', 'atlantisa_show_subcategories_settings_page');
}

// Prepare plugin settings page
add_action( 'admin_init', 'atlantisa_show_subcategories_settings' );
function atlantisa_show_subcategories_settings() {
	register_setting( 'atlantisa-show-subcategories-settings-group', 'atlantisa_show_first' );
	register_setting( 'atlantisa-show-subcategories-settings-group', 'atlantisa_show_more' );
}

// Implement styles
add_action('wp_head', 'atlantisa_subcat_list_styles');
function atlantisa_subcat_list_styles () {
	echo '<style>
			.atlantisaSubCat {
				display: none;
				width: 250px;
				word-wrap: break-word;
				font-weight: 900;
				border: 3px solid #8224e3;
				border-top: transparent;
				border-bottom: transparent;
				border-right: transparent;
				padding-left: 20px;
				padding-right: 20px;
				margin-top: 10px;
				margin-bottom: 10px;
				padding-top: 10px;
				padding-bottom: 10px;
				overflow: hidden;
			}
			#atlantisaLoadMore, #atlantisaLoadAll {
				width: 95%;
				margin-top: 20px;
				display: inline-block;
				font-weight: 900;
				text-decoration: none;
				padding: 10px;
				text-align: center;
				background-color: #8224e3;
				color: #fff;
				border-width: 0 1px 1px 0;
				border-style: solid;
				border-color: #fff;
				box-shadow: 0 1px 1px #ccc;
				transition: all 600ms ease-in-out;
				-webkit-transition: all 600ms ease-in-out;
				-moz-transition: all 600ms ease-in-out;
				-o-transition: all 600ms ease-in-out;
			}
			#atlantisaLoadMore:hover, #atlantisaLoadAll:hover {
				background-color: #8224e3;
				color: #fff;
				font-weight: 900;
				text-decoration: none;
				border-width: 0 1px 1px 0;
				border-style: solid;
				border-color: transparent;
				box-shadow: rgba(0, 0, 0, 0.1) 0px 5px 5px
			}
			#atlantisaLoadMore:visited, #atlantisaLoadAll:visited {
				text-decoration: none;
				display: inline-block;
			}
		</style>';
}

// Implement 'Show more' logic
add_action('wp_footer', 'atlantisa_subcat_list_js');
function atlantisa_subcat_list_js () {
	$sf = esc_attr( get_option('atlantisa_show_first') );
	$sm = esc_attr( get_option('atlantisa_show_more') );
	if ( !is_numeric($sf) ) {
		$sf = 10;
	}
	if ( !is_numeric($sm) ) {
		$sm = 10;
	}
	echo '<script type="text/javascript">
			(function ($) {
				$( document ).ready(function() {
					
					var more = 0
						all = 0;
						
					if ($(".atlantisaSubCat").length > 0) {
						
						$(".atlantisaSubCat").slice(0, ' . $sf . ').css("display", "inline-block");
						$("#atlantisaLoadMore").on("click", function (e) {
							if ( more == 0) {
								e.preventDefault();
								$(".atlantisaSubCat:hidden").slice(0, ' . $sm . ').css("display", "inline-block");
								if ($(".atlantisaSubCat:hidden").length == 0) {
									more = 1;
									$("#atlantisaLoadMore").text("Hide");
									$("#atlantisaLoadMore").css({"background-color": "#f0f0f0", "color": "black"});
									$("#atlantisaLoadAll").css("display", "none");
									
								}
							} else {
								more = 0;
								e.preventDefault();
								$(".atlantisaSubCat").css("display", "none");
								$(".atlantisaSubCat").slice(0, ' . $sf . ').css("display", "inline-block");
								$("#atlantisaLoadMore").text("Show more");
								$("#atlantisaLoadMore").css({"background-color": "#8224e3", "color": "#fff"});
								$("#atlantisaLoadAll").css("display", "inline-block");
							}
						});
						$("#atlantisaLoadAll").on("click", function (e) {
							if ( all == 0) {
								e.preventDefault();
								$(".atlantisaSubCat:hidden").css("display", "inline-block");
								if ($(".atlantisaSubCat:hidden").length == 0) {
									all = 1;
									$("#atlantisaLoadAll").text("Hide");
									$("#atlantisaLoadAll").css({"background-color": "#f0f0f0", "color": "black"});
									$("#atlantisaLoadMore").css("display", "none");
								}
							} else {
								all = 0;
								e.preventDefault();
								$(".atlantisaSubCat").css("display", "none");
								$(".atlantisaSubCat").slice(0, ' . $sf . ').css("display", "inline-block");
								$("#atlantisaLoadAll").text("Show all");
								$("#atlantisaLoadAll").css({"background-color": "#8224e3", "color": "#fff"});
								$("#atlantisaLoadMore").css("display", "inline-block");
							}
						});
					} else {
						$(".atlantisaSubCatWrapper").hide();
					}
				});
			})(jQuery);
		</script>';
}

// Implement main functionality
function atlantisa_generate_subcat_list($atts = []) {
	
	$subCatList;
	$subCategoryArr;
	$current_cat = get_queried_object();
	$atts = shortcode_atts(
		array(
			'parentid' => '',
			'catexclude' => '',
		), $atts
	);

	// If shortcode is used to show predefined category first childs
	if( $atts['parentid'] !== '' ) {
		$parentCatArr = explode(',', $atts['parentid']);
		foreach ($parentCatArr as $parentCatId) {
			if (is_numeric($parentCatId)) {
				if( ! term_exists((int)$parentCatId) ) {
					$subCatList = $subCatList . '<span class="atlantisaSubCatWarning">There is no category with ID ' . (int)$parentCatId . '</span>';
				}
				$subCategoryArr = get_categories( array(
												'parent' 		=>(int)$parentCatId,
												'hide_empty'	=> 0
											) );
				foreach($subCategoryArr as $subCat) {
					
					$subCatList = $subCatList . '<a class="atlantisaSubCat" href="' . get_category_link( $subCat->term_id ) . '">' . $subCat->name .'</a>';
				}
			}
		}
		return '<div class="atlantisaSubCatWrapper">' . $subCatList . '<a href="#" id="atlantisaLoadMore">Show more</a>' . '<a href="#" id="atlantisaLoadAll">Show all</a>' . '</div>';
	}
	
	// If shortcode is used in category template and some categories ids need to be excluded to show their first childs
	if ( $atts['catexclude'] !== '' ) {
		$catExcludeArr = explode(',', $atts['catexclude']);
		if ( in_array($current_cat->term_id, $catExcludeArr) ) {
			// If current category id is in excluded categories array don't show anything
			return '';
		}
	}
	
	// If shortcode is used in category template, current category first childs are shown
	if ($current_cat->taxonomy === 'category') {
		
		$subCategoryArr = get_categories( array(
											'parent' 		=> $current_cat->term_id,
											'hide_empty'	=> 0
										) );
		
		foreach($subCategoryArr as $subCat) {
			
			$subCatList = $subCatList . '<a class="atlantisaSubCat" href="' . get_category_link( $subCat->term_id ) . '">' . $subCat->name .'</a>';
		}
		return '<div class="atlantisaSubCatWrapper">' . $subCatList . '<a href="#" id="atlantisaLoadMore">Show more</a>' . '<a href="#" id="atlantisaLoadAll">Show all</a>' . '</div>';
		
	}
	
	// If shortcode is used without category parent id and not in current category
	if($current_cat->taxonomy !== 'category') {
		return '';
	} else {
		return '';
	}
}
add_shortcode( 'atlantisa_showsubcat', 'atlantisa_generate_subcat_list' );

// Get user option for number of visible categories
function atlantisa_show_subcategories_settings_page() {
?>
<div class="wrap">
<h2>Show Subcategories by AtlantisA</h2>
<h3><strong>How to use:</strong></h3>
<ol>
    <li>Go to Posts -> Categories</li>
    <li>Create or Select desired Category and open to edit it</li>
    <li>In Edit Category -> Description add: [atlantisa_showsubcat]</li>
    <li>Save the changes</li>
    <li>Create subcategories (child categories) and asign them to this category</li>
    <li>Add this category to a menu or in post/page and open it from the front-end of your website. You should see its subcategories.</li>
    <li>If you don't see its subcategories maybe this is a theme issue and you need to implement the shordcode directly in your thame page template by adding: &lt?php echo do_shortcode( '[atlantisa_showsubcat]' ); ?&gt</li>
</ol>

<form method="post" action="options.php">
    <?php settings_fields( 'atlantisa-show-subcategories-settings-group' ); ?>
    <?php do_settings_sections( 'atlantisa-show-subcategories-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Enter the number of the visible subcategories after the page is loaded (Default is 10)</th>
        <td><input width="100%" type="text" name="atlantisa_show_first" placeholder="Enter number" value="<?php echo esc_attr( get_option('atlantisa_show_first') ); ?>" /></td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Enter the number of the subcategories to be added when 'Show more' button is pressed (Default is 10)</th>
        <td><input width="100%" type="text" name="atlantisa_show_more" placeholder="Enter number" value="<?php echo esc_attr( get_option('atlantisa_show_more') ); ?>" /></td>
        </tr>
    </table>
    <?php submit_button(); ?>
</form>
<?php echo 'Made with Love <i class="fa fa-heart heart"></i> by <a class="designByAtlantisa" href="https://atlantisa.com" target="_blank">www.AtlantisA.com</a>' ?>
</div>
<?php } ?>