<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<hr>
<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
    <input type="hidden" name="action" value="tdmrep_save_protocol">
    <?php wp_nonce_field('tdmrep_save_protocol'); ?>
    <h2><?php esc_html_e('Protocol Rendering Style', 'tdmrep'); ?></h2>
    <label for="tdmrep_protocol"><?php esc_html_e('Select rendering style:', 'tdmrep'); ?></label>
    <select name="tdmrep_protocol" id="tdmrep_protocol">
        <option value="header" <?php selected($protocol, Tdmrep_Protocol::SET_VALUE_HEADER); ?>><?php esc_html_e('Header', 'tdmrep'); ?></option>
        <option value="meta" <?php selected($protocol, Tdmrep_Protocol::SET_VALUE_META); ?>><?php esc_html_e('Meta tags', 'tdmrep'); ?></option>
        <option value="well-known" <?php selected($protocol, Tdmrep_Protocol::SET_VALUE_WELL_KNOWN); ?>><?php esc_html_e('well-known/tdmrep.json file', 'tdmrep'); ?></option>
    </select>
    <br>
    <?php submit_button(esc_html__('Save Protocol', 'tdmrep')); ?>
</form>