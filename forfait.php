<?php
/*
 * @link              https://mak2com.fr
 * @since             2.1.6
 * @package           Forfait_Suivi
 *
 * @wordpress-plugin
 * Plugin Name:       Forfait Suivi
 * Plugin URI:        https://mak2com.fr
 * Description:       Permet la création de forfait de suivi des intervention techniques effectués pour le site du client, ainsi que la création et la gestion des tâches effectués.
 * Version:           3.1.8
 * Author:            Alexandre Celier
 * Author URI:        https://mak2com.fr/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       forfait-suivi
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

register_activation_hook(__FILE__, 'fs_create_db');
function fs_create_db(): void
{
    global $wpdb;

    $wpdb_collate = $wpdb->collate;
    $wbdb_charset = $wpdb->charset;
    $table_forfait = $wpdb->prefix.'forfait';
    $table_tasks = $wpdb->prefix.'tasks';
    $table_settings = $wpdb->prefix.'fs_settings';

    if ( $wpdb->get_var("SHOW TABLES LIKE '$table_forfait'") != $table_forfait ) {
        $sql_forfait =
            "CREATE TABLE IF NOT EXISTS {$table_forfait} (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, 
            `title` varchar(250) NOT NULL,
            `total_time` time NOT NULL,
            `description` varchar(250) NOT NULL,
            `created_at` datetime NULL,
            `updated_at` datetime NULL
            ) ENGINE=InnoDB DEFAULT CHARSET `$wbdb_charset` COLLATE `$wpdb_collate`";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $sql_forfait );
    }

    if ( $wpdb->get_var("SHOW TABLES LIKE '$table_tasks'") != $table_tasks ) {
        $sql_tasks =
            "CREATE TABLE IF NOT EXISTS {$table_tasks} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            forfait_id BIGINT UNSIGNED NOT NULL,
            task_time time NOT NULL,
            description varchar(500) NULL,
            usable TINYINT NULL,
            is_pp TINYINT NOT NULL DEFAULT 0,
            created_at datetime NULL,
            updated_at datetime NULL,
            FOREIGN KEY (forfait_id) REFERENCES $table_forfait(id)
            ) ENGINE=InnoDB DEFAULT CHARSET {$wbdb_charset} COLLATE {$wpdb_collate}";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $sql_tasks );
    }

    if ( $wpdb->get_var("SHOW TABLES LIKE '$table_settings'") != $table_settings ) {
        $sql_settings =
            "CREATE TABLE IF NOT EXISTS {$table_settings} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            key varchar(250) NULL,
            value varchar(500) NULL,
            created_at datetime NULL,
            updated_at datetime NULL,
            ) ENGINE=InnoDB DEFAULT CHARSET {$wbdb_charset} COLLATE {$wpdb_collate}";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $sql_settings );
    }
}

function fs_db_update(): void
{
    global $wpdb;
    $wpdb_collate = $wpdb->collate;
    $wbdb_charset = $wpdb->charset;
    $table_tasks = $wpdb->prefix.'tasks';

    $column_pp_exists = $wpdb->get_col("DESCRIBE $table_tasks is_pp");

    if (empty($column_exists)) {
        $sql = "ALTER TABLE $table_tasks ADD COLUMN is_pp TINYINT NOT NULL DEFAULT 0 AFTER usable";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

// Retrieve the plugin version from the plugin header
function get_plugin_version() {
    $plugin_data = get_plugin_data( __FILE__ );
    return $plugin_data['Version'];
}
// Get the current plugin version
$current_version = get_plugin_version();
// Compare the current version and execute a function based on the version
if (version_compare($current_version, '3.1.7', '>=')) {
    // Code to execute for version 1.0.0 and above
    fs_db_update();
} else {
    return;
}

/** INITIALISATION DU PLUGIN **/
add_action('admin_menu','fs_init_plugin_menu');
function fs_init_plugin_menu(): void
{

    add_menu_page(
        'Forfait Suivi',
        'Forfait Suivi',
        'manage_options',
        'forfait_suivi',
        'forfait_overview',
        'dashicons-calendar-alt',
        3
    );
    add_submenu_page(
        'forfait_suivi',
        'Réglages',
        'Réglages',
        'manage_options',
        'forfait_settings',
        'forfait_settings',
        1,
    );
}

add_action('admin_init', 'fs_dbOperatorFunctions');
function fs_dbOperatorFunctions(): void
{
    $DBAction = new DBActions();

    if (isset($_POST['save_forfait'])) {
        $DBAction->createForfait($_POST);
    }
    if (isset($_POST['update_forfait'])) {
        $DBAction->updateForfait($_POST);
    }
    if (isset($_POST['update_forfait_time'])) {
        $DBAction->updateForfaitTime($_POST);
    }
    if (isset($_POST['delete_forfait'])) {
        $DBAction->deleteForfait($_POST['id']);
    }
    if (isset($_POST['save_task'])) {
        $DBAction->createTask($_POST);
    }
    if (isset($_POST['delete_task'])) {
        $DBAction->deleteTask($_POST['id'], $_POST['forfait_id'], $_POST['time']);
    }
}

define('ROOTDIR', plugin_dir_path(__FILE__));
require_once(ROOTDIR . 'forfait-overview.php');
require_once(ROOTDIR . 'forfait-settings.php');

/** ACTIVATION CSS / JS / BOOTSTRAP **/
add_action('admin_init', 'fs_admin_js_css');
function fs_admin_js_css(): void
{
    wp_enqueue_script('font-awesome', 'https://kit.fontawesome.com/5397c1f880.js', null, null, true);
    wp_register_style('Forfait_css', plugins_url('css/admin.css', __FILE__));
    wp_enqueue_style('Forfait_css');
    wp_enqueue_script('Forfait_js', plugins_url('js/main.js', __FILE__), array('jquery'),'1.0',true);
    wp_enqueue_script('jQuery-Ui', 'https://code.jquery.com/ui/1.12.1/jquery-ui.min.js', null, null, true);
}

add_action('wp_dashboard_setup', 'fs_custom_dashboard_widgets');
function fs_custom_dashboard_widgets(): void
{
    global $wp_meta_boxes;
    wp_add_dashboard_widget('custom_help_widget', 'Forfait Suivi', 'fs_custom_dashboard_help');
}

function fs_custom_dashboard_help(): void
{
    echo '<p>Prévisualisation des forfaits de suivi : </p>';
    $DBAction = new DBActions();
    $forfaits = $DBAction->getListForfaits();

    echo '<table class="custom-table-widget">';
    echo '<thead>';
    echo '<tr>';
    echo '<th class="custom-col">Temps Restant</th>';
    echo '<th class="custom-col">Tâches Attribuées</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach ($forfaits as $forfait) :
        $forfaitTotalTime = $DBAction->getForfaitTime($forfait->id);
        echo '<tr class="overview-tasks">';
        echo '<th>'.$forfaitTotalTime.'</th>';
        echo '<th>'.$DBAction->getTasksNumberByForfait($forfait->id).'</th>';
        echo '</tr>';
    endforeach;
    echo '</tbody>';
    echo '</table>';
}
