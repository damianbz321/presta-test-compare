{if !empty($pendingSend) && $pendingSend}
    <script type="application/javascript">
        $.ajax({
            'url' : '{$sendUrl}',
            'async' : true
        });
    </script>
{/if}

<link type="text/css" href="{$base_url}modules/freshmail/views/css/freshmail-core.css" rel="stylesheet" />