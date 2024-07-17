function selectForfaitTimeCheck(value)
{
    if (value) {
        var inputTime = document.getElementById("task_time")
        var labelTime = document.getElementById("taskTimeLabel")
        var submitTask = document.getElementById("addTaskSubmit")

        if (inputTime) {
            if (inputTime.hasAttribute('max')) {
                inputTime.removeAttribute('max')
                labelTime.textContent = "Durée";
            }
            inputTime.setAttribute("max", value)
        }

        labelTime.textContent += " (Max "+value+")"

        if (value === '00:00:00') {
            submitTask.style.pointerEvents = 'none'
            submitTask.setAttribute("value", "IMPOSSIBLE")
        }
    }
}

function toggleForms() {
    // Update forfait informations
    var updateForfaitBtn = document.getElementById('updateForfaitBtn')
    var updateForfaitForm = document.getElementById('updateForfaitForm')
    // Update forfait time
    var updateForfaitTimeBtn = document.getElementById('updateForfaitTimeBtn')
    var updateForfaitTimeForm = document.getElementById('updateForfaitTimeForm')
    // Add Task Form
    var addTaskForm = document.getElementById('addTaskForm')
    var addTaskBtn = document.getElementById('addTaskBtn')

    if (updateForfaitForm) {
        updateForfaitBtn.addEventListener('click', function() {
            if (updateForfaitForm.classList.contains('displayBlock')) {
                updateForfaitForm.classList.remove('displayBlock')
            } else {
                updateForfaitForm.classList.add('displayBlock')
            }
            if (updateForfaitTimeBtn.classList.contains('displayBlock')) {
                updateForfaitTimeBtn.classList.remove('displayBlock')
            }
            if (addTaskForm.classList.contains('displayBlock')) {
                addTaskForm.classList.remove('displayBlock')
            }
        })
        var closeForfaitBtn = updateForfaitForm.querySelector(".closeFormButton")
        closeForfaitBtn.addEventListener("click", function() {
            if (updateForfaitForm.classList.contains('displayBlock')) {
                updateForfaitForm.classList.remove('displayBlock')
            }
        })
    }

    if (updateForfaitTimeForm) {
        updateForfaitTimeBtn.addEventListener('click', function() {
            if (updateForfaitTimeForm.classList.contains('displayBlock')) {
                updateForfaitTimeForm.classList.remove('displayBlock')
            } else {
                updateForfaitTimeForm.classList.add('displayBlock')
            }
            if (updateForfaitForm.classList.contains('displayBlock')) {
                updateForfaitForm.classList.remove('displayBlock')
            }
            if (addTaskForm.classList.contains('displayBlock')) {
                addTaskForm.classList.remove('displayBlock')
            }
        })
        var closeForfaitTimeBtn = updateForfaitTimeForm.querySelector(".closeFormButton")
        closeForfaitTimeBtn.addEventListener("click", function() {
            if (updateForfaitTimeForm.classList.contains('displayBlock')) {
                updateForfaitTimeForm.classList.remove('displayBlock')
            }
        })
    }

    if (addTaskForm) {
        addTaskBtn.addEventListener('click', function() {
            if (addTaskForm.classList.contains('displayBlock')) {
                addTaskForm.classList.remove('displayBlock')
            } else {
                addTaskForm.classList.add('displayBlock')
            }
            if (updateForfaitForm.classList.contains('displayBlock')) {
                updateForfaitForm.classList.remove('displayBlock')
            }
            if (updateForfaitTimeForm.classList.contains('displayBlock')) {
                updateForfaitTimeForm.classList.remove('displayBlock')
            }
        })
        var closeTaskFormBtn = addTaskForm.querySelector(".closeFormButton")
        closeTaskFormBtn.addEventListener("click", function() {
            if (addTaskForm.classList.contains('displayBlock')) {
                addTaskForm.classList.remove('displayBlock')
            }
        })
    }
}

function closeFormAlert() {
    var forfaitAlertBloc = document.getElementById('forfaitAlertBloc')
    var closeButton = document.getElementById('forfaitAlertClose')

    if(forfaitAlertBloc) {
        closeButton.addEventListener('click', function() {
            forfaitAlertBloc.style.display = 'none'
        })
    }
}

function alertDeleteConfirm() {
    var deleteBtn = document.getElementById('deleteBtn')
    var updateBtn = document.getElementById('updateForfaitTimeBtn')
    var deleteMessage = document.getElementById('deleteAlertMessage')
    var updateMessage = document.getElementById('updateAlertMessage')

    if (deleteBtn) {
        deleteBtn.addEventListener('mouseenter', function () {
            deleteMessage.classList.add('displayBlock')
        })
    }
    if (deleteBtn) {
        deleteBtn.addEventListener('mouseleave', function () {
            deleteMessage.classList.remove('displayBlock')
        })
    }       
    if (updateBtn) {
        updateBtn.addEventListener('mouseenter', function () {
            updateMessage.classList.add('displayBlock')
        })
    }
    if (updateBtn) {
        updateBtn.addEventListener('mouseleave', function () {
            updateMessage.classList.remove('displayBlock')
        })
    }
}

function formatTimeInput() {
    const taskTimeInput = document.getElementById('task_time');

    if (taskTimeInput) {
        taskTimeInput.addEventListener('input', (event) => {
            let value = event.target.value.replace(/[^0-9]/g, '');
            if (value.length > 6) {
                value = value.slice(0, 6);
            }
            if (value.length > 4) {
                value = value.slice(0, 2) + ':' + value.slice(2, 4) + ':' + value.slice(4);
            } else if (value.length > 2) {
                value = value.slice(0, 2) + ':' + value.slice(2);
            }
            event.target.value = value;
        });
    }
}

function setupEditTaskButtons() {
    const editButtons = document.querySelectorAll('.edit-btn');
    const editTaskForm = document.getElementById('editTaskForm');
    const addTaskForm = document.getElementById('addTaskForm');

    editButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const taskId = button.getAttribute('data-task-id');
            const taskTime = button.getAttribute('data-task-time');
            const taskDescription = button.getAttribute('data-task-description');

            document.getElementById('edit_task_id').value = taskId;
            document.getElementById('edit_task_time').value = taskTime;
            document.getElementById('edit_task_description').value = taskDescription;

            editTaskForm.style.display = 'block';
            addTaskForm.style.display = 'none';
        });
    });

    const closeTaskFormBtn = editTaskForm.querySelector(".closeFormButton");
    closeTaskFormBtn.addEventListener("click", function() {
        editTaskForm.style.display = 'none';
    });
}

jQuery(document).ready( function () {
    closeFormAlert()
    toggleForms()
    alertDeleteConfirm()
    formatTimeInput()
    setupEditTaskButtons()
})
