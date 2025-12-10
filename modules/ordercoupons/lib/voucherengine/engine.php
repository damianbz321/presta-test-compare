<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 * @version   of the productlike engine: 5.5
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
class ordercouponsVoucherEngine extends ordercoupons
{
    public $languages;
    public $id_lang_default;
    public $settings;
    public $prefix;

    public function __construct($prefix = null, $datetype = null)
    {
        $this->languages = Language::getLanguages(false);
        $this->id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $this->prefix = $prefix;
        $this->datetype = $datetype;
        parent::__construct();
    }

    public function generateInput($name, $default = false)
    {
        $input = '';
        $field_value = Configuration::get($this->prefix . $name);
        if (($field_value == false && $field_value == "") && $default != false) {
            $field_value = $default;
        }
        $input .= '<input type="text" class="' . $this->prefix . $name . '" value="' . $field_value . '" name="' . $this->prefix . $name . '">';
        return $input;
    }

    public function displayFlags($languages, $default_language, $ids, $id, $return = false, $use_vars_instead_of_ids = false)
    {
        if (count($languages) == 1) {
            return false;
        }

        $language = new Language($default_language);

        $output = '
        <button type="button" class="btn btn-default dropdown-toggle" onclick="toggleLanguageFlags(this);" alt="" tabindex="-1" data-toggle="dropdown"/>' . $language->iso_code . ' <i class="icon-caret-down"></i></button>
        <ul class="dropdown-menu">';
        foreach ($languages as $language) {
            if ($use_vars_instead_of_ids) {
                $output .= '<li class="languageli"><a tabindex="-1" onclick="changeMain($(this),"' . trim($language['iso_code']) . '");" href="javascript:changeLanguage(\'' . $id . '\', ' . $ids . ', ' . $language['id_lang'] . ', \'' . $language['iso_code'] . '\');" />' . $language['name'] . '</a></li>';
            } else {
                $output .= '<li class="languageli"><a tabindex="-1" onclick="changeMain($(this),\'' . trim($language['iso_code']) . '\');" href="javascript:changeLanguage(\'' . $id . '\', \'' . $ids . '\', ' . $language['id_lang'] . ', \'' . $language['iso_code'] . '\');" />' . $language['name'] . '</a></li>';
            }
        }
        $output .= '</ul>';

        if ($return) {
            return $output;
        }
        echo $output;
    }

    public function generateLangInput($name)
    {
        $input = '';
        foreach ($this->languages as $language) {
            $input .= '
            <div id="' . $this->prefix . $name . '_' . $language['id_lang'] . '" style="display: ' . ($language['id_lang'] == $this->id_lang_default ? 'block' : 'none') . '; float: left;">
            <input type="text" value="' . Configuration::get($this->prefix . $name, $language['id_lang']) . '" name="' . $this->prefix . $name . '[' . $language['id_lang'] . ']">
            </div>';
        }
        $input .= "<div class=\"voucherEngineFlags col-lg-2 row\">" . $this->displayFlags($this->languages, $this->id_lang_default, $this->prefix . $name, $this->prefix . $name, true) . "</div>";
        return $input;
    }

    public function generateLangTextarea($name)
    {
        $input = '';
        foreach ($this->languages as $language) {
            $input .= '
            <div id="' . $this->prefix . $name . '_' . $language['id_lang'] . '" style="display: ' . ($language['id_lang'] == $this->id_lang_default ? 'block' : 'none') . '; float: left;">
                <textarea style="width:250px;height:100px" name="' . $this->prefix . $name . '[' . $language['id_lang'] . ']">' . Configuration::get($this->prefix . $name, $language['id_lang']) . '</textarea>
            </div>';
        }
        $input .= "<div class='flags_block'>" . $this->displayFlags($this->languages, $this->id_lang_default, $this->prefix . $name, $this->prefix . $name, true) . "</div>";
        return $input;
    }

    public function generateTextarea($name)
    {
        $input = '';
        $input .= '<textarea style="width:250px;height:100px"  name="' . $this->prefix . $name . '">' . Configuration::get($this->prefix . $name) . '</textarea>';
        return $input;
    }

    public function generateCurrencySelect($name)
    {
        $input = '<select name="' . $this->prefix . $name . '">';
        foreach (Currency::getCurrencies() as $currency) {
            $input .= "<option " . (Configuration::get($this->prefix . $name) == $currency['id_currency'] ? 'selected="yes"' : '') . " value=\"" . $currency['id_currency'] . "\">" . $currency['name'] . " " . $currency['sign'] . "</option>";
        }
        $input .= '</select>';
        return $input;
    }

    public function generateTaxIncExc($name)
    {
        $input = '<select name="' . $this->prefix . $name . '">
        <option ' . (Configuration::get($this->prefix . $name) == 0 ? 'selected="yes"' : '') . ' value="0">' . $this->l('Tax excluded') . '</option>
        <option ' . (Configuration::get($this->prefix . $name) == 1 ? 'selected="yes"' : '') . ' value="1">' . $this->l('Tax included') . '</option>
        </select>';
        return $input;
    }

    public function generateShippingIncExc($name)
    {
        $input = '<select name="' . $this->prefix . $name . '">
        <option ' . (Configuration::get($this->prefix . $name) == 0 ? 'selected="yes"' : '') . ' value="0">' . $this->l('Shipping excluded') . '</option>
        <option ' . (Configuration::get($this->prefix . $name) == 1 ? 'selected="yes"' : '') . ' value="1">' . $this->l('Shipping included') . '</option>
        </select>';
        return $input;
    }

    public function generateYesOrNo($name, $category_restriction = null, $products_restriction = null, $manufacturers_restriction = null, $attributes_restriction = null, $send_free_gift = null, $carriers_restriction = null, $suppliers_restriction = null, $groups_restriction = null, $country_restriction = null)
    {
        $input = '';
        if ($category_restriction == 1) {
            $input .= '<input onclick="category_restriction(\'' . $this->prefix . $name . '\');" type="radio" name="' . $this->prefix . $name . '" id="' . $this->prefix . $name . '_on" value="1" ' . (Configuration::get($this->prefix . $name) == 1 ? 'checked="checked" ' : '') . '/><label class="t" for="' . $this->prefix . $name . '_on"> <img src="../img/admin/enabled.gif" alt="' . $this->l('Enabled') . '" title="' . $this->l('Enabled') . '" /></label><input onclick="category_restriction(\'' . $this->prefix . $name . '\');" type="radio" name="' . $this->prefix . $name . '" id="' . $this->prefix . $name . '_off" value="0" ' . (Configuration::get($this->prefix . $name) != 1 ? 'checked="checked" ' : '') . '/><label class="t" for="' . $this->prefix . $name . '_off"> <img src="../img/admin/disabled.gif" alt="' . $this->l('Disabled') . '" title="' . $this->l('Disabled') . '" /></label>';
        } else {
            if ($products_restriction == 1) {
                $input .= '<input onclick="products_restriction(\'' . $this->prefix . $name . '\');" type="radio" name="' . $this->prefix . $name . '" id="' . $this->prefix . $name . '_on" value="1" ' . (Configuration::get($this->prefix . $name) == 1 ? 'checked="checked" ' : '') . '/><label class="t" for="' . $this->prefix . $name . '_on"> <img src="../img/admin/enabled.gif" alt="' . $this->l('Enabled') . '" title="' . $this->l('Enabled') . '" /></label><input onclick="products_restriction(\'' . $this->prefix . $name . '\');" type="radio" name="' . $this->prefix . $name . '" id="' . $this->prefix . $name . '_off" value="0" ' . (Configuration::get($this->prefix . $name) != 1 ? 'checked="checked" ' : '') . '/><label class="t" for="' . $this->prefix . $name . '_off"> <img src="../img/admin/disabled.gif" alt="' . $this->l('Disabled') . '" title="' . $this->l('Disabled') . '" /></label>';
            } else {
                if ($manufacturers_restriction == 1) {
                    $input .= '<input onclick="manufacturers_restriction(\'' . $this->prefix . $name . '\');" type="radio" name="' . $this->prefix . $name . '" id="' . $this->prefix . $name . '_on" value="1" ' . (Configuration::get($this->prefix . $name) == 1 ? 'checked="checked" ' : '') . '/><label class="t" for="' . $this->prefix . $name . '_on"> <img src="../img/admin/enabled.gif" alt="' . $this->l('Enabled') . '" title="' . $this->l('Enabled') . '" /></label><input onclick="manufacturers_restriction(\'' . $this->prefix . $name . '\');" type="radio" name="' . $this->prefix . $name . '" id="' . $this->prefix . $name . '_off" value="0" ' . (Configuration::get($this->prefix . $name) != 1 ? 'checked="checked" ' : '') . '/><label class="t" for="' . $this->prefix . $name . '_off"> <img src="../img/admin/disabled.gif" alt="' . $this->l('Disabled') . '" title="' . $this->l('Disabled') . '" /></label>';
                } else {
                    if ($attributes_restriction == 1) {
                        $input .= '<input onclick="attributes_restriction(\'' . $this->prefix . $name . '\');" type="radio" name="' . $this->prefix . $name . '" id="' . $this->prefix . $name . '_on" value="1" ' . (Configuration::get($this->prefix . $name) == 1 ? 'checked="checked" ' : '') . '/><label class="t" for="' . $this->prefix . $name . '_on"> <img src="../img/admin/enabled.gif" alt="' . $this->l('Enabled') . '" title="' . $this->l('Enabled') . '"/></label><input onclick="attributes_restriction(\'' . $this->prefix . $name . '\');" type="radio" name="' . $this->prefix . $name . '" id="' . $this->prefix . $name . '_off" value="0" ' . (Configuration::get($this->prefix . $name) != 1 ? 'checked="checked" ' : '') . '/><label class="t" for="' . $this->prefix . $name . '_off"> <img src="../img/admin/disabled.gif" alt="' . $this->l('Disabled') . '" title="' . $this->l('Disabled') . '" /></label>';
                    } else {
                        if ($name == 'free_shipping') {
                            $input .= '<input type="radio" name="' . $this->prefix . $name . '" id="' . $this->prefix . $name . '_on" value="1" ' . (Configuration::get($this->prefix . $name) == 1 ? 'checked="checked" ' : '') . ' onclick="ps14freeshipping(\'' . $this->prefix . $name . '\',' . (parent::psversion() == 4 ? '1' : '') . ');"/><label class="t" for="' . $this->prefix . $name . '_on"> <img src="../img/admin/enabled.gif" alt="' . $this->l('Enabled') . '" title="' . $this->l('Enabled') . '" /></label><input type="radio" name="' . $this->prefix . $name . '" id="' . $this->prefix . $name . '_off" value="0" ' . (Configuration::get($this->prefix . $name) != 1 ? 'checked="checked" ' : '') . ' onclick="ps14freeshipping(\'' . $this->prefix . $name . '\',' . (parent::psversion() == 4 ? '1' : '') . ');"/><label class="t" for="' . $this->prefix . $name . '_off"> <img src="../img/admin/disabled.gif" alt="' . $this->l('Disabled') . '" title="' . $this->l('Disabled') . '" /></label>';
                            //onclick="ps14freeshipping(,'.(parent::psversion()==4 ? '1':'').');"
                        } else {
                            if ($carriers_restriction == 1) {
                                $input .= '<input onclick="carriers_restriction(\'' . $this->prefix . $name . '\');" type="radio" name="' . $this->prefix . $name . '" id="' . $this->prefix . $name . '_on" value="1" ' . (Configuration::get($this->prefix . $name) == 1 ? 'checked="checked" ' : '') . '/><label class="t" for="' . $this->prefix . $name . '_on"> <img src="../img/admin/enabled.gif" alt="' . $this->l('Enabled') . '" title="' . $this->l('Enabled') . '"/></label><input onclick="attributes_restriction(\'' . $this->prefix . $name . '\');" type="radio" name="' . $this->prefix . $name . '" id="' . $this->prefix . $name . '_off" value="0" ' . (Configuration::get($this->prefix . $name) != 1 ? 'checked="checked" ' : '') . '/><label class="t" for="' . $this->prefix . $name . '_off"> <img src="../img/admin/disabled.gif" alt="' . $this->l('Disabled') . '" title="' . $this->l('Disabled') . '" /></label>';
                            } else {
                                if ($send_free_gift == 1) {
                                    $input .= '<input onclick="send_free_gift(\'' . $this->prefix . $name . '\');" type="radio" name="' . $this->prefix . $name . '" id="' . $this->prefix . $name . '_on" value="1" ' . (Configuration::get($this->prefix . $name) == 1 ? 'checked="checked" ' : '') . '/><label class="t" for="' . $this->prefix . $name . '_on"> <img src="../img/admin/enabled.gif" alt="' . $this->l('Enabled') . '" title="' . $this->l('Enabled') . '"/></label><input onclick="send_free_gift(\'' . $this->prefix . $name . '\');" type="radio" name="' . $this->prefix . $name . '" id="' . $this->prefix . $name . '_off" value="0" ' . (Configuration::get($this->prefix . $name) != 1 ? 'checked="checked" ' : '') . '/><label class="t" for="' . $this->prefix . $name . '_off"> <img src="../img/admin/disabled.gif" alt="' . $this->l('Disabled') . '" title="' . $this->l('Disabled') . '" /></label>';
                                } else {
                                    if ($suppliers_restriction == 1) {
                                        $input .= '<input onclick="suppliers_restriction(\'' . $this->prefix . $name . '\');" type="radio" name="' . $this->prefix . $name . '" id="' . $this->prefix . $name . '_on" value="1" ' . (Configuration::get($this->prefix . $name) == 1 ? 'checked="checked" ' : '') . '/><label class="t" for="' . $this->prefix . $name . '_on"> <img src="../img/admin/enabled.gif" alt="' . $this->l('Enabled') . '" title="' . $this->l('Enabled') . '" /></label><input onclick="suppliers_restriction(\'' . $this->prefix . $name . '\');" type="radio" name="' . $this->prefix . $name . '" id="' . $this->prefix . $name . '_off" value="0" ' . (Configuration::get($this->prefix . $name) != 1 ? 'checked="checked" ' : '') . '/><label class="t" for="' . $this->prefix . $name . '_off"> <img src="../img/admin/disabled.gif" alt="' . $this->l('Disabled') . '" title="' . $this->l('Disabled') . '" /></label>';
                                    } else {
                                        if ($groups_restriction == 1) {
                                            $input .= '<input onclick="groups_restriction(\'' . $this->prefix . $name . '\');" type="radio" name="' . $this->prefix . $name . '" id="' . $this->prefix . $name . '_on" value="1" ' . (Configuration::get($this->prefix . $name) == 1 ? 'checked="checked" ' : '') . '/><label class="t" for="' . $this->prefix . $name . '_on"> <img src="../img/admin/enabled.gif" alt="' . $this->l('Enabled') . '" title="' . $this->l('Enabled') . '" /></label><input onclick="groups_restriction(\'' . $this->prefix . $name . '\');" type="radio" name="' . $this->prefix . $name . '" id="' . $this->prefix . $name . '_off" value="0" ' . (Configuration::get($this->prefix . $name) != 1 ? 'checked="checked" ' : '') . '/><label class="t" for="' . $this->prefix . $name . '_off"> <img src="../img/admin/disabled.gif" alt="' . $this->l('Disabled') . '" title="' . $this->l('Disabled') . '" /></label>';
                                        } else {
                                            if ($country_restriction == 1) {
                                                $input .= '<input onclick="countries_restriction(\'' . $this->prefix . $name . '\');" type="radio" name="' . $this->prefix . $name . '" id="' . $this->prefix . $name . '_on" value="1" ' . (Configuration::get($this->prefix . $name) == 1 ? 'checked="checked" ' : '') . '/><label class="t" for="' . $this->prefix . $name . '_on"> <img src="../img/admin/enabled.gif" alt="' . $this->l('Enabled') . '" title="' . $this->l('Enabled') . '" /></label><input onclick="countries_restriction(\'' . $this->prefix . $name . '\');" type="radio" name="' . $this->prefix . $name . '" id="' . $this->prefix . $name . '_off" value="0" ' . (Configuration::get($this->prefix . $name) != 1 ? 'checked="checked" ' : '') . '/><label class="t" for="' . $this->prefix . $name . '_off"> <img src="../img/admin/disabled.gif" alt="' . $this->l('Disabled') . '" title="' . $this->l('Disabled') . '" /></label>';
                                            } else {
                                                $input .= '<input type="radio" name="' . $this->prefix . $name . '" id="' . $this->prefix . $name . '_on" value="1" ' . (Configuration::get($this->prefix . $name) == 1 ? 'checked="checked" ' : '') . '/><label class="t" for="' . $this->prefix . $name . '_on"> <img src="../img/admin/enabled.gif" alt="' . $this->l('Enabled') . '" title="' . $this->l('Enabled') . '" /></label><input type="radio" name="' . $this->prefix . $name . '" id="' . $this->prefix . $name . '_off" value="0" ' . (Configuration::get($this->prefix . $name) != 1 ? 'checked="checked" ' : '') . '/><label class="t" for="' . $this->prefix . $name . '_off"> <img src="../img/admin/disabled.gif" alt="' . $this->l('Disabled') . '" title="' . $this->l('Disabled') . '" /></label>';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $input;
    }

    public function generateReductionType($name)
    {
        $array = array();
        $array['1']['img'] = 'enabled';
        $array['1']['name'] = $this->l('Percent(%)');
        $array['2']['img'] = 'enabled';
        $array['2']['name'] = $this->l('Amount');
        if (parent::psversion() == 5 || parent::psversion() == 6 || parent::psversion() == 7) {
            $array['3']['img'] = 'disabled';
            $array['3']['name'] = $this->l('None');
        }
        $input = '';
        foreach ($array as $key => $value) {
            $input .= "<input " . (Configuration::get($this->prefix . $name) == $key ? 'checked="true"' : '') . " onchange=\"reduction_type('{$this->prefix}','{$name}');\" name=\"" . $this->prefix . $name . "\" id=\"" . $this->prefix . $name . "_radio$key\" type=\"radio\" value=\"" . $key . "\"> " . ($value['img'] == "enabled" ? '<img src="../img/admin/enabled.gif" />' : '<img src="../img/admin/disabled.gif">') . "" . $value['name'] . "&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        $input .= '
        <div id="' . $this->prefix . $name . '_1" ' . (Configuration::get($this->prefix . $name) == 1 ? 'style="margin-top:15px;"' : 'style="display:none; margin-top:15px;"') . ' />
        ' . $this->l('Value') . ' ' . $this->generateInput('reduction_percent') . ' %
        </div>';
        $input .= '<div id="' . $this->prefix . $name . '_2" ' . (Configuration::get($this->prefix . $name) == 2 ? 'style="margin-top:15px;"' : 'style="display:none; margin-top:15px;"') . ' />
        ' . $this->l('Amount') . ' ' . $this->generateInput('reduction_amount') . ' ' . $this->generateCurrencySelect('reduction_currency') . ' ' . (parent::psversion() == 5 || parent::psversion() == 6 || parent::psversion() == 7 ? $this->generateTaxIncExc('reduction_tax') : '') . '
        </div>';
        return $input;
    }

    public function generateApplyDiscountTo($name)
    {
        $array = array();
        $array['apply_discount_to_order']['name'] = $this->l('Order (without shipping)');
        $array['apply_discount_to_order']['value'] = 'order';
        $array['apply_discount_to_product']['name'] = $this->l('Specific product');
        $array['apply_discount_to_product']['value'] = 'specific';
        $array['apply_discount_to_cheapest_product']['name'] = $this->l('Cheapest product');
        $array['apply_discount_to_cheapest_product']['value'] = 'cheapest';
        $array['apply_discount_to_selected_products']['name'] = $this->l('Selected products');
        $array['apply_discount_to_selected_products']['value'] = 'selected';
        $input = '';
        foreach ($array as $key => $value) {
            $input .= "<input " . (Configuration::get($this->prefix . $name) == $value['value'] ? 'checked="true"' : '') . " onchange=\"apply_discount_to('{$this->prefix}{$name}');\" name=\"" . $this->prefix . $name . "\" id=\"" . $this->prefix . $name . "_radio$key\" type=\"radio\" value=\"" . $value['value'] . "\"> " . $value['name'] . "&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        $input .= '<div id="' . $this->prefix . $name . '_specific" ' . (Configuration::get($this->prefix . $name) == "specific" ? 'style="margin-top:15px;"' : 'style="display:none; margin-top:15px;"') . ' />' . $this->l('Product ID') . ' ' . $this->generateInput('reduction_product') . '
        <p class="preference_description">' . $this->l('enter product ID number') . ' <a href="https://mypresta.eu/en/art/basic-tutorials/how-to-get-product-id-in-prestashop.html" target="blank"/>' . $this->l('how to get product id?') . '</a></p>
        </div>';
        return $input;
    }

    public function generateCategoriesSelect($name)
    {
        $cat_tree = $this->generateYesOrNo($name, 1);
        $exp = explode(',', Configuration::get($this->prefix . $name . '_cr'));
        $catchecks = array();
        foreach ($exp as $value) {
            $catchecks[$value] = true;
        }
        $id_lang = Configuration::get('PS_LANG_DEFAULT');
        $result = Category::getCategories($id_lang);
        $cat_tree .= '<div id="' . $this->prefix . $name . '_cr" style="clear:both; display:block; ' . (Configuration::get($this->prefix . $name) == 1 ? '' : 'display:none;') . '"><select name="' . $this->prefix . $name . '_cr[]" style="display:block; clear:both; overflow:auto; border:1px solid #AAAAAA; width:400px; height:160px; " multiple="">';
        foreach ($result as $value) {
            foreach ($value as $value1) {
                $cat_tree .= '<option ' . $this->catchecks($catchecks, $value1['infos']['id_category']) . ' value="' . $value1['infos']['id_category'] . '"/>' . $value1['infos']['name'] . '</option>';
            }
        }
        $cat_tree .= '</select><p class="preference_description">' . $this->l('Select categories from list above, use CTRL+click to select multiple categories, CTRL+A to select all of them') . '</p></div>';
        return $cat_tree;
    }

    public function generateProductsSelect($name)
    {
        $cat_tree = $this->generateYesOrNo($name, 0, 1);
        $exp = array();
        if (Configuration::get($this->prefix . $name . '_pr') != '') {
            $exp = explode(',', Configuration::get($this->prefix . $name . '_pr'));
            $prodchecks = array();
            foreach ($exp as $value) {
                $prodchecks[$value] = true;
            }
        }
        /** CODE FROM OLD VERSION OF ENGINE: $result = Product::getProducts($id_lang,0,0,'name','asc',false,false,null); **/
        $cat_tree .= '<div id="' . $this->prefix . $name . '_pr" style="overflow:hidden; display:block; clear:both; ' . (Configuration::get($this->prefix . $name) == 1 ? '' : 'display:none;') . '"><div style="display:inline-block; float:left;"><select id="selectbox_' . $this->prefix . $name . '_pr" name="' . $this->prefix . $name . '_pr[]" style="display:block; clear:both; overflow:auto; border:1px solid #AAAAAA; width:400px; height:220px; " multiple="">';
        if ($exp != '' && count($exp) > 0) {
            foreach ($exp as $value) {
                $cat_tree .= '<option selected="yes" value="' . ($value ? $value : '') . '"/>' . $this->getProductName(($value ? $value : '')) . '</option>';
            }
        }
        $cat_tree .= '</select></div><div style="display:inline-block; float:left; margin-left:10px; padding-top:5px;"><span class="button btn btn-default" onclick=\'$("#selectbox_' . $this->prefix . $name . '_pr option:selected").remove();\'>' . $this->l('Remove') . '</span><br/><br/>' . $this->l('Search for product:') . '<br/>' . '<input type="text" class="prod_search" /><input type="hidden" name="selectbox_prefix" value="' . $this->prefix . $name . '"><div id="prod_search_result" style="height:140px; overflow:auto; width:200px"></div></div><div style="clear:both; display:block; "><p class="preference_description">' . $this->l('Select products from list above, use CTRL+click to select multiple products, CTRL+A to select all of them') . '</p></div></div>';
        return $cat_tree;
    }

    public function getProductName($id)
    {
        $product = new Product($id, false, Configuration::get('PS_LANG_DEFAULT'));
        return $product->name;
    }

    public function generateCharactersSelect($name)
    {
        return '
        <select name="' . $this->prefix . $name . '">
            <option ' . (Configuration::get($this->prefix . $name) == 'alphanumeric' ? 'selected' : '') . ' value="alphanumeric">' . $this->l('alphanumeric') . '</option>
            <option ' . (Configuration::get($this->prefix . $name) == 'alpha' ? 'selected' : '') . ' value="alpha">' . $this->l('alpha') . '</option>
            <option ' . (Configuration::get($this->prefix . $name) == 'numeric' ? 'selected' : '') . ' value="numeric">' . $this->l('numeric') . '</option>
        </select>
        ';
    }

    public function generateManufacturersSelect($name)
    {
        $cat_tree = $this->generateYesOrNo($name, 0, 0, 1);
        $exp = explode(',', Configuration::get($this->prefix . $name . '_mr'));
        $prodchecks = array();
        foreach ($exp as $value) {
            $prodchecks[$value] = true;
        }
        $id_lang = Configuration::get('PS_LANG_DEFAULT');
        $result = Manufacturer::getManufacturers(false, $id_lang, false, false);
        $cat_tree .= '<div id="' . $this->prefix . $name . '_mr" style="display:block; clear:both; ' . (Configuration::get($this->prefix . $name) == 1 ? '' : 'display:none;') . '"><select name="' . $this->prefix . $name . '_mr[]" style="display:block; clear:both; overflow:auto; border:1px solid #AAAAAA; width:400px; height:160px; " multiple="">';
        foreach ($result as $value) {
            $cat_tree .= '<option ' . $this->prodchecks($prodchecks, $value['id_manufacturer']) . ' value="' . $value['id_manufacturer'] . '"/>' . $value['name'] . '</option>';
        }
        $cat_tree .= '</select><p class="preference_description">' . $this->l('Select manufacturers from list above, use CTRL+click to select multiple products, CTRL+A to select all of them') . '</p></div>';
        return $cat_tree;
    }

    public function generateSuppliersSelect($name)
    {
        $cat_tree = $this->generateYesOrNo($name, 0, 0, 1);
        $exp = explode(',', Configuration::get($this->prefix . $name . '_mr'));
        $prodchecks = array();
        foreach ($exp as $value) {
            $prodchecks[$value] = true;
        }
        $id_lang = Configuration::get('PS_LANG_DEFAULT');
        $result = Supplier::getSuppliers(false, $id_lang, false, false);
        $cat_tree .= '<div id="' . $this->prefix . $name . '_mr" style="display:block; clear:both; ' . (Configuration::get($this->prefix . $name) == 1 ? '' : 'display:none;') . '"><select name="' . $this->prefix . $name . '_mr[]" style="display:block; clear:both; overflow:auto; border:1px solid #AAAAAA; width:400px; height:160px; " multiple="">';
        foreach ($result as $value) {
            $cat_tree .= '<option ' . $this->prodchecks($prodchecks, $value['id_supplier']) . ' value="' . $value['id_supplier'] . '"/>' . $value['name'] . '</option>';
        }
        $cat_tree .= '</select><p class="preference_description">' . $this->l('Select suppliers from list above, use CTRL+click to select multiple products, CTRL+A to select all of them') . '</p></div>';
        return $cat_tree;
    }

    public function generateCarriersSelect($name)
    {
        $cat_tree = $this->generateYesOrNo($name, 0, 0, 0, 0, 0, 1);
        $exp = explode(',', Configuration::get($this->prefix . $name . '_mr'));
        $prodchecks = array();
        foreach ($exp as $value) {
            $prodchecks[$value] = true;
        }
        $id_lang = Configuration::get('PS_LANG_DEFAULT');
        $result = Carrier::getCarriers($id_lang, false, false);
        $cat_tree .= '<div id="' . $this->prefix . $name . '_mr" style="display:block; clear:both; ' . (Configuration::get($this->prefix . $name) == 1 ? '' : 'display:none;') . '"><select name="' . $this->prefix . $name . '_mr[]" style="display:block; clear:both; overflow:auto; border:1px solid #AAAAAA; width:400px; height:160px; " multiple="">';
        foreach ($result as $value) {
            $cat_tree .= '<option ' . $this->prodchecks($prodchecks, $value['id_reference']) . ' value="' . $value['id_reference'] . '"/>' . $value['name'] . '</option>';
        }
        $cat_tree .= '</select><p class="preference_description">' . $this->l('Select carriers from list above, use CTRL+click to select multiple items CTRL+A to select all of them') . '</p></div>';
        return $cat_tree;
    }

    public function generateGroupsSelect($name)
    {
        $cat_tree = $this->generateYesOrNo($name, 0, 0, 0, 0, 0, 0, 0, 1);
        $exp = explode(',', Configuration::get($this->prefix . $name . '_mr'));
        $prodchecks = array();
        foreach ($exp as $value) {
            $prodchecks[$value] = true;
        }
        $id_lang = Configuration::get('PS_LANG_DEFAULT');
        $result = Group::getGroups($id_lang);
        $cat_tree .= '<div id="' . $this->prefix . $name . '_mr" style="display:block; clear:both; ' . (Configuration::get($this->prefix . $name) == 1 ? '' : 'display:none;') . '"><select name="' . $this->prefix . $name . '_mr[]" style="display:block; clear:both; overflow:auto; border:1px solid #AAAAAA; width:400px; height:160px; " multiple="">';
        foreach ($result as $value) {
            $cat_tree .= '<option ' . $this->prodchecks($prodchecks, $value['id_group']) . ' value="' . $value['id_group'] . '"/>' . $value['name'] . '</option>';
        }
        $cat_tree .= '</select><p class="preference_description">' . $this->l('Select groups from list above, use CTRL+click to select multiple items CTRL+A to select all of them') . '</p></div>';
        return $cat_tree;
    }

    public function generateCountrySelect($name)
    {
        $cat_tree = $this->generateYesOrNo($name, 0, 0, 0, 0, 0, 0, 0, 0, 1);
        $exp = explode(',', Configuration::get($this->prefix . $name . '_mr'));
        $prodchecks = array();
        foreach ($exp as $value) {
            $prodchecks[$value] = true;
        }
        $id_lang = Configuration::get('PS_LANG_DEFAULT');
        $result = Country::getCountries($id_lang, true);
        $cat_tree .= '<div id="' . $this->prefix . $name . '_mr" style="display:block; clear:both; ' . (Configuration::get($this->prefix . $name) == 1 ? '' : 'display:none;') . '"><select name="' . $this->prefix . $name . '_mr[]" style="display:block; clear:both; overflow:auto; border:1px solid #AAAAAA; width:400px; height:160px; " multiple="">';
        foreach ($result as $value) {
            $cat_tree .= '<option ' . $this->prodchecks($prodchecks, $value['id_country']) . ' value="' . $value['id_country'] . '"/>' . $value['name'] . '</option>';
        }
        $cat_tree .= '</select><p class="preference_description">' . $this->l('Select countries from list above, use CTRL+click to select multiple items CTRL+A to select all of them') . '</p></div>';
        return $cat_tree;
    }

    public function generateFreeGiftSelect($name)
    {
        if (Configuration::get($this->prefix . $name) == 1) {
            $product = new Product(Configuration::get($this->prefix . '_fgp_id'), false, Configuration::get('PS_LANG_DEFAULT'));
            if (!isset($product->name)) {
                $product->name = '';
            }
        }
        $cat_tree = '	<label>' . $this->l('Send free gift') . '</label><div class="margin-form" style="position:relative;">' . $this->generateYesOrNo($name, 0, 0, 0, 0, 1);
        $cat_tree .= '<div style="' . (Configuration::get($this->prefix . $name) == 1 ? '' : 'display:none;') . '" id="' . $this->prefix . $name . '_sfg"  class="' . $this->prefix . $name . '_sfg">
								<div style="display:block; clear:both; margin-top:15px;">' . $this->l('Search for product') . ': <input type="text" value="' . (isset($product->name) ? $product->name : '') . '" name="' . $this->prefix . $name . '_fgp" class="free_gift_search"/>
								' . $this->l('or enter product ID') . ': <input style="width: 50px;" type="numeric" id="' . $this->prefix . '_fgp_id" value="' . Configuration::get($this->prefix . '_fgp_id') . '" name="' . $this->prefix . '_fgp_id"/><div id="' . $this->prefix . '_fgc_id_div" style="display:none!important;"> ' . $this->l('product combination ID') . ': <input style="width: 50px;" type="numeric" id="' . $this->prefix . '_fgc_id" value="' . (Configuration::get($this->prefix . '_fgc_id') != '' ? Configuration::get($this->prefix . '_fgc_id') : '0') . '" name="' . $this->prefix . '_fgc_id"/></div></div>
								<div id="free_gift_search_result"></div>
								</div></div>';
        return $cat_tree;
    }

    public function generateAttributesSelect($name)
    {
        $cat_tree = $this->generateYesOrNo($name, 0, 0, 0, 1);
        $exp = explode(',', Configuration::get($this->prefix . $name . '_mr'));
        $prodchecks = array();
        foreach ($exp as $value) {
            $prodchecks[$value] = true;
        }
        $id_lang = Configuration::get('PS_LANG_DEFAULT');
        $result = Attribute::getAttributes($id_lang);
        $cat_tree .= '<div id="' . $this->prefix . $name . '_mr" style="display:block; clear:both; ' . (Configuration::get($this->prefix . $name) == 1 ? '' : 'display:none;') . '"><select name="' . $this->prefix . $name . '_mr[]" style="display:block; clear:both; overflow:auto; border:1px solid #AAAAAA; width:400px; height:160px; " multiple="">';
        foreach ($result as $value) {
            $cat_tree .= '<option ' . $this->prodchecks($prodchecks, $value['id_attribute']) . ' value="' . $value['id_attribute'] . '"/>' . $value['attribute_group'] . ' - ' . $value['name'] . '</option>';
        }
        $cat_tree .= '</select><p class="preference_description">' . $this->l('Select Attributes from list above, use CTRL+click to select multiple products, CTRL+A to select all of them') . '</p></div>';
        return $cat_tree;
    }

    public function catchecks($array, $id)
    {
        if (isset($array[$id])) {
            return 'selected="yes"';
        } else {
            return '';
        }
    }

    public function prodchecks($array, $id)
    {
        if (isset($array[$id])) {
            return 'selected="yes"';
        } else {
            return '';
        }
    }

    public function getVoucherEngineSettings($prefix)
    {
        $return = array();
        $return['voucher'] = Configuration::getMultiple(array(
            $prefix . 'name',
            $prefix . 'description',
            $prefix . 'highlight',
            $prefix . 'partial_use',
            $prefix . 'priority',
            $prefix . 'prefix',
            $prefix . 'prefix_code',
            $prefix . 'sufix',
            $prefix . 'sufix_code',
            $prefix . 'length',
            $prefix . 'characters',
            $prefix . 'minimum_amount_currency',
            $prefix . 'minimum_amount',
            $prefix . 'expiry',
            $prefix . 'datefrom',
            $prefix . 'dateto',
            $prefix . 'minimum_amount_tax',
            $prefix . 'minimum_amount_shipping',
            $prefix . 'quantity',
            $prefix . 'quantity_per_user',
            $prefix . 'cart_rule_restriction',
            $prefix . 'shareshops',
            $prefix . 'cumulable_reduction',
            $prefix . 'restriction_categories',
            $prefix . 'reduction_type',
            $prefix . 'reduction_percent',
            $prefix . 'reduction_amount',
            $prefix . 'reduction_currency',
            $prefix . 'reduction_tax',
            $prefix . 'apply_discount_to',
            $prefix . 'reduction_product'
        ));
        return $return['voucher'];
    }

    public static function updateVoucher($prefix, $post)
    {
        if (parent::psversion() == 4 || parent::psversion() == 5 || parent::psversion() == 6 || parent::psversion() == 7) {
            if (parent::psversion() == 5 || parent::psversion() == 6 || parent::psversion() == 7) {
                Configuration::updateValue($prefix . "name", $post[$prefix . 'name']);
                Configuration::updateValue($prefix . "partial_use", (int)$post[$prefix . 'partial_use']);
                Configuration::updateValue($prefix . "priority", (int)$post[$prefix . 'priority']);
                Configuration::updateValue($prefix . "minimum_amount_currency", $post[$prefix . 'minimum_amount_currency']);
                Configuration::updateValue($prefix . "minimum_amount_shipping", $post[$prefix . 'minimum_amount_shipping']);
                Configuration::updateValue($prefix . "restriction_products", $post[$prefix . 'restriction_products']);
                Configuration::updateValue($prefix . "reduction_tax", $post[$prefix . 'reduction_tax']);
                Configuration::updateValue($prefix . "shareshops", $post[$prefix . 'shareshops']);
                if (tools::getValue($prefix . 'apply_discount_to', 'false') != 'false') {
                    Configuration::updateValue($prefix . "apply_discount_to", $post[$prefix . 'apply_discount_to']);
                }
                Configuration::updateValue($prefix . "reduction_product", $post[$prefix . 'reduction_product']);
                Configuration::updateValue($prefix . "res_manufacturers", $post[$prefix . 'res_manufacturers']);
                Configuration::updateValue($prefix . "res_suppliers", $post[$prefix . 'res_suppliers']);
                Configuration::updateValue($prefix . "res_carriers", $post[$prefix . 'res_carriers']);
                Configuration::updateValue($prefix . "res_groups", $post[$prefix . 'res_groups']);
                Configuration::updateValue($prefix . "res_countries", $post[$prefix . 'res_countries']);
                Configuration::updateValue($prefix . "restriction_qty", $post[$prefix . 'restriction_qty']);
                Configuration::updateValue($prefix . "restriction_attributes", $post[$prefix . 'restriction_attributes']);
            }

            if (parent::psversion() == 7) {
                Configuration::updateValue($prefix . "excludeSpecials", $post[$prefix . 'excludeSpecials']);
            }

            if (parent::psversion() == 4) {
                Configuration::updateValue($prefix . "cumulable_reduction", $post[$prefix . 'cumulable_reduction']);
            }
            Configuration::updateValue($prefix . "restriction_categories", $post[$prefix . 'restriction_categories']);
            Configuration::updateValue($prefix . "description", $post[$prefix . 'description']);
            Configuration::updateValue($prefix . "highlight", (int)$post[$prefix . 'highlight']);
            Configuration::updateValue($prefix . "prefix", $post[$prefix . 'prefix']);
            Configuration::updateValue($prefix . "prefix_code", $post[$prefix . 'prefix_code']);
            Configuration::updateValue($prefix . "sufix", $post[$prefix . 'sufix']);
            Configuration::updateValue($prefix . "sufix_code", $post[$prefix . 'sufix_code']);
            Configuration::updateValue($prefix . "length", (int)$post[$prefix . 'length']);
            Configuration::updateValue($prefix . "characters", (string)$post[$prefix . 'characters']);
            Configuration::updateValue($prefix . "minimum_amount", $post[$prefix . 'minimum_amount']);
            Configuration::updateValue($prefix . "active", (int)$post[$prefix . 'active']);
            if (isset($post[$prefix . 'expiry'])) {
                Configuration::updateValue($prefix . "expiry", $post[$prefix . 'expiry']);
            } else {
                Configuration::updateValue($prefix . "expiry", "#");
            }
            if (isset($post[$prefix . 'datefrom'])) {
                Configuration::updateValue($prefix . "datefrom", $post[$prefix . 'datefrom']);
            } else {
                Configuration::updateValue($prefix . "datefrom", "#");
            }
            if (isset($post[$prefix . 'dateto'])) {
                Configuration::updateValue($prefix . "dateto", $post[$prefix . 'dateto']);
            } else {
                Configuration::updateValue($prefix . "dateto", "#");
            }
            Configuration::updateValue($prefix . "minimum_amount_tax", $post[$prefix . 'minimum_amount_tax']);
            Configuration::updateValue($prefix . "quantity", $post[$prefix . 'quantity']);
            Configuration::updateValue($prefix . "quantity_per_user", $post[$prefix . 'quantity_per_user']);
            Configuration::updateValue($prefix . "cart_rule_restriction", (int)$post[$prefix . 'cart_rule_restriction']);
            Configuration::updateValue($prefix . "free_shipping", $post[$prefix . 'free_shipping']);
            Configuration::updateValue($prefix . "free_gift", $post[$prefix . 'free_gift']);
            if (Tools::getValue($prefix . 'free_gift', 'false') == '1') {
                if (Tools::getValue($prefix . '_fgp_id', 'false') != 'false') {
                    Configuration::updateValue($prefix . "_fgp_id", $post[$prefix . '_fgp_id']);
                }
                if (Tools::getValue($prefix . '_fgp_id', 'false') != 'false' && Tools::getValue($prefix . '_fgp_id', 'false') != '') {
                    Configuration::updateValue($prefix . "_fgc_id", $post[$prefix . '_fgc_id']);
                }
            }
            if (Tools::getValue($prefix . 'reduction_type', 'false') != 'false') {
                Configuration::updateValue($prefix . "reduction_type", $post[$prefix . 'reduction_type']);
            }
            Configuration::updateValue($prefix . "reduction_percent", $post[$prefix . 'reduction_percent']);
            Configuration::updateValue($prefix . "reduction_amount", $post[$prefix . 'reduction_amount']);
            Configuration::updateValue($prefix . "reduction_currency", $post[$prefix . 'reduction_currency']);
            if (parent::psversion() == 4 || parent::psversion() == 5 || parent::psversion() == 6 || parent::psversion() == 7) {
                // CATEGORIES
                if (isset($post[$prefix . 'restriction_categories_cr'])) {
                    $restriction_categories_cr = "";
                    foreach ($post[$prefix . 'restriction_categories_cr'] as $v) {
                        $restriction_categories_cr .= $v . ",";
                    }
                    $restriction_categories_cr = trim(Tools::substr($restriction_categories_cr, 0, -1));
                    Configuration::updateValue($prefix . "restriction_categories_cr", $restriction_categories_cr);
                } else {
                    Configuration::updateValue($prefix . "restriction_categories_cr", '');
                }

                // PRODUCTS
                if (isset($post[$prefix . 'restriction_products_pr'])) {
                    $restriction_products_pr = "";
                    foreach ($post[$prefix . 'restriction_products_pr'] as $v) {
                        @$restriction_products_pr .= $v . ",";
                    }
                    $restriction_products_pr = trim(Tools::substr($restriction_products_pr, 0, -1));
                    Configuration::updateValue($prefix . "restriction_products_pr", $restriction_products_pr);
                } else {
                    Configuration::updateValue($prefix . "restriction_products_pr", '');
                }

                // ATTRIBUTES
                if (isset($post[$prefix . 'restriction_attributes_mr'])) {
                    $restriction_attributes_mr = "";
                    foreach ($post[$prefix . 'restriction_attributes_mr'] as $v) {
                        @$restriction_attributes_mr .= $v . ",";
                    }
                    $restriction_attributes_mr = trim(Tools::substr($restriction_attributes_mr, 0, -1));
                    Configuration::updateValue($prefix . "restriction_attributes_mr", $restriction_attributes_mr);
                } else {
                    Configuration::updateValue($prefix . "restriction_attributes_mr", '');
                }

                // MANUFACTURERS
                if (isset($post[$prefix . 'res_manufacturers_mr'])) {
                    $res_manufacturers_mr = "";
                    foreach ($post[$prefix . 'res_manufacturers_mr'] as $v) {
                        $res_manufacturers_mr .= $v . ",";
                    }
                    $res_manufacturers_mr = trim(Tools::substr($res_manufacturers_mr, 0, -1));
                    Configuration::updateValue($prefix . "res_manufacturers_mr", $res_manufacturers_mr);
                } else {
                    Configuration::updateValue($prefix . "res_manufacturers_mr", '');
                }

                // SUPPLIERS
                if (isset($post[$prefix . 'res_suppliers_mr'])) {
                    $res_suppliers_mr = "";
                    foreach ($post[$prefix . 'res_suppliers_mr'] as $v) {
                        $res_suppliers_mr .= $v . ",";
                    }
                    $res_suppliers_mr = trim(Tools::substr($res_suppliers_mr, 0, -1));
                    Configuration::updateValue($prefix . "res_suppliers_mr", $res_suppliers_mr);
                } else {
                    Configuration::updateValue($prefix . "res_suppliers_mr", '');
                }


                // GROUPS
                if (isset($post[$prefix . 'res_groups_mr'])) {
                    $res_groups_mr = trim(implode(',', $post[$prefix . 'res_groups_mr']));
                    Configuration::updateValue($prefix . "res_groups_mr", $res_groups_mr);
                } else {
                    Configuration::updateValue($prefix . "res_groups_mr", '');
                }


                // CARRIERS
                if (isset($post[$prefix . 'res_carriers_mr'])) {
                    $res_carriers_mr = "";
                    foreach ($post[$prefix . 'res_carriers_mr'] as $v) {
                        $res_carriers_mr .= $v . ",";
                    }
                    $res_carriers_mr = trim(Tools::substr($res_carriers_mr, 0, -1));
                    Configuration::updateValue($prefix . "res_carriers_mr", $res_carriers_mr);
                } else {
                    Configuration::updateValue($prefix . "res_carriers_mr", '');
                }

                // COUNTRIES
                if (isset($post[$prefix . 'res_countries_mr'])) {
                    $res_countries_mr = "";
                    foreach ($post[$prefix . 'res_countries_mr'] as $v) {
                        $res_countries_mr .= $v . ",";
                    }
                    $res_countries_mr = trim(Tools::substr($res_countries_mr, 0, -1));
                    Configuration::updateValue($prefix . "res_countries_mr", $res_countries_mr);
                } else {
                    Configuration::updateValue($prefix . "res_countries_mr", '');
                }

            }
        }
        //self::AddVoucherCode("fvc_");
    }

    public static function generateVoucherCode($prefix, $characters = 'alphanumeric')
    {
        if ($characters == 'alphanumeric') {
            $validCharacters = "ABCDEFGHJKLMNOUPRSTUWQXYZV0123456789";
        } elseif ($characters == 'numeric') {
            $validCharacters = "0123456789";
        } elseif ($characters == 'alpha') {
            $validCharacters = "ABCDEFGHIJKLMNOUPRSTUWXYZV";
        } else {
            $validCharacters = "ABCDEFGHJKLMNOUPRSTUWQXYZV0123456789";
        }

        $length = Configuration::get($prefix . 'length');
        $last = "";
        $validCharNumber = Tools::strlen($validCharacters);
        $result = "";
        for ($i = 0; $i < $length; $i++) {
            $index = mt_rand(0, $validCharNumber - 1);
            while ($last == $index) {
                $index = mt_rand(0, $validCharNumber - 1);
            }
            $result .= $validCharacters[$index];
            $last = $index;
        }
        return $result;
    }

    public static function AddVoucherCode($prefix, $id_customer = null, $code = null, $name = null, $description = null, $value = null, $quantity = null, $quantity_per_user = null, $cumulable = null, $date_from = null, $date_to = null, $minimal_basket = null, $minimal_basket_currency = null, $currency_id = null, $partial_use = null, $free_shipping = null, $highlight = null, $id_cart_rule = false, $rule_products = false, $rule_categories = false)
    {
        if (parent::psversion() == 5 || parent::psversion() == 6 || parent::psversion() == 7) {
            $context = Context::getContext();
            if (parent::psversion() == 7 || parent::psversion() == 6) {
                if ($id_cart_rule == false) {
                    $voucher = new CartRule();
                } else {
                    $voucher = new CartRule($id_cart_rule);
                }
            } else {
                if ($id_cart_rule == false) {
                    $voucher = new Discount();
                } else {
                    $voucher = new Discount($id_cart_rule);
                }
            }
            // NAME
            if ($name != null) {
                $names = array();
                foreach (Language::getLanguages(false) as $v) {
                    $names[$v['id_lang']] = $name;
                }
                $voucher->name = $names;
            } else {
                $names = array();
                foreach (Language::getLanguages(false) as $v) {
                    $names[$v['id_lang']] = trim(Configuration::get($prefix . 'name', $v['id_lang']));
                }
                $voucher->name = $names;
            }


            // DESCRIPTION
            if ($description != null) {
                $voucher->description = trim($description);
            } else {
                $voucher->description = Configuration::get($prefix . 'description');
            }


            // DATE TO + DATE FROM
            if (Configuration::get($prefix . 'expiry') != "#") {
                $voucher->date_from = date("Y-m-d H:i:s");
                $voucher->date_to = date("Y-m-d H:i:s", date("U") + Configuration::get($prefix . 'expiry') * 24 * 60 * 60);
            } elseif (Configuration::get($prefix . 'datefrom') != "#" && Configuration::get($prefix . 'dateto') != "#") {
                $voucher->date_from = Configuration::get($prefix . 'datefrom');
                $voucher->date_to = Configuration::get($prefix . 'dateto');
            } else {
                $voucher->date_from = date("Y-m-d H:i:s");
                $voucher->date_to = date("Y-m-d H:i:s", date("U") + 7 * 24 * 60 * 60);
            }

            if ($date_from != null) {
                $voucher->date_from = $date_from;
            }
            if ($date_to != null) {
                $voucher->date_to = $date_to;
            }
            if (Shop::getContext() == Shop::CONTEXT_SHOP && Shop::isFeatureActive()) {
                $voucher->shop_restriction = 1;
            }


            // CODE
            if ($code != null) {
                $voucher->code = trim($code);
            } else {
                $voucher->code = trim(self::generateVoucherCode($prefix, Configuration::get($prefix . 'characters')));
            }

            if (Configuration::get($prefix . 'sufix') == 1) {
                $voucher->code = $voucher->code . Configuration::get($prefix . 'sufix_code', $context->language->id);
            }
            if (Configuration::get($prefix . 'prefix') == 1) {
                $voucher->code = Configuration::get($prefix . 'prefix_code', $context->language->id) . $voucher->code;
            }


            if (((int)$highlight == 0 || (int)$highlight == 1) && $highlight != null) {
                $voucher->highlight = ((int)$highlight == 1 ? 1 : 0);
            } else {
                $voucher->highlight = (Configuration::get($prefix . 'highlight') == 1 ? true : false);
            }

            //PARTIAL USE
            if ($partial_use != null) {
                $voucher->partial_use = ($partial_use == 1 ? true : false);
            } else {
                $voucher->partial_use = (Configuration::get($prefix . 'partial_use') == 1 ? true : false);
            }
            $voucher->priority = Configuration::get($prefix . 'priority');
            $voucher->active = (Configuration::get($prefix . 'active') == 1 ? true : false);

            // CUMULABLE WITH OTHER CART RULES
            if ($cumulable != null) {
                $voucher->cart_rule_restriction = ($cumulable == 1 ? true : false);
            } else {
                $voucher->cart_rule_restriction = (Configuration::get($prefix . 'cart_rule_restriction') == 1 ? true : false);
            }

            // MINIMAL BASKET
            if ($minimal_basket != null) {
                $voucher->minimum_amount = $minimal_basket;
            } else {
                $voucher->minimum_amount = Configuration::get($prefix . 'minimum_amount');
            }

            // MINIMAL BASKET CURRENCY
            if ($minimal_basket_currency != null && $minimal_basket != null) {
                $voucher->minimum_amount_currency = $minimal_basket_currency;
            } else {
                $voucher->minimum_amount_currency = Configuration::get($prefix . 'minimum_amount_currency');
            }

            $voucher->minimum_amount_tax = Configuration::get($prefix . 'minimum_amount_tax');
            $voucher->minimum_amount_shipping = Configuration::get($prefix . 'minimum_amount_shipping');

            if ($quantity != null) {
                $voucher->quantity = $quantity;
            } else {
                $voucher->quantity = Configuration::get($prefix . 'quantity');
            }

            if ($quantity_per_user != null) {
                $voucher->quantity_per_user = $quantity_per_user;
            } else {
                $voucher->quantity_per_user = Configuration::get($prefix . 'quantity_per_user');
            }

            if (Configuration::get($prefix . 'restriction_products') == 1 || Configuration::get($prefix . 'restriction_categories') == 1 || Configuration::get($prefix . 'res_manufacturers') == 1 || Configuration::get($prefix . 'res_suppliers') == 1 || Configuration::get($prefix . 'restriction_attributes') == 1) {
                $voucher->product_restriction = 1;
            }


            // RULE PRODUCTS
            if ($rule_products != false) {
                if (is_array($rule_products)) {
                    if (count($rule_products) > 0) {
                        $voucher->product_restriction = 1;
                    }
                }
            }

            // RULE CATEGORIES
            if ($rule_categories != false) {
                if (is_array($rule_categories)) {
                    if (count($rule_categories) > 0) {
                        $voucher->product_restriction = 1;
                    }
                }
            }

            // FREE SHIPPING
            if ($free_shipping != null) {
                $voucher->free_shipping = ($free_shipping == 1 ? true : false);
            } else {
                $voucher->free_shipping = Configuration::get($prefix . 'free_shipping');
            }

            // VOUCHER VALUE
            if (Configuration::get($prefix . 'reduction_type') == 1) {
                if ($value != null) {
                    $voucher->reduction_percent = $value;
                } else {
                    $voucher->reduction_percent = Configuration::get($prefix . 'reduction_percent');
                }
            }
            if (Configuration::get($prefix . 'reduction_type') == 2) {
                if ($value != null) {
                    $voucher->reduction_amount = $value;
                } else {
                    $voucher->reduction_amount = Configuration::get($prefix . 'reduction_amount');
                }
                if ($currency_id != null) {
                    $voucher->reduction_currency = $currency_id;
                } else {
                    $voucher->reduction_currency = Configuration::get($prefix . 'reduction_currency');
                }
                $voucher->reduction_tax = Configuration::get($prefix . 'reduction_tax');
            }
            if (Configuration::get($prefix . "apply_discount_to") == "specific") {
                $voucher->reduction_product = Configuration::get($prefix . "reduction_product");
            }
            if (Configuration::get($prefix . "apply_discount_to") == "cheapest") {
                $voucher->reduction_product = -1;
            }
            if (Configuration::get($prefix . "apply_discount_to") == "selected") {
                $voucher->reduction_product = -2;
            }
            if ($id_customer != null) {
                $voucher->id_customer = (int)$id_customer;
            }
            if (Configuration::get($prefix . "res_carriers") == 1) {
                $voucher->carrier_restriction = 1;
            }

            if (Configuration::get($prefix . "res_countries") == 1) {
                $voucher->country_restriction = 1;
            }

            if (Configuration::get($prefix . "res_groups") == 1) {
                $voucher->group_restriction = 1;
            }

            if (Configuration::get($prefix . "excludeSpecials") == 1 && self::psversion() == 7) {
                $voucher->reduction_exclude_special = 1;
            }


            if (Configuration::get($prefix . "free_gift") == 1) {
                if (Configuration::get($prefix . "_fgp_id") != null and Configuration::get($prefix . "_fgp_id") != '') {
                    $voucher->gift_product = Configuration::get($prefix . "_fgp_id");
                } else {
                    $voucher->gift_product = 0;
                }
                if (Configuration::get($prefix . "_fgc_id") != null and Configuration::get($prefix . "_fgc_id") != '') {
                    $voucher->gift_product_attribute = Configuration::get($prefix . "_fgc_id");
                } else {
                    $voucher->gift_product_attribute = 0;
                }
            } else {
                $voucher->gift_product = 0;
                $voucher->gift_product_attribute = 0;
            }
            if ($voucher->save() == true) {
                self::afterAdd($voucher, $prefix, $rule_products, $rule_categories);
            }
            //return $voucher->code; modification!
            return $voucher;
        }

        if (parent::psversion() == 4) {
            global $cookie;
            $voucher = new Discount();
            $desc = array();
            foreach (Language::getLanguages(false) as $v) {
                $desc[$v['id_lang']] = trim(Configuration::get($prefix . 'description'));
            }
            //$voucher->name=$name;
            $voucher->description = $desc;
            if (Configuration::get($prefix . 'expiry') != "#") {
                $voucher->date_from = date("Y-m-d h:i:s");
                $voucher->date_to = date("Y-m-d h:i:s", date("U") + Configuration::get($prefix . 'expiry') * 24 * 60 * 60);
            } elseif (Configuration::get($prefix . 'datefrom') != "#" && Configuration::get($prefix . 'dateto') != "#") {
                $voucher->date_from = Configuration::get($prefix . 'datefrom');
                $voucher->date_to = Configuration::get($prefix . 'dateto');
            } else {
                $voucher->date_from = date("Y-m-d h:i:s");
                $voucher->date_to = date("Y-m-d h:i:s", date("U") + 7 * 24 * 60 * 60);
            }
            $voucher->name = trim(self::generateVoucherCode($prefix, Configuration::get($prefix . 'characters')));

            if (Configuration::get($prefix . 'sufix') == 1) {
                $voucher->name = $voucher->name . Configuration::get($prefix . 'sufix_code', $cookie->id_lang);
            }
            if (Configuration::get($prefix . 'prefix') == 1) {
                $voucher->name = Configuration::get($prefix . 'prefix_code', $cookie->id_lang) . $voucher->name;
            }


            if ($highlight != null) {
                $voucher->highlight = ($highlight == 1 ? 1 : 0);
            } else {
                $voucher->highlight = (Configuration::get($prefix . 'highlight') == 1 ? true : false);
            }


            //$voucher->partial_use=(Configuration::get($prefix.'partial_use')==1 ? true:false);
            //$voucher->priority=Configuration::get($prefix.'priority');
            $voucher->active = (Configuration::get($prefix . 'active') == 1 ? true : false);
            $voucher->cumulable = (Configuration::get($prefix . 'cart_rule_restriction') == 1 ? false : true);
            $voucher->cumulable_reduction = (Configuration::get($prefix . 'cumulable_reduction') == 1 ? false : true);
            $voucher->minimal = Configuration::get($prefix . 'minimum_amount');
            $voucher->include_tax = Configuration::get($prefix . 'minimum_amount_tax');
            $voucher->quantity = Configuration::get($prefix . 'quantity');
            $voucher->quantity_per_user = Configuration::get($prefix . 'quantity_per_user');
            if (Configuration::get($prefix . 'free_shipping') == 1) {
                $voucher->id_discount_type = 3;
                $voucher->value = 0;
            } elseif (Configuration::get($prefix . "reduction_type") == 2) {
                $voucher->id_discount_type = 2;
                $voucher->value = Configuration::get($prefix . 'reduction_amount');
                $voucher->id_currency = Configuration::get($prefix . 'reduction_currency');
            } elseif (Configuration::get($prefix . "reduction_type") == 1) {
                $voucher->id_discount_type = 1;
                $voucher->value = Configuration::get($prefix . 'reduction_percent');
            }
            if ($id_customer != null) {
                $voucher->id_customer = (int)$id_customer;
            }
            if ($voucher->add() == true) {
                self::afterAddOldPrestaShop14($voucher, $prefix);
            }
            return $voucher;
        }
    }

    public static function afterAddOldPrestaShop14($currentObject, $prefix)
    {
        if (Configuration::get($prefix . 'restriction_categories') == 1) {
            Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'discount_category` WHERE `id_discount`=' . (int)($currentObject->id));
            $restrictions = Configuration::get($prefix . "restriction_categories_cr");
            $restrictions_array = explode(",", $restrictions);
            $values = array();
            foreach ($restrictions_array as $id) {
                $values[] = '(' . (int)$currentObject->id . ',' . (int)$id . ')';
            }
            $values = array_unique($values);
            if (count($values)) {
                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'discount_category` (`id_discount`, `id_category`) VALUES ' . implode(',', $values));
            }
        }
    }

    public static function afterAdd($currentObject, $prefix, $rule_products = false, $rule_categories = false)
    {
        // CART RULE RESTRICTIONS
        // If the new rule has no cart rule restriction, then it must be added to the white list of the other cart rules that have restrictions
        if (!$currentObject->cart_rule_restriction) {
            Db::getInstance()->execute('
			INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`) (
				SELECT id_cart_rule, ' . (int)$currentObject->id . ' FROM `' . _DB_PREFIX_ . 'cart_rule` WHERE cart_rule_restriction = 1
			)');
        } // And if the new cart rule has restrictions, previously unrestricted cart rules may now be restricted (a mug of coffee is strongly advised to understand this sentence)
        else {
            $ruleCombinations = Db::getInstance()->executeS('
			SELECT cr.id_cart_rule
			FROM ' . _DB_PREFIX_ . 'cart_rule cr
			WHERE cr.id_cart_rule != ' . (int)$currentObject->id . '
			AND cr.cart_rule_restriction = 0
			AND NOT EXISTS (
				SELECT 1
				FROM ' . _DB_PREFIX_ . 'cart_rule_combination
				WHERE cr.id_cart_rule = ' . _DB_PREFIX_ . 'cart_rule_combination.id_cart_rule_2 AND ' . (int)$currentObject->id . ' = id_cart_rule_1
			)
			AND NOT EXISTS (
				SELECT 1
				FROM ' . _DB_PREFIX_ . 'cart_rule_combination
				WHERE cr.id_cart_rule = ' . _DB_PREFIX_ . 'cart_rule_combination.id_cart_rule_1 AND ' . (int)$currentObject->id . ' = id_cart_rule_2
			)
			');
            foreach ($ruleCombinations as $incompatibleRule) {
                Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'cart_rule` SET cart_rule_restriction = 1 WHERE id_cart_rule = ' . (int)$incompatibleRule['id_cart_rule'] . ' LIMIT 1');
                Db::getInstance()->execute('
				INSERT IGNORE INTO `' . _DB_PREFIX_ . 'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`) (
					SELECT id_cart_rule, ' . (int)$incompatibleRule['id_cart_rule'] . ' FROM `' . _DB_PREFIX_ . 'cart_rule`
					WHERE active = 1
					AND id_cart_rule != ' . (int)$currentObject->id . '
					AND id_cart_rule != ' . (int)$incompatibleRule['id_cart_rule'] . '
				)');
            }
        }

        if (Configuration::get($prefix . 'restriction_products') == 1 || $rule_products != false) {
            $restrictions_array = array();
            $restrictions_array_db = explode(",", Configuration::get($prefix . "restriction_products_pr"));
            if (is_array($rule_products)) {
                if (count($rule_products) > 0) {
                    $restrictions_array = $rule_products;
                }
            }

            if (count($restrictions_array) <= 0) {
                if (count($restrictions_array_db) > 0) {
                    $restrictions_array = $restrictions_array_db;
                }
            }

            if (count($restrictions_array) > 0) {
                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`) VALUES (' . (int)$currentObject->id . ',  ' . Configuration::get($prefix . "restriction_qty") . ')');
                $id_product_rule_group = Db::getInstance()->Insert_ID();
                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule` (`id_product_rule_group`, `type`) VALUES (' . (int)$id_product_rule_group . ', "products")');
                $id_product_rule = Db::getInstance()->Insert_ID();

                $values = array();
                foreach ($restrictions_array as $id) {
                    $values[] = '(' . (int)$id_product_rule . ',' . (int)$id . ')';
                }
                $values = array_unique($values);
                if (count($values)) {
                    Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule_value` (`id_product_rule`, `id_item`) VALUES ' . implode(',', $values));
                }
            }
        }

        if (Configuration::get($prefix . 'restriction_categories') == 1 || $rule_categories != false) {
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`) VALUES (' . (int)$currentObject->id . ', ' . Configuration::get($prefix . "restriction_qty") . ')');
            $id_product_rule_group = Db::getInstance()->Insert_ID();
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule` (`id_product_rule_group`, `type`) VALUES (' . (int)$id_product_rule_group . ', "categories")');
            $id_product_rule = Db::getInstance()->Insert_ID();
            $restrictions_array = array();
            $restrictions_array_db = explode(",", Configuration::get($prefix . "restriction_categories_cr"));

            if (is_array($rule_categories)) {
                if (count($rule_categories) > 0) {
                    $restrictions_array = $rule_categories;
                }
            }

            if (count($restrictions_array) <= 0) {
                if (count($restrictions_array_db) > 0) {
                    $restrictions_array = $restrictions_array_db;
                }
            }

            $values = array();
            foreach ($restrictions_array as $id) {
                $values[] = '(' . (int)$id_product_rule . ',' . (int)$id . ')';
            }
            $values = array_unique($values);
            if (count($values)) {
                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule_value` (`id_product_rule`, `id_item`) VALUES ' . implode(',', $values));
            }
        }

        if (Configuration::get($prefix . 'res_manufacturers') == 1) {
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`) VALUES (' . (int)$currentObject->id . ', ' . Configuration::get($prefix . "restriction_qty") . ')');
            $id_product_rule_group = Db::getInstance()->Insert_ID();
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule` (`id_product_rule_group`, `type`) VALUES (' . (int)$id_product_rule_group . ', "manufacturers")');
            $id_product_rule = Db::getInstance()->Insert_ID();
            $restrictions = Configuration::get($prefix . "res_manufacturers_mr");
            $restrictions_array = explode(",", $restrictions);
            $values = array();
            foreach ($restrictions_array as $id) {
                $values[] = '(' . (int)$id_product_rule . ',' . (int)$id . ')';
            }
            $values = array_unique($values);
            if (count($values)) {
                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule_value` (`id_product_rule`, `id_item`) VALUES ' . implode(',', $values));
            }
        }


        if (Configuration::get($prefix . 'res_groups') == 1) {
            $array = explode(',', Configuration::get($prefix . 'res_groups_mr'));
            if ($array != false) {
                if (count($array) > 0) {
                    $values = array();
                    foreach ($array as $id) {
                        $values[] = '(' . (int)$currentObject->id . ',' . (int)$id . ')';
                    }
                    Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_group` (`id_cart_rule`, `id_group`) VALUES ' . implode(',', $values));
                }
            }
        }


        if (Configuration::get($prefix . 'res_suppliers') == 1) {
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`) VALUES (' . (int)$currentObject->id . ', ' . Configuration::get($prefix . "restriction_qty") . ')');
            $id_product_rule_group = Db::getInstance()->Insert_ID();
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule` (`id_product_rule_group`, `type`) VALUES (' . (int)$id_product_rule_group . ', "suppliers")');
            $id_product_rule = Db::getInstance()->Insert_ID();
            $restrictions = Configuration::get($prefix . "res_suppliers_mr");
            $restrictions_array = explode(",", $restrictions);
            $values = array();
            foreach ($restrictions_array as $id) {
                $values[] = '(' . (int)$id_product_rule . ',' . (int)$id . ')';
            }
            $values = array_unique($values);
            if (count($values)) {
                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule_value` (`id_product_rule`, `id_item`) VALUES ' . implode(',', $values));
            }
        }

        if (Configuration::get($prefix . 'res_carriers') == 1) {
            $restrictions = Configuration::get($prefix . "res_carriers_mr");
            $restrictions_array = explode(",", $restrictions);
            $values = array();
            foreach ($restrictions_array as $id) {
                $values[] = '(' . (int)$currentObject->id . ',' . (int)$id . ')';
            }
            $values = array_unique($values);
            if (count($values)) {
                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_carrier` (`id_cart_rule`, `id_carrier`) VALUES ' . implode(',', $values));
            }
        }

        if (Configuration::get($prefix . 'res_countries') == 1) {
            $restrictions = Configuration::get($prefix . "res_countries_mr");
            $restrictions_array = explode(",", $restrictions);
            $values = array();
            foreach ($restrictions_array as $id) {
                $values[] = '(' . (int)$currentObject->id . ',' . (int)$id . ')';
            }
            $values = array_unique($values);
            if (count($values)) {
                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_country` (`id_cart_rule`, `id_country`) VALUES ' . implode(',', $values));
            }
        }

        if (Configuration::get($prefix . 'restriction_attributes') == 1) {
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`) VALUES (' . (int)$currentObject->id . ', ' . Configuration::get($prefix . "restriction_qty") . ')');
            $id_product_rule_group = Db::getInstance()->Insert_ID();
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule` (`id_product_rule_group`, `type`) VALUES (' . (int)$id_product_rule_group . ', "attributes")');
            $id_product_rule = Db::getInstance()->Insert_ID();
            $restrictions = Configuration::get($prefix . "restriction_attributes_mr");
            $restrictions_array = explode(",", $restrictions);
            $values = array();
            foreach ($restrictions_array as $id) {
                $values[] = '(' . (int)$id_product_rule . ',' . (int)$id . ')';
            }
            $values = array_unique($values);
            if (count($values)) {
                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule_value` (`id_product_rule`, `id_item`) VALUES ' . implode(',', $values));
            }
        }
        // multistore support
        if (Shop::getContext() == Shop::CONTEXT_SHOP && Shop::isFeatureActive()) {
            if (Configuration::get($prefix . "shareshops") == 1) {
                foreach (Shop::getShops() AS $shop => $value) {
                    Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_shop` (`id_cart_rule`, `id_shop`) VALUES (' . (int)$currentObject->id . ', ' . (int)$value['id_shop'] . ')');
                }
            } else {
                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_shop` (`id_cart_rule`, `id_shop`) VALUES (' . (int)$currentObject->id . ', ' . (int)Context::getContext()->shop->id . ')');
            }
        }
    }

    public function generateForm()
    {
        $form = '<script>
            var selectbox_prefix = "' . $this->prefix . '";
            var module_name = "' . $this->name . '";
            var voucherengine_id_shop = "' . Context::getContext()->shop->id . '";
        </script>
        ' . (parent::psversion() == 5 || parent::psversion() == 6 || parent::psversion() == 7 ? $this->context->controller->addJqueryPlugin('datepicker') : '') . '
        <div class="bootstrap"><div class="alert alert-info">
        ' . $this->l('Please fill out each available field - do not leave fields empty. Otherwise module will not generate coupon codes or these codes will not work properly.') . ' ' . $this->l('Below you can find links to YouTube videos where you can find more informations about this voucher code configuration tool.') . '<br/><br/>
        ' . $this->l('Video description of advanced voucher configuration tool') . '<br/><a href="https://www.youtube.com/watch?v=cyqsBQkS_fU" target="_blank">https://www.youtube.com/watch?v=cyqsBQkS_fU</a><br/><br/>
        ' . $this->l('General settings') . '<br/><a href="https://www.youtube.com/watch?v=ubg60yWuLMA" target="_blank">https://www.youtube.com/watch?v=ubg60yWuLMA</a><br/><br/>
        ' . $this->l('Conditions settings') . '<br/><a href="https://www.youtube.com/watch?v=2L6DYcTo5n0" target="_blank">https://www.youtube.com/watch?v=2L6DYcTo5n0</a><br/><br/>
        ' . $this->l('Actions settings') . '<br/><a href="https://www.youtube.com/watch?v=JV4JXfYMVxY" target="_blank">https://www.youtube.com/watch?v=JV4JXfYMVxY</a><br/><br/>
        </div></div>
        ' . (parent::psversion() == 6 || parent::psversion() == 7 ? '
        <link rel="stylesheet" href="../modules/' . $this->name . '/lib/voucherengine/ps16.css" type="text/css" media="all">
        ' : '') . '
        <script type="text/javascript" src="../modules/' . $this->name . '/lib/voucherengine/script.js"></script><h2>' . $this->l('General settings') . '</h2>
        <input type="hidden" name="voucherPrefix" value="' . $this->prefix . '">';
        $form .= (parent::psversion() == 5 || parent::psversion() == 6 || parent::psversion() == 7 ? '
			    <div style="display:block; clear:both;">
                <label>' . $this->l('Name') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateLangInput("name") . '
    				<p class="preference_description"><br/>' . $this->l('This will be displayed in the cart summary, as well as on the invoice') . '</p>
    			</div>
            </div>' : '') . '<div style="display:block; clear:both;">
                <label>' . $this->l('Description') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateTextarea("description") . '
    				<p class="preference_description">' . $this->l('For your eyes only. This will never be displayed to the customer') . '</p>
    			</div>
            </div>
            <div style="display:block; clear:both;">
                <label>' . $this->l('Voucher length') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateInput("length", 6) . '
    				<p class="preference_description">' . $this->l('How many characters will be used to generate voucher code') . '</p>
    			</div>
            </div>
            <div style="display:block; clear:both;">
                <label>' . $this->l('Characters') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateCharactersSelect("characters") . '
    				<p class="preference_description">' . $this->l('Characters that will be used to generate voucher codes') . '</p>
    			</div>
            </div>            
            <div style="display:block; clear:both;">
                <label>' . $this->l('Enable sufix') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateYesOrNo("sufix") . '
    				<p class="preference_description">' . $this->l('Turn this option on if you want to enable sufix for your voucher code. It will be added AFTER generated code like CODE_sufix.') . '</p>
    			</div>
            </div>
            <div style="display:block; clear:both;">
                <label>' . $this->l('Sufix') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateLangInput("sufix_code") . '
    				<p class="preference_description"><br/>' . $this->l('Define sufix for your voucher code') . '</p>
    			</div>
            </div>
            <div style="display:block; clear:both;">
                <label>' . $this->l('Enable prefix') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateYesOrNo("prefix") . '
    				<p class="preference_description">' . $this->l('Turn this option on if you want to enable prefix for your voucher code. It will be added BEFORE generated code like prefix_CODE.') . '</p>
    			</div>
            </div>
            <div style="display:block; clear:both;">
                <label>' . $this->l('Prefix') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateLangInput("prefix_code") . '
    				<p class="preference_description"><br/>' . $this->l('Define prefix for your voucher code') . '</p>
    			</div>
            </div>
            <div style="display:block; clear:both;">
                <label>' . $this->l('Highlight') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateYesOrNo("highlight") . '
    				<p class="preference_description">' . $this->l('If the voucher is not yet in the cart, it will be displayed in the cart summary.') . '</p>
    			</div>
            </div>
            ' . (parent::psversion() == 5 || parent::psversion() == 6 || parent::psversion() == 7 ? '
            <div style="display:block; clear:both;">
                <label>' . $this->l('Partial use') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateYesOrNo("partial_use") . '
    				<p class="preference_description">' . $this->l('Only applicable if the voucher value is greater than the cart total. If you do not allow partial use, the voucher value will be lowered to the total order amount. If you allow partial use, however, a new voucher will be created with the remainder.') . '</p>
    			</div>
            </div>' : '') . '
            ' . (parent::psversion() == 5 || parent::psversion() == 6 || parent::psversion() == 7 ? '
            <div style="display:block; clear:both;">
                <label>' . $this->l('Priority') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateInput("priority", 1) . '
    				<p class="preference_description">' . $this->l('Cart rules are applied by y. A cart rule with a priority of "1" will be processed before a cart rule with a priority of "2".') . '</p>
    			</div>
            </div>' : '') . '
            <div style="display:block; clear:both;">
                <label>' . $this->l('Active') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateYesOrNo("active") . '
    			</div>
            </div>
            <h2>' . $this->l('Conditions') . '</h2>';
        if ($this->datetype == null || $this->datetype == "days") {
            $form .= '
            <label>' . $this->l('Expiration time') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateInput("expiry") . '
    				<p class="preference_description">' . $this->l('Define how long (in days) voucher code will be active') . '</p>
    			</div>';
        }
        if ($this->datetype == "date") {
            $form .= '
            <label>' . $this->l('Date from') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateInput("datefrom") . '
    				<p class="preference_description">' . $this->l('Start date, format: YYYY-MM-DD HH:MM:SS') . '</p>
    			</div>';
            $form .= '
            <label>' . $this->l('Date to') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateInput("dateto") . '
    				<p class="preference_description">' . $this->l('Expiry date, format: YYYY-MM-DD HH:MM:SS') . '</p>
    			</div>';
        }
        $form .= '
            <label>' . $this->l('Minimum amount') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateInput("minimum_amount", 1) . ' ' . (parent::psversion() == 5 || parent::psversion() == 6 || parent::psversion() == 7 ? $this->generateCurrencySelect("minimum_amount_currency") : '') . ' ' . $this->generateTaxIncExc("minimum_amount_tax") . ' ' . (parent::psversion() == 5 || parent::psversion() == 6 || parent::psversion() == 7 ? $this->generateShippingIncExc("minimum_amount_shipping") : '') . '
    				    <p class="preference_description">' . $this->l('You can choose a minimum amount for the cart either with or without the taxes and shipping.') . '</p>
    			 </div>
    			 <div class="bootstrap"><div class="alert alert-info">
    			 ' . $this->l('Please note that settings here are related to one unique voucher code that module will generate.') . ' ' . $this->l('Suggested values for fields below: Total available: 1, Total available for each user: 1') . '<br/>
    			 ' . $this->l('This means that customer that will receive one unique voucher will have possibility to use it during checkout only one time (as long as you will use suggested values)') . '<br/>
    			 </div></div>
        <label>' . $this->l('Total available') . '</label>
        <div class="margin-form" style="position:relative;">
        ' . $this->generateInput("quantity", 1) . '
    				<p class="preference_description">' . $this->l('The cart rule will be applied to the first "X" customers only.') . '</p>
    			 </div>

        <label>' . $this->l('Total available for each user') . '</label>
        <div class="margin-form" style="position:relative;">
        ' . $this->generateInput("quantity_per_user", 1) . '
    				<p class="preference_description">' . $this->l('A customer will only be able to use the cart rule "X" time(s).') . '</p>
    			 </div>

            <div style="display:block; clear:both;">
                <label>' . $this->l('Uncombinable with other codes') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateYesOrNo("cart_rule_restriction") . '
                    <p class="preference_description">' . $this->l('Turn this option on if you want dont want to allow to use this code with other voucher codes') . '</p>
    			</div>
            </div>

            ' . (parent::psversion() != 4 ? '<div style="display:block; clear:both;">
                <label>' . $this->l('Share voucher between shops') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateYesOrNo("shareshops") . '
    				<p class="preference_description">' . $this->l('If enabled - voucher will be shared between shops (multistore), if disabled - voucher will be available only in shop where it was generated') . '</p>
    			</div>
            </div>' : '') . '

            ' . (parent::psversion() == 4 ? '<div style="display:block; clear:both;">
                <label>' . $this->l('Cumulative with price reductions') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateYesOrNo("cumulable_reduction") . '
                    <p class="preference_description">' . $this->l('Turn this option on if you want to allow to use this code with price reductions') . '</p>
    			</div>
            </div>' : '') . '
            
            <div class="margin-form" style="clear:both; display:block;">
            
            <label>' . $this->l('How many product(s) matching the following rules (below) cart must contain?') . '</label>
                <div class="clearfix" style="display:block; clear:both; overflow:hidden;">
                    <div class="margin-form col-md-4" style="position:relative">
                    ' . $this->generateInput("restriction_qty", 1) . '
                    </div>
                </div>
            
            ' . (parent::psversion() == 4 || parent::psversion() == 5 || parent::psversion() == 6 || parent::psversion() == 7 ? '
            <label>' . $this->l('Add rule concerning categories') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateCategoriesSelect("restriction_categories") . '
    			</div>' : '') . '
            ' . (parent::psversion() == 5 || parent::psversion() == 6 || parent::psversion() == 7 ? '
            <label>' . $this->l('Add rule concerning products') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateProductsSelect("restriction_products") . '
    			</div>' : '') . '
            ' . (parent::psversion() == 5 || parent::psversion() == 6 || parent::psversion() == 7 ? '
            <label>' . $this->l('Add rule concerning attributes') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateAttributesSelect("restriction_attributes") . '
            </div>' : '') . '
            ' . (parent::psversion() == 5 || parent::psversion() == 6 || parent::psversion() == 7 ? '
            <label>' . $this->l('Add rule concerning manufacturers') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateManufacturersSelect("res_manufacturers") . '
    			</div>' : '') . '
    		' . (parent::psversion() == 5 || parent::psversion() == 6 || parent::psversion() == 7 ? '
            <label>' . $this->l('Add rule concerning suppliers') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateSuppliersSelect("res_suppliers") . '
    			</div>' : '') . '
    			
    		
    			
            ' . (parent::psversion() == 5 || parent::psversion() == 6 || parent::psversion() == 7 ? '
            <label>' . $this->l('Add rule concerning carriers') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateCarriersSelect("res_carriers") . '
    			</div>' : '') . '
            ' . (parent::psversion() == 5 || parent::psversion() == 6 || parent::psversion() == 7 ? '
            <label>' . $this->l('Add rule concerning groups of customers') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateGroupsSelect("res_groups") . '
    			</div>' : '') . '
    			
    			' . (parent::psversion() == 5 || parent::psversion() == 6 || parent::psversion() == 7 ? '
            <label>' . $this->l('Add rule concerning countries') . '</label>
                <div class="margin-form" style="position:relative;">
                    ' . $this->generateCountrySelect("res_countries") . '
    			</div>' : '') . '
    		</div>
    		
    			
            <h2>' . $this->l('Actions') . '</h2>
                <div style="display:block; clear:both;">
                    <label>' . $this->l('Free shipping') . '</label>
                    <div class="margin-form" style="position:relative;">
                        ' . $this->generateYesOrNo("free_shipping") . '
        			</div>
                </div>
                  
                <div style="display:block; clear:both;" class="' . (parent::psversion() == 4 ? 'ps14freeshipping' : '') . '">
                    <label>' . $this->l('Apply a discount') . '</label>
                    <div class="margin-form" style="position:relative;">
                        ' . $this->generateReductionType("reduction_type") . '
        		    </div>
                </div>
                
                 ' . (parent::psversion() == 7 ? '
                <div ' . (Configuration::get($this->prefix . "reduction_type") == 1 ? 'style="display:block; clear:both;"' : 'style="display:none;"') . '>
                    <label>' . $this->l('Exclude discounted products') . '</label>
                    <div class="margin-form" style="position:relative;">
                        ' . $this->generateYesOrNo("excludeSpecials") . '
                        <p class="preference_description">' . $this->l('If enabled, the voucher will not apply to products already on sale.') . '</p>
                    </div>
                </div>' : '') . '
                
                ' . (parent::psversion() == 5 || parent::psversion() == 6 || parent::psversion() == 7 ? '
                <div class="' . (parent::psversion() == 4 ? 'ps14freeshipping' : '') . '" id="' . $this->prefix . 'apply_discount_to" ' . (Configuration::get($this->prefix . "reduction_type") == 1 || Configuration::get($this->prefix . "reduction_type") == 2 ? 'style="display:block; clear:both; margin-top:15px;"' : 'style="display:block; clear:both; display:none; margin-top:15px;"') . '>
                    <label>' . $this->l('Apply discount to') . '</label>
                    <div class="margin-form" style="position:relative;">
                        ' . $this->generateApplyDiscountTo("apply_discount_to") . '
        			</div>
                </div>' : '') . '
                 ' . (parent::psversion() == 5 || parent::psversion() == 6 || parent::psversion() == 7 ? '
                 <div style="display:block; clear:both;">
                    ' . $this->generateFreeGiftSelect("free_gift") . '
                 </div>' : '');

        return $form;
    }
}