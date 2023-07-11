<?php
require_once('inc/class-db-actions.php');
function forfait_settings(): void
{
    ?>
    <div class="fs_settings_page">
        <div class="head-messages">
            <?php
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
            ?>
        </div>
        <h1>Réglages</h1>
        <div class="settings_body">
            <div class="settings_col_left">
                <div class="settings_forms">
                    <h2>Ajout d'un prestataire</h2>
                    <p>Vous pouvez ajouter un prestataire autre que l'agence, qui interviendra sur des modifications qui devront être décompté du forfait</p>
                    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <div class="forms-container-fields">
                            <label for="title">Nom</label>
                            <input name="title" type="text" placeholder="Nom du prestataire" required>
                        </div>
                        <div class="forms-container-fields">
                            <label for="description">Description</label>
                            <textarea name="description" placeholder="Description du forfait" rows="5" required></textarea>
                        </div>
                        <div class="forms-container-fields">
                            <label for="color">Code couleur</label>
                            <div class="form-color-row">
                                <div class="form-color-block">
                                    <input name="color" type="color" value="" required>
                                    <span class="color-btn add-color" onclick="manageColorField(this)">+</span>
                                </div>
                            </div>

                        </div>
                        <input class="custom-plugin-submit" type="submit" name="save_setting" value="Ajouter">
                    </form>
                </div>
            </div>
            <div class="settings_col_right">
                <div class="settings_listing_row">
                    <h3>Liste des prestataires</h3>
                </div>
                <div class="settings_listing_row">
                    <h3>Liste des réglages</h3>
                </div>
            </div>
        </div>
    </div>
    <?php
}