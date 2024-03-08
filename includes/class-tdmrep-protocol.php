<?php
class Tdmrep_Protocol
{
    public const SET_VALUE_WELL_KNOWN = 'well-known';
    public const SET_VALUE_META = 'meta';
    public const SET_VALUE_HEADER = 'header';
    public const FILENAME = 'tdmrep.json';
    private const POLICY_UID_PREFIX = 'tdmrep/v1/policies';

    private array $policies = [];

    public function __construct()
    {
        $policies_data = new Tdmrep_Policy_Data();
        $this->policies = $policies_data->get_policies();
    }

    private function process_policies(callable $callback): bool
    {
        foreach ($this->policies as $policy_json) {
            $policy = Tdmrep_Policy::from_json_ld($policy_json);
            $location = str_replace('*', '.*', $policy->get_location());

            $tdm_reservation = $policy->get_tdm_reservation();
            $policy_data = array(
                "location" => $location,
                "tdm-reservation" => $tdm_reservation
            );

            if (!empty($policy->get_assigner()) || !empty($policy->get_permissions())) {
                $policy_url = home_url('wp-json/' . self::POLICY_UID_PREFIX . '/' . $policy->get_uid());
                $policy_data["tdm-policy"] = $policy_url;
            }

            $callback($policy_data);
        }

        return true;
    }

    public function add_well_known_rules(): bool
    {
        $well_known_dir = trailingslashit(WP_CONTENT_DIR) . '.well-known';
        if (!file_exists($well_known_dir)) {
            wp_mkdir_p($well_known_dir);
        }

        $file = $well_known_dir . '/' . self::FILENAME;
        $policies = [];

        $this->process_policies(function($policy_data) use (&$policies) {
            $policies[] = $policy_data;
        });

        $content = wp_json_encode($policies, JSON_PRETTY_PRINT);
        // Use WP_Filesystem instead of file_put_contents
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once(ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }

        if (!$wp_filesystem->put_contents($file, $content)) {
            error_log("Failed to write to $file");
            return false;
        }

        return true;
    }

    public function add_htaccess_rules(): bool
    {
        $htaccess_file = ABSPATH . '.htaccess';
        $rules = ["<IfModule mod_headers.c>"];

        $this->process_policies(function($policy_data) use (&$rules) {
            $rules[] = "<FilesMatch \"^{$policy_data['location']}$\">";
            $rules[] = "Header set tdm-reservation \"{$policy_data['tdm-reservation']}\"";
            if (isset($policy_data['tdm-policy'])) {
                $rules[] = "Header set tdm-policy \"{$policy_data['tdm-policy']}\"";
            }
            $rules[] = "</FilesMatch>";
        });

        $rules[] = "</IfModule>";

        if (!insert_with_markers($htaccess_file, 'TDMREP', $rules)) {
            error_log("Failed to insert rules into $htaccess_file");
            return false;
        }

        return true;
    }

    public function add_meta_tags_rules(): void
    {
        foreach ($this->policies as $policy_json) {
            $policy = Tdmrep_Policy::from_json_ld($policy_json);
            if ($this->does_location_match($policy->get_location())) {
                echo '<meta name="tdm-reservation" content="' . esc_attr($policy->get_tdm_reservation()) . '">';

                if (!empty($policy->get_assigner()) || !empty($policy->get_permissions())) {
                    $policy_url = home_url('wp-json/' . self::POLICY_UID_PREFIX . '/' . $policy->get_uid());
                    echo '<meta name="tdm-policy" content="' . esc_url($policy_url) . '">';
                }

                break;
            }
        }
    }

    private function does_location_match(string $location): bool
    {
        // Get the current URL
        $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        // Parse the URL and get the path
        $current_path = wp_parse_url($current_url, PHP_URL_PATH);

        // Remove WordPress installation path from the path
        $wp_path = wp_make_link_relative(home_url());
        if ($wp_path !== '/') {
            $current_path = str_replace($wp_path, '', $current_path);
        }

        $location = str_replace('/', '\/', $location);
        $location = str_replace('*', '.*', $location);
        $location = '/^' . $location . '$/';

        return (bool) preg_match($location, $current_path);
    }
}