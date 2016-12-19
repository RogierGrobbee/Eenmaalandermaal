/**
 * Created by Jamiel on 30-11-2016.
 */

/**
 * This function makes a new date for every browser (including IE) to parse to a Date.
 * @param dateStr The date returned from mysql timestamp/datetime field.
 * @returns {Date} The date that even IE can parse.
 */
function newDate(dateStr) {
    var dateAndTimeSplit = dateStr.split(".")[0].split(" ");
    var date = dateAndTimeSplit[0].split("-");
    var time = dateAndTimeSplit[1].split(":");
    return new Date(date[0],(date[1]-1),date[2],time[0],time[1],time[2]);
}

/**
 * Formats a segment of the a timestamp (hours, minutes or seconds).
 * If a segment is under 10 (Single digits) it will put a 0 at the beginning of the number.
 * @param timeSegment The segment of time.
 * @returns {*} A two digit segment.
 */
function formatTimeSegment(timeSegment) {
    if (timeSegment < 10) {
        timeSegment = '0' + timeSegment;
    }
    return timeSegment;
}

/**
 * Will display the remaining time between the current time and the time in the given time element.
 * This function requires the time element to have a data-tijd attribute.
 * @param timeElement The time element to display the remaining time.
 */
function showDifference(timeElement) {
    var timeNoMilSec = timeElement.getAttribute('data-tijd').split('.')[0];
    var end = newDate(timeNoMilSec);

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
        if (timeElement.innerHTML != 'Beëindigd!') {
            timeElement.innerHTML = 'Beëindigd!';

            endVeiling(timeElement.getAttribute('data-nummer'));
        }

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

/**
 * Function to refresh the remaining time of all the veiling items. Will get called every second.
 */
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


function endVeiling(voorwerpId) {
    $.ajax({
        type: 'POST',
        url: "partial files/endVeiling.php",
        data: ({
            voorwerp: voorwerpId
        }),
        success: function (output) {
            console.log(output);
        }
    });
}