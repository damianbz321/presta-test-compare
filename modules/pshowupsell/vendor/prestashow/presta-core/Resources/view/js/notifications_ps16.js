/* globals PShowMainControllerUrl */

function getDateTime() {
    let today = new Date();
    let date = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
    let time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
    return date + ' ' + time;
}

function getPShowNotifications() {
    let currentDateTime = getDateTime();
    if ($('li#pshow_notif').hasClass('open')) {
        setTimeout('getPShowNotifications()', 15000);
        return;
    }
    $.ajax({
        url: PShowMainControllerUrl + '&ajax=1&action=renderNotifications',
        success: function(result){
            if (result.indexOf('pshow_notif') < 0) {
                return;
            }
            $('li#pshow_notif').remove();
            $('#header_notifs_icon_wrapper').append(result);
            $('#list_pshow_notif').css({
                "max-height": "300px",
                "overflow-y": "auto"
            });
            $('#header_notifs_icon_wrapper li#pshow_notif .notifs_panel_footer a').attr(
                'href',
                PShowMainControllerUrl + '&page=allNotifications'
            );
            let read = false;
            $('li#pshow_notif a.notifs').on('click', function () {
                if (read) return;
                read = true;
                $('#pshow_notif_number_wrapper').hide();
                $.get(PShowMainControllerUrl + '&ajax=1&action=readNotifications&date=' + currentDateTime);
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
