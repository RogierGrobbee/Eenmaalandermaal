/**
 * Created by jamiel on 30-11-2016.
 */

function formatTimeSegment(timeSegment) {
    if (timeSegment < 10) {
        timeSegment = '0' + timeSegment;
    }
    return timeSegment;
}

function showDifference(timeElement) {
    var end = new Date(timeElement.getElementsByClassName("tijd-hidden")[0].innerHTML);

    var _second = 1000;
    var _minute = _second * 60;
    var _hour = _minute * 60;
    var _day = _hour * 24;

    var now = new Date();
    var distance = end - now;
    if (distance < 0) {

        end.innerHTML = 'Beëindigd!';
        return;
    }
    var days = Math.floor(distance / _day);
    var hours = Math.floor((distance % _day) / _hour);
    var minutes = Math.floor((distance % _hour) / _minute);
    var seconds = Math.floor((distance % _minute) / _second);

    var displayElement = timeElement.getElementsByClassName("tijd-display")[0];

    displayElement.innerHTML = '';

    if (days > 0) {
        var pluralCounter = days > 1 ? 'dagen' : 'dag';
        displayElement.innerHTML += days + ' ' + pluralCounter;
    }
    else {
        displayElement.innerHTML += formatTimeSegment(hours) + ':';
        displayElement.innerHTML += formatTimeSegment(minutes) + ':';
        displayElement.innerHTML += formatTimeSegment(seconds);
    }
}

function refreshVeilingTijd () {
    var veilingsTijden = document.getElementsByClassName('tijd');

    for (var i = 0; i < veilingsTijden.length; i++) {
        showDifference(veilingsTijden[i]);
    }

    //TODO: Javascript collects the garbage every time setInterval is called. Is there a better alternative?
    var timer = setInterval(refreshVeilingTijd, 1000);
}

window.onload = function() {
    refreshVeilingTijd();
};