var Lang = {
    common: {
        'deleteDialogHeader': 'Удаление объекта'
        , 'deleteBtn': 'Удалить'
        , 'cancelBtn': 'Отменить'
        , 'windowOpened': 'Открыто вспомогательное окно'
        , 'windowClose': 'закрыть'
        , 'autocompleteHint': 'Поле с автодополнением текста'
    }
    , dateFormat: {
        dayNames: [
            'Вс','Пн','Вт','Ср','Чт','Пт','Сб',
            'Воскресенье','Понедельник','Вторник','Среда','Четверг','Пятница','Суббота'
        ],
        monthNames: [
            'Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек',
            'Январь','Февраль','Март','Апрель','Май','Июнь', 'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'
        ]
    }
    , declOfNum: function (number, titles) {
        cases = [2, 0, 1, 1, 1, 2];
        return titles[ (number%100>4 && number%100<20)? 2 : cases[(number%10<5)?number%10:5] ];
    }
    , parseTime: function ( string ) {
        var timeArr = string.split(':');
        if ( timeArr.length < 2 ) {
            return 'не указано';
        }

        timeArr[0] = parseFloat( timeArr[0] );
        timeArr[1] = parseFloat( timeArr[1] );

        hours = Lang.declOfNum( timeArr[0], ['час', 'часа', 'часов'] );
        minutes = Lang.declOfNum( timeArr[1], ['минута', 'минуты', 'минут'] );

        if( timeArr[0] == 0 && timeArr[1] == 0 ) {
            return 'не указано';
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
            'января'
            , 'февраля'
            , 'марта'
            , 'апреля'
            , 'мая'
            , 'июня'
            , 'июля'
            , 'августа'
            , 'сентября'
            , 'октября'
            , 'ноября'
            , 'декабря'
        ];
        return date.getDate() + ' ' + months[date.getMonth( )];
    }
};

/**
 * i18n for calendar
 */
jQuery(function($){
	$.datepicker.regional['ru'] = {clearText: 'Очистить', clearStatus: '',
		closeText: 'Закрыть', closeStatus: '',
		prevText: '&lt;Пред',  prevStatus: '',
		nextText: 'След&gt;', nextStatus: '',
		currentText: 'Сегодня', currentStatus: '',
		monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
		'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
		monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
		'Июл','Авг','Сен','Окт','Ноя','Дек'],
		monthStatus: '', yearStatus: '',
		weekHeader: 'Не', weekStatus: '',
		dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
		dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
		dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
		dayStatus: 'DD', dateStatus: 'D, M d',
		dateFormat: 'dd.mm.yy', firstDay: 1,
		initStatus: '', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['ru']);
});