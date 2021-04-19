<?php

// exit if uninstall constant is not defined
if (!defined('WP_UNINSTALL_PLUGIN')) exit;

// delete database table
global $wpdb;

$table_tasks = $wpdb->prefix.'tasks';
$wpdb->query("DROP TABLE IF EXISTS $table_tasks");


$table_forfait = $wpdb->prefix.'forfait';
$wpdb->query("DROP TABLE IF EXISTS $table_forfait");
