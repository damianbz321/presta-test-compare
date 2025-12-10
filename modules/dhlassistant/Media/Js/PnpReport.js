document.addEventListener("DOMContentLoaded", function (event) {

    document.getElementById("field_div_ApiCode").style.display = "none";

    PackageType = function () {
        var valPackageType = $('#field_PackageType').val();

        if (valPackageType == 'DHLPS') {
            // $("select.field_ApiCode").val("DHLPS").change();
            $("#field_ApiCode").val('DHLPS');
            //alert($('#field_ApiCode').val());
        } else {
            $("#field_ApiCode").val('DHL24');
        }
    }

    ApiCodeChange = function () {

        var val = $('#field_ApiCode').val();

        if (val == 'DHL24') {
            $('#field_div_PackageType').show();
        }
    }
    /*READY*/
    $(function () {
        $('#field_ApiCode').bind('change', ApiCodeChange);
        $('#field_PackageType').bind('change', PackageType);
    });
});