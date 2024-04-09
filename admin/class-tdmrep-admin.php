<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tdmrep
 * @subpackage Tdmrep/admin
 * @author     Pierre Vieville <contact@pierrevieville.fr>
 * @link       https://www.pierrevieville.fr
 * @since      1.0.0
 */
class Tdmrep_Admin
{
    public const POLICY_API_PREFIX = 'tdmrep/v1';

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
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/tdmrep-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_register_script( 'tdmrep-policy-form', plugin_dir_url( __FILE__ ) . 'js/tdmrep-policy-form.js', array( 'jquery' ), $this->version, true );

        wp_localize_script( 'tdmrep-policy-form', 'tdmrep_object', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'wpPath' => wp_make_link_relative(home_url('/')),
            'nonce' => wp_create_nonce('get_policy_form'),
            'i18n' => $this->getI18nArray(),
            'policy' => null,
            'assigner' => null,
            'permission' => array(),
        ));

        wp_enqueue_script( 'tdmrep-policy-form' );
        //wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tdmrep-admin.js', array( 'jquery' ), $this->version, false );
    }

    /**
     * Returns an array of internationalized (i18n) strings.
     * 
     * @return array An associative array where the keys are the original English strings and the values are their translations.
     */
    private function getI18nArray() {
        return array(
            'editPolicy' => __( 'Edit Policy', 'tdmrep' ),
            'addPolicy' => __( 'Add New Policy', 'tdmrep' ),
            'addAssigner' => __( 'Add Assigner', 'tdmrep' ),
            'fn' => __( 'Full Name', 'tdmrep' ),
            'nickname' => __( 'Nickname', 'tdmrep' ),
            'has_email' => __( 'Email', 'tdmrep' ),
            'address' => __( 'Address', 'tdmrep' ),
            'has_address_street' => __( 'Street Address', 'tdmrep' ),
            'has_address_postal-code' => __( 'Postal Code', 'tdmrep' ),
            'has_address_locality' => __( 'Locality', 'tdmrep' ),
            'has_address_country-name' => __( 'Country Name', 'tdmrep' ),
            'has_telephone' => __( 'Telephone', 'tdmrep' ),
            'has_url' => __( 'URL', 'tdmrep' ),
            'addPermission' => __( 'Add Permission', 'tdmrep' ),
            'target' => __( 'Target', 'tdmrep' ),
            'action' => __( 'Action', 'tdmrep' ),
            'duties' => __( 'Duties', 'tdmrep' ),
            'constraints' => __( 'Constraints', 'tdmrep' ),
            'textDataMine' => __( 'Text Data Mine', 'tdmrep' ),
            'noDuty' => __( 'No Duty', 'tdmrep' ),
            'dutyToObtainConsent' => __( 'Duty To Obtain Consent', 'tdmrep' ),
            'dutyToCompensate' => __( 'Duty To Compensate', 'tdmrep' ),
            'noConstraint' => __( 'No Constraint', 'tdmrep' ),
            'research' => __( 'Research', 'tdmrep' ),
            'nonResearch' => __( 'Non Research', 'tdmrep' ),
            'Identity' => __( 'Identity', 'tdmrep' ),
            'Contact Info' => __( 'Contact Info', 'tdmrep' ),
            'Address' => __( 'Address', 'tdmrep' ),
            'delete' => __( 'Delete', 'tdmrep' ),
        );
    }

    /**
     * Adds a menu page to the WordPress admin dashboard.
     * 
     * The page title and menu title are 'TDMRep', the required capability to access the page is 'manage_options', 
     * the menu slug is 'tdmrep', the function to output the content of the page is 'admin_page', 
     * the icon URL is 'dashicons-code-standards', and the position in the menu order is 6.
     */
    public function add_menu() {
        add_menu_page(
            __( 'TDMRep', 'tdmrep' ),
            __( 'TDMRep', 'tdmrep' ),
            'manage_options',
            'tdmrep',
            array( $this, 'admin_page' ),
            'dashicons-code-standards',
            20
        );
    }

    /**
     * Registers the REST routes for policies.
     */
    public function register_policies() {
        register_rest_route(self::POLICY_API_PREFIX, '/policies', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_policies'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route(self::POLICY_API_PREFIX, '/policies/(?P<uid>[a-f0-9\-]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_policy_by_id'),
            'permission_callback' => '__return_true'
        ));
    }

    /**
     * Retrieves all policies.
     * 
     * @param WP_REST_Request $request The REST request.
     * @return WP_REST_Response The REST response.
     */
    public function get_policies(WP_REST_Request $request) {
        $policy_data = new Tdmrep_Policy_Data();
        $policies = $policy_data->get_policies();
    
        if (empty($policies)) {
            return new WP_REST_Response(array('message' => 'No policies found'), 404);
        }
    
        $policies_json_ld = array_map(function($policy) {
            return $policy->to_json_ld(true);
        }, $policies);
    
        return new WP_REST_Response($policies_json_ld, 200);
    }

    /**
     * Retrieves a policy by its ID.
     * 
     * @param WP_REST_Request $request The REST request.
     * @return WP_REST_Response The REST response.
     */
    public function get_policy_by_id(WP_REST_Request $request) {
        $policy_data = new Tdmrep_Policy_Data();
        $uid = $request->get_param('uid');

        if (!$uid) {
            return new WP_REST_Response(array('message' => 'Policy UID not provided'), 400);
        }

        $policy = $policy_data->get_policy($uid);
        if (!$policy) {
            return new WP_REST_Response(array('message' => 'Policy not found'), 404);
        }

        return new WP_REST_Response($policy->to_json_ld(true), 200);
    }

    /**
     * Saves a policy.
     */
    public function save_policy() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'tdmrep'));
        }

        check_admin_referer('tdmrep_save_policy');

        $id = sanitize_text_field($_POST['policy']['uid'] ?? null);

        if (isset($_POST['assigner'])) {
            $assigner = new Tdmrep_Assigner();
            $assigner->set_fn(sanitize_text_field($_POST['assigner']['fn'] ?? ''));
            $assigner->set_nickname(sanitize_text_field($_POST['assigner']['nickname'] ?? ''));
            $assigner->set_has_email(filter_var($_POST['assigner']['has_email'] ?? '', FILTER_SANITIZE_EMAIL));
            $assigner->set_has_telephone(sanitize_text_field($_POST['assigner']['has_telephone'] ?? ''));
            $assigner->set_has_url(filter_var($_POST['assigner']['has_url'] ?? '', FILTER_SANITIZE_URL));
            $assigner->set_has_address([
                'street' => sanitize_text_field($_POST['assigner']['has_address_street'] ?? ''),
                'postal-code' => sanitize_text_field($_POST['assigner']['has_address_postal-code'] ?? ''),
                'locality' => sanitize_text_field($_POST['assigner']['has_address_locality'] ?? ''),
                'country-name' => sanitize_text_field($_POST['assigner']['has_address_country-name'] ?? '')
            ]);
        }
        
        if (isset($_POST['permission'])) {
            $permission = new Tdmrep_Permission();
            $permission->set_target(filter_var($_POST['permission']['target'] ?? '', FILTER_SANITIZE_URL));
            $permission->set_action(sanitize_text_field($_POST['permission']['action'] ?? ''));

            if (isset($_POST['permission']['duties'])) {
                $duty = new Tdmrep_Duty(sanitize_text_field($_POST['permission']['duties'] ?? ''));
                $permission->add_duty($duty);
            }

            if (isset($_POST['permission']['constraints'])) {
                $constraint = new Tdmrep_Constraint(sanitize_text_field($_POST['permission']['constraints'] ?? ''));
                $permission->add_constraint($constraint);
            }
        }

        $policy = new Tdmrep_Policy($id, $assigner, $permission);
        $policy->set_location(sanitize_text_field($_POST['policy']['location'] ?? ''));
        $policy->set_tdm_reservation(sanitize_text_field($_POST['policy']['tdm_reservation'] ?? ''));

        $policy_data = new Tdmrep_Policy_Data();
        if ($id) {
            $policy_data->update_policy($id, $policy);
        } else {
            $policy_data->add_policy($policy);
        }

        $url = add_query_arg([
            'page' => 'tdmrep',
            'message' => 'success',
            '_wpnonce' => wp_create_nonce('tdmrep_admin_page')
        ], admin_url('admin.php'));

        wp_safe_redirect($url);
        exit;
    }

    /**
     * Deletes a policy.
     */
    public function delete_policy() {
        check_admin_referer('tdmrep_delete_policy');
    
        $policy_uid = isset($_GET['uid']) ? sanitize_text_field($_GET['uid']) : null;
    
        if ($policy_uid) {
            $policy_data = new Tdmrep_Policy_Data();
            $policy_data->delete_policy($policy_uid);

            $url = add_query_arg([
                'page' => 'tdmrep',
                'message' => 'deleted',
                '_wpnonce' => wp_create_nonce('tdmrep_admin_page')
            ], admin_url('admin.php'));

            wp_safe_redirect($url);
            exit;
        } else {
            $url = add_query_arg([
                'page' => 'tdmrep',
                'message' => 'error',
                '_wpnonce' => wp_create_nonce('tdmrep_admin_page')
            ], admin_url('admin.php'));

            wp_safe_redirect($url);
            exit;
        }
    }

    public function admin_page(): void
    {
        $policy_data = new Tdmrep_Policy_Data();
        $policies = $policy_data->get_policies();
        $protocol_data = new Tdmrep_Protocol_Data();  
        $protocol = $protocol_data->get_protocol();

        // Check if nonce is set and valid
        $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : null;
        if ($nonce && wp_verify_nonce($nonce, 'tdmrep_admin_page')) {
            $message = sanitize_text_field($_GET['message'] ?? null);
            if ($message === 'success') {
                add_settings_error('tdmrep', 'tdmrep_message', __('Saved successfully', 'tdmrep'), 'updated');
            } elseif ($message === 'deleted') {
                add_settings_error('tdmrep', 'tdmrep_message', __('Deleted successfully', 'tdmrep'), 'updated');
            } elseif ($message === 'error') {
                add_settings_error('tdmrep', 'tdmrep_message', __('An error occurred', 'tdmrep'), 'error');
            }
        }

        require_once plugin_dir_path(__FILE__) . 'partials/tdmrep-admin-display.php';
    }

    /**
     * Retrieves the policy form.
     */
    public function get_policy_form(): void
    {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : null;
        if ($nonce && wp_verify_nonce($nonce, 'get_policy_form')) {
            $policy_uid = sanitize_text_field($_POST['policy_uid'] ?? null);
            $policy_data = $this->get_policy_data($policy_uid);

            $assigner = $policy_data && $policy_data->get_assigner() ? $policy_data->get_assigner()->to_array_or_null() : null;
            $permission = $policy_data && $policy_data->get_first_permission() ? $policy_data->get_first_permission()->to_array_or_null() : null;

            $html = $this->generate_policy_form_html($policy_data);
            
            wp_send_json_success(array(
                'html' => $html,
                'assigner' => $assigner,
                'permission' => $permission,
            ));
        }

        wp_send_json_error('Invalid nonce', 403);
    }

    /**
     * Retrieves the data of a policy.
     * 
     * @param string $policy_uid The policy's ID.
     * @return Tdmrep_Policy|null The policy or null if it doesn't exist.
     */
    private function get_policy_data(string $policy_uid): ?Tdmrep_Policy
    {
        $policy_data = new Tdmrep_Policy_Data();
        $policy = $policy_data->get_policy($policy_uid);

        return $policy ?? new Tdmrep_Policy();
    }

    /**
     * Generates the HTML of the policy form.
     * 
     * @param Tdmrep_Policy $policy The policy.
     * @return string The HTML of the form.
     */
    private function generate_policy_form_html(Tdmrep_Policy $policy): string
    {
        ob_start();
        $policy_form = new Tdmrep_Policy_Form($policy);
        $policy_form->render();
        return ob_get_clean();
    }

    /**
     * Saves a protocol.
     */
    public function save_protocol(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'tdmrep'));
        }

        check_admin_referer('tdmrep_save_protocol');
        $protocol_value = sanitize_text_field($_POST['tdmrep_protocol']);

        $protocol_data = new Tdmrep_Protocol_Data();
        $protocol_data->save_protocol($protocol_value);

        $success = true;
        if ($protocol_value === Tdmrep_Protocol::SET_VALUE_WELL_KNOWN) {
            $protocol = new Tdmrep_Protocol();
            $success = $protocol->add_well_known_rules();
        }
        else if($protocol_value === Tdmrep_Protocol::SET_VALUE_HEADER) {
            $protocol = new Tdmrep_Protocol();
            $success = $protocol->add_htaccess_rules();
        }

        if ($success) {
            $url = add_query_arg([
                'page' => 'tdmrep',
                'message' => 'success',
                '_wpnonce' => wp_create_nonce('tdmrep_admin_page')
            ], admin_url('admin.php'));

            wp_safe_redirect($url);
        } else {
            $url = add_query_arg([
                'page' => 'tdmrep',
                'message' => 'error',
                '_wpnonce' => wp_create_nonce('tdmrep_admin_page')
            ], admin_url('admin.php'));

            wp_safe_redirect($url);
        }
        exit;
    }
}
