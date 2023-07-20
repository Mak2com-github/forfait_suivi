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
        <div class="provider_body">
            <div class="provider_col_left">
                <div class="provider_forms">
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
                                    <input name="color-1" type="color" value="#000000" required>
                                    <span class="color-btn add-color" onclick="manageColorField(this)">+</span>
                                </div>
                            </div>

                        </div>
                        <input class="custom-plugin-submit" type="submit" name="save_setting" value="Ajouter">
                    </form>
                </div>
            </div>
            <div class="providers_col_right">
                <div class="providers_listing_row">
                    <h3>Liste des prestataires</h3>
                    <table class="">
                        <thead>
                            <tr>
                                <th scope="col">Nom</th>
                                <th scope="col">Description</th>
                                <th scope="col">Couleur</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Data 1</td>
                                <td>Data 2</td>
                                <td>Data 3</td>
                            </tr>
                            <tr>
                                <td>Data 4</td>
                                <td>Data 5</td>
                                <td>Data 6</td>
                            </tr>
                            <tr>
                                <td>Data 7</td>
                                <td>Data 8</td>
                                <td>Data 9</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php
}