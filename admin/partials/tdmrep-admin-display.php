<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html(get_admin_page_title()); ?></h1>
    <a class="page-title-action add-policy-button thickbox">
        <?php esc_html_e('Add New Policy', 'tdmrep'); ?>
    </a>
    <hr class="wp-header-end">

    <?php settings_errors('tdmrep'); ?>

    <?php
        require_once plugin_dir_path(__FILE__) . 'tdmrep-admin-policy-table-display.php';
        require_once plugin_dir_path(__FILE__) . 'tdmrep-admin-protocol-form-display.php';
    ?>

    <?php add_thickbox(); ?>
    <div id="tdmrep-popup-policy" style="display:none;"></div>
</div>