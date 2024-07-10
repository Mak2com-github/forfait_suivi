<?php
/*
 * Tasks Actions Forms
 */
?>
<div>
    <!-- Add Task Form -->
    <div class="forms-container" id="addTaskForm">
        <div class="close-form">
            <button class="closeFormButton">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="post" action="admin.php?page=forfait_suivi">
            <div class="displayNone">
                <?php
                global $wpdb;
                $DBActions = new DBActions();
                $forfait_table = $wpdb->prefix. "forfait";
                $forfait = $wpdb->get_results("SELECT * FROM $forfait_table");
                $forfaitTotalTime = $DBActions->getForfaitTime($forfait[0]->id);
                ?>
                <input type="hidden" name="forfait_id" value="<?= $forfait[0]->id ?>">
            </div>
            <div class="forms-container-fields">
                <label id="taskTimeLabel" for="task_time">Durée <span>MAX: <?= $forfaitTotalTime ?></span></label>
                <input id="task_time" name="task_time" type="text" placeholder="HH:MM:SS" required pattern="^([0-9]{1,3}):([0-5][0-9]):([0-5][0-9])$">
            </div>
            <div class="forms-container-fields">
                <label for="task_description">Description</label>
                <textarea id="task_description" name="description" placeholder="Description de la tâche" rows="5" required></textarea>
            </div>
            <?php wp_nonce_field('save_task_action', 'save_task_nonce'); ?>
            <input id="addTaskSubmit" class="custom-plugin-submit" type="submit" name="save_task" value="Ajouter">
        </form>
    </div>

</div>
