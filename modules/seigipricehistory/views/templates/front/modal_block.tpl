<div id="seigi-price-history-modal" class="modal" data-chart-options='{$options}'>
    <div class="modal-content" style="width: 800px; height: 600px;">
        <div class="modal-header">
            <span class="close">&times;</span>
            {if $show_dates}
                <p>
                    <span>{l s='From' mod='seigipricehistory'}:&nbsp;<input id="chart_date_start" type="date" name="date_start" data-type="min" max="{date('Y-m-d')}"></span>
                    <span>{l s='To' mod='seigipricehistory'}:&nbsp;<input id="chart_date_end" type="date" name="date_end" data-type="max" max="{date('Y-m-d')}"></span>
                </p>
            {/if}
            <p id="chart-name">{l s='History of prices' mod='seigipricehistory'}</p>
            <div id="chart-attr"></div>
        </div>
        <div class="modal-body">
            <canvas id='myChart' width='800' height='500' aria-label="Price graph" role="img">
                <p>{l s='Your Browser does not support canvas' mod='seigipricehistory'}</p>
            </canvas>
        </div>
        <div class="modal-footer">
            <p id="footer-price-text">
                {l s='Lowest Price of this product' mod='seigipricehistory'}: <span class="lowest-price"></span>
            </p>
            <p id="footer-price-custom-text" style="display: none;"></p>
        </div>
    </div>
</div>
