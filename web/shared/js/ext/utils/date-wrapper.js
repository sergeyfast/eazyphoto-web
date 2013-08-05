function DateWrapper() {
    return DateWrapper;
};

/**
 * Get the date from {day,month,year} object
 * @param dateObj
 */
DateWrapper.getDate = function ( dateObj ) {
    return new Date(
            parseInt( dateObj.year),
            parseInt( dateObj.month ) - 1,
            parseInt( dateObj.day )
    );
};

/**
 * Get the difference between two dates (in days)
 * @param date1
 * @param date2
 * @return int
 */
DateWrapper.diffDates = function ( date1, date2 ) {
    return Math.ceil( (date1.getTime() - date2.getTime())/(1000*60*60*24) );
};

DateWrapper.getFloatFromTime = function ( t ) {
    var timeArray = t.split(':');
    return (parseFloat( timeArray[0] ) + parseFloat( timeArray[1] )/60);
}

DateWrapper.getTimeFromFloat = function ( f ) {
    var mins = Math.round((f-Math.floor(f))*60);
    if (mins < 10 ) {
        mins = '0' + mins;
    }
    if ( mins >= 60 ) {
        mins = mins - 60;
        f += 1;
    }
    var hours = Math.floor(f);
    if ( hours < 10 ) {
        hours = '0' + hours;
    }
    return hours + ':' + mins;
}