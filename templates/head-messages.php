<?php
/*
 * Forfait head messages
 */

if (isset($_SESSION['create_success'])) :
    echo '<div class="session-msg session-success"><p>'.$_SESSION['create_success'].'<i class="fas fa-smile"></i></p></div>';
elseif (isset($_SESSION['delete_success'])) :
    echo '<div class="session-msg session-success"><p>'.$_SESSION['delete_success'].'<i class="fas fa-smile"></i></p></div>';
elseif (isset($_SESSION['errors'])) :
    echo '<div class="session-msg session-alert">';
    foreach ($_SESSION['errors'] as $error) :
        echo '<p>'.$error.'</p>';
    endforeach;
    echo '<i class="fas fa-frown"></i></div>';
endif;

