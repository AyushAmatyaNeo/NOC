$(document).ready(function () {
    var itemVal = document.todoTaskList;
    $('#lobilist').lobiList({
        sortable: false,
        onSingleLine: true,
        lists: [
            {
                id: 'lob',
                title: 'Tasks',
                defaultStyle: 'lobilist-warning',
                controls: [],
                useCheckboxes: true,
                items: itemVal,

            }
        ],
        actions: {
            'update': document.todoUpdateUrl,
            'insert': document.todoAddUrl,
            'delete': document.todoDeleteUrl
        },
        afterMarkAsDone: function ($list, $object) {
            updateStatus($object);
        },
        afterMarkAsUndone: function ($list, $object) {
            updateStatus($object);
        },

    });

    var $list = $('#lob').data('lobiList');
    var $dueDateInput = $list.$el.find('form [name=dueDate]');
    window.app.addDatePicker($dueDateInput);


    updateStatus = function ($data) {
        $.ajax({
            type: 'POST',
            url: document.todoTaskUpdateStatus,
            data: {'item': $data},
        }).done(function (res) {
        });
    };
});
