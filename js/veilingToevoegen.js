/**
 * Created by jamiel on 13-1-2017.
 */

jQuery('[name="add-veiling-form"]').on("submit",selectAllRubriekenInSelect);

function selectAllRubriekenInSelect()
{
    jQuery('[name="rubriekenList[]"] option').prop('selected', true);
}

document.getElementById('delete-rubriek').addEventListener("click", deleteRubrieken);

function deleteRubrieken() {
    var selectElement = document.getElementById('rubrieken-list');
    var options = selectElement.options;

    for (var i = options.length - 1; i >= 0; i--) {
        if (options[i].selected) {
            selectElement.options[i] = null;
        }
    }
    disableDeleteButton();
}

function isRubriekInSelect(rubriek) {
    var selectElement = document.getElementById('rubrieken-list');
    var options = selectElement.options;

    for (var i = 0; i < options.length; i++) {
        if (options[i].text === rubriek) {
            return true;
        }
    }
    return false;
}

function disableDeleteButton() {
    var selectElement = document.getElementById('rubrieken-list');
    if (getSelectValues(selectElement).length >= 1) {
        document.getElementById('delete-rubriek').disabled = false;
    }
    else {
        document.getElementById('delete-rubriek').disabled = true;
    }
}

function getSelectValues(select) {
    var result = [];
    var options = select && select.options;

    for (var i=0, iLen=options.length; i<iLen; i++) {
        if (options[i].selected) {
            result.push(options[i].value || options[i].text);
        }
    }
    return result;
}

$('#rubrieken-list').change(function(e) {
    disableDeleteButton();
});


function rubriekClick(sender) {
    var state = sender.getAttribute('data-state');

    if (state == 0) {
        loadSubrubrieken(sender);
    }
    else if (state == 1) {
        addRubriekToList(sender);
    }
}
function loadSubrubrieken(sender) {
    $.ajax({
        type: 'POST',
        url: "/AJAXRequestHandler/AJAXRequestHandler.php",
        data: ({
            action: 'getSubrubrieken',
            rubrieknummer: sender.getAttribute('data-id')
        }),
        success: function(data) {
            var rubrieken = JSON.parse(data);
            if (rubrieken.length > 1) {
                showSubrubrieken(rubrieken);
                sender.setAttribute('data-state', -1);
            }
            else {
                sender.setAttribute('data-state', 1);
                addRubriekToList(sender);
            }
        }
    });
}

function showSubrubrieken(rubrieken) {
    var superRubriekId = rubrieken[0].superrubriek;

    for (i = 0; i < rubrieken.length; i++) {
        var subRubriek = $("<a href='#" + rubrieken[i].rubrieknummer +
            "' data-id='" + rubrieken[i].rubrieknummer +
                    "' data-toggle='collapse' onclick='rubriekClick(this)' data-state='0'>" + rubrieken[i].rubrieknaam + "</a>");
        var container = $("#" + superRubriekId);
        container.append(subRubriek);
        container.append("<br>");
        container.append("<div id='" + rubrieken[i].rubrieknummer + "' class='collapse margin-left'></div>");
    }
}

function addRubriekToList(sender) {
    if (!isRubriekInSelect(sender.text)) {
        var selectElement = document.getElementById('rubrieken-list');
        var objOption = document.createElement("option");
        objOption.text = sender.text;
        objOption.value = sender.getAttribute('data-id');

        selectElement.options.add(objOption);
        $('#myModal').modal('hide');
    }
}

function addRubriekToSelectList(sender) {
    var form = document.getElementById('add-veiling-form');
    var input = document.createElement("input");

    input.type = "hidden";
    input.value = "";
    form.appendChild()
}