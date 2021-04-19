<?php
/*
 * INDEX
 *
 * 1. FORFAIT CRUD
 *      - createForfait (create a forfait)
 *      - deleteForfait (delete a forfait)
 *      - updateForfait (update a forfait)
 *
 * 2. TASKS CRUD
 *      - createTask (create a task)
 *      - deleteTask (delete a task)
 *      - updateTask (update a task)
 *
 * 3. SPECIFIC ACTIONS
 *      - getForfaitTitleByID       (get forfait title by forfait id)
 *      - getTasksNumberByForfait   (get number of tasks for a forfait, by forfait id)
 *      - getListTasks              (get all the tasks)
 *      - getListForfaits           (get all the forfaits)
 *      - getForfaitByID            (get a forfait by forfait id)
 */
class DBActions
{
    /*
     * CREATE A FORFAIT
     */
    public function createForfait($datas) {
        global $wpdb;

        $forfait_table = $wpdb->prefix. "forfait";

        if (empty($datas['title'])) {
            $errors['title'] = 'Le titre est vide';
        }
        if (empty($datas['total_time'])) {
            $errors['total_time'] = 'Le temps total est vide';
        }
        if (empty($datas['description'])) {
            $errors['description'] = 'La description est vide';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
        } else {
            // Nettoyer les données contre les injections XSS
            $title = strip_tags($datas['title']);
            $total_time = strip_tags($datas['total_time']);
            $description = htmlspecialchars($datas['description']);
            $created_at = date('Y-m-d H:i:s', time());
            $updated_at = date('Y-m-d H:i:s', time());

            // Prépare la requete
            $sql = $wpdb->prepare(
                "INSERT INTO {$forfait_table}
                        (title, total_time, description, created_at, updated_at) VALUES (%s,%s,%s,%s,%s )",
                $title,
                $total_time,
                $description,
                $created_at,
                $updated_at
            );
            // Execution de la requete
            $wpdb->query($sql);
            // Redirection sur url
            $_SESSION['create_success'] = "Forfait Ajouté ! ";
        }
    }

    /*
     * DELETE A FORFAIT
     */
    public function deleteForfait($id) {
        global $wpdb;

        $table_forfait = $wpdb->prefix.'forfait';
        $table_tasks = $wpdb->prefix.'tasks';

        // Nettoyer les données contre les injections XSS
        $id = strip_tags($id);

        // Préparation de la requête
        $sqlTasks = "DELETE FROM ".$table_tasks." WHERE forfait_id={$id}";
        $sqlForfait = "DELETE FROM ".$table_forfait." WHERE id={$id}";

        // Execution de la requete
        $wpdb->query($sqlTasks);
        $wpdb->query($sqlForfait);

        // Session message
        $_SESSION['delete_success'] = "Forfait Supprimé ! ";
    }

    /*
     * UPDATE A FORFAIT
     */
    public function updateForfait($datas) {
        global $wpdb;
        $table_forfait = $wpdb->prefix.'forfait';
        $table_tasks = $wpdb->prefix.'tasks';

        if (empty($datas['title'])) {
            $errors['title'] = 'Le titre est vide';
        }
        if (empty($datas['total_time'])) {
            $errors['total_time'] = 'Le temps total est vide';
        }
        if (empty($datas['description'])) {
            $errors['description'] = 'La description est vide';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
        } else {
            // Nettoyer les données contre les injections XSS
            $id = strip_tags($datas['id']);
            $title = strip_tags($datas['title']);
            $totalTime = strip_tags($datas['total_time']);
            $description = htmlspecialchars($datas['description']);
            $updated_at = date('Y-m-d H:i:s', time());

            // Préparation de la requête
            $sql = "UPDATE $table_forfait SET 
                 title='$title', 
                 total_time='$totalTime', 
                 description='$description', 
                 updated_at='$updated_at' 
                WHERE id=$id";
            // Execution de la requete
            $wpdb->query($sql);

            // Préparation de la requête
            $sql2 = "UPDATE $table_tasks SET 
                 usable=0";
            // Execution de la requete
            $wpdb->query($sql2);

            // Session message
            $_SESSION['update_success'] = "Forfait Modifié ! ";
        }
    }

    /*
     * CREATE A TASK
     */
    public function createTask($datas) {
        global $wpdb;

        $tasks_table = $wpdb->prefix. "tasks";

        if (empty($datas['task_time'])) {
            $errors['task_time'] = 'Le temps total est vide';
        }
        if (empty($datas['description'])) {
            $errors['description'] = 'La description est vide';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
        } else {
            // Nettoyer les données contre les injections XSS
            $forfait_id = $datas['forfait_id'];
            $remaining_time = $datas['remaining_time'];
            $total_time = strip_tags($datas['task_time']);
            $description = htmlspecialchars($datas['description']);
            $created_at = date('Y-m-d H:i:s', time());
            $updated_at = date('Y-m-d H:i:s', time());

            // Prépare la requete
            $sql = $wpdb->prepare(
                "INSERT INTO {$tasks_table}
                        (forfait_id, task_time, description, remaining_time, created_at, updated_at) VALUES (%d,%s,%s,%s,%s,%s )",
                $forfait_id,
                $total_time,
                $description,
                $remaining_time,
                $created_at,
                $updated_at
            );

            // Execution de la requete
            $wpdb->query($sql);
            // Redirection sur url
            $_SESSION['create_success'] = 'Tâche Ajoutée ! ';
        }
    }

    /*
     * DELETE TASK BY ID
     */
    public function deleteTask($id) {
        global $wpdb;

        $table_tasks = $wpdb->prefix.'tasks';

        // Nettoyer les données contre les injections XSS
        $id = strip_tags($id);

        // Préparation de la requête
        $sql = "DELETE FROM ".$table_tasks." WHERE id={$id}";

        // Execution de la requete
        $wpdb->query($sql);

        // Session message
        $_SESSION['delete_success'] = "Tâche Supprimé ! ";
    }

    /*
     * UPDATE TASK BY ID
     */
    public function updateTask($datas) {
        global $wpdb;
        $table_tasks = $wpdb->prefix.'tasks';

        if (empty($datas['forfait_id'])) {
            $errors['forfait_id'] = "Le forfait n'est pas sélectionné";
        }
        if (empty($datas['task_time'])) {
            $errors['task_time'] = 'Le temps total est vide';
        }
        if (empty($datas['description'])) {
            $errors['description'] = 'La description est vide';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
        } else {
            // Nettoyer les données contre les injections XSS
            $id = strip_tags($datas['id']);
            $forfait_id = strip_tags($datas['forfait_id']);
            $taskTime = strip_tags($datas['task_time']);
            $description = htmlspecialchars($datas['description']);
            $updated_at = date('Y-m-d H:i:s', time());

            // Préparation de la requête
            $sql = "UPDATE $table_tasks SET 
                 forfait_id='$forfait_id', 
                 task_time='$taskTime', 
                 description='$description', 
                 updated_at='$updated_at' 
                WHERE id=$id";
            // Execution de la requete
            $wpdb->query($sql);

            // Session message
            $_SESSION['update_success'] = "Tâche Modifiée ! ";
        }
    }

    /*
     * GET ONLY TITLE FOR A FORFAIT BY ID
     */
    public function getForfaitTitleByID($forfait_id) {
        global $wpdb;

        $table_forfait = $wpdb->prefix.'forfait';

        $sql = "SELECT title FROM {$table_forfait} WHERE id=$forfait_id";
        $forfaitTitle = $wpdb->get_var($sql);

        return $forfaitTitle;
    }

    /*
     * GET NUMBER OF TASKS BY FORFAITS
     */
    public function getTasksNumberByForfait($forfait_id) {
        global $wpdb;

        $table_forfait = $wpdb->prefix.'forfait';
        $table_tasks = $wpdb->prefix.'tasks';

        $sql = "SELECT count(*) FROM $table_tasks as tblTasks JOIN $table_forfait as tblForfait WHERE tblTasks.forfait_id=$forfait_id AND tblTasks.usable=1 AND tblForfait.id=$forfait_id";
        $forfaitCount = $wpdb->get_var($sql);

        return $forfaitCount;
    }

    /*
     * GET TOTAL TASKS TIME FOR A FORFAIT
     */
    public function getTimeTotalsForTasks($forfait_id) {
        global $wpdb;

        $table_tasks = $wpdb->prefix.'tasks';

        $sql = "SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( task_time ) ) ) FROM {$table_tasks} WHERE forfait_id=$forfait_id AND usable='1'";

        $tasksTotalTime = $wpdb->get_var($sql);

        return $tasksTotalTime;
    }

    /*
     * GET LIST OF ALL TASKS BY FORFAIT ID
     */
    public function getListTasks($forfait_id){
        global $wpdb;
        $table_tasks = $wpdb->prefix.'tasks';
        $sql = "SELECT * FROM {$table_tasks} WHERE forfait_id={$forfait_id} ORDER BY `created_at` DESC;";
        $tasksList = $wpdb->get_results($sql);
        return $tasksList;
    }

    /*
     * GET LIST OF ALL FORFAITS
     */
    public function getListForfaits(){
        global $wpdb;
        $table_forfait = $wpdb->prefix.'forfait';
        $sql = "SELECT * FROM {$table_forfait}";
        $forfaitsList = $wpdb->get_results($sql);
        return $forfaitsList;
    }

    /*
     * GET A TASK BY ID
     */
    public function getTaskByID($id){
        global $wpdb;
        $table_tasks = $wpdb->prefix.'tasks';
        $sql = "SELECT * FROM $table_tasks WHERE id=$id";
        $task = $wpdb->get_results($sql);
        return $task;
    }

    /*
     * GET A FORFAIT BY ID
     */
    public function getForfaitByID($id){
        global $wpdb;
        $table_forfait = $wpdb->prefix.'forfait';
        $sql = "SELECT * FROM $table_forfait WHERE id=$id";
        $forfait = $wpdb->get_results($sql);
        return $forfait;
    }

    /*
     * GET CREATED AT FOR THE FORFAIT
     */
    public function getForfaitCreatedAt($id){
        global $wpdb;
        $table_forfait = $wpdb->prefix.'forfait';
        $sql = "SELECT created_at FROM $table_forfait WHERE id=$id";
        $result = $wpdb->get_var($sql);
        $result = new DateTime($result, new DateTimeZone('Europe/Paris'));
        $result = $result->format('d-m-Y H:i:s');
        return $result;
    }

    /*
     * GET UPDATED AT FOR THE FORFAIT
     */
    public function getForfaitUpdatedAt($id){
        global $wpdb;
        $table_forfait = $wpdb->prefix.'forfait';
        $sql = "SELECT updated_at FROM $table_forfait WHERE id=$id";
        $result = $wpdb->get_var($sql);
        $result = new DateTime($result, new DateTimeZone('Europe/Paris'));
        $result = $result->format('d-m-Y H:i:s');
        return $result;
    }

    /*
     * GET CREATED AT FOR THE FORFAIT
     */
    public function getTaskCreatedAt($id){
        global $wpdb;
        $table_tasks = $wpdb->prefix.'tasks';
        $sql = "SELECT created_at FROM $table_tasks WHERE id=$id";
        $result = $wpdb->get_var($sql);
        $result = new DateTime($result, new DateTimeZone('Europe/Paris'));
        $result = $result->format('d-m-Y H:i:s');
        return $result;
    }

}
