SELECT_TAB = {
    init: function (tab_id)
    {
        $('nav#nav-sidebar li').removeClass('active');

        $('#' + tab_id)
                .addClass('active')
                .addClass('-active')
                .parent()
                .parent()
                .addClass('active')
                .addClass('-active')
                .addClass('open')
        ;
    }

};
