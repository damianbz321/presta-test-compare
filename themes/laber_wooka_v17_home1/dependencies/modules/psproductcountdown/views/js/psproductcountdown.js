/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2016 Presta.Site
 * @license   LICENSE.txt
 */
$(function () {
    initCountdown();

    // Blocklayered etc compatibility
    if (pspc_psv < 1.7 && $('#layered_block_left').length) {
        $(document).ajaxComplete(function () {
            initCountdown('.pspc-inactive');
        });
    } else if (typeof prestashop == 'object' && typeof prestashop.on == 'function') {
        prestashop.on('updateProductList', function () {
            initCountdown('.pspc-inactive');
        });

        prestashop.on('updatedProduct', function (event) {
            initCountdown('.pspc-inactive');
        });
    }
});

// Parse countdown string to an object
function pspc_strfobj(str) {
    var pieces = str.split(':');
    var obj = {};
    pspc_labels.forEach(function(label, i) {
        obj[label] = pieces[i]
    });
    return obj;
}
// Return the time components that diffs
function pspc_diff(obj1, obj2) {
    var diff = [];
    pspc_labels.forEach(function(key) {
        if (obj1[key] !== obj2[key]) {
            diff.push(key);
        }
    });
    return diff;
}

function initCountdown(selector) {
    selector = (selector ? selector : '.psproductcountdown');
    $(selector).each(function(){
        $(this).removeClass('pspc-inactive');
        var to = parseInt($(this).data('to'));
        var now = + new Date();
        if (!to || to < now) {
            $(this).hide();
            return true;
        }

        var labels = pspc_labels,
            template = _.template(pspc_countdown_tpl),
            $pspc = $(this).find('.pspc-main');
        if (pspc_show_weeks) {
            var currDate = '00:00:00:00:00';
            var nextDate = '00:00:00:00:00';
        } else {
            var currDate = '00:00:00:00';
            var nextDate = '00:00:00:00';
        }

        // Build the layout
        var initData = pspc_strfobj(currDate);
        labels.forEach(function(label, i) {
            $pspc.append(template({
                curr: initData[label],
                next: initData[label],
                label: label,
                label_lang: pspc_labels_lang[label]
            }));
        });
        // Starts the countdown
        $pspc.countdown(to, function(event) {
            var now = + new Date();

            if (to < now) {
                $pspc.parents('.psproductcountdown').hide(400);
            }

            var data;
            if (pspc_show_weeks)
                var newDate = event.strftime('%w:%d:%H:%M:%S');
            else
                var newDate = event.strftime('%D:%H:%M:%S');

            if (newDate !== nextDate) {
                currDate = nextDate;
                nextDate = newDate;
                // Setup the data
                data = {
                    'curr': pspc_strfobj(currDate),
                    'next': pspc_strfobj(nextDate)
                };
                // Apply the new values to each node that changed
                pspc_diff(data.curr, data.next).forEach(function(label) {
                    var selector = '.%s'.replace(/%s/, label),
                        $node = $pspc.find(selector);
                    // Update the node
                    $node.removeClass('flip hidden');
                    $node.find('.curr').text(data.curr[label]);
                    $node.find('.next').text(data.next[label]);
                    // Wait for a repaint to then flip
                    _.delay(function($node) {
                        $node.addClass('flip');
                    }, 50, $node);
                });
            }
        });
    });
}

var pspc_countdown_tpl = '' +
        '<div class="time <%= label %>">' +
            '<span class="count curr top"><%= curr %></span>' +
            '<span class="count next top"><%= next %></span>' +
            '<span class="count next bottom"><%= next %></span>' +
            '<span class="count curr bottom"><%= curr %></span>' +
            '<span class="label"><%= label_lang.length < 6 ? label_lang : label_lang.substr(0, 3)  %></span>' +
    '</div>';