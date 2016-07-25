function getStatsByMonth() {
    var nowTemp = new Date();
    
    var datePicked = $('#datetimepickerMonths').data('date');
    var year = datePicked.substring(3, 7);
    var month = datePicked.substring(0, 2);
    if (year < nowTemp.getFullYear() ||
            (year == nowTemp.getFullYear() && month <= (nowTemp.getMonth() + 1))) {
        $('#month-search-error').hide();
        $('#month-stats').slideDown();
        $.ajax({
            type: "POST",
            url: "index.php?action=get_month_stats",
            data: {'datePicked': datePicked},
            success: function(data, textStatus, jqXHR) {
                $('#month-stats').html(data);
            }
        });
    } else {
        //@TODO vérifier le debut du système aussi !
        $('#month-stats').slideUp();
        $('#month-search-error').show();
    }
}

function getStatsByNDays() {
    var nDays = parseInt($('#nDays').val());
    if(!$.isNumeric(nDays) || $('#nDays').val() === ''){
        $('#nDays-stats').slideUp();
        $('#nDays-search-error').show();
        return false;
    }

        $('#nDays-search-error').hide();
        $('#nDays-stats').slideDown();
        $.ajax({
            type: "POST",
            url: "index.php?action=get_nDays_stats",
            data: {'nDays': nDays},
            success: function(data, textStatus, jqXHR) {
                $('#nDays-stats').html(data);
            }
        });

}

function getCsvByAsset(){
    
    $.ajax({
            type: "POST",
            url: "index.php?action=get_csv_assets",
            data: {},
            success: function(data, textStatus, jqXHR) {
                location.replace('csv/csv_assets.csv')
            }
        });
        
}