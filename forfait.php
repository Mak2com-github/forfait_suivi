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
 * Version:           3.2.1
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

define('ROOTDIR', plugin_dir_path(__FILE__));

require_once plugin_dir_path(__FILE__) . 'includes/class-db-actions.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions.php';
require_once plugin_dir_path(__FILE__) . 'views/admin/forfait-overview.php';

register_activation_hook(__FILE__, 'fs_create_db');
function fs_create_db(): void
{
    global $wpdb;

    $wpdb_collate = $wpdb->collate;
    $wbdb_charset = $wpdb->charset;
    $table_forfait = $wpdb->prefix.'forfait';
    $table_tasks = $wpdb->prefix.'tasks';

    $sql_forfait = "CREATE TABLE IF NOT EXISTS {$table_forfait} (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, 
        `title` varchar(250) NOT NULL,
        `total_time` time NOT NULL,
        `description` varchar(250) NOT NULL,
        `created_at` datetime NULL,
        `updated_at` datetime NULL 
    ) ENGINE=InnoDB DEFAULT CHARSET `$wbdb_charset` COLLATE `$wpdb_collate`";

    $sql_tasks = "CREATE TABLE IF NOT EXISTS {$table_tasks} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        forfait_id BIGINT UNSIGNED NOT NULL,
        task_time time NOT NULL,
        description varchar(500) NULL,
        usable TINYINT NULL,
        created_at datetime NULL,
        updated_at datetime NULL,
        FOREIGN KEY (forfait_id) REFERENCES $table_forfait(id)
    ) ENGINE=InnoDB DEFAULT CHARSET {$wbdb_charset} COLLATE {$wpdb_collate}";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_forfait);
    dbDelta($sql_tasks);
}

add_action('admin_init', 'fs_dbOperatorFunctions');
function fs_dbOperatorFunctions(): void
{
    $DBAction = new DBActions();

    if (isset($_POST['save_forfait'])) {
        check_admin_referer('save_forfait_action', 'save_forfait_nonce');
        $DBAction->createForfait($_POST);
    }
    if (isset($_POST['update_forfait'])) {
        check_admin_referer('update_forfait_action', 'update_forfait_nonce');
        $DBAction->updateForfait($_POST);
    }
    if (isset($_POST['update_forfait_time'])) {
        check_admin_referer('update_forfait_time_action', 'update_forfait_time_nonce');
        $DBAction->updateForfaitTime($_POST);
    }
    if (isset($_POST['delete_forfait'])) {
        check_admin_referer('delete_forfait_action', 'delete_forfait_nonce');
        $DBAction->deleteForfait($_POST['id']);
    }
    if (isset($_POST['save_task'])) {
        check_admin_referer('save_task_action', 'save_task_nonce');
        $DBAction->createTask($_POST);
    }
    if (isset($_POST['delete_task'])) {
        check_admin_referer('delete_task_action', 'delete_task_nonce');
        $DBAction->deleteTask($_POST['id'], $_POST['forfait_id'], $_POST['time']);
    }
    if (isset($_POST['edit_task'])) {
        check_admin_referer('edit_task_action', 'edit_task_nonce');
        $DBAction->updateTask($_POST['task_id'], $_POST['description'], $_POST['task_time']);
    }
}

add_action('admin_post_edit_task', 'handle_edit_task');
add_action('admin_enqueue_scripts', 'fs_admin_js_css');
add_action('wp_dashboard_setup', 'fs_custom_dashboard_widgets');