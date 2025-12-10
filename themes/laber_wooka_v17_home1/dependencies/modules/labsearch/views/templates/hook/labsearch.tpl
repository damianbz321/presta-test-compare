<div id="search_widget" class=" search-widget" data-search-controller-url="{$link->getPageLink('search')|escape:'html':'UTF-8'}">
    <div class="wrap_search_widget">
        <form method="get" action="{$link->getPageLink('search')|escape:'html':'UTF-8'}" id="searchbox">
            <input type="hidden" name="controller" value="search" />
            <input type="text" id="input_search" name="search_query" placeholder="{l s='Search our catalog' d='Shop.Theme.Laberthemes'}" class="ui-autocomplete-input" autocomplete="off" />
            <button type="submit">
                <i class="material-icons search"></i>
                <span class="hidden-xl-down">{l s='Search' d='Shop.Theme.Laberthemes'}</span>
            </button>
        </form>
        <div id="search_popup"></div>
    </div>
</div>
