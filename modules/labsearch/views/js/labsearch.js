$(document).ready(function() {
    var timer;

    $( "#input_search" ).keyup(function() {
        clearTimeout(timer);
        timer = setTimeout(function() {
            var search_key = $( "#input_search" ).val();
            $.ajax({
                type: 'GET',
                url: prestashop['urls']['base_url'] + 'modules/labsearch/ajax.php',
                headers: { "cache-control": "no-cache" },
                async: true,
                data: 'search_key=' + search_key,
                success: function(data) {
                    $('#search_popup').innerHTML = data;
                }
            }) .done(function( msg ) {
                $( "#search_popup" ).html(msg);
            });
        }, 1000);
    })

    $('html').click(function() {
        $( "#search_popup" ).html('');
    });

    $('#search_popup').click(function(event){
        event.stopPropagation();
    });
});