{assign var=translations value="{
    official_module: '{l s='The official free FreshMail module for PrestaShop' mod='freshmail'  }',
    boost_your_conversion_rates: '{l s='Boost your conversion rates' mod='freshmail' }',
    do_you_have_account: '{l s='Do you have a FreshMail account?' mod='freshmail' }',
    no_account: '{l s='I don’t have an account' mod='freshmail' }',
    create_trial: '{l s='Create a trial account and send' mod='freshmail'  }',
    create_trial_2: '{l s='60,000 emails for free' mod='freshmail' }',
    create_account: '{l s='Create a FreshMail account' mod='freshmail'  }',
    my_account: '{l s='My FreshMail account' mod='freshmail' }',
    my_account_connected: '{l s='Connected to a FreshMail account' mod='freshmail' }',
    have_account: '{l s='I have a FreshMail account' mod='freshmail' }',
    token: '{l s='Bearer token' mod='freshmail' }',
    find_token: '{l s='Find out where to download the token' mod='freshmail' }',
    continue: '{l s='Continue' mod='freshmail' }',
    integrating_makes_difference: '{l s='Integrating the module with FreshMail makes all the difference' mod='freshmail' }',
    features: '{l s='Features' mod='freshmail' }',
    module_without_freshMail: '{l s='Module without FreshMail' mod='freshmail' }',
    module_with_freshMail: '{l s='Module with FreshMail' mod='freshmail' }',
    automated_messages: '{l s='Sending of automated messages (abandoned baskets, birthday shipment)' mod='freshmail' }',
    automated_emails: '{l s='Sending of automated emails (abandoned baskets, birthday shipment) from FreshMail servers' mod='freshmail' }',
    email_reports: '{l s='Transactional email reports' mod='freshmail' }',
    email_templates: '{l s='Ready-to-use transactional email templates' mod='freshmail' }',
    resolving_email_issues: '{l s='Support in resolving email issues' mod='freshmail' }',
    subscriber_list_synchronisation: '{l s='Subscriber list synchronisation with FreshMail' mod='freshmail' }',
    message_alerts: '{l s='Undelivered message alerts' mod='freshmail' }',
    module_without_freshMail_account: '{l s='I wish to use the module without a FreshMail account' mod='freshmail' }',
    send_all_emails: '{l s='Yes, I wish to send all emails from FreshMail servers' mod='freshmail' }',
    synchronize_prestaShop: '{l s='Yes, I wish to synchronize my PrestaShop email list with FreshMail' mod='freshmail' }',
    make_full_use: '{l s='Make full use of what is offered by the module’s integration with FreshMail' mod='freshmail' }',
    back_step: '{l s='Back to step' mod='freshmail' }',
    learn_more: '{l s='Find out more' mod='freshmail' }',
    please_select: '{l s='Please select the list of recipients with which you want to synchronize' mod='freshmail' }',
    first_rate: '{l s='First-rate deliverability' mod='freshmail' }',
    send_automated_messages: '{l s='Send automated messages and notifications from your store to your customers via FreshMail’s secure SMTP. Rest assured that your emails have the best deliverability and do not end up in the spam folder.' mod='freshmail' }',
    reports: '{l s='Reports on the messages sent' mod='freshmail' }',
    access_detailed_reports: '{l s='Access detailed reports on the messages you send and monitor their effectiveness. Check the number of page openings, link clicks and bounce backs in order to optimize your email marketing. Browse detailed message dispatch logs.' mod='freshmail' }',
    expert_support: '{l s='Expert support' mod='freshmail' }',
    get_the_support: '{l s='Get the support of the best email marketing specialists 7 days a week. We answer every phone call and reply to every email.' mod='freshmail' }',
    simple_email_editing: '{l s='Simple email editing' mod='freshmail' }',
    ready_made_templates: '{l s='Use hundreds of ready-made templates (also those specially prepared for PrestaShop customers) and edit them freely using the intuitive, easy-to-use drag & drop editor.' mod='freshmail' }',
    back: '{l s='Back' mod='freshmail' }',
    discover_benefits: '{l s='Discover the benefits of the module’s integration with FreshMail' mod='freshmail' }',
    start_using: '{l s='Start using it for free' mod='freshmail' }',
    installed_free: '{l s='The module can be installed free of charge. Moreover, as part of the 60-day trial period, FreshMail lets you send as many as <b>60,000 transactional emails</b> for free!' mod='freshmail' }',
    i_dont_want: '{l s='I don’t want to benefit from the above features' mod='freshmail' }',
    i_wish_to_start: '{l s='I wish to start the free trial and send secure emails' mod='freshmail' }',
    configuration_overview: '{l s='Configuration overview' mod='freshmail' }',
    freshMail_servers: '{l s='Sending of emails from FreshMail servers' mod='freshmail' }',
    subscriber_list: '{l s='Subscriber list synchronisation with FreshMail' mod='freshmail' }',
    full_potential: '{l s='To use the module’s full potential, you must integrate it with a FreshMail account' mod='freshmail' }',
    change_settings: '{l s='Change settings' mod='freshmail' }',
    finish_your_configuration: '{l s='Finish your configuration' mod='freshmail' }',
    help: '{l s='Help' mod='freshmail' }',
    number_of_subscribers: '{l s='Number of subscribers'}'
  }"
}

{assign var=priceRules value="{
        assets_url: '{$links.base_url}modules/freshmail/views'
  }"
}

<div class="freshmail">
{if !empty($is_wizard_available)}
    <div data-vue="wizard">
        <wizard
            :translations="{$translations}"
            :links='{$links|@json_encode nofilter }'
            :module="{$priceRules}">
        </wizard>
    </div>
    {* <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> *}
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.12"></script>
    <script type="text/javascript" src="{$links.base_url}modules/freshmail/views/js/freshmail-wizard.js?{$module_version}"></script>
    <script type="text/javascript" src="{$links.base_url}modules/freshmail/views/js/freshmail-core.js?{$module_version}"></script>
{else}
    Wizard nieaktywny
{/if}
</div>