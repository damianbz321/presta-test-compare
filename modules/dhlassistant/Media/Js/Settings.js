document.addEventListener("DOMContentLoaded", function(event) {

    $('#field_div_DhlUser_Dhl24Link').css("display","none");
    $('#field_div_DhlUser_DhlPsLink').css("display","none");

    SelectApiLink = function () {

        var typeAccount = $('#field_DhlUser_AccountType').val();
        var Dhl24Link = document.getElementById("field_DhlUser_Dhl24Link");
        var DhlPsLink = document.getElementById("field_DhlUser_DhlPsLink");

        $.each(Dhl24Link, function (key, value) {
            if (typeAccount == value.innerText) {
                value.setAttribute('selected', true);
            } else {
                value.removeAttribute('selected');
            }
        });

        $.each(DhlPsLink, function (key, value) {
            if (typeAccount == value.innerText) {
                value.setAttribute('selected', true);
            } else {
                value.removeAttribute('selected');
            }
        });
    }
    $(function () {
        $('#field_DhlUser_AccountType').bind('change', SelectApiLink);
    });
});