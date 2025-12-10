<!--
/**
 * @author    Marcin Bogdański
 * @copyright OpenNet 2021
 * @license   license.txt
 */
-->
 
{extends file="helpers/list/list_header.tpl"}

{block name=leadin}
<script>
    var ruch_pdf_uri = '{$ruch_pdf_uri|escape:'htmlall':'UTF-8'}';
    var ruch_token = '{$ruch_token|escape:'htmlall':'UTF-8'}';
        
    function ruchDoDownload(id_label) {
    	link = ruch_pdf_uri + '?printLabel=true&id_label=' + id_label + '&token=' + encodeURIComponent(ruch_token);
    	ifr = window.document.getElementById('ruch_down');
    	ifr.src = link;
    	return true;
    }

</script>
<iframe id="ruch_down" style="display: none;"></iframe>

{if isset($printLabels_mode) && $printLabels_mode}
<iframe id="ruch_down_bulk" style="display: none;"></iframe>
<script>
    link = ruch_pdf_uri + '?printLabel=true&bulk=true&ids={$ids|escape:'htmlall':'UTF-8'}&token=' + encodeURIComponent(ruch_token);
    ifr = window.document.getElementById('ruch_down_bulk');
    ifr.src = link;
</script>
{/if}

{/block}