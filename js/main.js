console.log('plugin main.js loaded ;)')


function selectForfaitTimeCheck(value)
{
    console.log(value);
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
    var addTaskButton = document.getElementById('addTask')
    var addTaskForm = document.getElementById('addTaskForm')
    var addForfaitButton = document.getElementById('addForfait')
    var addForfaitForm = document.getElementById('addForfaitForm')

    if (addTaskButton) {
        addTaskButton.addEventListener('click', function() {
            addTaskForm.classList.toggle('displayBlock')
        })
    }
    if (addForfaitButton) {
        addForfaitButton.addEventListener('click', function() {
            addForfaitForm.classList.toggle('displayBlock')
        })
    }
}

function closeForms() {
    var closeButton = document.getElementsByClassName('closeFormButton')
    var addTaskForm = document.getElementById('addTaskForm')
    var addForfaitForm = document.getElementById('addForfaitForm')
    var updateForfaitForm = document.getElementById('updateForfaitForm')

    if (closeButton) {
        for (var i = 0; i < closeButton.length; i++) {
            closeButton[i].addEventListener('click', function () {

                if (addTaskForm.classList.contains('displayBlock')) {
                    addTaskForm.classList.remove('displayBlock')
                }
                if (addForfaitForm.classList.contains('displayBlock')) {
                    addForfaitForm.classList.remove('displayBlock')
                }
                if (updateForfaitForm.classList.contains('displayBlock')) {
                    updateForfaitForm.classList.remove('displayBlock')
                }
            })
        }
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
    var deleteMessage = document.getElementById('deleteAlertMessage')
    deleteBtn.addEventListener('mouseenter', function () {
        deleteMessage.classList.add('displayBlock')
    })
    deleteBtn.addEventListener('mouseleave', function () {
        deleteMessage.classList.remove('displayBlock')
    })
}

jQuery(document).ready( function () {
    closeFormAlert()
    toggleForms()
    closeForms()
    alertDeleteConfirm()
})