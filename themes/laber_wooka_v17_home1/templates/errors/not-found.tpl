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
<section id="content" class="page-content page-not-found" style="max-width:100%;margin: 0;overflow: hidden;">
  {block name='page_content'}

    <h4>Wybrana strona nie została odnaleziona</h4>
    <p>Możesz skożystać z wyszukiwarki lub poprostu użyj przycisku aby wrócić do strony głownej.</p>

    <a class="btn btn-primary add-to-cart" href="https://hifood.pl/">Przejdź do strony głównej</a>

    {block name='hook_not_found'}
      {hook h='displayNotFound'}
    {/block}

    <div class="wrap_search_widget">
      <form method="get" action="https://hifood.pl/szukaj" id="searchbox">
        <input type="hidden" name="controller" value="search">
        <input style="width: 300px;margin-top: 30px;float: left;" type="text" id="input_search" name="search_query" placeholder="Wpisz czego szukasz" class="form-control" autocomplete="off">
        <button type="submit" class="btn btn-primary" style="min-width:50px;width: 50px;float: left;margin-top: 30px;height: 38px;">
          <i class="icon icon-search"></i>
          <span class="hidden-xl-down">Search</span>
        </button>
      </form>
    </div>

  {/block}
</section>
