{**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}


{block name='header_banner'}
  <div class="header-banner">
    {hook h='displayBanner'}
  </div>
{/block}

{block name='header_nav'}
  <nav class="header-nav">
    <div class="container">
      <div class="row">
        <div class="hidden-sm-down">
			<div class="col-xs-12">
				{hook h='displayNav1'}
			</div>
        </div>
        <div class="hidden-md-up text-sm-center mobile">
          <div class="float-xs-left" id="menu-icon">
            <i class="material-icons d-inline">&#xE5D2;</i>
          </div>
          <div class="float-xs-right" id="_mobile_Setting"></div>
          <div class="float-xs-right" id="_mobile_cart"></div>
          <div class="float-xs-right" id="_mobile_wishtlistTop"></div>
          <div class="float-xs-right" id="_mobile_user_info"></div>
          <div class="clearfix"></div>
		  <div class="top-logo" id="_mobile_logo"></div>
		  <div class="Search_top" id="_mobile_Search_top"></div>
        </div>
      </div>
    </div>
  </nav>
{/block}

{block name='header_top'}
  <div class="header-top">
    <div class="container">
       <div class="row">
        <div class="top-logo pull-left hidden-sm-down" id="_desktop_logo">
            {if $page.page_name == 'index'}
              <h1>
                <a href="{$urls.base_url}">
                  <img width="200" height="40" class="logo img-responsive" src="{$shop.logo}" alt="{$shop.name}">
                </a>
              </h1>
            {else}
				<div class="h1">
					<a href="{$urls.base_url}">
					  <img width="200" height="40" class="logo img-responsive" src="{$shop.logo}" alt="{$shop.name}">
					</a>
				</div>
            {/if}
        </div>
        <div class="pull-right position-static hidden-sm-down">
			<div class="laberIpad">
			<div id="_desktop_Setting" class="pull-right">
				<div class="laberSetting">
					<div class="dropdown js-dropdown ">
						<div class="expand-more" data-toggle="dropdown">
							<i class="icon-setting  icon-align-right"></i>
						</div>
						<div class="laberSetting-i dropdown-menu">
							{hook h='displayNav2'}
						</div>
					</div>
				</div>
			</div>
			{hook h='displayTop'}
			<div class="clearfix"></div>
			</div>
        </div>
		<div class="container_lab_megamenu hidden-sm-down">
			<div class="laberMegamenu">
				<div class="displayMegamenu">
					{hook h='displayMegamenu'}
				</div>
			</div>
		</div>
      </div>
      <div id="mobile_top_menu_wrapper" class="row hidden-md-up" style="display:none;">
        <div class="js-top-menu mobile" id="_mobile_top_menu"></div>
        <div class="js-top-menu-bottom">
			{hook h='displayMegamenu'}
        </div>
      </div>
    </div>
  </div>
  {hook h='displayNavFullWidth'}
{/block}


<style>
.laberCart.laberItem button {
  height:42px;
  font-size:12px;
  min-width: 50%;
  width: 50%;
  padding-left: 5px;
}
.laberCart.laberItem a.new_variant {
  height:42px;
  width: 50%;
  text-align: center;
}
.new_variant {
  text-transform: uppercase;
  font-size: 11px;
}
h2.productName {
  height: 35px;
}
</style>
