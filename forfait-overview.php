<?php
require_once('inc/class-db-actions.php');

function forfait_overview() {
    global $wpdb;
    $DBAction = new DBActions();
    ?>
    <div class="forfait-main">
        <div class="head">
            <p id="deleteAlertMessage" class="delete-alert-message">Attention ! La suppression du forfait, entraineras la suppression des tâches qui lui sont associées !</p>
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
            <h2>Vue Générale</h2>
            <p>Liste de tous les forfaits et taches</p>
            <p class="post-scriptum">Ici vous pouvez ajouter ou supprimer une tâche, et consulter les informations, modifier ou supprimer le forfait</p>
        </div>

        <div>

            <div class="overview-list-container">
                <div class="overview-forfaits-infos">
                    <?php
                    $forfait_table = $wpdb->prefix. "forfait";
                    $forfait = $wpdb->get_results("SELECT * FROM $forfait_table");

                    if (!empty($forfait)) :
                    ?>
                    <div class="selected-forfait-datas">
                        <?php
                        $forfaitTasks = $DBAction->getListTasks($forfait[0]->id);
                        $tasksTotalTime = $DBAction->getTimeTotalsForTasks($forfait[0]->id);
                        $forfaitTotalTime = $forfait[0]->total_time;

                        if ($tasksTotalTime) {
                            $totalForfait = new DateTime($forfaitTotalTime, new DateTimeZone('Europe/Paris'));
                            $totalTasks = new DateTime($tasksTotalTime, new DateTimeZone('Europe/Paris'));

                            $interval = $totalForfait->diff($totalTasks);
                            $interval = $interval->format('%H:%I:%S');

                            $totalForfaitDisplay = $totalForfait->format('H:i:s');
                            $totalTasksDisplay = $totalTasks->format('H:i:s');
                        } else {
                            $interval = $forfaitTotalTime;
                        }
                        ?>
                        <?php if ($interval <= '00:00:00') : ?>
                            <div class="selected-forfait-alert">
                                <p>Forfait épuisé !</p>
                            </div>
                        <?php elseif ($interval <= '00:01:00') : ?>
                            <div class="selected-forfait-alert">
                                <p>Attention !</br> Le temps de ce forfait est bientôt épuisé !</p>
                                <p>Temps Restant : <?= $interval ?></p>
                            </div>
                        <?php endif; ?>
                        <h3>Infos du Forfait</h3>
                        <table class="selected-forfait-table">
                            <tr>
                                <th>Nombres de tâches attribuées: </th>
                                <td><?= $DBAction->getTasksNumberByForfait($forfait[0]->id) ?></td>
                            </tr>
                            <?php if (isset($totalTasksDisplay)) : ?>
                            <tr>
                                <th>Total temps des tâches :</th>
                                <td><?= $totalTasksDisplay ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th>Temps Restant:</th>
                                <td><?= $interval ?></td>
                            </tr>
                            <tr>
                                <th>Crée le: </th>
                                <td><?= $DBAction->getForfaitCreatedAt($forfait[0]->id) ?></td>
                            </tr>
                            <tr>
                                <th>Mis à jour le: </th>
                                <td><?= $DBAction->getForfaitUpdatedAt($forfait[0]->id) ?></td>
                            </tr>
                            <tr>
                                <th>Actions</th>
                                <td>
                                    <div class="update-btn-container">
                                        <a href="admin.php?page=forfait_suivi&id=<?= $forfait[0]->id ?>">
                                            <button class="update-btn">Modifier</button>
                                        </a>
                                    </div>
                                    <form class="delete-btn-container" action="" method="POST">
                                        <input type="hidden" name="id" value="<?= $forfait[0]->id ?>">
                                        <input id="deleteBtn" title="Attention !" class="delete-btn" type="submit" name="delete_forfait" value="Supprimer">
                                    </form>
                                    <div class="create-btn-container">
                                        <button class="create-btn" onclick="selectForfaitTimeCheck('<?= $interval ?>')" id="addTask">Ajouter une Tâche</button>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php
                    endif;
                    if (empty($forfait)) :
                    ?>
                        <div class="create-forfait-form">
                            <h2>Ajouter un forfait</h2>
                            <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                                <div class="add-form-fields">
                                    <label for="title">Nom</label>
                                    <input name="title" type="text" placeholder="Titre du forfait" required>
                                </div>
                                <div class="add-form-fields">
                                    <label for="total_time">Temps Total</label>
                                    <input name="total_time" type="time" step="15" required>
                                </div>
                                <div class="add-form-fields">
                                    <label for="description">Description</label>
                                    <textarea name="description" placeholder="Description du forfait" rows="5" required></textarea>
                                </div>
                                <input class="custom-plugin-submit" type="submit" name="save_forfait" value="Ajouter">
                            </form>
                        </div>
                    <?php
                    endif;
                    ?>
                </div>

                <!-- Add Task Form -->
                <div class="add-form" id="addTaskForm">
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
                            <input type="hidden" name="remaining_time" value="<?= $interval ?>">
                            <input type="hidden" name="forfait_id" value="<?= $forfait[0]->id ?>">
                        </div>
                        <div class="add-form-fields">
                            <label for="title">Nom</label>
                            <input name="title" type="text" placeholder="Titre de la tâche" required>
                        </div>
                        <div class="add-form-fields">
                            <label id="taskTimeLabel" for="task_time">Durée</label>
                            <input id="taskTimeInput" name="task_time" step="1" type="time" required>
                        </div>
                        <div class="add-form-fields">
                            <label for="description">Description</label>
                            <textarea name="description" placeholder="Description de la tâche" rows="5" required></textarea>
                        </div>
                        <input id="addTaskSubmit" class="custom-plugin-submit" type="submit" name="save_task" value="Ajouter">
                    </form>
                </div>

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

                <!-- Update Forfait Form -->
                <?php
                if (isset($_GET['id'])) :
                    $forfait = $DBAction->getForfaitByID($_GET["id"]);
                    ?>
                    <div class="add-form displayBlock" id="updateForfaitForm">
                        <div class="close-form">
                            <button class="closeFormButton">X</button>
                        </div>
                        <form method="post" action="admin.php?page=forfait_suivi">
                            <input type="hidden" name="id" value="<?php if(!empty($forfait[0]->id)) { echo $forfait[0]->id; } ?>">
                            <div class="add-form-fields">
                                <label for="title">Nom</label>
                                <input name="title" type="text" value="<?php if(!empty($forfait[0]->title)) { echo $forfait[0]->title; } ?>" placeholder="Titre du forfait" required>
                            </div>
                            <div class="add-form-fields">
                                <label for="total_time">Temps à ajouter</label>
                                <input name="total_time" type="time" step="1" required>
                            </div>
                            <div class="add-form-fields">
                                <label for="description">Description</label>
                                <textarea name="description" placeholder="Description du forfait" rows="5" required><?php if (!empty($forfait[0]->description)) { echo $forfait[0]->description; } ?></textarea>
                            </div>
                            <input class="custom-plugin-submit" type="submit" name="update_forfait" value="Modifier">
                        </form>
                    </div>
                <?php endif; ?>

                <div class="tasks-listing">
                    <h3>Liste des Tâches</h3>
                    <?php
                    if (!empty($forfait)) :
                    ?>
                    <table class="custom-table-overview">
                        <thead>
                        <tr>
                            <th class="custom-col">ID</th>
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
                                <th scope="row"><?= $task->id ?></th>
                                <th><?= $task->description ?></th>
                                <th><?= $task->task_time ?></th>
                                <th><?= $DBAction->getTaskCreatedAt($task->id) ?></th>
                                <th>
                                    <form class="delete-btn-container" action="" method="POST">
                                        <input type="hidden" name="id" value="<?= $task->id ?>">
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
}
