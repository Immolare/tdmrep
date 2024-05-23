<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html(get_admin_page_title()); ?></h1>
    <a class="page-title-action add-policy-button thickbox">
        <?php esc_html_e('Add New Policy', 'tdmrep'); ?>
    </a>
    <hr class="wp-header-end">
    <br/>
    <br/>

    <?php settings_errors('tdmrep'); ?>

    <?php require_once plugin_dir_path(__FILE__) . 'tdmrep-admin-policy-table-display.php'; ?>
    <br/><br/>
    <?php require_once plugin_dir_path(__FILE__) . 'tdmrep-admin-protocol-form-display.php'; ?>

    <hr/>
    <div>
        <h2><?php esc_html_e('Your Policy', 'tdmrep'); ?></h2>
        <p>
            <?php echo esc_html_e('If you use .well-known method, you can check your generated policy right here :', 'tdmrep'); ?>
            <a title="TDMREP.json" target="_blank" href="<?php echo site_url(). '/.well-known/tdmrep.json'; ?>">tdmrep.json</a>
        </p>

        <p>
            <strong>
                <?php echo esc_html_e('You can also use and customize the following text to add the policies to the legal parts of your website :', 'tdmrep'); ?>
            </strong>
            <br/>
            <br/>
            <pre>
    Parts published on this website are protected by copyright and may not, 
    under any circumstances, be reused without the express permission of their author.

    ---- TODO: List there the protected resources of the website + exceptions if needed, for example : ----

    The images on this site, along with associated data such as title or author's name, 
    may not in any way be subject, in whole or in part, to reproductions in any form, 
    carried out for the purpose of text and data mining, except for those conducted 
    exclusively for scientific research purposes by an entity referred to in Article L.
    122-5-3, II of the Intellectual Property Code.

    ---- END TODO ----
    
    This prohibition, as expressed within the framework provided in Article R. 122-8 of the
    Intellectual Property Code, particularly applies to the use of works to feed or train 
    artificial intelligence systems designed or adapted to generate creations, such as images 
    or audiovisual content, intended for public dissemination.
    
    This prohibition is also expressed, in accordance with the TDMRep protocol accessible at 
    www.w3.org/2022/tdmrep, as follows: <?php echo htmlspecialchars('<TDM-RESERVATION: 1>'); ?>
    

    For any request for reproduction or public communication of a work, please contact <?php echo get_site_url(); ?>.
    </pre>
   
        </p>
    </div>

    <?php add_thickbox(); ?>
    <div id="tdmrep-popup-policy" style="display:none;"></div>
</div>