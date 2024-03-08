<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.pierrevieville.fr
 * @since      1.0.0
 *
 * @package    Tdmrep
 * @subpackage Tdmrep/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Tdmrep
 * @subpackage Tdmrep/includes
 * @author     Pierre Vieville <contact@pierrevieville.fr>
 */
class Tdmrep {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Tdmrep_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'TDMREP_VERSION' ) ) {
			$this->version = TDMREP_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'tdmrep';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Tdmrep_Loader. Orchestrates the hooks of the plugin.
	 * - Tdmrep_i18n. Defines internationalization functionality.
	 * - Tdmrep_Admin. Defines all hooks for the admin area.
	 * - Tdmrep_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tdmrep-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tdmrep-i18n.php';

        /**
		 * The class responsible for saving functionality
		 * of the plugin.
		 */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tdmrep-policy-data.php';

        /**
		 * The class responsible for saving functionality
		 * of the plugin.
		 */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tdmrep-protocol-data.php';

        /**
		 * The class responsible for defining protocol functionality
		 * of the plugin.
		 */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tdmrep-protocol.php';

        /**
		 * The class responsible for defining policy functionality
		 * of the plugin.
		 */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tdmrep-policy.php';

        /**
		 * The class responsible for defining policy/assigner functionality
		 * of the plugin.
		 */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tdmrep-assigner.php';

        /**
		 * The class responsible for defining policy/permission functionality
		 * of the plugin.
		 */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tdmrep-permission.php';

        /**
		 * The class responsible for defining policy/permission/constraint functionality
		 * of the plugin.
		 */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tdmrep-constraint.php';
        
        /**
		 * The class responsible for defining policy/permission/duty functionality
		 * of the plugin.
		 */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tdmrep-duty.php';
        
        /**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tdmrep-policy-form.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tdmrep-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-tdmrep-public.php';

		$this->loader = new Tdmrep_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Tdmrep_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Tdmrep_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Tdmrep_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu' );
		$this->loader->add_action( 'admin_post_tdmrep_save_policy', $plugin_admin, 'save_policy' );

		$this->loader->add_action( 'admin_post_tdmrep_delete_policy', $plugin_admin, 'delete_policy' );
        $this->loader->add_action( 'admin_post_nopriv_tdmrep_delete_policy', $plugin_admin, 'delete_policy' );

        $this->loader->add_action('wp_ajax_get_policy_form', $plugin_admin, 'get_policy_form');
        $this->loader->add_action('wp_ajax_nopriv_get_policy_form', $plugin_admin, 'get_policy_form');

        $this->loader->add_action('admin_post_tdmrep_delete_policy', $plugin_admin, 'delete_policy');

        $this->loader->add_action('rest_api_init', $plugin_admin, 'register_policies');

        $this->loader->add_action('admin_post_tdmrep_save_protocol', $plugin_admin, 'save_protocol');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Tdmrep_Public( $this->get_plugin_name(), $this->get_version() );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Tdmrep_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
