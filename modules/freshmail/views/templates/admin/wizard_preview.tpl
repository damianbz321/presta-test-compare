{assign var=priceRules value="{
        assets_url: '{$links.base_url}modules/freshmail/views'
  }"
}

<div class="freshmail">
    <div id="wizard" class="freshmail__wizard" price-rules="[object Object],[object Object],[object Object]">
        <section class="section-bg--white section-bg--nopadding">
            <div class="col-md-12 header__logo"><img
                    src="{$links.base_url}modules/freshmail/views/img/freshmail-logo.svg" alt="FreshMail"></div>
        </section>
        <div class="form-wrapper">
            <div class="wizard__step wizard__step--1">
                <div class="row">
                    <section class="section-bg--white pt-15">
                        <div class="col-md-12 text-center">
                            <h4>{l s="Boost your conversion rates" mod="freshmail"}</h4>
                        </div>
                    </section>
                    <section class="section-bg--green pb-110">
                        <div class="col-md-12 text-center mb-25 mt-20">
                            <h4 class="text--white">
                                {l s='I have a FreshMail account' mod='freshmail'}
                            </h4>
                        </div>
                        <div class="col-md-4 col-md-offset-4 text-center">
                                <a href="{$links.reset}"
                                    class="btn btn--yellow">
                                    {l s='Clear settings' mod='freshmail'}
                                </a>
                            </div>
                        </div>
                    </section>
                    <section class="section-bg--grey pt-100 pb-100">

                    </section>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript" src="{$links.base_url}modules/freshmail/views/js/freshmail-core.js"></script>
</div>