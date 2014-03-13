<?php
/*
Plugin Name: Hivista slider
Plugin URI: http://www.gurievcreative.com
Description: Hivista slider
Version: 1.0
Author: Guriev Eugen
Author URI: http://www.gurievcreative.com
*/

class Slider{
	//                          __              __      
	//   _________  ____  _____/ /_____ _____  / /______
	//  / ___/ __ \/ __ \/ ___/ __/ __ `/ __ \/ __/ ___/
	// / /__/ /_/ / / / (__  ) /_/ /_/ / / / / /_(__  ) 
	// \___/\____/_/ /_/____/\__/\__,_/_/ /_/\__/____/  
	const PLUGIN_FOLDER = 'hivista_slider';

	//                __  _                 
	//   ____  ____  / /_(_)___  ____  _____
	//  / __ \/ __ \/ __/ / __ \/ __ \/ ___/
	// / /_/ / /_/ / /_/ / /_/ / / / (__  ) 
	// \____/ .___/\__/_/\____/_/ /_/____/  
	//     /_/                              
	protected $plugin_path;
	protected $plugin_url;

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct()
	{
		$this->plugin_path = dirname(__FILE__);
		$this->plugin_url  = WP_PLUGIN_URL.'/'.self::PLUGIN_FOLDER;
		// =========================================================
		// HOOKS
		// =========================================================
		add_action('init', array($this, 'createPostTypeSlider'));
		add_action('add_meta_boxes', array($this, 'metaBoxSlider'));
		add_action('save_post', array($this, 'saveSlider'), 0);	
		add_shortcode('slider', array($this, 'displaySlider'));
		add_image_size('slide-image', 1024, 440, true);
		// =========================================================
		// Just for admin panel
		// =========================================================
		if(is_admin())
		{
			wp_enqueue_style('admin-styles', $this->plugin_url.'/css/admin-styles.min.css');
			wp_enqueue_style('font-awesome', $this->plugin_url.'/css/afont-awesome.min.css');
		}
		// =========================================================
		// Just for template
		// =========================================================
		else
		{
			wp_enqueue_script('slider', $this->plugin_url.'/js/slider.js', array('jquery'));
		}
	}
	/**
	 * Create GCEvents post type and his taxonomies
	 */
	public function createPostTypeSlider()
	{

		$post_labels = array(
			'name'               => __('Slides'),
			'singular_name'      => __('Slide'),
			'add_new'            => __('Add new'),
			'add_new_item'       => __('Add new slide'),
			'edit_item'          => __('Edit slide'),
			'new_item'           => __('New slide'),
			'all_items'          => __('Slides'),
			'view_item'          => __('View slide'),
			'search_items'       => __('Search slide'),
			'not_found'          => __('Slide not found'),
			'not_found_in_trash' => __('Slide not found in trash'),
			'parent_item_colon'  => '',
			'menu_name'          => __('Slides'));

		$post_args = array(
			'labels'             => $post_labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'slide' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt'));

		register_post_type('slide', $post_args);
	}

	/**
	 * Add GCEvents meata box
	 */
	public function metaBoxSlider($post_type)
	{
		$post_types = array('slide');
		if(in_array($post_type, $post_types))
		{
			add_meta_box('metaBoxSlider', __('Slider settings'), array($this, 'metaBoxSliderRender'), $post_type, 'side', 'high');	
		}
		
	}

	/**
	 * render Slider Meta box
	 */
	public function metaBoxSliderRender($post)
	{
		$slider    = get_post_meta($post->ID, 'slider', true);		
		wp_nonce_field( 'slider_box', 'slider_box_nonce' );

		?>	
		<div class="gcslider">
			<p>
				<label for="slider_precent"><?php _e('Precent'); ?>:</label>
				<input type="text" name="slider[precent]" id="slider_precent" value="<?php echo $slider['precent']; ?>" class="w100">
			</p>
			<p>
				<label for="slider_prev_text"><?php _e('Previous text'); ?>:</label>
				<textarea name="slider[prev_text]" id="slider_prev_text" cols="25" rows="10" class="w100"><?php echo $slider['prev_text']; ?></textarea>
			</p>	
			<p>
				<label for="slider_next_text"><?php _e('Next text'); ?>:</label>
				<textarea name="slider[next_text]" id="slider_next_text" cols="25" rows="10" class="w100"><?php echo $slider['next_text']; ?></textarea>
			</p>	
		</div>	
		<?php
	}

	/**
	 * Save post
	 * @param  integer $post_id [description]
	 * @return [type]          [description]
	 */
	public function saveSlider($post_id)
	{
		// =========================================================
		// Check nonce
		// =========================================================
		if(!isset( $_POST['slider_box_nonce'])) return $post_id;
		if(!wp_verify_nonce($_POST['slider_box_nonce'], 'slider_box')) return $post_id;
		if(defined( 'DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;

		// =========================================================
		// Check the user's permissions.
		// =========================================================
		if ( 'page' == $_POST['post_type'] ) 
		{			
			if (!current_user_can( 'edit_page', $post_id)) return $post_id;
		} 
		else 
		{
			if(!current_user_can( 'edit_post', $post_id)) return $post_id;
		}

		// =========================================================
		// Save
		// =========================================================		
		if(isset($_POST['slider']))
		{
			update_post_meta($post_id, 'slider', $_POST['slider']);
		}

		return $post_id;
	}
	
	/**
	 * Get all slides
	 * @param  mixed $post_type 
	 * @return array           
	 */
	public function getSlides($count = -1)
	{
		$all = array(
			'posts_per_page'   => $count,
			'offset'           => 0,			
			'orderby'          => 'post_date',
			'order'            => 'DESC',
			'post_type'        => 'slide',
			'post_status'      => 'publish');

		$arr = get_posts($all);
		foreach ($arr as $key => $value) 
		{
			$new_arr[$value->ID] = $value;
		}
		return $new_arr;
	}

	/**
	 * Get Slider HTML
	 * @return string
	 */
	public function getSlider($count = -1, $active = 1)
	{
		$i = 1;
		$slides_html = '';
		$slides      = $this->getSlides($count);		
		foreach ($slides as $key => $value) 
		{
			if(has_post_thumbnail($key))
			{
				$args        = array('class' => 'image', 'alt' => $value->post_title, 'title' => $value->post_title);
				$meta        = get_post_meta($key, 'slider', true);
				$slides_html.= '<li id="hivista_slide'.$i.'"'.$this->active(($i == $active)).'>
									<div class="text">
										<img alt="image description" src="/wp-content/themes/techbridge/images/text-for-girl-2.png">
									</div>
									<div class="row cf">
										<a class="link jcarousel-slider-control-next" href="#">next</a>
										<div class="holder cf">
											<p><span class="s-text">'.$meta['prev_text'].'</span><strong class="percentage p-'.$meta['precent'].'"><span>'.$meta['precent'].'%</span></strong> <span class="s-text">'.$meta['next_text'].'</span></p>
										</div> 
									</div>
									'.get_the_post_thumbnail($key, 'slide-image', $args).'								
								</li>';
				$indicators .= '<li id="hivista_indicator'.$i.'"'.$this->active(($i == $active)).'><a href="#" onclick="jumpToSlide('.$i.'); return false;"></a></li>';
				$i++;
			}
		}
		$out = '<section class="slider-holder">';
		$out.= '<div class="jcarousel-slider">';
		$out.= '<ul class="slides">'.$slides_html.'</ul>';
		$out.= '</div><!-- jcarousel -->';
		$out.= '<ul class="jcarousel-slider-pagination switcher cf"></ul>';
		$out.= '</section><!-- slider-holder -->';
		return $out;
	}

	/**
	 * Replace shortcode to Slider HTML
	 * @param  array $args 
	 * @return string       
	 */
	public function displaySlider($args)
	{
		$count  = (isset($args['count'])) ? intval($args['count']) : -1;
		$active = (isset($args['active'])) ? intval($args['active']) : 1;
		return $this->getSlider($count, $active);
	}

	/**
	 * Set active slider item
	 * @param  boolean $yes 
	 * @return string
	 */
	private function active($yes = false)
	{
		if($yes) return ' class="active"';
		return '';
	}
}
// =========================================================
// LAUNCH
// =========================================================
$GLOBALS['slider'] = new Slider();