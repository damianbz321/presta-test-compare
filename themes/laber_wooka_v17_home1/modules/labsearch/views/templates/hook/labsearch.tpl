<div class="laberDisplaySearch pull-right">
	<div class="laberSearch dropdown js-dropdown ">
		<div class="expand-more" data-toggle="dropdown">
			<i class="icon_search icon-search"></i>
		</div>
		<div class="laberSearch-i dropdown-menu">
			<div id="_desktop_Search_top">
				<div id="search_widget" class=" search-widget" data-search-controller-url="{$link->getPageLink('search')|escape:'html':'UTF-8'}">
					<div class="wrap_search_widget">
						<form method="get" action="{$link->getPageLink('search')|escape:'html':'UTF-8'}" id="searchbox">
							<input type="hidden" name="controller" value="search" />
							<input type="text" id="input_search" name="search_query" placeholder="Wpisz czego szukasz" class="ui-autocomplete-input" autocomplete="off" />
							<button type="submit">
								<i class="icon icon-search"></i>
								<span class="hidden-xl-down">{l s='Search' d='Shop.Theme.Laberthemes'}</span>
							</button>
						</form>
						<div id="search_popup"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
