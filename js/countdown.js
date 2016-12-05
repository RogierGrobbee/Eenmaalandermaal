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
    var end = new Date(timeElement.getAttribute('data-tijd'));

    var _second = 1000;
    var _minute = _second * 60;
    var _hour = _minute * 60;
    var _day = _hour * 24;

    var now = new Date();
    var distance = end - now;

    var days = Math.floor(distance / _day);
    var hours = Math.floor((distance % _day) / _hour);
    var minutes = Math.floor((distance % _hour) / _minute);
    var seconds = Math.floor((distance % _minute) / _second);

    if (distance < 0) {

        timeElement.innerHTML = 'Beëindigd!';
        return;
    }

    timeElement.innerHTML = '';

    if (days > 0) {
        var pluralCounter = days > 1 ? 'dagen' : 'dag';
        timeElement.innerHTML += days + ' ' + pluralCounter;
    }
    else {
        timeElement.innerHTML += formatTimeSegment(hours) + ':';
        timeElement.innerHTML += formatTimeSegment(minutes) + ':';
        timeElement.innerHTML += formatTimeSegment(seconds);
    }
}

function refreshVeilingTijd () {
    var veilingsTijden = document.getElementsByClassName('tijd');

    for (var i = 0; i < veilingsTijden.length; i++) {
        showDifference(veilingsTijden[i]);
    }

    setTimeout(refreshVeilingTijd, 1000);
}

window.onload = function() {
    refreshVeilingTijd();
};