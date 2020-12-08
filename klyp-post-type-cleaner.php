<?php
/**
 * Plugin Name: Klyp Post Type Cleaner
 * Plugin URI: https://github.com/klyp/klyp-post-type-cleaner
 * Description: This plugin allows you to clean your database by deleting obsolete post types.
 * Version: 1.0.0
 * Author: Klyp
 * Author URI: https://klyp.co
 * License: GPL2
 */

// See if wordpress is properly installed
defined( 'ABSPATH' ) || die( 'Wordpress is not installed properly.' );

/**
 * Create menu under settings
 * 
 * @return void
 */
function klyp_post_type_cleaner_menu() {
    add_options_page( 'Klyp Post Type Cleaner', 'Klyp Post Type Cleaner', 'manage_options', 'klyp-post-type-cleaner', 'klyp_post_type_cleaner_options' );
}
add_action( 'admin_menu', 'klyp_post_type_cleaner_menu' );

/**
 * Create the option page
 * 
 * @return void
 */
function klyp_post_type_cleaner_options() {
    $posttypes = get_post_types();
    ?>
    <form method="post" action="<?= admin_url( 'options-general.php?page=klyp-post-type-cleaner' ); ?>">
    <div class="wrap">
        <h2>Klyp Post Type Cleaner</h2>
        <p>Select the post type that you wish to clean below.</p>
        <input type="hidden" name="run" value="true">
        <select id="posttype" name="posttype">
            <option value="">Please select</option>
            <?php
                foreach ($posttypes as $posttype) {
                    echo '<option value="' . $posttype . '">' . $posttype . '</option>';
                }
            ?>
        </select>
        <?= submit_button('Clean'); ?>
        <p>PLEASE DO NOT REFRESH THIS PAGE</p>
    </div>
    </form>
<?php
    if ((isset($_POST['run']) && $_POST['run'] == 'true') && (isset($_POST['posttype']) && $_POST['posttype'] != '')) {
        klyp_post_type_cleaner_clean($_POST['posttype']);
    }
}

/**
 * function to clean custom post types
 */
function klyp_post_type_cleaner_clean($posttype = null) {
    if (! $posttype) {
        return;
    }

    global $wpdb;

    $wpdb->query(
        $wpdb->prepare(
            "
            DELETE a,b,c
            FROM $wpdb->posts a
            LEFT JOIN $wpdb->term_relationships b
            ON (a.ID = b.object_id)
            LEFT JOIN $wpdb->postmeta c
            ON (a.ID = c.post_id)
            WHERE a.post_type = %s
            ", $posttype
        )
    );
    echo '<strong>DONE</strong>';

    return;
}

?>