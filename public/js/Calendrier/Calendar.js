$(document).ready(function() {
    $('.today').live('click', function() {
        const today = new Date();
        const annee = today.getFullYear();
        let jour = today.getDate();
        let mois = today.getMonth() + 1; 
        if (jour < 10) jour = '0' + jour;
        if (mois < 10) mois = '0' + mois;

        const dateToday = jour + "/" + mois + "/" + annee;
        $(this).prev().attr('value',dateToday);
    });

    
    $('.date').live('click', function() {
        $(this).datepicker({ showOn: 'focus', dateFormat: 'dd/mm/yy', firstDay: 1 }).focus();
    });
});