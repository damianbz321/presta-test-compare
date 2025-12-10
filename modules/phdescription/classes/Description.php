<?php

class Description
{

    public static function addDescription($id_product, $type, $id_shop)
    {
        $position = Description::getProductDescriptionPosition($id_product);
        Db::getInstance()->insert('phdescription', array(
            'id_product' => (int)$id_product,
            'type' => (int)$type,
            'position' => (int)$position,
            'active' => 1,
            'id_shop' => (int)$id_shop,
            'date_add' => date('Y-m-d H:i:s'),
            'date_upd' => date('Y-m-d H:i:s'),
        ));
        return (int)Db::getInstance()->Insert_ID();
    }

    public static function addDescriptionLang($id_description, $id_product, $text, $text2, $image, $image2, $id_lang)
    {
//        echo $id_product.' || '.$id_description.' || '.$text.' || '.$text2.' || '.$image.' || '.$image2.' || '.$id_lang;
        return Db::getInstance()->insert('phdescription_lang', array (
            'id_product' => (int)$id_product,
            'id_description' => (int)$id_description,
            'image' => $image,
            'image2' => $image2,
            'text' => addslashes($text),
            'text2' => addslashes($text2),
            'id_lang' => (int)$id_lang,
            'date_add' => date('Y-m-d H:i:s'),
            'date_upd' => date('Y-m-d H:i:s'),
        ));
    }

    public static function deleteDescription($id_description)
    {
        $current = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'phdescription` WHERE `id_description` = '.(int)$id_description);

        $res = Db::getInstance()->delete('phdescription', 'id_description = '.(int)$id_description);
        $res &= Db::getInstance()->delete('phdescription_lang', 'id_description = '.(int)$id_description);

        $positions = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'phdescription` WHERE `id_product` = '.(int)$current['id_product'].' AND `position` > '.$current['position']);
        if (!empty($positions)) {
            foreach ($positions as $position) {
                Db::getInstance()->update('phdescription', array(
                    'position' => ($position['position'] - 1)
                ), 'id_description = '.$position['id_description']);
            }
        }
        return $res;
    }

    public static function updateDescriptionLang($id_description, $id_product, $text, $text2, $image, $image2, $id_lang)
    {
        return Db::getInstance()->update('phdescription_lang', array (
            'image' => $image,
            'image2' => $image2,
            'text' => addslashes($text),
            'text2' => addslashes($text2),
            'date_upd' => date('Y-m-d H:i:s'),
        ), 'id_description = '.(int)$id_description.' AND id_lang = '.(int)$id_lang);
    }

    public static function getProductDescriptionPosition($id_product)
    {
        return (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'phdescription` WHERE `id_product` = '.(int)$id_product);
    }

    public static function getProductDescriptionLang($id_description, $id_product, $id_lang)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'phdescription_lang` WHERE `id_product` = '.(int)$id_product.' AND `id_description` = '.(int)$id_description.' AND `id_lang` = '.(int)$id_lang);
    }

    public static function getProductDescription($id_product, $id_lang)
    {
        $return = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'phdescription` WHERE `id_product` = '.(int)$id_product.' ORDER BY `position` ASC');
        if (!empty($return)) {
            foreach ($return as &$item) {
                $item['lang'] = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'phdescription_lang` WHERE `id_product` = '.(int)$id_product.' AND `id_description` = '.(int)$item['id_description'].' AND `id_lang` = '.(int)$id_lang);
            }
        }
        return $return;
    }

    public static function getProductDescriptionByLang($id_product, $id_shop = 1)
    {
        $return = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'phdescription` WHERE `id_product` = '.(int)$id_product.' ORDER BY `position` ASC');
        if (!empty($return)) {
            $languages = Language::getLanguages(false, $id_shop);
            foreach ($return as &$item) {
                foreach ($languages as $language) {
                    $item['lang'][$language['id_lang']] = Description::getProductDescriptionLang((int)$item['id_description'], $id_product, $language['id_lang']);
                }
//                $item['lang'] = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'phdescription_lang` WHERE `id_product` = '.(int)$id_product.' AND `id_description` = '.(int)$item['id_description'].' AND `id_lang` = '.(int)$id_lang);
            }
        }
        return $return;
    }

}
