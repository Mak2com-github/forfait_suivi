function selectForfaitTimeCheck(value)
{
    if(value){
        var inputTime = document.getElementById("taskTimeInput")
        var labelTime = document.getElementById("taskTimeLabel")
        var submitTask = document.getElementById("addTaskSubmit")
        if (inputTime.hasAttribute('max')) {
            inputTime.removeAttribute('max')
            labelTime.textContent = "Durée";
        }
        inputTime.setAttribute("max", value)

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

function formatTimeNumber() {
    var taskTime = document.querySelector("input[name='task_time']")
    var forfaitTime = document.querySelector("input[name='total_time']")
    forfaitTime.addEventListener('keyup', function() {
        var input = this
        var value = input.value.replace(/:/g, '')
        var newValue = ''
        for (var i = 0; i < value.length; i++) {
            if (i % 2 === 1 && i !== value.length - 1) {
                newValue += value[i] + ":"
            } else {
                newValue += value[i]
            }
        }
        input.value = newValue
    })
}

function manageColorField(element) {
    var formColorBlock = element.parentNode

    if (element.classList.contains('add-color')) {
        var clonedNode = formColorBlock.cloneNode(true)
        var clonedInput = clonedNode.querySelector("input")
        clonedInput.setAttribute('name', 'color-' + (formColorBlock.parentNode.children.length + 1))
        formColorBlock.after(clonedNode)
        element.classList.remove('add-color')
        element.classList.add('remove-color')
        element.innerHTML = "-"
    } else {
        formColorBlock.remove()
    }
}

jQuery(document).ready( function () {
    formatTimeNumber()
    closeFormAlert()
    toggleForms()
    alertDeleteConfirm()
})
