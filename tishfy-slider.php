<?php
/*
Plugin Name: Tishfy Slider
Plugin URI: https://tishonator.com/plugins/tishfy-slider
Description: Configure a Responsive Slick jQuery Carousel Slider and Insert it in any Page or Post as a Shortcode. Admin slide fields for pre-title, title, text, image.
Author: tishonator
Version: 1.0.2
Author URI: http://tishonator.com/
Contributors: tishonator
Text Domain: tishfy-slider
*/

if ( !class_exists('tishonator_TishfySliderPlugin') ) :

    /**
     * Register the plugin.
     *
     * Display the administration panel, insert JavaScript etc.
     */
    class tishonator_TishfySliderPlugin {
        
    	/**
    	 * Instance object
    	 *
    	 * @var object
    	 * @see get_instance()
    	 */
    	protected static $instance = NULL;

        /**
         * an array with all Slider settings
         */
        private $settings = array();

        /**
         * Constructor
         */
        public function __construct() {}

        /**
         * Setup
         */
        public function setup() {

            register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );

            if ( is_admin() ) { // admin actions

                add_action('admin_menu', array(&$this, 'add_admin_page'));

                add_action('admin_enqueue_scripts', array(&$this, 'admin_scripts'));
            }

            add_action( 'init', array(&$this, 'register_shortcode') );
        }

        public function register_shortcode() {

            add_shortcode( 'tishfy-slider', array(&$this, 'display_shortcode') );
        }

        public function display_shortcode($atts) {

            $result = '';

            $options = get_option( 'tishfy_slider_options' );
            
            if ( ! $options )
                return $result;

            // Add slick.js
            wp_register_script('slick_js',
                plugins_url('js/slick.js', __FILE__), array('jquery') );

            wp_enqueue_script('slick_js',
                    plugins_url('js/slick.js', __FILE__), array('jquery') );

            // Linear Icons CSS
            wp_register_style('linearicons-css',
                plugins_url('css/linearicons.min.css', __FILE__), true);

            wp_enqueue_style( 'linearicons-css',
                plugins_url('css/linearicons.min.css', __FILE__), array() );

            // Linear Icons CSS
            wp_register_style('animation-min-css',
                plugins_url('css/animation.min.css', __FILE__), true);

            wp_enqueue_style( 'animation-min-css',
                plugins_url('css/animation.min.css', __FILE__), array() );

            // CSS
            wp_register_style( 'slick_css',
                plugins_url('css/slick.css', __FILE__), true);

            wp_enqueue_style( 'slick_css',
                plugins_url('css/slick.css', __FILE__), array() );

            // Tishfy Slider CSS
            wp_register_style( 'tishfy-slider_css',
                plugins_url('css/tishfy-slider.css', __FILE__), true);

            wp_enqueue_style( 'tishfy-slider_css',
                plugins_url('css/tishfy-slider.css', __FILE__), array() );

            $result .= '<div class="hero-box-area">';
            $result .= '<div class="hero-area hero-slider-7">';

            for ( $slideNumber = 1; $slideNumber <= 3; ++$slideNumber ) {

                $slidePreTitle = array_key_exists('slide_' . $slideNumber . '_pretitle', $options)
                                ? $options[ 'slide_' . $slideNumber . '_pretitle' ] : '';

                $slideTitle = array_key_exists('slide_' . $slideNumber . '_title', $options)
                                ? $options[ 'slide_' . $slideNumber . '_title' ] : '';

                $slideText = array_key_exists('slide_' . $slideNumber . '_text', $options)
                                ? $options[ 'slide_' . $slideNumber . '_text' ] : '';

                $slideImage = array_key_exists('slide_' . $slideNumber . '_image', $options)
                                ? $options[ 'slide_' . $slideNumber . '_image' ] : '';

                if ( $slideImage || $slideTitle || $slideText ) :

                    $result .= '<div class="single-hero-slider-7">';
                    $result .= '<div class="tishfy-slider-container">';
                    $result .= '<div class="hero-content-wrap">';

                    $result .= '<div class="hero-text-7 mt-lg-5">';

                    if ($slidePreTitle != '') {
                        $result .= '<h6 class="pre-title mb-10">' . esc_attr($slidePreTitle) . '</h6>';
                    }

                    if ($slideTitle != '') {
                        $result .= '<h1 class="hero-title">' . esc_attr($slideTitle) . '</h1>';
                    }

                    if ($slideText != '') {
                        $result .= '<p>' . esc_attr($slideText) . '</p>';
                    }

                    $result .= '</div>'; // .hero-text-7 mt-lg-5

                    $result .= '<div class="inner-images">';
                    $result .= '<div class="image-one">';
                    
                    $result .= '<img src="' . esc_url($slideImage)
                                . '" alt="' . esc_attr($slideTitle) . '" class="img-fluid" />';

                    $result .= '</div>'; // .inner-images
                    $result .= '</div>'; // .image-one

                    $result .= '</div>'; // .hero-content-wrap
                    $result .= '</div>'; // .tishfy-slider-container
                    $result .= '</div>'; // .single-hero-slider-7

                endif;
            }

            $result .= '</div>'; // .hero-area hero-slider-7
            $result .= '</div>'; // .hero-box-area

            return $result;
        }

        public function admin_scripts($hook) {

            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');

            wp_register_script('tishfy_slider_upload_media',
                plugins_url('js/upload-media.js', __FILE__), array('jquery'));

            wp_enqueue_script('tishfy_slider_upload_media');

            wp_enqueue_style('thickbox');
        }

    	/**
    	 * Used to access the instance
         *
         * @return object - class instance
    	 */
    	public static function get_instance() {

    		if ( NULL === self::$instance ) {
                self::$instance = new self();
            }

    		return self::$instance;
    	}

        /**
         * Unregister plugin settings on deactivating the plugin
         */
        public function deactivate() {

            unregister_setting('tishfy_slider', 'tishfy_slider_options');
        }

        /** 
         * Print the Section text
         */
        public function print_section_info() {}

        public function admin_init_settings() {
            
            register_setting('tishfy_slider', 'tishfy_slider_options');

            // add separate sections for each of Sliders
            add_settings_section( 'tishfy_slider_section',
                __( 'Slider Settings', 'tishfy-slider' ),
                array(&$this, 'print_section_info'),
                'tishfy_slider' );

            for ( $i = 1; $i <= 3; ++$i ) {

                // Slide Pre-Title
                add_settings_field(
                    'slide_' . $i . '_pretitle',
                    sprintf( __( 'Slide %s Pre-Title', 'tishfy-slider' ), $i ),
                    array(&$this, 'input_callback'),
                    'tishfy_slider',
                    'tishfy_slider_section',
                    [ 'id' => 'slide_' . $i . '_pretitle',
                      'page' =>  'tishfy_slider_options' ]
                );

                // Slide Title
                add_settings_field(
                    'slide_' . $i . '_title',
                    sprintf( __( 'Slide %s Title', 'tishfy-slider' ), $i ),
                    array(&$this, 'input_callback'),
                    'tishfy_slider',
                    'tishfy_slider_section',
                    [ 'id' => 'slide_' . $i . '_title',
                      'page' =>  'tishfy_slider_options' ]
                );

                // Slide Text
                add_settings_field(
                    'slide_' . $i . '_text',
                    sprintf( __( 'Slide %s Text', 'tishfy-slider' ), $i ),
                    array(&$this, 'textarea_callback'),
                    'tishfy_slider',
                    'tishfy_slider_section',
                    [ 'id' => 'slide_' . $i . '_text',
                      'page' =>  'tishfy_slider_options' ]
                );

                // Slide Image
                add_settings_field(
                    'slide_' . $i . '_image',
                    sprintf( __( 'Slide %s Image', 'tishfy-slider' ), $i ),
                    array(&$this, 'image_callback'),
                    'tishfy_slider',
                    'tishfy_slider_section',
                    [ 'id' => 'slide_' . $i . '_image',
                      'page' =>  'tishfy_slider_options' ]
                );
            }
        }

        public function textarea_callback($args) {

            // get the value of the setting we've registered with register_setting()
            $options = get_option( $args['page'] );
 
            // output the field

            $fieldValue = $options && $args['id'] && array_key_exists(esc_attr( $args['id'] ), $options)
                                ? $options[ esc_attr( $args['id'] ) ] : '';
            ?>

            <textarea id="<?php echo esc_attr( $args['page'] . '[' . $args['id'] . ']' ); ?>"
                name = "<?php echo esc_attr( $args['page'] . '[' . $args['id'] . ']' ); ?>"
                rows="10" cols="39"><?php echo esc_attr($fieldValue); ?></textarea>
            <?php
        }

        public function input_callback($args) {

            // get the value of the setting we've registered with register_setting()
            $options = get_option( $args['page'] );
 
            // output the field
            $fieldValue = ($options && $args['id'] && array_key_exists(esc_attr( $args['id'] ), $options))
                                ? $options[ esc_attr( $args['id'] ) ] : 
                                    (array_key_exists('default_val', $args) ? $args['default_val'] : '');
            ?>

            <input type="text" id="<?php echo esc_attr( $args['page'] . '[' . $args['id'] . ']' ); ?>"
                name="<?php echo esc_attr( $args['page'] . '[' . $args['id'] . ']' ); ?>"
                class="regular-text"
                value="<?php echo esc_attr( $fieldValue ); ?>" />
<?php
        }

        public function image_callback($args) {

            // get the value of the setting we've registered with register_setting()
            $options = get_option( $args['page'] );
 
            // output the field

            $fieldValue = $options && $args['id'] && array_key_exists(esc_attr( $args['id'] ), $options)
                                ? $options[ esc_attr( $args['id'] ) ] : '';
            ?>

            <input type="text" id="<?php echo esc_attr( $args['page'] . '[' . $args['id'] . ']' ); ?>"
                name="<?php echo esc_attr($args['page'] . '[' . $args['id'] . ']' ); ?>"
                class="regular-text"
                value="<?php echo esc_attr( $fieldValue ); ?>" />
            <input class="upload_image_button button button-primary" type="button"
                   value="<?php _e('Change Image', 'tishfy-slider'); ?>" />

            <p><img class="slider-img-preview" <?php if ( $fieldValue ) : ?> src="<?php echo esc_attr($fieldValue); ?>" <?php endif; ?> style="max-width:300px;height:auto;" /><p>
<?php         
        }

        public function add_admin_page() {

            add_menu_page( __('Tishfy Slider Settings', 'tishfy-slider'),
                __('Tishfy Slider', 'tishfy-slider'), 'manage_options',
                'tishfy-slider.php', array(&$this, 'show_settings'),
                'dashicons-format-gallery', 6 );

            //call register settings function
            add_action( 'admin_init', array(&$this, 'admin_init_settings') );
        }

        /**
         * Display the settings page.
         */
        public function show_settings() { ?>

            <div class="wrap">
                <div id="icon-options-general" class="icon32"></div>

                <div class="notice notice-info"> 
                    <p><strong><?php _e('Upgrade to Tishfy Slider PRO Plugin', 'tishfy-slider'); ?>:</strong></p>
                    <ul>
                        <li><?php _e('Configure Up to 10 Different Sliders', 'tishfy-slider'); ?></li>
                        <li><?php _e('Insert Up to 10 Slides per Slider', 'tishfy-slider'); ?></li>
                        <li><?php _e('Button Links Options', 'tishfy-slider'); ?></li>
                        <li><?php _e('Color Options', 'tishfy-slider'); ?></li>
                    </ul>
                    <a href="https://tishonator.com/plugins/tishfy-slider" class="button-primary">
                        <?php _e('Upgrade to Tishfy Slider PRO Plugin', 'tishfy-slider'); ?>
                    </a>
                    <p></p>
                </div>

                <h2><?php _e('Tishfy Slider Settings', 'tishfy-slider'); ?></h2>

                <form action="options.php" method="post">
                    <?php settings_fields('tishfy_slider'); ?>
                    <?php do_settings_sections('tishfy_slider'); ?>
                    
                    <h3>
                      Usage
                    </h3>
                    <p>
                        <?php _e('Use the shortcode', 'tishfy-slider'); ?> <code>[tishfy-slider]</code> <?php echo _e( 'to display Slider to any page or post.', 'tishfy-slider' ); ?>
                    </p>
                    <?php submit_button(); ?>
              </form>
            </div>
    <?php
        }
    }

endif; // tishonator_TishfySliderPlugin

add_action('plugins_loaded', array( tishonator_TishfySliderPlugin::get_instance(), 'setup' ), 10);
