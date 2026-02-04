<?php
/*
 * Forfait Actions Forms
 */
?>
<div>
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
                <textarea name="description" id="wysiwygArea" placeholder="Description du forfait" rows="5" required><?php if (!empty($forfait[0]->description)) { echo $forfait[0]->description; } ?></textarea>
            </div>
            <?php wp_nonce_field('update_forfait_action', 'update_forfait_nonce'); ?>
            <input class="custom-plugin-submit" type="submit" name="update_forfait" value="Modifier">
        </form>
    </div>

    <!-- Update Forfait Time Form -->
    <div class="forms-container" id="updateForfaitTimeForm">
        <div class="close-form">
            <button class="closeFormButton">X</button>
        </div>
        <form method="post" action="admin.php?page=forfait_suivi">
            <?php
            $forfaitSeconds = isset($forfait[0]->total_time) ? $DBAction->TimeToSec($forfait[0]->total_time) : null;
            if (is_int($forfaitSeconds) && $forfaitSeconds > 0) :
            ?>
            <div class="form-information">
                <p>Il reste <?= $forfait[0]->total_time ?> sur ce forfait <br>Le temps que vous ajoutez sera additionné au temps restant</p>
            </div>
            <?php elseif (is_int($forfaitSeconds) && $forfaitSeconds < 0) : ?>
            <div class="form-information">
                <p>Forfait en dépassement de <?= $DBAction->SecToTime(abs($forfaitSeconds)) ?> <br>Le temps ajouté réduira d'abord ce dépassement</p>
            </div>
            <?php elseif (is_int($forfaitSeconds) && $forfaitSeconds === 0) : ?>
            <div class="form-information">
                <p>Forfait épuisé. Le temps ajouté repartira de zéro</p>
            </div>
            <?php endif; ?>
            <input type="hidden" name="id" value="<?php if(!empty($forfait[0]->id)) { echo $forfait[0]->id; } ?>">
            <div class="forms-container-fields">
                <label for="total_time">Temps Total <span>FORMAT: 00:00:00</span></label>
                <input name="total_time" type="text" placeholder="HH:MM:SS" required pattern="^([0-9]{1,3}):([0-5][0-9]):([0-5][0-9])$">
            </div>
            <?php wp_nonce_field('update_forfait_time_action', 'update_forfait_time_nonce'); ?>
            <input class="custom-plugin-submit" type="submit" name="update_forfait_time" value="Modifier">
        </form>
    </div>
</div>
