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
                $forfait_table = $wpdb->prefix. "forfait";
                $forfait = $wpdb->get_results("SELECT * FROM $forfait_table");
                $tasksTotalTime = $DBAction->getTimeTotalsForTasks($forfait[0]->id);
                $forfaitTotalTime = $forfait[0]->total_time;

                if ($tasksTotalTime) {
                    $totalForfait = new DateTime($forfaitTotalTime, new DateTimeZone('Europe/Paris'));
                    $totalTasks = new DateTime($tasksTotalTime, new DateTimeZone('Europe/Paris'));

                    $interval = $totalForfait->diff($totalTasks);
                    $interval = $interval->format('%H:%I:%S');

                } else {
                    $interval = $forfaitTotalTime;
                }
                ?>
                <input type="hidden" name="forfait_id" value="<?= $forfait[0]->id ?>">
            </div>
            <div class="forms-container-fields">
                <label id="taskTimeLabel" for="task_time">Durée</label>
                <input name="task_time" type="text" placeholder="HH:MM:SS" required pattern="^([0-9]{2}):([0-5][0-9]):([0-5][0-9])$">
            </div>
            <div class="forms-container-fields">
                <label for="description">Description</label>
                <textarea name="description" placeholder="Description de la tâche" rows="5" required></textarea>
            </div>
            <input id="addTaskSubmit" class="custom-plugin-submit" type="submit" name="save_task" value="Ajouter">
        </form>
    </div>

</div>
