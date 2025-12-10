
     {if $lab_showskin !=0 }

        <div class="lab-wrap hidden-sm-down">
		<h2 class="lab-title">Theme Options</h2>
		<div class="lab-option">
			<!-- <div class="cl-table">
				<div class="cl-tr cl-tr-mode-label">
					<div class="cl-tr cl-tr-style-label">{l s='Mode Layout'}</div>
				</div>
				<div class="cl-tr cl-tr-mode">
					<div class="pull-left">
						<input class="mode_theme" id="labbox" type="radio" value="box" name="mode_css">{l s='Box'}
					</div>
					<div class="pull-right">
						<input class="mode_theme" id="labwide" type="radio" value="wide" name="mode_css" checked="checked">{l s='Wide'}
					</div>
				</div>
			</div> -->
			
			
			<div class="cl-wrapper">
				<div class="cl-container">
					<div class="cl-tr cl-tr-mode-label">
						<div class="cl-tr cl-tr-style-label">{l s='Theme color'}</div>
					</div>
					<div class="cl-table">
                        <div class="cl-tr cl-tr-style box-layout">
                            <div class="cl-td-l cl-td-layout cl-td-layout1"><a href="javascript:void(0)" id="color1" title="{l s='color1'}"><span class="cl1"></span></a></div>
                            <div class="cl-td-l cl-td-layout cl-td-layout2"><a href="javascript:void(0)" id="color2" title="{l s='color2'}"><span class="cl1"></span></a></div>
                        
                            <div class="cl-td-l cl-td-layout cl-td-layout3"><a href="javascript:void(0)" id="color3" title="{l s='color3'}"><span class="cl1"></span></a></div>
                            <div class="cl-td-l cl-td-layout cl-td-layout4"><a href="javascript:void(0)" id="color4" title="{l s='color4'}"><span class="cl1"></span></a></div>
                        
                            <div class="cl-td-l cl-td-layout cl-td-layout5"><a href="javascript:void(0)" id="color5" title="{l s='color5'}"><span class="cl1"></span></a></div>
                            <div class="cl-td-l cl-td-layout cl-td-layout6"><a href="javascript:void(0)" id="color6" title="{l s='color6'}"><span class="cl1"></span></a></div>
                        
                            <!-- <div class="cl-td-l cl-td-layout cl-td-layout7"><a href="javascript:void(0)" id="color7" title="{l s='color7'}"><span class="cl1"></span></a></div>
                            <div class="cl-td-l cl-td-layout cl-td-layout8"><a href="javascript:void(0)" id="color8" title="{l s='color8'}"><span class="cl1"></span></a></div> -->
                        </div>
                    </div>
					<div class="cl-table">	
                        <div class="cl-tr">
                            <div class="cl-tr cl-tr-style-label">{l s='Background Image body:'}</div>
                            <div class="cl-td-bg">
                                <div class="cl-pattern">
                                    {for $id=1 to 30}
                                        <div class="cl-image pattern{$id}" id="pattern{$id}"></div>
                                    {/for}
                                </div>
                            </div>
                        </div>
                        <div class="cl-tr cl-row-reset">
                            <span class="cl-reset"></span>
                        </div>
					</div>
                </div>
			</div>
		</div>
		<div class="control inactive"><a href="javascript:void(0)"><i class="fa fa-cog fa-spin"></i></a></div>

	</div>
     {/if}
     <script src="{$modules_dir}labthemeoptions/js/jquery.min.js"></script>
     <script src="{$modules_dir}labthemeoptions/js/jquery.cookie.js"></script>

<script type="text/javascript">
    var date = new Date();
    date.setTime(date.getTime() + (1440 * 60 * 1000));
    // set default background image
    {for $id=1 to 30}
    $('#pattern{$id}').click(function(){
        $('.cl-image').removeClass('active');
        $(this).addClass('active');
        $("body").css('background-image','url("{$urls.base_url}modules/labthemeoptions/views/templates/front/colortool/images/pattern/pattern{$id}.png")');

    });
    {/for}
    if ($.cookie('theme_skin_cookie')!= '' && $.cookie('theme_skin_cookie') != null ){
        $('<link rel="stylesheet" type="text/css" href="'+$.cookie('theme_skin_cookie')+'" />').appendTo('head');
    }     //set theme color cookie
    $('.cl-td-layout a').click(function(){
        $('<link rel="stylesheet" type="text/css" href="{$urls.base_url}themes/{$LAB_THEMENAME}/assets/css/color/'+this.id+'.css" />').appendTo('head');
        });
    /*   $(' body > input:checkbox').click(function(){
     if($('#wide').is(":checked")) {
     $('body').addClass("checked");
     } else  if($('#box').is(":checked")) {
     $('body').removeClass("checked");
     }
     });*/
    $(document).ready(function () {
        $('.mode_theme').click(function () {
            $id = $(this).attr('id');
            $('#page').removeAttr('class');
            $('#page').addClass($id);
        });
		
		
		$(function () {
			$(window).scroll(function () {
				if ($(this).scrollTop() > 100) {
					$('.mypresta_scrollup').addClass('open');
				} else {
					$('.mypresta_scrollup').removeClass('open');
				}
			});
			// scroll body to 0px on click
			$('.mypresta_scrollup').click(function () {
				$('body,html').animate({
					scrollTop: 0
				}, 800);
				return false;
			});
		});
		
    });

    $('.cl-reset').click(function(){
        //reset main
        location.reload();

        })
		

</script>