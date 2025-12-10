<?php

require_once __DIR__.'/../../classes/Description.php';

class phdescriptionaddFileModuleFrontController extends ModuleFrontController
{

    private $name = 'phdescription';

    public function initContent()
    {
        parent::initContent();

        $id_product = (int)$_REQUEST['id_product'];
        $id_description = (int)$_REQUEST['id_description'];
        $type = (int)$_REQUEST['type'];
        $return = array();
        if (Tools::isSubmit('uploadFiles')) {
            $file = $this->makeUpload($id_product, $_FILES['file'], $type, $id_description);
            if (array_key_exists('success', $file)) {
                echo json_encode(array(
                    'success' => 'done',
                    'filepath' => $file['filepath'],
                    'filename' => $file['filename'],
                    'id_product' => $id_product,
                    'type' => $type,
                ));
                exit();
            } else {
                echo json_encode(array('error' => $file));
                exit();
            }
        } else {
            $return['error'] = 'NO file';
        }
        echo json_encode($return);
        exit();
    }

    public function makeUpload($id_product, $file, $type, $id_description)
    {
        if (!empty($file)) {
            $file_name = $file['name'];
            $rand_number = rand(0, 900);
            $temp = $file['tmp_name'];
            if ($error = ImageManager::validateUpload($file)) {
                return $error;
            } else {
                $ssl = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
                $ext = substr($file_name, strrpos($file_name, '.') + 1);
                $file_name = substr($file_name,0,strrpos($file_name,'.'));
                $name = md5($id_product.'-'.$id_description.'-'.$type).'-';
                $file_name = $name.$file_name.'.'.$ext;
                $fn = md5($name.$file_name);
                $file_name2 = $fn.'.'.$ext;
                
                if (!file_exists(_PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$id_product)) {
                    mkdir(_PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$id_product, 0777, true);
                }
                $dest = _PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$id_product.DIRECTORY_SEPARATOR.$file_name2;
                if (!move_uploaded_file($temp, $dest)) {
                    return $this->displayError($this->trans('An error occurred while attempting to upload the file.', array(), 'Admin.Notifications.Error'));
                }

                $image_size = getimagesize($dest);
                $image_width = $image_size[0];
                $image_height = $image_size[1];
                $tmpfile = $ssl.$this->context->shop->domain.$this->context->shop->physical_uri.'modules/'.$this->name.'/files/'.$id_product.'/'.$file_name2;
                $path = _PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$id_product.DIRECTORY_SEPARATOR.$file_name;
                $height = $image_height;
                if ($type == 1) {
                    $width = 1080;
                    if ($image_width > $width) {
                        $height = number_format(($image_height * $width) / $image_width, 2, '.', '');
                    }
                } else {
                    $width = 540;
                    if ($image_width > $width) {
                        $height = number_format(($image_height * $width) / $image_width, 2, '.', '');
                    }
                }
                $tgt_width = $tgt_height = $width;
                $src_width = $src_height = $height;
                $error = 0;
                $xxx = ImageManager::resize($dest, $path, $width, $height, 'jpg', false, $error, $tgt_width, $tgt_height, 6, $src_width, $src_height);
                unlink($dest);
                if (!$xxx) {
                    return array("Nie można utworzyć pliku.");
                }

                return array(
                    'success' => true,
                    'filepath' => $ssl.$this->context->shop->domain.$this->context->shop->physical_uri.'/modules/'.$this->name.'/files/'.$id_product.'/'.$file_name,
                    'filename' => $file_name,
                    'id_product' => $id_product,
                    'type' => $type,
                );
            }
        }
    }

}
