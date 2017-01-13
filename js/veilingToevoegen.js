/**
 * Created by jamiel on 13-1-2017.
 */

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