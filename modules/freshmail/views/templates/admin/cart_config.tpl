{assign var=translations value="{
    abandoned_cart: '{l s='Abandoned cart' mod='freshmail'  }',
    settings: '{l s='Settings' mod='freshmail'  }',
    fontcolor: '{l s='Font color' mod='freshmail'  }',
    bgcolor: '{l s='Background color' mod='freshmail'  }',
    logo: '{l s='Shop logo' mod='freshmail'  }',
    left: '{l s='Left' mod='freshmail'  }',
    center: '{l s='Center' mod='freshmail'  }',
    right: '{l s='Right' mod='freshmail'  }',
    save_sucess: '{l s='Template saved' mod='freshmail'  }',
    buttonbg: '{l s='Button color' mod='freshmail'  }',
    cron_nfo: '{l s='Full functionality requires the task to run periodically' mod='freshmail'}: {$links.cron_url} {l s='or use cli' mod='freshmail'}: {$links.cron_cli}',
    modules: {
        base: '{l s='Template background' mod='freshmail' }',
        blockLogo: '{l s='Logo' mod='freshmail' }',
        blockText: '{l s='Text block' mod='freshmail' }',
        blockProducts: '{l s='Products' mod='freshmail' }',
        blockDiscount: '{l s='Discout info' mod='freshmail' }',
        blockCTA: '{l s='Call To Action' mod='freshmail' }',
    },
    send_notifications: '{l s='Send notifications about an abandoned cart?' mod='freshmail' }',
    yes: '{l s='Yes' mod='freshmail' }',
    no: '{l s='No' mod='freshmail' }',
    send_after: '{l s='Send after' mod='freshmail' }',
    discount_select: '{l s='Discount code' mod='freshmail' }',
    discount_none: '{l s='I dont want to grant a discount' mod='freshmail' }',
    discount_percent: '{l s='Percentage discount' mod='freshmail' }',
    discount_custom: '{l s='Own discount code' mod='freshmail' }',
    discout_livetime: '{l s='The period of validity discount code' mod='freshmail' }',
    discout_value: '{l s='Discount value' mod='freshmail' }',
    discount_custom_value: '{l s='Discount code' mod='freshmail' }',
    email_panel: '{l s='Email' mod='freshmail' }',
    email_template: '{l s='Default E-mail template' mod='freshmail' }',
    email_subject: '{l s='Email subject' mod='freshmail' }',
    email_preheader: '{l s='Email preheader text' mod='freshmail' }',
    email_test_email: '{l s='Test email address' mod='freshmail' }',
    email_send_test_btn: '{l s='Send a test email' mod='freshmail' }',
    template_default_logo: '{l s='Use default shop logo' mod='freshmail' }',
    template_bg: '{l s='Background color' mod='freshmail' }',
    template_fc: '{l s='Font color' mod='freshmail' }',
    template_logo_url: '{l s='Shop logo url' mod='freshmail' }',
    template_left: '{l s='Left' mod='freshmail' }',
    template_center: '{l s='Center' mod='freshmail' }',
    template_right: '{l s='Right' mod='freshmail' }',
    save: '{l s='Save' mod='freshmail' }',
    email_template_panel: '{l s='Email template' mod='freshmail'  }',
    create_freshmail: '{l s='Create your own template' mod='freshmail'  }',
    attention: '{l s='Attention!' mod='freshmail'  }',
    external_tpl_nfo: '{l s='Make sure that the template created in FreshMail contains all the necessary Prestashop tags: {shop_name}, {shop_url}, {shop_logo},{firstname}, {lastname}, {products_list}, {discount_code}, {cart_url}' mod='freshmail' }',
    def_mod_template: '{l s='Default module template' mod='freshmail'  }',
    def_mod_template_name: '{l s='Default template' mod='freshmail'  }',
    current_tpl: '{l s='My current template' mod='freshmail'  }',
    use_tpl: '{l s='Use this template' mod='freshmail'  }',
    edit_tpl: '{l s='Edit in FreshMail' mod='freshmail'  }'
  }"
}

{assign var=initConfig value="{
        config: {
            emails: {$cart_config->enabled|boolval|json_encode},
            send_after: '{$cart_config->send_after}',
            discount: '{$cart_config->discount_type}',
            discount_percent_livetime: {$cart_config->discount_lifetime|intval},
            discount_percent_value: {$cart_config->discount_percent|intval},
            discount_custom_value: '{$cart_config->discount_code}',
            template_id_hash: '{$cart_config->template_id_hash}',
            loggedin: {($is_logged) ? 'true' : 'false'},
            email_subject: '{$cart_config->email_subject[$id_lang]}',
            email_preheader: '{$cart_config->email_preheader[$id_lang]}',
        },
        urls: {
            module: '{$links.base_url}modules/freshmail/',
            shop_logo: '{$logo}',
            fm_editor: 'https://app.freshmail.com/pl/designer/newedit/?template_hash=',
            fm_library: 'https://app.freshmail.com/pl/library/#external'
        }
  }"
}


<div data-vue="abandonedcart-config">
    <abandonedcart
        :init-config="{$initConfig}"
        :translations="{$translations}"
    >
    </abandonedcart>
</div>

<div class="form-wrapper">
    <div class="alert alert-info"><button data-dismiss="alert" type="button" class="close">
            ×
        </button>
        {l s='Here you can set a reminder about the products in the basket and thus close the sales path. Set the amount of the discount, choose a ready template or create it yourself in FreshMail Designer and take care of personalized communication.' mod='freshmail'}
    </div>
</div>

{*<script src="https://cdn.jsdelivr.net/npm/vue@2.6.12"></script>*}
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script type="text/javascript" src="{$links.base_url}modules/freshmail/views/js/freshmail-abandoned-cart.js"></script>
<script type="text/javascript" src="{$links.base_url}modules/freshmail/views/js/freshmail-core.js"></script>