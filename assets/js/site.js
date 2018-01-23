/**
 * Created by Léo on 21/01/2018.
 */

$( document ).ready(function() {
    $('.tooltipped').tooltip({delay: 50, position: 'top'});
    $('.modal').modal();
    $('select').material_select();
    $('.counter').characterCounter();

    $('.notification-new').click(function() {
        var notification = $(this).data('notification');

        if (notification != '') {
            $.ajax({
                type: 'POST',
                url: notificationPath,
                data: { notification: notification },
                dataType: 'json',
                success: function (data) {
                    if (data.type == 'success') $('#notification-' + notification).removeClass('notification-new');
                    else Materialize.toast(data.content, 3000, 'bc-' + data.type);
                },
                error: function(xhr, status, error) {
                    Materialize.toast(xhr.responseText, 3000, 'toast-error');
                }
            });
        }
    });
});

function datePicker() {
    $('.date-picker').pickadate({
        container: "body",
        labelMonthNext: 'Mois suivant',
        labelMonthPrev: 'Mois précédent',
        labelMonthSelect: 'Selectionner le mois',
        labelYearSelect: 'Selectionner une année',
        monthsFull: [ 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre' ],
        monthsShort: [ 'Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aou', 'Sep', 'Oct', 'Nov', 'Dec' ],
        weekdaysFull: [ 'Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi' ],
        weekdaysShort: [ 'Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam' ],
        weekdaysLetter: [ 'D', 'S', 'T', 'Q', 'Q', 'S', 'S' ],
        today: 'Aujourd\'hui',
        clear: 'Réinitialiser',
        close: 'Fermer',
        format: 'dd/mm/yyyy',
        formatSubmit: 'yyyymmdd',
        closeOnSelect: true
    });
}