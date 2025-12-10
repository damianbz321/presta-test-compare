<li id="pshow_notif" class="dropdown">
    <a href="javascript:void(0);" class="dropdown-toggle notifs" data-toggle="dropdown">
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
    <div class="dropdown-menu notifs_dropdown">
        <section id="orders_notif_wrapper" class="notifs_panel">
            <div class="notifs_panel_header">
                <h3>{l s='New notifications from PrestaShow modules'}</h3>
            </div>
            <div id="list_pshow_notif" class="list_notif">
                {if $notifications|count}
                    {foreach from=$notifications item='notification'}
                        {assign var='url' value="javascript:void(0);"}
                        {if !empty($notification->getUrl())}
                            {assign var='url' value=$notification->getUrl()}
                        {/if}
                        <a href='{$url}'>
                            <p><strong>{$notification->getModuleName()}</strong></p>
                            <p>{$notification->getMessage()}</p>
                            <small class='text-muted'>
                                <i class='icon-time'></i>
                                {$notification->getDateAdd()}
                            </small>
                        </a>
                    {/foreach}
                {else}
                    <span class="no_notifs">
                        {l s='No new notifications.'}
                    </span>
                {/if}
            </div>
            <div class="notifs_panel_footer">
                <a href="#">{l s='Show all notifications'}</a>
            </div>
        </section>
    </div>
</li>
