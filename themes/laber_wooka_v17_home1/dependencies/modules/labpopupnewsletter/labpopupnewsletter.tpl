<div id="laberPopupnewsletter" class="modal fade" tabindex="-1" role="dialog">
  <div class="laberPopupnewsletter-i" role="document" style="{if $laber_popupnewlestter.LAB_WIDTH}max-width:{$laber_popupnewlestter.LAB_WIDTH}px;{/if}">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
<div class="labpopupnewsletter" style="{if $laber_popupnewlestter.LAB_HEIGHT}height:{$laber_popupnewlestter.LAB_HEIGHT}px;{/if}{if $laber_popupnewlestter.LAB_BG == 1 && !empty($laber_popupnewlestter.LAB_BG_IMAGE)}background-image: url({($laber_popupnewlestter.LAB_BG_IMAGE)});{/if}">
	{if $laber_popupnewlestter.LAB_NEWSLETTER == 1}
	<div id="newsletter_block_popup" class="block">
		<div class="block_content">
		{if isset($msg) && $msg}
			<p class="{if $nw_error}warning_inline{else}success_inline{/if}">{$msg}</p>
		{/if}
		<form method="post">
				{if $laber_popupnewlestter.LAB_TITLE}					<div class="newsletter_title">						<h3 class="h3">{$laber_popupnewlestter.LAB_TITLE|stripslashes nofilter}</h3>					</div>				{/if}
				{if $laber_popupnewlestter.LAB_TEXT}					<div class="laberContent">						{$laber_popupnewlestter.LAB_TEXT|stripslashes nofilter}					</div>				{/if}
				<div class="labAlert"></div>
				<input class="inputNew" id="labnewsletter-input" type="text" name="email" placeholder="{l s='Enter your mail...' mod='labpopupnewsletter'}"/>
				<div class="send-reqest button_unique">{l s='Subscribe!' mod='labpopupnewsletter'}</div>
		</form>
		</div>
			<div class="newsletter_block_popup-bottom">
				<div class="subscribe-bottom">	
					<input id="laber_newsletter_dont_show_again" type="checkbox">
				</div>
				<label class="laber_newsletter_dont_show_again" for="laber_newsletter_dont_show_again">{l s='Do not show this popup again' mod='labpopupnewsletter'}</label>
			</div>
		</div>
	{/if}
</div>
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

{if $laber_popupnewlestter.LAB_NEWSLETTER == 1}
<script type="text/javascript">
    var placeholder2 = "{l s='Enter your mail...' mod='labpopupnewsletter' js=1}";
    {literal}
        $(document).ready(function() {
            $('#labnewsletter-input').on({
                focus: function() {
                    if ($(this).val() == placeholder2) {
                        $(this).val('');
                    }
                },
                blur: function() {
                    if ($(this).val() == '') {
                        $(this).val(placeholder2);
                    }
                }
            });
        });
    {/literal}
</script>
{/if}
<script type="text/javascript">
var field_width={$laber_popupnewlestter.LAB_WIDTH};
var field_height={$laber_popupnewlestter.LAB_HEIGHT};
var field_newsletter={$laber_popupnewlestter.LAB_NEWSLETTER};
var field_path='{$laber_popupnewlestter.LAB_PATH}';
</script>
