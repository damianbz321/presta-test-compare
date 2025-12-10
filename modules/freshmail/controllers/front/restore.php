<?php


class freshmailRestoreModuleFrontController extends ModuleFrontController
{
    /** @var string controller name */
    public $controller_name = 'Restore';

    public function init()
    {
        parent::init();
        $hash = Tools::getValue('hash');


        $fmCart = (new \FreshMail\Repository\Carts(Db::getInstance()))->getByHash($hash);
        $cart = new Cart($fmCart->id_cart);

        if(!Validate::isLoadedObject($cart) || $cart->id_shop != $this->context->shop->id){
            Tools::redirect('/');
        }

        $this->context->updateCustomer(new Customer($cart->id_customer));
        $this->context->cookie->id_cart = (int) $cart->id;
        $this->context->cookie->write();
        $this->context->cart = $cart;

        if(!empty($fmCart->id_cart_rule)){
            $cartRule = new CartRule($fmCart->id_cart_rule);
        } elseif(!empty($fmCart->discount_code)){
            $cartRule = new CartRule(CartRule::getIdByCode($fmCart->discount_code));
        }
        if(Validate::isLoadedObject($cartRule)){
            $this->context->cart->addCartRule($cartRule->id);
        }

        $this->context->cart->save();

        Tools::redirect(
            $this->context->link->getPageLink('cart', null, null, ['token' => Tools::getToken(false)])
        );
    }

}
