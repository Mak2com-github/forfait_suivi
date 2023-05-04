<?php
require_once('inc/class-db-actions.php');

function forfait_overview() {
    global $wpdb;
    $DBAction = new DBActions();
    $forfait_table = $wpdb->prefix. "forfait";
    $forfait = $wpdb->get_results("SELECT * FROM $forfait_table");

    if (!empty($forfait)) {
        $tasksTotalTime = $DBAction->getTimeTotalsForTasks($forfait[0]->id);
        $remainingTime = $forfait[0]->total_time;
        $remainingTime = $DBAction->getForfaitTime($forfait[0]->id);
    }
    ?>
    <div class="forfait-main">
        <div class="head">
            <p id="deleteAlertMessage" class="alert-message">Attention ! La suppression du forfait, entrainera la suppression des tâches qui lui sont associées !</p>
            <p id="updateAlertMessage" class="alert-message">Attention ! La modification du forfait aura pour effet de détacher les tâches de ce forfait </br> Elles seront toujours présentes mais ne seront plus comptabilisées sur ce forfait.</p>
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
        <div class="overview-head">
            <div class="status-legend-main">
                <h3>Status</h3>
                <div class="status-legend-block">
                    <div class="usable-false"></div>
                    <p>Tâche débitée sur un ancien forfait</p>
                </div>
                <div class="status-legend-block">
                    <div class="usable-true"></div>
                    <p>Tâche débitée sur le forfait en cours</p>
                </div>
            </div>
            <div class="head-title">
                <h2>Vue Générale</h2>
                <p>Liste de tous les forfaits et taches</p>
                <p class="post-scriptum">Ici vous pouvez ajouter ou supprimer une tâche, et consulter les informations, modifier ou supprimer le forfait</p>
            </div>
        </div>

        <div>
            <div class="overview-list-container">
                <div class="overview-forfaits-infos">
                    <?php
                    if (!empty($forfait)) :
                    ?>
                    <div class="selected-forfait-datas">
                        <?php if (!empty($remainingTime) && $remainingTime <= '00:00:00') : ?>
                            <div class="selected-forfait-alert">
                                <p>Forfait épuisé !</p>
                            </div>
                        <?php elseif (!empty($remainingTime) && $remainingTime <= '00:01:00') : ?>
                            <div class="selected-forfait-alert">
                                <p>Attention !</br> Le temps de ce forfait est bientôt épuisé !</p>
                                <p>Temps Restant : <?= $tasksTotalTime ?></p>
                            </div>
                        <?php endif; ?>
                        <h3><?= $forfait[0]->title ?></h3>
                        <p class="forfait-description"><?= $forfait[0]->description ?></p>
                        <table class="selected-forfait-table">
                            <tr>
                                <th>Nombres de tâches attribuées: </th>
                                <td><?= $DBAction->getTasksNumberByForfait($forfait[0]->id) ?></td>
                            </tr>
                            <?php if (isset($tasksTotalTime)) : ?>
                            <tr>
                                <th>Total temps des tâches :</th>
                                <td><?= $tasksTotalTime ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th>Temps Restant:</th>
                                <td><?= $remainingTime ?></td>
                            </tr>
                            <tr>
                                <th>Crée le: </th>
                                <td><?= $DBAction->getForfaitCreatedAt($forfait[0]->id) ?></td>
                            </tr>
                            <tr>
                                <th>Rechargé le: </th>
                                <td><?= $DBAction->getForfaitUpdatedAt($forfait[0]->id) ?></td>
                            </tr>
                            <tr>
                                <th>Actions</th>
                                <td>
                                    <div class="update-btn-container">
                                        <button id="updateForfaitBtn" class="update-btn">Modifier</button>
                                    </div>
                                    <div class="update-btn-container">
                                        <button id="updateForfaitTimeBtn" class="update-btn">Ajouter du temps</button>
                                    </div>
                                    <form class="delete-btn-container" action="" method="POST">
                                        <input type="hidden" name="id" value="<?= $forfait[0]->id ?>">
                                        <input id="deleteBtn" title="Attention !" class="delete-btn" type="submit" name="delete_forfait" value="Supprimer">
                                    </form>
                                    <?php if (!empty($remainingTime) && $remainingTime > '00:00:00') : ?>
                                        <div class="create-btn-container">
                                            <button class="create-btn" onclick="selectForfaitTimeCheck('<?= $remainingTime ?>')" id="addTaskBtn">Ajouter une Tâche</button>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <?php
                include "templates/forfait-forms.php";
                include "templates/tasks-forms.php";
                ?>

                <div class="tasks-listing">
                    <h3>Liste des Tâches</h3>
                    <?php
                    if (isset($forfait) && !empty($forfait)) :
                    ?>
                    <table class="custom-table-overview">
                        <thead>
                        <tr>
                            <th class="custom-col">Status</th>
                            <th class="custom-col">Description</th>
                            <th class="custom-col">Durée de la tâche</th>
                            <th class="custom-col">Date de création</th>
                            <th class="custom-col">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $tasks = $DBAction->getListTasks($forfait[0]->id);
                        if (!empty($tasks)) :
                            foreach ($tasks as $task) :
                            ?>
                                <tr class="overview-tasks <?= $task->forfait_id ?>">
                                    <th scope="row">
                                        <?php if ($task->usable === '0') : ?>
                                        <div class="usable-false"></div>
                                        <?php elseif ($task->usable === '1') : ?>
                                        <div class="usable-true"></div>
                                        <?php endif; ?>
                                    </th>
                                    <th><?= $task->description ?></th>
                                    <th><?= $task->task_time ?></th>
                                    <th><?= $DBAction->getTaskCreatedAt($task->id) ?></th>
                                    <th>
                                        <form class="delete-btn-container" action="" method="POST">
                                            <input type="hidden" name="id" value="<?= $task->id ?>">
                                            <input type="hidden" name="forfait_id" value="<?= $task->forfait_id ?>">
                                            <input type="hidden" name="time" value="<?= $task->task_time ?>">
                                            <input class="delete-btn" type="submit" name="delete_task" value="Supprimer">
                                        </form>
                                    </th>
                                </tr>
                            <?php
                            endforeach;
                        else :
                        ?>
                            <tr>
                                <th>*</th>
                                <th>Aucune Tâche</th>
                                <th>**:**:**</th>
                                <th>**:**:**</th>
                                <th>**:**:**</th>
                                <th>*</th>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                    <?php else : ?>
                        <p>Aucun Forfait</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <?php
    endif;
}
