<?php
/*
 * @link              http://mak2com.fr
 * @since             2.1.5
 * @package           Forfait_Suivi
 *
 * @wordpress-plugin
 * Plugin Name:       Forfait Suivi
 * Plugin URI:        http://mak2com.fr
 * Description:       Permet la création de forfait de suivi des intervention techniques effectués pour le site du client, ainsi que la création et la gestion des tâches effectués.
 * Version:           2.1.5
 * Author:            Mak2com
 * Author URI:        http://mak2com.fr/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       forfait-suivi
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

register_activation_hook(__FILE__, 'create_db');
function create_db() {

    global $wpdb;
    global $forfait_db_version;

    $wpdb_collate = $wpdb->collate;
    $wbdb_charset = $wpdb->charset;
    $table_forfait = $wpdb->prefix.'forfait';
    $table_tasks = $wpdb->prefix.'tasks';

    if ( $wpdb->get_var("SHOW TABLES LIKE '$table_forfait'") != $table_forfait ) {
        $sql_forfait =
            "CREATE TABLE IF NOT EXISTS {$table_forfait} (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, 
            `title` varchar(250) NULL,
            `total_time` time NULL,
            `description` varchar(250) NULL,
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
            task_time time NULL,
            description varchar(500) NULL,
            remaining_time time NULL,
            usable TINYINT NOT NULL DEFAULT 1,
            created_at datetime NULL,
            updated_at datetime NULL,
            FOREIGN KEY (forfait_id) REFERENCES $table_forfait(id)
            ) ENGINE=InnoDB DEFAULT CHARSET {$wbdb_charset} COLLATE {$wpdb_collate}";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $sql_tasks );
    }
}

/** INITIALISATION DU PLUGIN **/
add_action('admin_menu','init_plugin_menu');
function init_plugin_menu(){

    add_menu_page(
        'Forfait Suivi',
        'Forfait Suivi',
        'manage_options',
        'forfait_suivi',
        'forfait_overview',
        'dashicons-calendar-alt',
        3
    );
}

add_action('admin_init', 'dbOperatorFunctions');
function dbOperatorFunctions() {

    if (isset($_POST['save_forfait'])) {
        $DBAction = new DBActions();
        $DBAction->createForfait($_POST);
    }

    if (isset($_POST['delete_forfait'])) {
        $DBAction = new DBActions();
        $DBAction->deleteForfait($_POST['id']);
    }

    if (isset($_POST['update_forfait'])) {
        $DBAction = new DBActions();
        $DBAction->updateForfait($_POST);
    }

    if (isset($_POST['save_task'])) {
        $DBAction = new DBActions();
        $DBAction->createTask($_POST);
    }

    if (isset($_POST['delete_task'])) {
        $DBAction = new DBActions();
        $DBAction->deleteTask($_POST['id']);
    }

    if (isset($_POST['update_task'])) {
        $DBAction = new DBActions();
        $DBAction->updateTask($_POST);
    }

}

define('ROOTDIR', plugin_dir_path(__FILE__));
require_once(ROOTDIR . 'forfait-overview.php');

/** ACTIVATION CSS / JS / BOOTSTRAP **/
add_action('admin_init', 'forfait_admin_js_css');
function forfait_admin_js_css(){
    wp_enqueue_script('font-awesome', 'https://kit.fontawesome.com/5397c1f880.js', null, null, true);
    wp_register_style('Forfait_css', plugins_url('css/admin-forfait.css', __FILE__));
    wp_enqueue_style('Forfait_css');
    wp_enqueue_script('Forfait_js', plugins_url('js/main.js', __FILE__), array('jquery'),'1.0',true);
    wp_enqueue_script('jQuery-Ui', 'https://code.jquery.com/ui/1.12.1/jquery-ui.min.js', null, null, true);
}

add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');
function my_custom_dashboard_widgets() {
    global $wp_meta_boxes;
    wp_add_dashboard_widget('custom_help_widget', 'Forfait Suivi', 'custom_dashboard_help');
}

function custom_dashboard_help() {
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

                $forfaitTasks = $DBAction->getListTasks($forfait->id);
                $tasksTotalTime = $DBAction->getTimeTotalsForTasks($forfait->id);
                $forfaitTotalTime = $forfait->total_time;

                if ($tasksTotalTime) {
                    $totalForfait = new DateTime($forfaitTotalTime, new DateTimeZone('Europe/Paris'));
                    $totalTasks = new DateTime($tasksTotalTime, new DateTimeZone('Europe/Paris'));

                    $interval = $totalForfait->diff($totalTasks);
                    $interval = $interval->format('%H:%I:%S');

                    $totalForfaitDisplay = $totalForfait->format('H:i');
                    $totalTasksDisplay = $totalTasks->format('H:i');
                } else {
                    $interval = $forfaitTotalTime;
                }

            echo '<tr class="overview-tasks">';
                echo '<th>'.$interval.'</th>';
                echo '<th>'.$DBAction->getTasksNumberByForfait($forfait->id).'</th>';
            echo '</tr>';
            endforeach;
        echo '</tbody>';
    echo '</table>';
}
