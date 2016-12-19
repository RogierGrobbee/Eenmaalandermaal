/**
 * Created by jamiel on 19-12-2016.
 */
function searchFilterSelect(filter, search) {
    window.location.href = "./zoeken.php?search="+search+"&filter="+filter;
}

function rubriekFilterSelect(filter, rubriek) {
    window.location.href = "./rubriek.php?rubriek="+rubriek+"&filter="+filter;
}