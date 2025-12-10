{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-2021 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}

{if Module::isEnabled('myprestacomments')}
    {assign var='myprestaRatingsAvg' value=MyprestaComment::getAveragesByProduct(Tools::getValue('id_product'), Context::getContext()->language->id)}
    {assign var='myprestaCommentsNb' value=MyprestaComment::getCommentNumber(Tools::getValue('id_product'))}
    {if isset($myprestaRatingsAvg[1])}
        "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "{$myprestaRatingsAvg[1]|escape:'html':'UTF-8'}",
        "ratingCount": "{$myprestaCommentsNb|escape:'html':'UTF-8'}",
        "worstRating": "0",
        "bestRating": "5"
        },
        "review":
        [
        {foreach from=MyprestaComment::getByProduct((int)Tools::getValue('id_product'), 1, null, Context::getContext()->customer->id) item=cmt name=foo}
            {
            "@type": "Review",
            "reviewRating": {
            "@type": "Rating",
            "ratingValue": {$cmt.grade}
            },
            "name": "{$product.name|strip_tags:false}",
            "author": {
            "@type": "Person",
            "name": "{$cmt.customer_name}"
            }
            }
            {if not $smarty.foreach.foo.last},{/if}
        {/foreach}
        ],
    {/if}
{/if}