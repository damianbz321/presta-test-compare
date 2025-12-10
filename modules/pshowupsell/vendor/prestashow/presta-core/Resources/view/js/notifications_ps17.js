/* globals PShowMainControllerUrl */

function getDateTime() {
    let today = new Date();
    let date = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
    let time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
    return date + ' ' + time;
}

function getPShowNotifications() {
    let currentDateTime = getDateTime();
    if ($('#header_infos #pshow_notif li').hasClass('open')) {
        setTimeout('getPShowNotifications()', 15000);
        return;
    }
    $.ajax({
        url: PShowMainControllerUrl + '&ajax=1&action=renderNotifications',
        success: function(result){
            if (result.indexOf('pshow_notif') < 0) {
                return;
            }

            $('#header_infos #pshow_notif').remove();
            $('#header_infos #header_employee_box').before(result);

            $('#pshow_notif a#link-see-all-pshow-notif').attr(
                'href',
                PShowMainControllerUrl + '&page=allNotifications'
            );

            let read = false;
            $('#header_infos #pshow_notif a.notifs').on('click', function () {
                if (read) return;
                read = true;
                $('#header_infos #pshow_notif_number_wrapper').hide();
                $.get(PShowMainControllerUrl + '&ajax=1&action=readNotifications&date=' + currentDateTime);
            });

            $('#header_infos #pshow_notif li').click(function(){
                $(this).toggleClass('open');
            });

            setTimeout('getPShowNotifications()', 60000);
        }
    });
}

let waitForjQueryInterval = setInterval(function () {
    if (typeof $ === 'undefined') {
        return;
    }
    clearInterval(waitForjQueryInterval);
    if (typeof window.pshow_notif_header === 'undefined') {
        window.pshow_notif_header = 1;
        getPShowNotifications();
    }
}, 100);
