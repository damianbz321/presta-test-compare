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
{foreach $stylesheets.external as $stylesheet}
  <link rel="stylesheet" href="{$stylesheet.uri}" type="text/css" media="{$stylesheet.media}">
{/foreach}

{foreach $stylesheets.inline as $stylesheet}
  <style>
    {$stylesheet.content}
  </style>
{/foreach}

<style>
#pdpopupspro .checkbox a.iframe, #pdpopupspro .checkbox label[for="ncp"] {
  color: #fff;
}

.laberCart.laberItem a.new_variant {
  font-size: 10px;
}
  .promo-code-button.display-promo {
    background: rgb(171, 204, 53) !important;
    padding: 8px!important;
  }
  .promo-code-button.display-promo a:hover {
    color: #222!important;
  }
#cm{
  padding: 15px!important;
  opacity: .8!important;
}
#c-inr{
  max-width: 100%!important;
}
#c-ttl,#c-txt{
  text-align: center!important;
}
#c-bns{
  display: block!important;
  max-width: 100%!important;
  text-align: center!important;
}
.displayMegamenu ul.menu-content li:nth-child(8) {
  padding-left: 0!important;
}
.displayMegamenu ul.menu-content li{
  height: auto!important;
}
.displayMegamenu ul.menu-content li a{
  line-height: inherit!important;
  padding: 15px 0;
}
.cart_block.block.exclusive {
  max-height: 400px;
  overflow-y: scroll;
  padding-right: 15px;
}
.product-information .addcomment a {
  color: #fff;
}
.product-information .addcomment a:hover {
  color: #fff;
}
.displayPosition .laberFeatured .laber-product-description .productName {
  height: 50px;
}
.total-rating-items-block-footer{
  padding-top: 0;
  padding-left: 0;
}
@media all and (device-width: 768px) and (device-height: 1024px) and (orientation:portrait) {
  #more_menu { padding-top: 15px; }
  .displayMegamenu { width: 100%; margin-left: 20px; }
}

@media (max-width: 600px) {
  #product .laberTabs .nav-tabs .nav-item {
    margin: 0 10px 0 0!important;
  }
  #product .laberTabs .nav-tabs .nav-link {
    font-size:16px!important;
  }
  .laberProduct {
    margin-bottom: 20px!important;
  }
}
#header .laber-cart .cart_block .products .cart-info h2.productName {
  height: auto;
}

#checkout #_desktop_logo {
  width: 20%!important;
}

#checkout #header .header-nav .container .row div.col-md-6:nth-child(2){
  width: 80%!important;
}

@media (min-width: 1400px) and (max-width: 1550px) {
  #_desktop_logo {
    float: none!important;
  }
  .container_lab_megamenu {
    clear: both!important;
    width: 100%!important;
    text-align: left!important;
    margin-left: 22px!important;
  }
}


@media (min-width: 1215px) and (max-width: 1399px) {
  #_desktop_logo {
    float: left!important;
  }
  .container_lab_megamenu {
      width: 500px;
      text-align: center;
      transform: translate(280px, -95px);
      -webkit-transform: translate(280px, -95px);
      -moz-transform: translate(280px, -95px);
      -ms-transform: translate(280px, -95px);
  }
    .lab-menu-horizontal .menu-dropdown {
        top: 170px;
    }

}

</style>
