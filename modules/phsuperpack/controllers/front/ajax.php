<?php

class PhsuperpackAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        // Usefull vars derivated from getContext
        $context = Context::getContext();

        // Get the search term from the request
        $searchTerm = Tools::getValue('searchTerm');

        // Minimum length of the search term
        $minLength = 3;

        // Check if the search term meets the minimum length requirement
        if (strlen($searchTerm) >= $minLength) {
            // Get the categories that match the search term
            $categories = Category::searchByName($context->language->id, $searchTerm);

            // Format the categories as an array of JSON objects
            $categoryList = array();
            foreach ($categories as $category) {
                $categoryList[] = array(
                    'id' => $category['id_category'],
                    'name' => $category['name'],
                    'link' => $context->link->getCategoryLink($category['id_category'])
                );
            }

            // Send the category list as a JSON response
            header('Content-Type: application/json');
            echo Tools::jsonEncode($categoryList);
            exit;
        }
    }
}