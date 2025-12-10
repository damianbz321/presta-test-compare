<?php

require dirname(__FILE__).'/config/config.inc.php';




$comments = Db::getInstance()->executeS('
        SELECT 
            `ps_product_comment`.*, 
            `ps_product`.*, 
            `ps_product_lang`.`name` as product_name, 
            `ps_customer`.`firstname` as customer_firstname, 
            `ps_customer`.`lastname` as customer_lastname,
            `ps_customer`.`email` as customer_email1,
            `ps_manufacturer`.`name` as manufacturer_name
        FROM `ps_product_comment`
        LEFT JOIN `ps_product` ON `ps_product`.`id_product`=`ps_product_comment`.`id_product`
        LEFT JOIN `ps_product_lang` ON `ps_product_lang`.`id_product`=`ps_product_comment`.`id_product`
        LEFT JOIN `ps_customer` ON `ps_customer`.`id_customer`=`ps_product_comment`.`id_customer`
        LEFT JOIN `ps_manufacturer` ON `ps_product`.`id_manufacturer`=`ps_manufacturer`.`id_manufacturer`
        WHERE `ps_product_comment`.`validate` = 1
');

$row = '';

foreach ($comments as $comment) {


    $dateReview = date("Y-m-j\\TH:i:s\\Z",strtotime($comment['date_add']));
    $id_lang = Context::getContext()->language->id;
    $product = new Product($comment['id_product'], false, $id_lang);
    $sku = $product->reference;
    $gtin = $product->ean13;
    $link = new Link();
    $product_url = $link->getProductLink($product);


    $row .= "<review>
            <review_id>{$comment['id_product_comment']}</review_id>
            <reviewer>
                <name is_anonymous=\"true\">{$comment['customer_email1']}</name>
                <reviewer_id>{$comment['id_customer']}</reviewer_id>
            </reviewer>
            <review_timestamp>{$dateReview}</review_timestamp>
            <title>{$comment['title']}</title>
            <content>{$comment['content']}</content>
            <review_language>pl</review_language>
            <review_country>PL</review_country>
            <review_url type=\"singleton\">{$product_url}#comment-{$comment['id_product_comment']}</review_url>
            <ratings>
                <overall min=\"1\" max=\"5\">{$comment['grade']}</overall>
            </ratings>
            <products>
                <product>
                    <product_ids>
                        <gtins>
                            <gtin>{$gtin}</gtin>
                        </gtins>
                        <mpns>
                            <mpn>{$comment['supplier_reference']}</mpn>
                        </mpns>
                        <skus>
                            <sku>{$sku}</sku>
                        </skus>
                        <brands>
                            <brand>{$comment['manufacturer_name']}</brand>
                        </brands>
                    </product_ids>
                    <product_name>{$comment['product_name']}</product_name>
                    <product_url>{$product_url}</product_url>
                </product>
            </products>
        </review>";



//
//    $review = $xml->addChild('review');
//    $review->addChild('product', htmlspecialchars($comment['product_name']));
//    $review->addChild('author', htmlspecialchars($comment['customer_firstname'])." ".htmlspecialchars($comment['customer_lastname']).' '.htmlspecialchars($comment['customer_email1']));
//    $review->addChild('rating', $comment['grade']);
//    $review->addChild('ean', $comment['ean13']);
//    $review->addChild('id_product', $comment['id_product']);
//    $review->addChild('comment', htmlspecialchars($comment['content']));
//    $review->addChild('date', $comment['date_add']);
}



$XML = '
<feed xmlns:vc="http://www.w3.org/2007/XMLSchema-versioning"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xsi:noNamespaceSchemaLocation=
 "http://www.google.com/shopping/reviews/schema/product/2.4/product_reviews.xsd">
    <version>2.4</version>
    <aggregator>
        <name>HiFood</name>
    </aggregator>
    <publisher>
        <name>HiFood</name>
    </publisher>
    <reviews>
    '.$row.'
    </reviews>
</feed>';

Header('Content-type: text/xml');
echo $XML;

