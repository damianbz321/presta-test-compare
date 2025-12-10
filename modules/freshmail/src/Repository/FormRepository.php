<?php

namespace FreshMail\Repository;


use FreshMail\Entity\Form;

/**
 * Class FormRepository
 */
class FormRepository extends AbstractRepository
{
    public function getByHooks($idShop, $hookName)
    {
        $query = new \DbQuery();
        $query->select('*')
            ->from(Form::$definition['table'])
            ->where('id_shop = '.(int)$idShop)
            ->where('lower(hook) = "'.pSQL(strtolower($hookName)).'"')
            ->where('active = 1')
            ->orderBy('position ASC')
        ;

        return $this->db->executeS($query);
    }
}