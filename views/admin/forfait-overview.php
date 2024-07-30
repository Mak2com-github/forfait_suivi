<?php
require_once plugin_dir_path(__FILE__) . '../../includes/class-db-actions.php';

function forfait_overview(): void
{
    global $wpdb;
    $DBAction = new DBActions();
    $forfait_table = $wpdb->prefix. "forfait";
    $forfait = $wpdb->get_results("SELECT * FROM $forfait_table");

    if (!empty($forfait)) {
        $tasksTotalTime = $DBAction->getTimeTotalsForTasks($forfait[0]->id);
        $remainingTime = $DBAction->getForfaitTime($forfait[0]->id);
    }
    ?>
    <div class="forfait-main">
        <div class="head">
            <p id="deleteAlertMessage" class="alert-message">Attention ! La suppression du forfait, entrainera la suppression des tâches qui lui sont associées !</p>
            <p id="updateAlertMessage" class="alert-message">Attention ! La modification du forfait aura pour effet de détacher les tâches de ce forfait </br> Elles seront toujours présentes mais ne seront plus comptabilisées sur ce forfait.</p>
            <?php
            if (isset($_SESSION['create_success'])) :
                echo '<div class="session-msg session-success"><p><i class="fas fa-smile"></i>'.$_SESSION['create_success'].'<i class="fas fa-smile"></i></p></div>';
            elseif (isset($_SESSION['delete_success'])) :
                echo '<div class="session-msg session-success"><p><i class="fas fa-smile"></i>'.$_SESSION['delete_success'].'<i class="fas fa-smile"></i></p></div>';
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
                <button id="infosBtn" class="toggle-infos" title="Afficher les informations">
                    <img src="<?= plugins_url('../assets/img/infos.svg', dirname(__FILE__)); ?>" alt="Afficher les informations">
                </button>
                <h2>Status</h2>
                <div class="status-legend-block">
                    <div class="usable-false"></div>
                    <p>Tâche débitée sur un ancien forfait</p>
                </div>
                <div class="status-legend-block">
                    <div class="usable-true"></div>
                    <p>Tâche débitée sur le forfait en cours</p>
                </div>
            </div>
            <?php
            if (!empty($forfait)) :
                ?>
                <div class="selected-forfait">
                    <?php
                    $title = htmlspecialchars_decode($forfait[0]->title, ENT_QUOTES);
                    $description = htmlspecialchars_decode($forfait[0]->description, ENT_QUOTES);
                    ?>
                    <div class="selected-forfait-head">
                        <h1><?= stripslashes($title) ?></h1>
                        <p class="forfait-description"><?= stripslashes($description) ?></p>
                    </div>
                </div>
            <?php endif; ?>
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
                        <?php elseif (!empty($remainingTime) && $remainingTime <= '01:00:00') : ?>
                            <div class="selected-forfait-alert">
                                <p>Attention !</br> Le temps de ce forfait est bientôt épuisé !</p>
                                <p>Temps Restant : <?= $remainingTime ?></p>
                            </div>
                        <?php endif;
                        $title = htmlspecialchars_decode($forfait[0]->title, ENT_QUOTES);
                        $description = htmlspecialchars_decode($forfait[0]->description, ENT_QUOTES);
                        ?>
                        <div class="selected-forfait-head">
                            <h2>Détails</h2>
                        </div>
                        <div class="selected-forfait-table">
                            <div class="selected-forfait-table-row">
                                <h3>Nombres de tâches attribuées: </h3>
                                <p><?= $DBAction->getTasksNumberByForfait($forfait[0]->id) ?></p>
                            </div>
                            <?php if (isset($tasksTotalTime)) : ?>
                            <div class="selected-forfait-table-row">
                                <h3>Total temps des tâches :</h3>
                                <p><?= $tasksTotalTime ?></p>
                            </div>
                            <?php endif; ?>
                            <div class="selected-forfait-table-row">
                                <h3>Temps Restant:</h3>
                                <p><?= $remainingTime ?></p>
                            </div>
                            <div class="selected-forfait-table-row">
                                <h3>Crée le: </h3>
                                <p><?= $DBAction->getForfaitCreatedAt($forfait[0]->id) ?></p>
                            </div>
                            <div class="selected-forfait-table-row">
                                <h3>Rechargé le: </h3>
                                <p><?= $DBAction->getForfaitUpdatedAt($forfait[0]->id) ?></p>
                            </div>
                            <?php if (render_action_buttons()) : ?>
                            <div class="selected-forfait-table-row forfait-row-actions">
                                <h3>Actions</h3>
                                <div class="forfait-actions">
                                    <div class="action-btn-container">
                                        <button id="updateForfaitBtn" class="update-btn" title="Modifier le forfait">
                                            <img src="<?= plugins_url('../assets/img/update.svg', dirname(__FILE__)); ?>" alt="Modifier">
                                        </button>
                                    </div>
                                    <div class="action-btn-container">
                                        <button id="updateForfaitTimeBtn" class="add-time-btn" title="Ajouter du temps">
                                            <img src="<?= plugins_url('../assets/img/add-time.svg', dirname(__FILE__)); ?>" alt="Ajouter du temps">
                                        </button>
                                    </div>
                                    <form class="action-btn-container" action="" method="POST">
                                        <?php wp_nonce_field('delete_forfait_action', 'delete_forfait_nonce'); ?>
                                        <input type="hidden" name="id" value="<?= $forfait[0]->id ?>">
                                        <button id="deleteBtn" title="Supprimer le forfait" class="delete-btn" type="submit" name="delete_forfait">
                                            <img src="<?= plugins_url('../assets/img/trash.svg', dirname(__FILE__)); ?>" alt="Supprimer">
                                        </button>
                                    </form>
                                    <?php if (!empty($remainingTime) && $remainingTime > '00:00:00') : ?>
                                        <div class="action-btn-container">
                                            <button class="create-btn" onclick="selectForfaitTimeCheck('<?= $remainingTime ?>')" id="addTaskBtn" title="Ajouter une tâche">
                                                <img src="<?= plugins_url('../assets/img/add-task.svg', dirname(__FILE__)); ?>" alt="Ajouter une tache">
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="forfait-instructions">
                        <h2>Instructions</h2>
                        <h3>Cette page permet de suivre les interventions techniques effectuées sur le site du client.</h3>
                        <div class="instructions-content">
                            <p>Voici les actions réalisables pour gérer le forfait :</p>
                            <ul>
                                <li>
                                    <p>Lors de l’installation, il vous sera demandé de créer un forfait. Pour cela, renseignez le Titre du forfait, le temps total du forfait ainsi qu’une description</p>
                                </li>
                                <li>
                                    <p>Vous pourrez en suite ajouter des tâches à ce forfait. Le temps de chaque tâche sera débité du forfait. Lors de l’ajout d’une tâche, renseignez une durée ainsi qu’une description. La tâche nouvellement ajoutée sera inscrite dans le tableau de suivi.</p>
                                </li>
                                <li>
                                    <p>Si l’icône est verte, elle indique que la tâche est ajoutée sur le forfait en cours et a donc déduit du temps sur ce forfait. Si vous la supprimez elle rendra le temps déduit au forfait. </p>
                                </li>
                                <li>
                                    <p>Si l’icône est rouge, elle indique que la tâche est une tâche historique, elle n’est ainsi pas déduite du forfait en cours, et sa suppression n’aura aucune conséquence sur le forfait en cours.</p>
                                </li>
                                <li>
                                    <p>Vous pouvez recharger un forfait à tout moment, même s’il est épuisé. Cependant, <strong>ATTENTION</strong> recharger un forfait aura pour résultat de délier les tâches en ajoutées depuis sa création (ou depuis sa dernière recharge).</p>
                                </li>
                                <li>
                                    <p>Vous pouvez modifier le titre et la description d’un forfait, cela n’a aucune incidence sur le forfait ou les tâches</p>
                                </li>
                                <li>
                                    <p>Vous pouvez aussi supprimer un forfait. <strong>ATTENTION</strong> cela aura pour résultat de supprimer aussi toutes les tâches.</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="tasks-listing">
                    <h3>Liste des Tâches</h3>
                    <?php
                    if (isset($forfait) && !empty($forfait)) :
                    ?>
                    <div class="task-table">
                        <div class="task-table-head">
                            <div class="task-table-head-col">
                                <p class="custom-col">Status</p>
                            </div>
                            <div class="task-table-head-col">
                                <p class="custom-col">Description</p>
                            </div>
                            <div class="task-table-head-col">
                                <p class="custom-col">Durée de la tâche</p>
                            </div>
                            <div class="task-table-head-col">
                                <p class="custom-col">Date de création</p>
                            </div>
                            <?php if (render_action_buttons()) : ?>
                                <div class="task-table-head-col">
                                    <p class="custom-col">Action</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="task-table-body">
                        <?php
                        $tasks = $DBAction->getListTasks($forfait[0]->id);
                        if (!empty($tasks)) :
                            foreach ($tasks as $task) :
                                $task->description = stripslashes($task->description);
                                $task->description = htmlspecialchars_decode($task->description, ENT_QUOTES);
                            ?>
                                <div class="task-table-row <?= $task->forfait_id ?>">
                                    <div class="task-table-row-col">
                                        <?php if ($task->usable === '0') : ?>
                                        <div class="usable-false"></div>
                                        <?php elseif ($task->usable === '1') : ?>
                                        <div class="usable-true"></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="task-table-row-col">
                                        <p><?= $task->description ?></p>
                                    </div>
                                    <div class="task-table-row-col">
                                        <p><?= $task->task_time ?></p>
                                    </div>
                                    <div class="task-table-row-col">
                                        <p><?= $DBAction->getTaskCreatedAt($task->id) ?></p>
                                    </div>
                                    <?php if (render_action_buttons()) : ?>
                                    <div class="task-table-row-col">
                                        <form class="delete-btn-container" action="" method="POST">
                                            <?php wp_nonce_field('delete_task_action', 'delete_task_nonce'); ?>
                                            <input type="hidden" name="id" value="<?= $task->id ?>">
                                            <input type="hidden" name="forfait_id" value="<?= $task->forfait_id ?>">
                                            <input type="hidden" name="time" value="<?= $task->task_time ?>">
                                            <button title="Supprimer la tache" class="delete-btn" type="submit" name="delete_task">
                                                <img src="<?= plugins_url('../assets/img/trash.svg', dirname(__FILE__)); ?>" alt="Supprimer">
                                            </button>
                                        </form>
                                        <button class="update-btn edit-btn" title="Modifier le forfait" data-task-id="<?= $task->id ?>" data-task-time="<?= $task->task_time ?>" data-task-description="<?= $task->description ?>">
                                            <img src="<?= plugins_url('../assets/img/update.svg', dirname(__FILE__)); ?>" alt="Modifier">
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            <?php
                            endforeach;
                        else :
                        ?>
                            <div class="default-table-line">
                                <div></div>
                                <div><p>Aucune Tâche</p></div>
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                        <?php endif; ?>
                        </div>
                    </div>
                    <?php else : ?>
                        <p>Aucun Forfait</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php
    require_once plugin_dir_path(__FILE__) . "../../templates/forfait-forms.php";
    require_once plugin_dir_path(__FILE__) . "../../templates/tasks-forms.php";

    else :
        ?>
        <!-- Add Forfait Form -->
        <div class="forms-container translateY0" id="addForfaitForm">
            <div class="close-form">
                <button class="closeFormButton">X</button>
            </div>
            <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <h2>Ajouter un forfait</h2>
                <div class="forms-container-fields">
                    <label for="title">Nom</label>
                    <input name="title" type="text" placeholder="Titre du forfait" required>
                </div>
                <div class="forms-container-fields">
                    <label for="total_time">Temps Total</label>
                    <input name="total_time" type="text" placeholder="HH:MM:SS" required pattern="^([0-9]{1,3}):([0-5][0-9]):([0-5][0-9])$">
                </div>
                <div class="forms-container-fields">
                    <label for="description">Description</label>
                    <textarea name="description" placeholder="Description du forfait" rows="5" required></textarea>
                </div>
                <?php wp_nonce_field('save_forfait_action', 'save_forfait_nonce'); ?>
                <input class="custom-plugin-submit" type="submit" name="save_forfait" value="Ajouter">
            </form>
        </div>
        <?php
    endif;
}
