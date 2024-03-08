<?php 
if (empty($policies)) {
    echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__('No policies found.', 'tdmrep') . '</p></div>';
    return;
}
?>

<table class="wp-list-table widefat striped">
    <thead>
        <tr>
            <th><?php echo esc_html__('Location', 'tdmrep'); ?></th>
            <th><?php echo esc_html__('TDM Reservation', 'tdmrep'); ?></th>
            <th><?php echo esc_html__('TDM Policy', 'tdmrep'); ?></th>
            <th><?php echo esc_html__('Actions', 'tdmrep'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($policies as $policy_id => $policy_json) {
            $policy = Tdmrep_Policy::from_json_ld($policy_json);
            $uid = $policy->get_uid();
            $uid_string = $policy->get_uid(true);
            $delete_url = add_query_arg([
                'action' => 'tdmrep_delete_policy',
                'uid' => $uid,
            ], admin_url('admin-post.php'));
            $delete_url = wp_nonce_url($delete_url, 'tdmrep_delete_policy');
            ?>
            <tr>
                <td><?php echo esc_html($policy->get_location()); ?></td>
                <td><?php echo ($policy->get_tdm_reservation() === '1' ? esc_html__('Reserved', 'tdmrep') : esc_html__('Not Reserved', 'tdmrep')); ?></td>
                <td>
                    <?php 
                    $assignments = $policy->get_assigner();
                    $permissions = $policy->get_permissions();
                    if (empty($assignments) && empty($permissions)) {
                        echo '-';
                    } 
                    else {
                        $uid = $policy->get_uid();
                        $resource_url = home_url('wp-json/' . Tdmrep_Admin::POLICY_API_PREFIX . 'policies/' . esc_attr($uid));
                        echo '<a target="_blank" title="'.esc_html__('Show', 'tdmrep').'" href="' . esc_url($resource_url) . '">' . esc_attr($uid) . '</a>';
                    }
                    ?>
                </td>
                <td>
                    <span class="edit">
                        <a class="thickbox edit-policy-button" data-policy-uid="<?php echo esc_attr($uid); ?>" aria-label="<?php esc_attr_e('Edit policy', 'tdmrep'); ?>">
                            <?php echo esc_html__('Edit', 'tdmrep'); ?>
                        </a>
                    </span>
                    | 
                    <span class="trash">
                        <a href="<?php echo esc_url($delete_url); ?>" class="submitdelete" aria-label="<?php esc_attr_e('Move policy to trash', 'tdmrep'); ?>">
                            <?php echo esc_html__('Trash', 'tdmrep'); ?>
                        </a>
                    </span>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>