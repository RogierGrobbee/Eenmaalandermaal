/**
 * Created by jamiel on 13-1-2017.
 */

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

function loadSubrubrieken(sender) {
    $.ajax({
        type: 'POST',
        url: "AJAXRequestHandler/AJAXRequestHandler.php",
        data: ({
            action: 'getSubrubrieken',
            rubrieknummer: sender.getAttribute('data-id')
        }),
        success: function(data) {
            var rubrieken = JSON.parse(data);
            sender.onclick = null;
            if (rubrieken.length > 1) {
                showSubrubrieken(rubrieken);
            }
            else {
                sender.onclick = addRubriekToList(sender);
            }
        }
    });
}

function showSubrubrieken(rubrieken) {
    var superRubriekId = rubrieken[0].superrubriek;

    for (i = 0; i < rubrieken.length; i++) {
        var subRubriek = $("<a href='#" + rubrieken[i].rubrieknummer +
            "' data-id='" + rubrieken[i].rubrieknummer +
                    "' data-toggle='collapse' onclick='loadSubrubrieken(this)'>" + rubrieken[i].rubrieknaam + "</a>");
        var container = $("#" + superRubriekId);
        container.append(subRubriek);
        container.append("<br>");
        container.append("<div id='" + rubrieken[i].rubrieknummer + "' class='collapse margin-left'></div>");
    }
}

function addRubriekToList(sender) {
    var myselect = document.getElementById('rubriekenList');
    var objOption = document.createElement("option");
    objOption.text = sender.text;
    objOption.value = sender.getAttribute('data-id');

    myselect.options.add(objOption);
}