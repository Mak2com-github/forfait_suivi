<?php

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
}

/** ACTIVATION CSS / JS / BOOTSTRAP **/
add_action('admin_init', 'fs_admin_js_css');
function fs_admin_js_css(): void
{
    wp_enqueue_script('font-awesome', 'https://kit.fontawesome.com/5397c1f880.js', null, null, true);
    wp_register_style('Forfait_css', plugins_url('../assets/css/admin.css', __FILE__));
    wp_enqueue_style('Forfait_css');
    wp_enqueue_script('Forfait_js', plugins_url('../assets/js/main.js', __FILE__), array('jquery'),'1.0',true);
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