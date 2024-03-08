<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Tdmrep
 * @subpackage Tdmrep/public
 * @author     Pierre Vieville <contact@pierrevieville.fr>
 */
class Tdmrep_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        $this->apply_protocol();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tdmrep-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tdmrep-public.js', array( 'jquery' ), $this->version, false );
	}

    /**
	 * Apply protocol and add meta tags rules if needed
	 *
	 * @since    1.0.0
	 */
    public function apply_protocol(): void {
        $protocol_data = new Tdmrep_Protocol_Data();
        $protocol = $protocol_data->get_protocol();

        if ($protocol === Tdmrep_Protocol::SET_VALUE_META) {
            add_action('wp_head', [new Tdmrep_Protocol(), 'add_meta_tags_rules']);
        }
    }
}
