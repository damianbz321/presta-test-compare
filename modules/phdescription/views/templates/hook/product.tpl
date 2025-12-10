<div class="phdescription row">
    {foreach $list as $l}
        <div class="row row-{$l.type}">
            {if $l.type == 1}
                <div class="col-lg-12">
                    <div class="image-box">
                        <img src="{$l.lang.image}" alt="" />
                    </div>
                </div>
            {elseif $l.type == 2}
                <div class="col-lg-12">
                    <div class="description">
                        {$l.lang.text nofilter}
                    </div>
                </div>
            {elseif $l.type == 3}
                <div class="col-lg-6">
                    <div class="image-box">
                        <img src="{$l.lang.image}" alt="" />
                    </div>
                </div>
                <div class="col-lg-6 row-desc">
                    <div class="description">
                        {$l.lang.text nofilter}
                    </div>
                </div>
            {elseif $l.type == 4}
                <div class="col-lg-6 row-desc">
                    <div class="description">
                        {$l.lang.text nofilter}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="image-box">
                        <img src="{$l.lang.image}" alt="" />
                    </div>
                </div>
            {elseif $l.type == 5}
                <div class="col-lg-6 row-image-1">
                    <div class="image-box">
                        <img src="{$l.lang.image}" alt="" />
                    </div>
                </div>
                <div class="col-lg-6 row-image-2">
                    <div class="image-box">
                        <img src="{$l.lang.image2}" alt="" />
                    </div>
                </div>
            {elseif $l.type == 6}
                <div class="col-lg-6">
                    <div class="description">
                        {$l.lang.text nofilter}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="description">
                        {$l.lang.text2 nofilter}
                    </div>
                </div>
            {/if}
        </div>
    {/foreach}
</div>
