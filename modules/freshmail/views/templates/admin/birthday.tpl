<div class="panel">
    <div class="content-div ">
       <div class="row ">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <h3 class="card-header">
                                <i class="material-icons">info_outline</i>{l s='Information' mod="freshmail"}
                            </h3>
                            <div class="card-block">
                                <div class="card-text">
                                    <p>{l s='In email template You can use tags:' mod="freshmail"}</p>
                                    {literal}
                                    <ul>
                                        <li>{firstname}</li>
                                        <li>{lastname}</li>
                                        <li>{content}</li>
                                        <li>{shop_url}</li>
                                    </ul>
                                    {/literal}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-wrapper">
    <div class="alert alert-info"><button data-dismiss="alert" type="button" class="close">
            ×
        </button>
        {l s='Please your clients and show them how important they are to you. Build a relationship - make them a wish and offer a discount if you want.' mod='freshmail'}
    </div>
</div>