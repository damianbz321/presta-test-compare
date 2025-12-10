<ul id="pshow_notif" class="header-list component">
    <li id="notification" class="dropdown">
        <a href="javascript:void(0);" class="notification dropdown-toggle notifs">
            <i class="icon-PrestashowModules"></i>

            {if $notifications|count}
                <span id="pshow_notif_number_wrapper" class="notifs_badge">
                    <span id="pshow_notif_value">
                        {if $notifications|count >= 5}
                            {$notifications|count}+
                        {else}
                            {$notifications|count}
                        {/if}
                    </span>
                </span>
            {/if}
        </a>
        <div class="dropdown-menu dropdown-menu-right notifs_dropdown">
            <div class="notifications">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item active" style="width: 100%;">
                        <a class="nav-link" data-toggle="tab" href="#pshow-notifications" role="tab">
                            {l s='New notifications from PrestaShow modules'}
                            {if $notifications|count}
                                <span id="pshow_notif_value" data-nb="{$notifications|count}">
                                    {if $notifications|count >= 5}
                                        ({$notifications|count}+)
                                    {else}
                                        ({$notifications|count})
                                    {/if}
                                </span>
                            {/if}
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active {if $notifications|count == 0}empty{/if}" id="pshow-notifications"
                         role="tabpanel">
                        {if $notifications|count}
                            <div class="notification-elements">
                                {foreach from=$notifications item='notification'}
                                    {assign var='url' value="javascript:void(0);"}
                                    {if !empty($notification->getUrl())}
                                        {assign var='url' value=$notification->getUrl()}
                                    {/if}
                                    <a class="notif" href='{$url}'>
                                        <small class='text-muted pull-right'>
                                            <i class='icon-time'></i>
                                            {$notification->getDateAdd()}
                                        </small>
                                        <p><strong>{$notification->getModuleName()}</strong></p>
                                        <p>{$notification->getMessage()}</p>
                                    </a>
                                {/foreach}
                            </div>
                        {else}
                            <p class="no-notification">
                                {l s='No new notifications.'}
                            </p>
                        {/if}
                    </div>
                </div>

                <footer class="panel-footer">
                    <a href="#" id="link-see-all-pshow-notif">
                        {l s='Click to see all notifications'}
                        <i class="material-icons">chevron_right</i>
                    </a>
                </footer>
            </div>
        </div>
    </li>
</ul>
