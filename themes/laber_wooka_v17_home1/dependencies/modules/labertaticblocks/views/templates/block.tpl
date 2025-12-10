
     {foreach from=$staticblocks key=key item=block}
	  {if $block.active == 1}
		  <h3 class ="laberTitle_html"> {l s={$block.title nofilter} } </h3>
	  {/if}
	  {$block.description nofilter}
     {/foreach}