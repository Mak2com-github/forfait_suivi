<?php
/*
 * Forfait Actions Forms
 */
?>
<div>
    <?php if (empty($forfait)) : ?>
    <!-- Add Forfait Form -->
    <div class="add-form" id="addForfaitForm">
        <div class="close-form">
            <button class="closeFormButton">X</button>
        </div>
        <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
            <div class="add-form-fields">
                <label for="title">Nom</label>
                <input name="title" type="text" placeholder="Titre du forfait" required>
            </div>
            <div class="add-form-fields">
                <label for="total_time">Temps Total</label>
                <input name="total_time" step="1" type="time" required>
            </div>
            <div class="add-form-fields">
                <label for="description">Description</label>
                <textarea name="description" placeholder="Description du forfait" rows="5" required></textarea>
            </div>
            <input class="custom-plugin-submit" type="submit" name="save_forfait" value="Ajouter">
        </form>
    </div>
    <?php endif; ?>

    <!-- Update Forfait Form -->
    <div class="forms-container" id="updateForfaitForm">
        <div class="close-form">
            <button class="closeFormButton">X</button>
        </div>
        <form method="post" action="admin.php?page=forfait_suivi">
            <input type="hidden" name="id" value="<?php if(!empty($forfait[0]->id)) { echo $forfait[0]->id; } ?>">
            <div class="forms-container-fields">
                <label for="title">Nom</label>
                <input name="title" type="text" value="<?php if(!empty($forfait[0]->title)) { echo $forfait[0]->title; } ?>" placeholder="Titre du forfait" required>
            </div>
            <div class="forms-container-fields">
                <label for="description">Description</label>
                <textarea name="description" placeholder="Description du forfait" rows="5" required><?php if (!empty($forfait[0]->description)) { echo $forfait[0]->description; } ?></textarea>
            </div>
            <input class="custom-plugin-submit" type="submit" name="update_forfait" value="Modifier">
        </form>
    </div>

    <!-- Update Forfait Time Form -->
    <div class="forms-container" id="updateForfaitTimeForm">
        <div class="close-form">
            <button class="closeFormButton">X</button>
        </div>
        <form method="post" action="admin.php?page=forfait_suivi">
            <?php if ($forfait[0]->total_time > "00:00:00") : ?>
            <div class="form-information">
                <p>Il reste <?= $forfait[0]->total_time ?> sur se forfait <br>Le temps que vous ajoutez sera additionné au temps restant</p>
            </div>
            <?php endif; ?>
            <input type="hidden" name="id" value="<?php if(!empty($forfait[0]->id)) { echo $forfait[0]->id; } ?>">
            <div class="forms-container-fields">
                <label for="total_time">Temps Total</label>
                <input name="total_time" type="text" placeholder="HH:MM:SS" required pattern="^([0-9]{2}):([0-5][0-9]):([0-5][0-9])$">
            </div>
            <input class="custom-plugin-submit" type="submit" name="update_forfait_time" value="Modifier">
        </form>
    </div>
</div>
