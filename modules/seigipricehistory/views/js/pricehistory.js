document.addEventListener('readystatechange', function (){
    if(document.readyState != 'complete')
        return;
    if(typeof prestashop === 'object'){
        prestashop.on('clickQuickView', chartParser._registerUpdateTimeout);
    }
    chartParser.modal = document.getElementById("seigi-price-history-modal");
    if(chartParser.modal)
        document.body.append(chartParser.modal);
    if(document.getElementById('open-chart')){
        $('body').on('click', '#open-chart', function (){
            chartParser._makeChart(this);
        })
    }
    document.addEventListener('click', function (event){
        if((elem = event.target.closest('a[data-call="open-chart"]'))){
            try {
                chartParser._makeChart(elem.querySelector('.lowest-price-listing'));
            }catch (e){}
        }
    });
    if(sph_combs && document.getElementById('lowest-price')){
        if(typeof prestashop === 'object'){
            prestashop.on('updatedProduct', chartParser._registerTimeout);
        }else {
            var targetNode = document.getElementById('main') || document.getElementById('our_price_display');
            let config = { attributes: false, childList: true, subtree: (targetNode ? false : true)};
            let observer_callback = () => {
                chartParser._registerTimeout();
            };
            chartParser.observer = new MutationObserver(observer_callback);
            chartParser.observer.observe(targetNode || document.body, config);

            window.addEventListener('hashchange', chartParser._registerTimeout);
            window.addEventListener('hashchange', function (event){
                let hash = event.newURL.slice(event.newURL.indexOf('#')+1);
                //tries to disconnect observer since hashes are present
                if(hash.indexOf('/') !== -1)
                    chartParser.observer.disconnect();
            }, {once: true});
        }
        if(chartParser._getButton() && chartParser._getCombinationId())
            chartParser._registerTimeout();
    }
    if(sph_translations.unavailable.length){
        if($('#open-chart:visible').length !== $('#open-chart').length || $('#lowest-price:visible').length !== $('#lowest-price').length){
            document.querySelector('h1').textContent = sph_translations.unavailable;
            document.querySelector('h1').style.color = '#d11623';
            document.querySelector('h1').style.border = '5px solid #d11623';
            alert(sph_translations.unavailable);
        }
    }
});
chartParser = {
    init: false,
    current_id: null,
    current_comb: null,
    cache: {},
    attr_cache: {},
    bottom_text: {},
    text_cache: {},
    lang: '',
    curr_id: '',
    curr_iso: '',
    timeout: null,
    observer: null,
    prodName: false,
    fromZero: false,
    selector: '',
    _getToken: function(){
        if(typeof static_token === 'undefined'){
            return prestashop.token;
        }
        return static_token;
    },
    _getCombinationId: function (){
        if(typeof sph_combs != 'undefined' && !sph_combs)
            return 0;
        if(document.getElementById('idCombination'))
            return document.getElementById('idCombination').value
        if(document.querySelector('[data-product]')){
            let data = document.querySelector('[data-product]').dataset.product;
            var prod_data;
            try{
                prod_data = JSON.parse(data);
            }catch (e) {prod_data = false;}
            if(prod_data)
                return prod_data.id_product_attribute;
        }
        chartParser.attributeList = {};
        document.querySelectorAll('[data-product-attribute]').forEach((a)=>{
            if(a.checked == void 0 || a.checked == 1)
                chartParser.attributeList[a.dataset.productAttribute] = a.value;
        });
        if(Object.entries(chartParser.attributeList).length == 0)
            return 0;
        return JSON.stringify(chartParser.attributeList);
    },
    _getCurrency: function (){
        if(!chartParser.curr_id){
            chartParser.curr_id = JSON.parse(chartParser._getButton().dataset.currency).id;
        }
        return chartParser.curr_id;
    },
    _getCurrencyISO: function (){
        if(!chartParser.curr_iso){
            chartParser.curr_iso = JSON.parse(chartParser._getButton().dataset.currency).iso;
        }
        return chartParser.curr_iso;
    },
    _getButton: function(){
        return chartParser.btn = document.getElementById('open-chart') || document.getElementById('lowest-price');
    },
    _makeChart: function (button){
        if(!chartParser.modal){
            chartParser.getModalForChart();
            chartParser.modal = document.getElementById("seigi-price-history-modal");
        }
        if(!chartParser.init){
            chartParser.span = chartParser.modal.getElementsByClassName("close")[0];
            chartParser.lang = button.dataset.lang;
            let options = JSON.parse(chartParser.modal.dataset.chartOptions);
            if(typeof options.fromZero != 'undefined'){
                chartParser.fromZero = options.fromZero;
            }
            if(typeof options.prodName != 'undefined'){
                chartParser.prodName = options.prodName;
            }
            options.options.plugins.tooltip = {
                callbacks: {
                    title: function(context) {
                        return context[0].raw.x;
                    },
                    label: function(context) {
                        if(context.raw.i)
                            return;
                        var label = sph_translations.price || context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed.y !== null) {
                            label += new Intl.NumberFormat(chartParser.lang, { style: 'currency', currency: chartParser._getCurrencyISO() }).format(context.parsed.y);
                        }
                        return label;
                    },
                    footer: function(context) {
                        if(context[0].raw.red){
                            let red = context[0].raw.red;
                            return sph_translations.reduction+' '+red.perc+'% ('+new Intl.NumberFormat(chartParser.lang, { style: 'currency', currency: chartParser._getCurrencyISO() }).format(red.val)+')';
                        }
                        return '';
                    },
                    labelColor: function(context) {
                        return {
                            borderColor: context.dataset.borderColor,
                            backgroundColor: context.dataset.borderColor
                        };
                    },
                }
            }
            let ctx = document.getElementById('myChart').getContext('2d');
            chartParser.chart = new Chart(ctx, options);
            chartParser.init = true;
        }
        chartParser._checkDataComatibility(button);
        chartParser.modal.style.display = "block";
        chartParser._addEvents();
        chartParser._resize();
    },
    _closeChart: function (e){
        if(chartParser._close(e))
            chartParser._removeEvents();
    },
    _addEvents:function (){
        window.addEventListener('resize', chartParser._resizeEvent);
        chartParser.span.addEventListener('click',chartParser._closeChart);
        chartParser.modal.addEventListener('click',chartParser._closeChart);
        chartParser.modal.querySelector('#chart_date_start').addEventListener('change',chartParser._changeDateRange);
        chartParser.modal.querySelector('#chart_date_end').addEventListener('change',chartParser._changeDateRange);
    },
    _removeEvents:function (){
        window.removeEventListener('resize', chartParser._resizeEvent);
        chartParser.span.removeEventListener('click',chartParser._closeChart);
        chartParser.modal.removeEventListener('click',chartParser._closeChart);
        chartParser.modal.querySelector('#chart_date_start').removeEventListener('change',chartParser._changeDateRange);
        chartParser.modal.querySelector('#chart_date_end').removeEventListener('change',chartParser._changeDateRange);
    },
    _close: function (e){
        if(!(e.target === chartParser.span || e.target === chartParser.modal))
            return false;
        chartParser.modal.style.display = "none";
        return true;
    },
    _checkDataComatibility: function (button){
        var id, curr = null;
        if(button.dataset.idProduct)
            var id = button.dataset.idProduct;
        if(button.dataset.currency)
            var curr = JSON.parse(button.dataset.currency);
        let comb_id = chartParser._getCombinationId();
        if(chartParser.current_id !== id || chartParser.current_comb === null || chartParser.current_comb !== comb_id){
            var json = chartParser._getChartData(comb_id, id, curr);
            var cache = (id ? id+'_':'') + comb_id;
            var attrs = [];
            if(chartParser.prodName){
                chartParser.attr_cache[cache].forEach((a) =>{attrs.push('<b>' +a.name + ':</b> ' + a.value)});
                document.getElementById('chart-name').innerHTML = document.querySelector('h1').textContent;
                document.getElementById('chart-attr').innerHTML = attrs.join(' • ');
            }
            if(!json)
                return;
            json.data.forEach((a,b)=>{
                chartParser.chart.data.datasets[b].data = a;
            })
            if(chartParser.bottom_text[cache].length){
                document.getElementById('footer-price-custom-text').style.display = 'block';
                document.getElementById('footer-price-text').style.display = 'none';
                document.getElementById('footer-price-custom-text').innerText = chartParser.bottom_text[cache];
            }else{
                document.getElementById('footer-price-custom-text').style.display = 'none';
                document.getElementById('footer-price-text').style.display = 'block';
                chartParser.modal.getElementsByClassName('lowest-price')[0].textContent = new Intl.NumberFormat(chartParser.lang, { style: 'currency', currency: chartParser._getCurrencyISO() }).format(json.low);
            }
            chartParser.chart.options.scales.yAxes.min = chartParser._calcMin(json.low);
            chartParser.chart.options.scales.yAxes.max = chartParser._calcMax(json.high);
            chartParser.chart.update();
            chartParser.current_id = id;
            chartParser.current_comb = comb_id;
        }
    },
    _calcMin: function (num){
        if(chartParser.fromZero){
            return 0;
        }
        let decimals = Math.max(Math.floor(Math.log10(num)), 2);
        return Math.max(Math.round(num/Math.pow(10,decimals-1))*Math.pow(10,decimals-1)-Math.pow(10,decimals-1), 0);
    },
    _calcMax: function (num){
        let decimals = Math.max(Math.floor(Math.log10(num)), 2);
        return Math.round(num/Math.pow(10,decimals-1))*Math.pow(10,decimals-1)+Math.pow(10,decimals-1);
    },
    _changeDateRange: function (e){
        var type = '';
        if(!(type = e.target.dataset.type))
            return;
        chartParser.chart.options.scales.xAxes[type] = e.target.value;
        chartParser.chart.update();
    },
    _resizeEvent: function(){
        if(chartParser.resizeTimer)
            clearTimeout(chartParser.resizeTimer)
        chartParser.resizeTimer = setTimeout(function (){
            chartParser._resize();
        },200);
    },
    _resize: function (){
        if(window.innerWidth/window.innerHeight > 1.6){
            var height = window.innerHeight *0.8;
            var width = height * 1.6;
        }else{
            var width = window.innerWidth *0.95;
            var height = width / 1.6;
        }
        chartParser.modal.getElementsByClassName('modal-content')[0].style.width = width+'px';
        chartParser.modal.getElementsByTagName('canvas')[0].width = width;
        chartParser.modal.getElementsByTagName('canvas')[0].height = height;
        height += chartParser.modal.getElementsByClassName('modal-header')[0].getBoundingClientRect().height + chartParser.modal.getElementsByClassName('modal-footer')[0].getBoundingClientRect().height;
        chartParser.modal.getElementsByClassName('modal-content')[0].style.height = height+'px';
        chartParser.chart.update();
    },
    _getChartData: function (comb_id, id_prod = null, currency = null){
        if(currency){
            chartParser.curr_id = currency.id;
            chartParser.curr_iso = currency.iso;
        }
        if(!id_prod)
            id_prod = chartParser._getButton().dataset.idProduct;

        var cache = (id_prod ? id_prod+'_':'') + comb_id;
        if(typeof chartParser.cache[cache] == 'undefined') {
            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: chartUri+'?rand=' + new Date().getTime(),
                async: false,
                cache: true,
                dataType: "json",
                data: 'token=' + chartParser._getToken() + '&ajax=true&action=getChartData&id_product=' + id_prod + '&id_combination=' + comb_id + '&currency=' + chartParser._getCurrency(),
                success: function (jsonData) {
                    if (jsonData.result) {
                        chartParser.cache[cache] = jsonData.data;
                        chartParser.attr_cache[cache] = jsonData.attr_list;
                        chartParser.bottom_text[cache] = jsonData.text;
                    } else
                        console.log('Failed to access chart data.')
                },
                error: function () {
                    console.log('Failed to access chart data.');
                }
            });
        }
        return chartParser.cache[cache];
    },
    _registerTimeout: function (){
        chartParser.timeout = setTimeout(chartParser._getTextData, 10, chartParser._getCombinationId(), chartParser._getButton().dataset.idProduct)
    },
    _registerUpdateTimeout: function (params){
        setTimeout(chartParser._updateQuickView, 300, params)
    },
    _updateQuickView: function(params) {
        chartParser.selector = `#quickview-modal-${params.dataset.idProduct}-${params.dataset.idProductAttribute} `;
        var id_comb, id_prod;
        if(params){
            id_prod = params.dataset.idProduct;
            if(params.dataset.id_product_attribute)
                id_comb = params.dataset.idProductAttribute;
        }else{
            id_prod = chartParser._getButton().dataset.idProduct;
            id_comb = chartParser._getCombinationId();
        }
        chartParser.timeout = setTimeout(chartParser._getTextData, 10, id_comb, id_prod)
    },
    _getTextData: function (id_comb, id_prod){
        if(typeof chartParser.text_cache[id_comb] == 'undefined') {
            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: chartUri+'?rand=' + new Date().getTime(),
                async: false,
                cache: true,
                dataType: "json",
                data: 'token=' + chartParser._getToken() + '&ajax=true&action=getTextData&id_product=' + id_prod + '&id_combination=' + id_comb + '&currency=' + chartParser._getCurrency(),
                success: function (jsonData) {
                    if (jsonData.result) {
                        chartParser.text_cache[id_comb] = jsonData.data;
                        if(chartParser.selector && !document.querySelector(chartParser.selector))
                            chartParser.selector = '';
                        document.querySelector(chartParser.selector +'#lowest-price[data-id-product="'+id_prod+'"]').outerHTML = jsonData.data;
                    } else
                        console.log('Failed to access chart data.')
                },
                error: function () {
                    console.log('Failed to access chart data.');
                }
            });
        }else{
            if(chartParser.selector && !document.querySelector(chartParser.selector))
                chartParser.selector = '';
            document.querySelector(chartParser.selector +'#lowest-price[data-id-product="'+id_prod+'"]').outerHTML = chartParser.text_cache[id_comb];
        }
    },
    getModalForChart: function (){
        $.ajax({
            type: 'POST',
            headers: {"cache-control": "no-cache"},
            url: chartUri+'?rand=' + new Date().getTime(),
            async: false,
            cache: true,
            dataType: "json",
            data: 'token=' + chartParser._getToken() + '&ajax=true&action=renderChart',
            success: function (jsonData) {
                if (jsonData.result) {
                    document.body.insertAdjacentHTML(
                        'beforeend',
                        jsonData.data
                    );
                    chartParser.init = false;
                    chartParser.current_id = 0;
                    chartParser.current_comb = null;
                } else
                    console.log('Failed to connect to server.')
            },
            error: function () {
                console.log('Failed to connect to server.');
            }
        });
    }
}
