{*
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
*}
<style>
    .module_page_tab {
        display:none;
    }
</style>
<script>
    function displayModulePageTab(tab) {
        $('.module_page_tab').hide();
        $('.tab-row.active').removeClass('active');
        $('#module_page_' + tab).show();
        $('#module_page_link_' + tab).parent().addClass('active');
    }

    $(document).ready(function(){
        {if Tools::getValue('ocoupon_edit', 'false') != 'false' && Tools::getValue('id_reward',0) != 0}
            displayModulePageTab('ocNewReward');
        {/if}
        {if Tools::isSubmit('submit_settings_updates_now') == 1}
            displayModulePageTab('updates');
        {/if}
        {if Tools::isSubmit('btnSubmit_ostates') == 1}
            displayModulePageTab('ocMain');
        {/if}
        {if Tools::isSubmit('btnSubmit_newVoucher') == 1 || Tools::isSubmit('ocoupon_remove')}
            displayModulePageTab('ocListVouchers');
        {/if}
        {if Tools::getValue('addNewReward') == 1}
            displayModulePageTab('ocNewReward');
        {/if}
        {if Tools::getValue('id_reward_pattern', 0) != 0}
            displayModulePageTab('ocPattern');
        {/if}
    })
</script>

<div class="clearfix">
    <div class="productTabs">
        <ul class="tab nav nav-tabs">
            <li class="tab-row">
                <a class="tab-page" id="module_page_link_ocMain" href="javascript:displayModulePageTab('ocMain');"><i class="icon-wrench"></i> {l s='Main settings' mod='ordercoupons'}</a>
            </li>
            <li class="tab-row  active">
                <a class="tab-page" id="module_page_link_ocListVouchers" href="javascript:displayModulePageTab('ocListVouchers');"><i class="icon-wrench"></i> {l s='Rewards' mod='ordercoupons'}</a>
            </li>
            <li class="tab-row ">
                {if Tools::getValue('ocoupon_edit', 'false') != 'false' && Tools::getValue('id_reward',0) != 0}
                    <a class="tab-page" id="module_page_link_ocNewReward" href="javascript:displayModulePageTab('ocNewReward');"><i class="icon-pencil"></i> {l s='Edit reward' mod='ordercoupons'}</a>
                {else}
                    <a class="tab-page" id="module_page_link_ocNewReward" href="javascript:displayModulePageTab('ocNewReward');"><i class="icon-plus-circle"></i> {l s='New reward' mod='ordercoupons'}</a>
                {/if}
            </li>
            {if Tools::getValue('id_reward_pattern', 0) != 0}
                <li class="tab-row ">
                    <a class="tab-page" id="module_page_link_ocPattern" href="javascript:displayModulePageTab('ocPattern');"><i class="icon-tag"></i> {l s='Voucher pattern' mod='ordercoupons'}</a>
                <li>
            {/if}
            <li class="tab-row" style="float:right;">
                <a class="tab-page" id="module_page_link_updates" href="javascript:displayModulePageTab('updates');"><i class="icon-refresh"></i> {l s='Updates' mod='regpro'}</a>
            </li>
        </ul>
    </div>
    <div id="module_page_ocMain" class="module_page_tab">
        {$ocMain nofilter}
    </div>
    <div id="module_page_ocListVouchers" class="module_page_tab"  style="display: block;">
        <div class="panel">
            {$ocListVouchers nofilter}
        </div>
    </div>
    <div id="module_page_ocNewReward" class="module_page_tab">
        <div class="panel">
            {if Tools::getValue('ocoupon_edit', 'false') != 'false' && Tools::getValue('id_reward',0) != 0}
                <div class="alert alert-info">
                    {l s='You are editing existing reward now. If you want to create new reward, just press this button' mod='ordercoupons'}
                    <br/><br/>
                    <div class="clearfix">
                        <button onclick="window.location.href = '{Context::getContext()->link->getAdminLink('AdminModules')}&configure=ordercoupons&addNewReward=1'" value="1" class="btn btn-default "><i class="process-icon-plus"></i>
                            {l s='Add new reward' mod='ordercoupons'}
                        </button>
                    </div>
                </div>
            {/if}
            {$ocNewReward nofilter}
        </div>
    </div>
    <div id="module_page_ocPattern" class="module_page_tab" style="display: none;">
        <div class="panel">
            {$ocPattern nofilter}
        </div>
    </div>
    <div id="module_page_updates" class="module_page_tab">
        {$ocUpdates nofilter}
    </div>
</div>