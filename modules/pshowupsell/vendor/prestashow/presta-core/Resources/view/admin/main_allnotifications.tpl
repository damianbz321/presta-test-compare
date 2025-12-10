<div class="panel">

    <div class="panel-heading">
        <i class="icon-folder-close"></i>
        Notifications from PrestaShow modules
    </div>

    <div class="table-responsive-row">

        <table class="table table-condensed">

            <thead>
            <tr>
                <th class="fixed-width-lg">Module</th>
                <th>Message</th>
                <th class="fixed-width-lg"><i class='icon-time'></i> Date</th>
            </tr>
            </thead>

            <tbody>
            {foreach from=$notifications item='notification'}
                {assign var='trClass' value=''}
                {if $notification->getDateAdd() > $employeeLastRead->getDate()}
                    {assign var='trClass' value='highlighted'}
                {/if}
                {assign var='url' value="javascript:void(0);"}
                {if !empty($notification->getUrl())}
                    {assign var='url' value='document.location=\''|cat:$notification->getUrl()|cat:'\''}
                {/if}
                <tr style="cursor: pointer" class="{$trClass}">
                    <td>{$notification->getModuleName()}</td>
                    <td onclick="{$url}">{$notification->getMessage()}</td>
                    <td>{$notification->getDateAdd()}</td>
                </tr>
            {/foreach}
            </tbody>

        </table>

    </div>

</div>



