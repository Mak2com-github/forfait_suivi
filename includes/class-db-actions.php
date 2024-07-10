<?php
class DBActions
{
    private $wpdb;
    private string $ForfaitTable;
    private string $TasksTable;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->ForfaitTable = $this->wpdb->prefix . "forfait";
        $this->TasksTable = $this->wpdb->prefix . "tasks";
    }

    /**
     * Handles any error that occurred during a database operation.
     *
     * @param mixed $error The error that occurred.
     * @return string|null A string error message if an error occurred, or null if no error occurred.
     */
    private function handleError($error): ?string
    {
        if ($this->wpdb->last_error) {
            error_log("Error: " . $this->wpdb->last_error);
            error_log("Query: " . $this->wpdb->last_query);
            return "Une erreur s'est produite. Veuillez réessayer.";
        }
        return null;
    }

    /*
     * CREATE A FORFAIT
     */
    public function createForfait($datas): void
    {
        if (!empty($datas)) {
            $forfait = $this->validateDatas($datas);
            $sql = $this->wpdb->prepare(
                "INSERT INTO {$this->ForfaitTable}
                (title, total_time, description, created_at, updated_at) VALUES (%s, (SEC_TO_TIME(%d)), %s, %s, %s)",
                $forfait['title'],
                $forfait['total_time'],
                $forfait['description'],
                $forfait['created_at'],
                $forfait['updated_at']
            );
            $this->wpdb->query($sql);
            if ($error = $this->handleError($this->wpdb->last_error)) {
                $_SESSION['errors'] = $error;
            } else {
                $_SESSION['create_success'] = "Forfait Ajouté !";
            }
        }
    }

    /*
     * DELETE A FORFAIT
     */
    public function deleteForfait($id): void
    {
        $id = strip_tags($id);
        $this->wpdb->delete($this->TasksTable, ['forfait_id' => $id]);
        $this->wpdb->delete($this->ForfaitTable, ['id' => $id]);
        if ($error = $this->handleError($this->wpdb->last_error)) {
            $_SESSION['errors'] = $error;
        } else {
            $_SESSION['delete_success'] = "Forfait Supprimé !";
        }
    }

    /*
     * UPDATE A FORFAIT
     */
    public function updateForfait($datas): void
    {
        if (!empty($datas)) {
            $forfait = $this->ValidateDatas($datas);
            if (empty($_SESSION['errors'])) {
                $sql = $this->wpdb->prepare(
                    "UPDATE {$this->ForfaitTable} 
                            SET title = %s,
                                description = %s,
                                updated_at = %s
                            WHERE id = %d",
                    $forfait['title'],
                    $forfait['description'],
                    $forfait['updated_at'],
                    $forfait['id']
                );
                $this->wpdb->query($sql);
                if ($error = $this->handleError($this->wpdb->last_error)) {
                    $_SESSION['errors'] = $error;
                } else {
                    $_SESSION['update_success'] = "Forfait mis à jour !";
                }
            }
        }
    }

    /*
     * UPDATE A FORFAIT
     */
    public function updateForfaitTime($datas): void
    {
        if (!empty($datas)) {
            $remainingTime = $this->getForfaitTime($datas['id']);
            $remainingTime = $this->TimeToSec($remainingTime);
            $submitedTime = $datas['total_time'];
            $submitedTime = $this->TimeToSec($submitedTime);
            $timeToAdd = $remainingTime + $submitedTime;
            $timeToAdd = $this->SecToTime($timeToAdd);

            $datas['total_time'] = $timeToAdd;

            $forfait = $this->ValidateDatas($datas);

            if (empty($_SESSION['errors'])) {
                $sql = $this->wpdb->prepare(
                    "UPDATE {$this->ForfaitTable} 
                            SET total_time = (SEC_TO_TIME(%d)),
                                updated_at = %s
                            WHERE id = %d",
                    $forfait['total_time'],
                    $forfait['updated_at'],
                    $forfait['id']
                );
                $this->wpdb->query($sql);

                if ($error = $this->handleError($this->wpdb->last_error)) {
                    $_SESSION['errors'] = $error;
                } else {
                    $this->deactivateTasks($forfait['id']);
                    $_SESSION['update_success'] = "Temps du forfait mis à jour !";
                }
            }
        }
    }

    /*
     * CREATE A TASK
     */
    public function createTask($datas): void
    {
        if (!empty($datas)) {

            $task = $this->ValidateDatas($datas);
            $task['usable'] = 1;

            $this->decrementForfaitTime($task['forfait_id'], $datas['task_time']);

            if (empty($_SESSION['errors'])) {
                $sql = $this->wpdb->prepare(
                    "INSERT INTO {$this->TasksTable}
                        (forfait_id, task_time, description, usable, created_at, updated_at) VALUES (%d,(SEC_TO_TIME(%d)),%s,%d,%s,%s)",
                    $task['forfait_id'],
                    $task['task_time'],
                    $task['description'],
                    $task['usable'],
                    $task['created_at'],
                    $task['updated_at']
                );
                $this->wpdb->query($sql);
                if ($error = $this->handleError($this->wpdb->last_error)) {
                    $_SESSION['errors'] = $error;
                } else {
                    $_SESSION['create_success'] = "Tâche Ajoutée !";
                }
            }
        }
    }

    /*
     * DELETE TASK BY ID
     */
    public function deleteTask($id, $forfait, $time): void
    {
        $id = strip_tags($id);

        if ($this->isTaskActive($id) === "1") {
            $this->incrementForfaitTime($forfait, $time);
        }

        $this->wpdb->delete($this->TasksTable, array('id' => $id));

        if ($error = $this->handleError($this->wpdb->last_error)) {
            $_SESSION['errors'] = $error;
        } else {
            $_SESSION['delete_success'] = "Tâche Supprimé !";
        }
    }

    private function isTaskActive($id): ?string
    {
        return $this->wpdb->get_var("SELECT usable FROM $this->TasksTable WHERE id = $id");
    }

    private function deactivateTasks($id): void
    {
        $id = strip_tags($id);
        $datas['id'] = $id;
        $datas = $this->ValidateDatas($datas);
        $datas['usable'] = 0;

        if (empty($_SESSION['errors'])) {
            $sql = $this->wpdb->prepare(
                "UPDATE {$this->TasksTable} 
                        SET usable = %d,
                            updated_at = %s
                        WHERE usable = %d",
                $datas['usable'],
                $datas['updated_at'],
                1
            );

            $this->wpdb->query($sql);

            if ($error = $this->handleError($this->wpdb->last_error)) {
                $_SESSION['errors'] = $error;
            } else {
                $_SESSION['deactivate_success'] = "Tâche désactivée !";
            }
        }
    }

    /*
     * GET NUMBER OF TASKS BY FORFAITS
     */
    public function getTasksNumberByForfait($forfait_id): ?string
    {
        $table_tasks = $this->wpdb->prefix . 'tasks';

        $sql = $this->wpdb->prepare("SELECT COUNT(*) FROM $table_tasks WHERE forfait_id = %d AND usable = 1", $forfait_id);
        return $this->wpdb->get_var($sql);
    }

    /*
     * GET TOTAL TASKS TIME FOR A FORFAIT
     */
    public function getTimeTotalsForTasks($forfait_id): ?string
    {
        $table_tasks = $this->wpdb->prefix.'tasks';

        $sql = "SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( task_time ) ) ) FROM {$table_tasks} WHERE forfait_id=$forfait_id AND usable='1'";

        $tasksTotalTime = $this->wpdb->get_var($sql);

        return $tasksTotalTime;
    }

    /*
     * GET LIST OF ALL TASKS BY FORFAIT ID
     */
    public function getListTasks($forfait_id): array|object|null
    {
        $table_tasks = $this->wpdb->prefix.'tasks';
        $sql = "SELECT * FROM {$table_tasks} WHERE forfait_id={$forfait_id} ORDER BY `created_at` DESC;";
        $tasksList = $this->wpdb->get_results($sql);
        return $tasksList;
    }

    /*
     * GET LIST OF ALL FORFAITS
     */
    public function getListForfaits(): array|object|null
    {
        $table_forfait = $this->wpdb->prefix.'forfait';
        $sql = "SELECT * FROM {$table_forfait}";
        $forfaitsList = $this->wpdb->get_results($sql);
        return $forfaitsList;
    }

    /*
     * GET A TASK BY ID
     */
    public function getTaskByID($id): array|object|null
    {
        $table_tasks = $this->wpdb->prefix.'tasks';
        $sql = "SELECT * FROM $table_tasks WHERE id=$id";
        $task = $this->wpdb->get_results($sql);
        return $task;
    }

    /*
     * GET A FORFAIT BY ID
     */
    public function getForfaitByID($id): array|object|null
    {
        $table_forfait = $this->wpdb->prefix.'forfait';
        $sql = "SELECT * FROM $table_forfait WHERE id=$id";
        $forfait = $this->wpdb->get_results($sql);
        return $forfait;
    }

    /*
     * GET CREATED AT FOR THE FORFAIT
     */
    public function getForfaitCreatedAt($id): string
    {
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
    public function getForfaitUpdatedAt($id): string
    {
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
    public function getTaskCreatedAt($id): string
    {
        $table_tasks = $this->wpdb->prefix.'tasks';
        $sql = "SELECT created_at FROM $table_tasks WHERE id=$id";
        $result = $this->wpdb->get_var($sql);
        $result = new DateTime($result, new DateTimeZone('Europe/Paris'));
        $result = $result->format('d-m-Y');
        return $result;
    }

    public function getForfaitTime($id): ?string
    {
        $id = strip_tags($id);
        return $this->wpdb->get_var("SELECT total_time FROM $this->ForfaitTable WHERE id=$id");
    }

    private function checkTimeFormat($time): bool|string
    {
        // Validation de la valeur soumise
        if (preg_match('/^([0-9]{1,3}):([0-5][0-9]):([0-5][0-9])$/', $time, $matches)) {
            // Transformation en nombre de secondes
            return $result['result'] = $matches[1] * 3600 + $matches[2] * 60 + $matches[3];
        } else {
            return false;
        }
    }

    private function incrementForfaitTime($id, $time): void
    {
        $forfaitTime = $this->getForfaitTime($id);
        $forfaitSeconds = $this->TimeToSec($forfaitTime);
        $tasksSeconds = $this->TimeToSec($time);
        $remainingTime = $forfaitSeconds + $tasksSeconds;

        $updatedAt = date('Y-m-d H:i:s', time());

        $sql = $this->wpdb->prepare(
            "UPDATE {$this->ForfaitTable} 
                    SET total_time = (SEC_TO_TIME(%d)),
                        updated_at = %s
                    WHERE id = %d",
            $remainingTime,
            $updatedAt,
            $id
        );
        $this->wpdb->query($sql);
        if ($error = $this->handleError($this->wpdb->last_error)) {
            $_SESSION['errors'] = $error;
        } else {
            $_SESSION['update_success'] = "Temps mis à jour !";
        }
    }

    private function decrementForfaitTime($id, $time): void
    {
        $forfaitTime = $this->getForfaitTime($id);
        $forfaitSeconds = $this->TimeToSec($forfaitTime);
        $tasksSeconds = $this->TimeToSec($time);
        if ($tasksSeconds > $forfaitSeconds) {
            $_SESSION['errors'] = array("Le temps de la tâche dépasse le temps disponible sur le forfait");
        } else {
            $remainingTime = $forfaitSeconds - $tasksSeconds;

            $updatedAt = date('Y-m-d H:i:s', time());

            $sql = $this->wpdb->prepare(
                "UPDATE {$this->ForfaitTable} 
                SET total_time = (SEC_TO_TIME(%d)),
                    updated_at = %s
                WHERE id = %d",
                $remainingTime,
                $updatedAt,
                $id
            );
            $this->wpdb->query($sql);
            if ($error = $this->handleError($this->wpdb->last_error)) {
                $_SESSION['errors'] = $error;
            } else {
                $_SESSION['update_success'] = "Temps mis à jour !";
            }
        }
    }

    public function TimeToSec($time): float|int|string
    {
        // Validation de la valeur soumise avec 3 chiffre pour les heures
        if (preg_match('/^([0-9]{1,3}):([0-5][0-9]):([0-5][0-9])$/', $time, $matches)) {
            return $result['seconds'] = $matches[1] * 3600 + $matches[2] * 60 + $matches[3];
        // Validation de la valeur soumise avec 2 chiffre pour les heures
        } else {
            return $result['total_time'] = 'Le temps total n\'est pas au bon format';
        }
    }

    public function SecToTime($seconds): string
    {
        if (is_int($seconds)) {
            // Calcul des heures, minutes et secondes
            $heures = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            $secondes = floor($seconds % 60);

            return $time = sprintf('%02d:%02d:%02d', $heures, $minutes, $secondes);
        } else {
            // La durée n'est pas un nombre entier positif
            return $result['error'] = "La durée n'est pas un nombre entier positif";
        }
    }

    private function validateDatas($datas): array
    {
        $errors = [];
        $result = [];

        // Title
        if (isset($datas['title'])) {
            if (empty($datas['title'])) {
                $errors['title'] = 'Le titre est vide';
            } else {
                $result['title'] = htmlspecialchars($datas['title'], ENT_QUOTES);
            }
        }

        // Total Time
        if (isset($datas['total_time'])) {
            if (empty($datas['total_time'])) {
                $errors['total_time'] = 'Le temps total est vide';
            } else {
                $result['total_time'] = strip_tags($datas['total_time']);
                // Validation de la valeur soumise
                if (($result['total_time'] = $this->checkTimeFormat($result['total_time'])) === false) {
                    $errors['total_time'] = 'Le temps total n\'est pas au bon format';
                }
            }
        }

        // Task Time
        if (isset($datas['task_time'])) {
            if (empty($datas['task_time'])) {
                $errors['task_time'] = 'Le temps de la tâche est vide';
            } else {
                $result['task_time'] = strip_tags($datas['task_time']);
                // Validation de la valeur soumise
                if (($result['task_time'] = $this->checkTimeFormat($result['task_time'])) === false) {
                    $errors['task_time'] = 'Le temps de la tâche n\'est pas au bon format';
                }
            }
        }

        // Description
        if (isset($datas['description'])) {
            if (empty($datas['description'])) {
                $errors['description'] = 'La description est vide';
            } else {
                $result['description'] = htmlspecialchars($datas['description'], ENT_QUOTES);
            }
        }

        // Forfait ID
        if (isset($datas['forfait_id'])) {
            $result['forfait_id'] = strip_tags($datas['forfait_id']);
        }

        // ID
        if (isset($datas['id'])) {
            $result['id'] = strip_tags($datas['id']);
        }

        // TimeStamps
        $result['created_at'] = date('Y-m-d H:i:s', time());
        $result['updated_at'] = date('Y-m-d H:i:s', time());

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
        }

        return $result;
    }

}
