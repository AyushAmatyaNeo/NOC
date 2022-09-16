'use strict';
window.nepaliDatePickerExt = (function () {
    var monthsInStringFormat = {
        1: 'Jan',
        2: 'Feb',
        3: 'Mar',
        4: 'Apr',
        5: 'May',
        6: 'Jun',
        7: 'Jul',
        8: 'Aug',
        9: 'Sep',
        10: 'Oct',
        11: 'Nov',
        12: 'Dec'
    };
    var pad = function (d) {
        return (d < 10) ? '0' + d.toString() : d.toString();
    };

    return {
        fromNepaliToEnglish: function (dateInNepali) {
            var englishDate = new Date(BS2AD(dateInNepali));
            return pad(englishDate.getDate()) + "-" + monthsInStringFormat[englishDate.getMonth() + 1] + "-" + englishDate.getFullYear();
        },
        fromEnglishToNepali: function (dateInEnglish) {
            var englishDate = this.getDate(dateInEnglish);
            var englishDateFormatted =
                    englishDate.getFullYear() + '-'
                    + pad(englishDate.getMonth() + 1) + '-'
                    + pad(englishDate.getDate());
            return AD2BS(englishDateFormatted);
        },
        getDate: function (formattedDate) {
            var splittedDate = formattedDate.split("-");
            monthsInStringFormat.getKeyByValue = function (value) {
                for (var prop in this) {
                    if (this.hasOwnProperty(prop)) {
                        if (this[ prop ] === value)
                            return prop;
                    }
                }
            };
            return new Date(splittedDate[2], monthsInStringFormat.getKeyByValue(splittedDate[1]) - 1, parseInt(splittedDate[0]));
        },
        getFormatedDate: function (date) {
            return  date.getDate() + "-" + monthsInStringFormat[date.getMonth() + 1].toUpperCase() + "-" + date.getFullYear();
        }
    };
})();