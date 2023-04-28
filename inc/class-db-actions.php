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
    private $wpdb;
    private $ForfaitTable;
    private $TasksTable;

    private function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->ForfaitTable = $this->wpdb->prefix . "forfait";
        $this->TasksTable = $this->wpdb->prefix . "tasks";
    }
    /*
     * CREATE A FORFAIT
     */
    public function createForfait($datas) {
        // Title
        if (empty($datas['title'])) {
            $errors['title'] = 'Le titre est vide';
        } else {
            $title = strip_tags($datas['title']);
        }
        // Total Time
        if (empty($datas['total_time'])) {
            $errors['total_time'] = 'Le temps total est vide';
        } else {
            $total_time = strip_tags($datas['total_time']);
            // Validation de la valeur soumise
            $total_time = self::checkTimeFormat($total_time);
            var_dump($total_time);
            die();
        }
        // Description
        if (empty($datas['description'])) {
            $errors['description'] = 'La description est vide';
        } else {
            $description = htmlspecialchars($datas['description']);
        }
        // TimeStamps
        $created_at = date('Y-m-d H:i:s', time());
        $updated_at = date('Y-m-d H:i:s', time());

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
        } else {
            // Prépare la requete
            $sql = $this->wpdb->prepare(
                "INSERT INTO {$this->ForfaitTable}
                        (title, total_time, description, created_at, updated_at) VALUES (%s,(SEC_TO_TIME(%d)),%s,%s,%s )",
                $title,
                $total_time,
                $description,
                $created_at,
                $updated_at
            );
            // Execution de la requete
            $this->wpdb->query($sql);
            // Redirection sur url
            $_SESSION['create_success'] = "Forfait Ajouté !";
        }
    }

    /*
     * DELETE A FORFAIT
     */
    public function deleteForfait($id) {
        $table_forfait = $this->wpdb->prefix.'forfait';
        $table_tasks = $this->wpdb->prefix.'tasks';

        // Nettoyer les données contre les injections XSS
        $id = strip_tags($id);

        // Préparation de la requête
        $sqlTasks = "DELETE FROM ".$table_tasks." WHERE forfait_id={$id}";
        $sqlForfait = "DELETE FROM ".$table_forfait." WHERE id={$id}";

        // Execution de la requete
        $this->wpdb->query($sqlTasks);
        $this->wpdb->query($sqlForfait);

        // Session message
        $_SESSION['delete_success'] = "Forfait Supprimé ! ";
    }

    /*
     * UPDATE A FORFAIT
     */
    public function updateForfait($datas) {
        $table_forfait = $this->wpdb->prefix.'forfait';
        $table_tasks = $this->wpdb->prefix.'tasks';

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
            $this->wpdb->query($sql);

            // Préparation de la requête
            $sql2 = "UPDATE $table_tasks SET 
                 usable=0";
            // Execution de la requete
            $this->wpdb->query($sql2);

            // Session message
            $_SESSION['update_success'] = "Forfait Modifié ! ";
        }
    }

    /*
     * CREATE A TASK
     */
    public function createTask($datas) {
        $tasks_table = $this->wpdb->prefix. "tasks";

        $task_time = strip_tags($datas['task_time']);
        $remaining_time = $datas['remaining_time'];

        $task_time = self::checkTimeFormat($task_time);
        $remaining_time = self::checkTimeFormat($remaining_time);

        if (empty($datas['description'])) {
            $errors['description'] = 'La description est vide';
        }
        if (empty($remaining_time)) {
            // Validation de la valeur soumise
            if (preg_match('/^([0-9]{2}):([0-5][0-9]):([0-5][0-9])$/', $remaining_time, $matches)) {
                // Transformation en nombre de secondes
                $remaining_time = $matches[1] * 3600 + $matches[2] * 60 + $matches[3];
            } else {
                $errors['total_time'] = 'Le temps restant n\'est pas au bon format';
            }
        }
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
        } else {
            // Nettoyer les données contre les injections XSS
            $forfait_id = $datas['forfait_id'];

            $description = htmlspecialchars($datas['description']);
            $created_at = date('Y-m-d H:i:s', time());
            $updated_at = date('Y-m-d H:i:s', time());

            // Prépare la requete
            $sql = $this->wpdb->prepare(
                "INSERT INTO {$tasks_table}
                        (forfait_id, task_time, description, remaining_time, created_at, updated_at) VALUES (%d,(SEC_TO_TIME(%d)),%s,(SEC_TO_TIME(%d)),%s,%s )",
                $forfait_id,
                $task_time,
                $description,
                $remaining_time,
                $created_at,
                $updated_at
            );

            // Execution de la requete
            $this->wpdb->query($sql);
            // Redirection sur url
            $_SESSION['create_success'] = 'Tâche Ajoutée ! ';
        }
    }

    /*
     * DELETE TASK BY ID
     */
    public function deleteTask($id) {
        $table_tasks = $this->wpdb->prefix.'tasks';

        // Nettoyer les données contre les injections XSS
        $id = strip_tags($id);

        // Préparation de la requête
        $sql = "DELETE FROM ".$table_tasks." WHERE id={$id}";

        // Execution de la requete
        $this->wpdb->query($sql);

        // Session message
        $_SESSION['delete_success'] = "Tâche Supprimé ! ";
    }

    /*
     * UPDATE TASK BY ID
     */
    public function updateTask($datas) {
        $table_tasks = $this->wpdb->prefix.'tasks';

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
            $this->wpdb->query($sql);

            // Session message
            $_SESSION['update_success'] = "Tâche Modifiée ! ";
        }
    }

    /*
     * GET ONLY TITLE FOR A FORFAIT BY ID
     */
    public function getForfaitTitleByID($forfait_id) {
        $table_forfait = $this->wpdb->prefix.'forfait';

        $sql = "SELECT title FROM {$table_forfait} WHERE id=$forfait_id";
        $forfaitTitle = $this->wpdb->get_var($sql);

        return $forfaitTitle;
    }

    /*
     * GET NUMBER OF TASKS BY FORFAITS
     */
    public function getTasksNumberByForfait($forfait_id) {
        $table_forfait = $this->wpdb->prefix.'forfait';
        $table_tasks = $this->wpdb->prefix.'tasks';

        $sql = "SELECT count(*) FROM $table_tasks as tblTasks JOIN $table_forfait as tblForfait WHERE tblTasks.forfait_id=$forfait_id AND tblTasks.usable=1 AND tblForfait.id=$forfait_id";
        $forfaitCount = $this->wpdb->get_var($sql);

        return $forfaitCount;
    }

    /*
     * GET TOTAL TASKS TIME FOR A FORFAIT
     */
    public function getTimeTotalsForTasks($forfait_id) {
        $table_tasks = $this->wpdb->prefix.'tasks';

        $sql = "SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( task_time ) ) ) FROM {$table_tasks} WHERE forfait_id=$forfait_id AND usable='1'";

        $tasksTotalTime = $this->wpdb->get_var($sql);

        return $tasksTotalTime;
    }

    /*
     * GET LIST OF ALL TASKS BY FORFAIT ID
     */
    public function getListTasks($forfait_id){
        $table_tasks = $this->wpdb->prefix.'tasks';
        $sql = "SELECT * FROM {$table_tasks} WHERE forfait_id={$forfait_id} ORDER BY `created_at` DESC;";
        $tasksList = $this->wpdb->get_results($sql);
        return $tasksList;
    }

    /*
     * GET LIST OF ALL FORFAITS
     */
    public function getListForfaits(){
        $table_forfait = $this->wpdb->prefix.'forfait';
        $sql = "SELECT * FROM {$table_forfait}";
        $forfaitsList = $this->wpdb->get_results($sql);
        return $forfaitsList;
    }

    /*
     * GET A TASK BY ID
     */
    public function getTaskByID($id){
        $table_tasks = $this->wpdb->prefix.'tasks';
        $sql = "SELECT * FROM $table_tasks WHERE id=$id";
        $task = $this->wpdb->get_results($sql);
        return $task;
    }

    /*
     * GET A FORFAIT BY ID
     */
    public function getForfaitByID($id){
        $table_forfait = $this->wpdb->prefix.'forfait';
        $sql = "SELECT * FROM $table_forfait WHERE id=$id";
        $forfait = $this->wpdb->get_results($sql);
        return $forfait;
    }

    /*
     * GET CREATED AT FOR THE FORFAIT
     */
    public function getForfaitCreatedAt($id){
        $table_forfait = $this->wpdb->prefix.'forfait';
        $sql = "SELECT created_at FROM $table_forfait WHERE id=$id";
        $result = $this->wpdb->get_var($sql);
        $result = new DateTime($result, new DateTimeZone('Europe/Paris'));
        $result = $result->format('d-m-Y');
        return $result;
    }

    /*
     * GET UPDATED AT FOR THE FORFAIT
     */
    public function getForfaitUpdatedAt($id){
        $table_forfait = $this->wpdb->prefix.'forfait';
        $sql = "SELECT updated_at FROM $table_forfait WHERE id=$id";
        $result = $this->wpdb->get_var($sql);
        $result = new DateTime($result, new DateTimeZone('Europe/Paris'));
        $result = $result->format('d-m-Y');
        return $result;
    }

    /*
     * GET CREATED AT FOR THE FORFAIT
     */
    public function getTaskCreatedAt($id){
        $table_tasks = $this->wpdb->prefix.'tasks';
        $sql = "SELECT created_at FROM $table_tasks WHERE id=$id";
        $result = $this->wpdb->get_var($sql);
        $result = new DateTime($result, new DateTimeZone('Europe/Paris'));
        $result = $result->format('d-m-Y');
        return $result;
    }

    private function checkTimeFormat($time): float|int|string
    {
        if (empty($time)) {
            return $result['error'] = 'Le temps total est vide';
        } else {
            // Validation de la valeur soumise
            if (preg_match('/^([0-9]{2}):([0-5][0-9]):([0-5][0-9])$/', $time, $matches)) {
                // Transformation en nombre de secondes
                return $result['result'] = $matches[1] * 3600 + $matches[2] * 60 + $matches[3];
            } else {
                return $result['error'] = 'Le temps total n\'est pas au bon format';
            }
        }
    }

    public function TimeToSec($time) {
        // Validation de la valeur soumise
        if (preg_match('/^([0-9]{2}):([0-5][0-9]):([0-5][0-9])$/', $time, $matches)) {
            // Transformation en nombre de secondes
            return $result['seconds'] = $matches[1] * 3600 + $matches[2] * 60 + $matches[3];
        } else {
            return $result['total_time'] = 'Le temps total n\'est pas au bon format';
        }
    }

    public function SecToTime($seconds) {
        if (is_int($seconds)) {
            // Calcul des heures, minutes et secondes
            $heures = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            $secondes = $seconds % 60;

            return $time = $heures.":".$minutes.":".$secondes;
        } else {
            // La durée n'est pas un nombre entier positif
            return $result['error'] = "La durée n'est pas un nombre entier positif";
        }
    }

}
