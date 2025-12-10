<?php

if (!class_exists('ProductPack')) {
    require_once _PS_ROOT_DIR_ . '/modules/phsuperpack/classes/ProductPack.php';
}

define('PACK_DIR', _PS_MODULE_DIR_ . 'phsuperpack/');

class PhsuperpackPackModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        $this->db = Db::getInstance();
        $this->context = Context::getContext();
        $this->ssl = true;
    }

    /**
     * Search product
     */
    private function searchProduct($search)
    {
        $sql = "SELECT id_product, name FROM "._DB_PREFIX_."product_lang WHERE name LIKE '%".$search."%' AND id_lang=".$this->context->language->id;
        $query = $this->db->executeS($sql);

        $products = [];
        if (!empty($query)) {
            foreach ($query as $prod) {
                $prod['quantity'] = StockAvailable::getQuantityAvailableByProduct($prod['id_product']);
                $prod['price'] = Tools::displayPrice(Product::getPriceStatic($prod['id_product']));

                $products[] = $prod;
            }
        }

        return $products;
    }

    /**
     * Get product
     */
    private function getProductImage($id_product)
    {
        $image = Image::getCover($id_product);
        if ($image) {
            $imageInstance = new Image($image['id_image']);

            // if ssl turn into https
            $http = 'http://';
            if (Configuration::get('PS_SSL_ENABLED') == 1) {
                $http = 'https://';
            }

            $imagePath = $http.Configuration::get('PS_SHOP_DOMAIN')._THEME_PROD_DIR_.$imageInstance->getExistingImgPath().'.'.$imageInstance->image_format;
            return $imagePath;
        }

        return null;
    }

    /**
     * Get searched product data
     */
    private function getSearchProductData($id_product)
    {
        $data = [
            'id_product' => $id_product,
            'image' => $this->getProductImage($id_product),
            'name' => Product::getProductName($id_product),
            'quantity' => StockAvailable::getQuantityAvailableByProduct($id_product),
            'basic_price' => Product::getPriceStatic($id_product),
        ];

        return $data;
    }

    /**
     * Crete pack
     */
    private function createPack($id_product_pack, $products)
    {
        foreach ($products as $product) {
            $sql = "INSERT INTO "._DB_PREFIX_."pack(id_product_pack, id_product_item, id_product_attribute_item, quantity) VALUES(".$id_product_pack.", ".$product->id_product.", 0, ".$product->quantity.")";
            $this->db->execute($sql);
        }
    }

    /**
     * Crete pack single
     */
    private function createPackSingle($id_product_pack, $id_product_main)
    {
        $sql = "INSERT INTO "._DB_PREFIX_."pack(id_product_pack, id_product_item, id_product_attribute_item, quantity) VALUES(".(int)$id_product_pack.", ".(int)$id_product_main.", 0, 1)";
        $this->db->execute($sql);
    }

    /**
     * Get hieghts position
     */
    private function getHighetPosition($id_product_main)
    {
        $sql = "SELECT position FROM "._DB_PREFIX_."phsuperpack_product WHERE id_product=".$id_product_main." ORDER BY position DESC";
        $query = $this->db->executeS($sql);

        if (!empty($query)) {
            return $query[0]['position'];
        }
        return 0;
    }

    /**
     * Product pack
     */
    private function createProductPackData($id_product_main, $id_product_pack, $active, $id_product_attribute = 0)
    {
        $position = $this->getHighetPosition($id_product_main) + 1;
        $id_shop = $this->context->shop->id;

        $sql = "INSERT INTO "._DB_PREFIX_."phsuperpack_product(id_product, id_product_attribute, id_product_pack, id_shop, active, position) VALUES(".$id_product_main.", ".$id_product_attribute.", ".$id_product_pack.", ".$id_shop.", $active, ".$position.")";
        $this->db->execute($sql);
    }

    /**
     * Create specific price for pack
     */
    private function setSepcificPrice($id_product_pack, $whole_quantity, $reduction, $reduction_type)
    {
        if ($reduction_type == 'percentage') {
            $reduction = $reduction / 100;
        }

        $specificPrice = new SpecificPrice();
        $specificPrice->id_product = (int)$id_product_pack;
        $specificPrice->id_shop = $this->context->shop->id;
        $specificPrice->id_currency = 0;
        $specificPrice->id_country = 0;
        $specificPrice->id_group = 0;
        $specificPrice->id_customer = 0;
        $specificPrice->from_quantity = 1;
        $specificPrice->price = -1;
        $specificPrice->reduction = $reduction;
        $specificPrice->reduction_type = $reduction_type;
        $specificPrice->from = '0000-00-00 00:00:00';
        $specificPrice->to = '0000-00-00 00:00:00';
        $specificPrice->add();
    }

    /**
     * Get full link to created image
     */
    private function getFullLink($image_id)
    {
        $link = _PS_PROD_IMG_DIR_;
        $explode = str_split((string)$image_id);

        for ($i = 0; $i < sizeof($explode); $i++) {
            $link .= $explode[$i].'/';
        }

        mkdir($link, 0755, true);

        return $link;
    }

    /**
     * Upload images
     */
    private function uploadImages($path, $image_id, $image_url)
    {
        $imageTypes = ImageType::getImagesTypes();
        foreach ($imageTypes as $type) {
            if ($type['products'] == 1) {
                $filename = $image_id.'-'.$type['name'].'.jpg';

                if (!empty(file_get_contents($image_url))) {
                    if (!file_put_contents($path.$filename, file_get_contents($image_url))) {
                        $image->delete();
                        return false;
                    }
                }
            }
        }

        if (!empty(file_get_contents($image_url))) {
            if (!file_put_contents($path.$image_id.'.jpg', file_get_contents($image_url))) {
                $image->delete();
                return false;
            }
        }

        return true;
    }

    /**
     * Set image for product
     */
    private function setImage($id_product, $image_url = null)
    {
        $explode = explode('/', $image_url);
        $last = explode('.', $explode[sizeof($explode) - 1]);
        $legend = $last[0];

        $image = new Image();
        $image->id_product = $id_product;
        // $image->legend = $legend;
        $image->cover = 1;
        $image->position = Image::getHighestPosition($id_product) + 1;
        if (!$image->add()) {
            return false;
        }

        $id_image = $image->id;

        $path = $this->getFullLink($id_image);

        if (!$this->uploadImages($path, $id_image, $image_url)) {
            return false;
        }

        return true;
    }

    /**
     * Get file content
     */
    private function getFileUrl($file)
    {
        if ($file['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $file['tmp_name'];

            return $fileTmpPath;
        }

        return null;
    }

    /**
     * Create empty pack if not exist
     */
    private function createIfNotExist($id_pack)
    {
        if (!empty($id_pack)) {
            return $id_pack;
        } else {
            $product = new Product();
            $product->save();

            return $product->id;
        }
    }

    /**
     * Delete pack data on update/save
     */
    private function deletePackData($id_pack)
    {
        $this->db->execute("DELETE FROM "._DB_PREFIX_."pack WHERE id_product_pack=".$id_pack);
        $this->db->execute("DELETE FROM "._DB_PREFIX_."phsuperpack_product WHERE id_product_pack=".$id_pack);
    }

    /**
     * Create pack product
     */
    private function createPackProduct()
    {
        // if not exist create empty product for pack
        $id_pack = $this->createIfNotExist((int)$_POST['id_pack']);
        // delete pack and prodiuct pack
        $this->deletePackData($id_pack);

        $product = new Product($id_pack);

        // delete images
        $product->deleteImages();

        $langs = Language::getIDs();
        // set names
        foreach ($langs as $id_lang) {
            $product->name[$id_lang] = $_POST['name'];
            $product->link_rewrite[$id_lang] = Tools::str2url($_POST['name']);
        }

        $product->id_tax_rules_group = (int)Configuration::get('PH_SUPERPACK_TAX');
        $id_category = empty(Configuration::get('PS_HOME_CATEGORY')) ? 2 : Configuration::get('PS_HOME_CATEGORY');
        $product->id_category_default = $id_category;
        $product->addToCategories([$id_category]);

        $price = $_POST['price'];
        if (empty($price)) {
            $price = 0;
        }

        $product->type = 'pack';
        // set price
        $product->price = $price;
        $product->update();

        // whole quantity
        $whole_quantity = 0;
        foreach (json_decode($_POST['products']) as $productPack) {
            $whole_quantity += $productPack->quantity;
        }

        // set quantity for pack
        StockAvailable::setQuantity($product->id, null, $whole_quantity);

        // create single pack
        $this->createPackSingle((int)$product->id, (int)$_POST['id_main_product']);
        // create pack
        $this->createPack($product->id, json_decode($_POST['products']));
        // create pack products
        $this->createProductPackData($_POST['id_main_product'], $product->id, $_POST['active']);

        $for_all_products = $_POST['for_all_products'];
        $for_all_cobinations = $_POST['for_all_cobinations'];

        // if for all combinations
        if ($for_all_cobinations == 'true') {
            $combinations = Product::getProductAttributesIds($_POST['id_main_product'], true);
            foreach ($combinations as $combination) {
                $this->createProductPackData($_POST['id_main_product'], $product->id, $_POST['active'], $combination['id_product_attribute']);
            }
        }

        // create pack for all products
        if ($for_all_products == 'true') {
            foreach (json_decode($_POST['products']) as $pack_prod) {
                $this->createProductPackData($pack_prod->id_product, $product->id, $_POST['active']);

                // if for all combinations
                if ($for_all_cobinations == 'true') {
                    $combinations = Product::getProductAttributesIds($pack_prod->id_product, true);
                    foreach ($combinations as $combination) {
                        $this->createProductPackData($pack_prod->id_product, $product->id, $_POST['active'], $combination['id_product_attribute']);
                    }
                }
            }
        }

        // if exist specific price create
        if (!empty($_POST['reduction']) && !empty($_POST['reduction_type'])) {
            $this->setSepcificPrice($product->id, $whole_quantity, $_POST['reduction'], $_POST['reduction_type']);
        }

        $image_url = $this->getProductImage($_POST['id_main_product']);
        if (!empty($_FILES['image'])) {
            // get file url
            $image_url = $this->getFileUrl($_FILES['image']);
        } elseif (!empty(Configuration::get('PH_BANNER_IMG'))) {
            // get file url
            $image_url = _PS_MODULE_DIR_.'phsuperpack/img/'.Configuration::get('PH_BANNER_IMG');
        }

        // set image
        if (!empty($image_url)) {
            $this->setImage($product->id, $image_url);
        }
    }

    /**
     * Get active pack
     */
    public function getActivePackProduct($id_product_pack, $id_product)
    {
        $sql = "SELECT DISTINCT active, position FROM "._DB_PREFIX_."phsuperpack_product WHERE id_product=".$id_product." AND id_product_pack=".$id_product_pack;
        $query = Db::getInstance()->executeS($sql);

        if (!empty($query)) {
            return $query[0];
        }
        return false;
    }

    private function getProductsNames($id_pack, $id_product_main)
    {
        $sql = "SELECT id_product_item, quantity FROM "._DB_PREFIX_."pack WHERE id_product_pack=".$id_pack." AND id_product_attribute_item=0";
        $products = Db::getInstance()->executeS($sql);

        $productsString = '';
        foreach ($products as $product) {
            if ($product['id_product_item'] != $id_product_main) {
                $tmp = ' | '.Product::getProductName($product['id_product_item'], null, Context::getContext()->language->id).' ('.$product['quantity'].')';

                $productsString .= $tmp;
            }
        }

        return $productsString;
    }

    /**
     * Get packs
     */
    public function getPacks($id_product)
    {
        $sql = "SELECT DISTINCT id_product_pack FROM "._DB_PREFIX_."phsuperpack_product WHERE id_product=".$id_product." ORDER BY position ASC";
        $products = Db::getInstance()->executeS($sql);

        if (!empty($products)) {
            $data = [];
            foreach ($products as $product) {
                $id_product_pack = $product['id_product_pack'];
                $productObj = new Product($id_product_pack);

                $packData = $this->getActivePackProduct($id_product_pack, $id_product);
                $data[] = [
                    'id' => $id_product_pack,
                    'name' => $productObj->name[Context::getContext()->language->id],
                    'products_names' => $this->getProductsNames($productObj->id, $id_product),
                    'price_nett' => Tools::displayPrice($productObj->getPriceWithoutReduct(true)),
                    'price_gross' => Tools::displayPrice($productObj->getPriceWithoutReduct()),
                    'discount_nett' => Tools::displayPrice(Product::getPriceStatic($id_product_pack, false)),
                    'discount_gross' => Tools::displayPrice(Product::getPriceStatic($id_product_pack)),
                    'quantity' => StockAvailable::getQuantityAvailableByProduct($id_product_pack),
                    'active' => $packData['active'],
                    'position' => $packData['position'],
                ];
            }

            return $data;
        }

        return null;
    }

    /**
     * Get reduction
     */
    public function getSpecificPriceDiscount($productId, $idShop = null)
    {
        if ($idShop === null) {
            $idShop = (int)Context::getContext()->shop->id;
        }

        $db = Db::getInstance();

        $query = 'SELECT reduction, reduction_type
				  FROM ' . _DB_PREFIX_ . 'specific_price
				  WHERE id_product = ' . (int)$productId . '
				  AND id_shop = ' . (int)$idShop . '
				  AND (reduction > 0 OR reduction_type != "amount")
				  ORDER BY reduction DESC
				  LIMIT 1';

        $result = $this->db->executeS($query);

        if (!$result) {
            return null;
        }

        if ($result[0]['reduction_type'] == 'percentage') {
            $reduction_percent =  $result[0]['reduction'] * 100 . '%';
            return ['reduction' => $reduction_percent, 'reduction_type' => $result[0]['reduction_type']];
        } else {
            return ['reduction' => $result[0]['reduction'], 'reduction_type' => $result[0]['reduction_type']];
        }
    }

    /**
     * Get pack products
     */
    private function getPackProducts($id_pack, $id_product_main)
    {
        $sql = "SELECT id_product_item, quantity FROM "._DB_PREFIX_."pack WHERE id_product_pack=".$id_pack." AND id_product_attribute_item=0";
        $products = $this->db->executeS($sql);

        $productsArray = [];
        foreach ($products as $product) {
            if ($product['id_product_item'] != $id_product_main) {
                $tmp = $this->getSearchProductData($product['id_product_item']);
                $tmp['choosed_quantity'] = $product['quantity'];

                $productsArray[] = $tmp;
            }
        }

        return $productsArray;
    }

    /**
     * Check if created for products in pack
     */
    private function checkCreateForProducts($id_pack)
    {
        $sql = "SELECT DISTINCT id_product FROM "._DB_PREFIX_."phsuperpack_product WHERE id_product_pack=".$id_pack;
        $query = $this->db->executeS($sql);

        if (sizeof($query) > 1) {
            return true;
        }
        return false;
    }

    /**
     * Check if created for products combinations in pack
     */
    private function checkCreateForProductsCombination($id_pack)
    {
        $sql = "SELECT COUNT(*) AS combinations FROM "._DB_PREFIX_."phsuperpack_product WHERE id_product_attribute<>0 AND id_product_pack=".$id_pack;
        $query = $this->db->executeS($sql);

        if ($query[0]['combinations'] > 0) {
            return true;
        }
        return false;
    }

    /**
     * Get pack data
     */
    private function getPackDataForEdit($id_pack, $id_product_main)
    {
        $pack = new Product($id_pack);

        $reduction = $this->getSpecificPriceDiscount($id_pack);
        $data = [
            'id' => $id_pack,
            'price' => Tools::ps_round($pack->getPriceWithoutReduct(), 6),
            'name' => $pack->name[$this->context->language->id],
            'reduction' => !empty($reduction['reduction']) ? $reduction['reduction'] : 0.00,
            'reduction_type' => !empty($reduction['reduction_type']) ? $reduction['reduction_type'] : null,
            'image' => $this->getProductImage($id_pack),
            'products' => $this->getPackProducts($id_pack, $id_product_main),
            'for_all_products' => $this->checkCreateForProducts($id_pack),
            'for_all_combination' => $this->checkCreateForProductsCombination($id_pack),
            'active' => $this->getActivePackProduct($id_pack, $id_product_main)['active'],
        ];

        return $data;
    }

    /**
     * Delete pack
     */
    private function deletePack($id_pack)
    {
        // delete pack data
        $this->deletePackData($id_pack);

        // delete product
        $product = new Product($id_pack);
        $product->delete();
    }

    /**
     * Change active
     */
    private function changeActive($id_pack, $active)
    {
        $sql = "UPDATE "._DB_PREFIX_."phsuperpack_product SET active=".(int)$active." WHERE id_product_pack=".(int)$id_pack;
        $this->db->execute($sql);
    }

    /**
     * Get new positions
     */
    private function getNewPosition($old_position, $new_position)
    {
        $sql = "SELECT id_product_pack, position FROM "._DB_PREFIX_."phsuperpack_product WHERE position=".$new_position;
        $query = $this->db->executeS($sql);

        if (!empty($query)) {
            $sqlOld = "UPDATE "._DB_PREFIX_."phsuperpack_product SET position=".(int)$old_position." WHERE id_product_pack=".(int)$query[0]['id_product_pack'];
            $this->db->execute($sqlOld);

            return $query[0]['position'];
        }
        return $old_position;
    }

    /**
     * Change position
     */
    private function changePosition($id_pack, $id_product, $what_to_do, $current_position)
    {
        if ($current_position == 1 && $what_to_do == 'upper') {
            return;
        } elseif ($this->getHighetPosition($id_product) == $current_position && $what_to_do == 'lower') {
            return;
        }

        $new_position_tmp = $current_position;

        if ($what_to_do == 'upper') {
            $new_position_tmp -= 1;
        } else {
            $new_position_tmp += 1;
        }

        // get new position
        $new_position = $this->getNewPosition($current_position, $new_position_tmp);

        $sql = "UPDATE "._DB_PREFIX_."phsuperpack_product SET position=".(int)$new_position." WHERE id_product_pack=".(int)$id_pack;
        $this->db->execute($sql);
    }

    public function initContent()
    {
        $action = Tools::getValue('action');
        if (!empty($action)) {
            if ($action == 'search_product') {
                $products = $this->searchProduct(Tools::getValue('search'));

                header('Content-Type: application/json');
                echo json_encode($products);
                die();
            } elseif ($action == 'get_searched_product_data') {
                $product = $this->getSearchProductData(Tools::getValue('id_product'));

                header('Content-Type: application/json');
                echo json_encode($product);
                die();
            } elseif ($action == 'createPack') {
                //$postData = file_get_contents("php://input");
                //$data = json_decode($postData, true);

                $this->createPackProduct();

                header('Content-Type: application/json');
                echo json_encode(['response' => true]);
                die();
            } elseif ($action == 'fetchPacks') {
                $packs = $this->getPacks(Tools::getValue('id_product'));

                header('Content-Type: application/json');
                echo json_encode(['packs' => $packs]);
                die();
            } elseif ($action == 'getPackData') {
                $pack = $this->getPackDataForEdit(Tools::getValue('id_pack'), Tools::getValue('id_product_main'));

                header('Content-Type: application/json');
                echo json_encode(['pack' => $pack]);
                die();
            } elseif ($action == 'deletePack') {
                $this->deletePack(Tools::getValue('id_pack'));

                header('Content-Type: application/json');
                echo json_encode(['response' => true]);
                die();
            } elseif ($action == 'changeActive') {
                $this->changeActive(Tools::getValue('id_pack'), Tools::getValue('active'));

                header('Content-Type: application/json');
                echo json_encode(['response' => true]);
                die();
            } elseif ($action == 'changePosition') {
                $this->changePosition(Tools::getValue('id_pack'), Tools::getValue('id_product'), Tools::getValue('what_to_do'), Tools::getValue('current_position'));

                header('Content-Type: application/json');
                echo json_encode(['response' => true]);
                die();
            }
        }
    }
}
