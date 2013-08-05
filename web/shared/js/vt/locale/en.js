var Lang = {
    common: {
        'deleteDialogHeader': 'Delete object'
        , 'deleteBtn': 'Delete'
        , 'cancelBtn': 'Cancel'
        , 'windowOpened': 'External window is opened'
        , 'windowClose': 'unblock'
        , 'autocompleteHint': 'Text field with autocompletion'
    }
    , dateFormat: {
        dayNames: [
            "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
            "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
        ],
        monthNames: [
            "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
            "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
        ]
    }
    , parseTime: function ( string ) {
        var timeArr = string.split(':');
        if ( timeArr.length < 2 ) {
            return 'not specified';
        }
        timeArr[0] = parseFloat( timeArr[0] );
        timeArr[1] = parseFloat( timeArr[1] );

        var hours = 'hours';
        var minutes = 'minutes';

        if ( timeArr[0] == 1 ) {
            hours = 'hour';
        }

        if ( timeArr[1] == 1 ) {
            minutes = 'minute';
        }

        if( timeArr[0] == 0 && timeArr[1] == 0 ) {
            return 'not specified';
        }
        if ( timeArr[0] == 0 ) {
            return timeArr[1] + ' ' + minutes;
        }
        if ( timeArr[1] == 0 ) {
            return timeArr[0] + ' ' + hours;
        }
        return timeArr[0] + ' ' + hours + ' ' + timeArr[1] + ' ' + minutes;
    }
    , parseDate: function ( date ) {
        var months = [
            'January'
            , 'February'
            , 'March'
            , 'April'
            , 'May'
            , 'June'
            , 'July'
            , 'August'
            , 'September'
            , 'October'
            , 'November'
            , 'December'
        ];
        return date.getDate() + ' ' + months[date.getMonth( )];
    }
};