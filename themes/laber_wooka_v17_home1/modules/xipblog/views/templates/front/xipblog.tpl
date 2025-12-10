{if $page.page_name == 'index'}
<div class="laberthemes clearfix padding-0-15">
<div class="home_blog_post_area {$xipbdp_designlayout} {$hookName}">
	<div class="home_blog_post">
		<div class="labertitle">
			{if isset($xipbdp_title)}
				<h2 class="page-heading">
                                    <a href="https://blog.hifood.pl/">{$xipbdp_title}</a>
				</h2>
			{/if}
			{if isset($xipbdp_subtext)}
				<p class="page_subtitle">{$xipbdp_subtext}</p>
			{/if}
			
		</div>
		<div class="laberblog-i">
		<div class="row">
		{if (isset($wp_posts) && !empty($wp_posts))}
		<div class="home_blog_post_inner">
		
			{foreach from=$wp_posts item=xipblgpst name=xipblg}
				<div class="item-inner">
					<div class="item">
						<article class="blog_post">
							<div class="blog_post_content">
									<div class="blog_post_content_top">
										
										<div class="post_thumbnail laberMedia-body">
											<a class="thumbnail" href="{$xipblgpst.url}">
												<img class="xipblog_img img-responsive" src="{$xipblgpst.img}" alt="{$xipblgpst.title}">
											</a>
											<div class="blog_mask">
												<div class="blog_mask_content">
													<a class="thumbnail_lightbox" href="{$xipblgpst.img}">
														<i class="icon_plus"></i>
													</a>
												</div>
											</div>
										</div>
										
									</div>
							
								
									<div class="blog_post_content_bottom">
											<div class="post_meta clearfix">
												<p class="meta_author"> 
													{l s='by' d='Shop.Theme.Laberthemes'}
													<span>{$xipblgpst.author}</span>
												</p> &nbsp;/&nbsp;
												<p class="meta_date">
													<span>{$xipblgpst.date}</span>
												</p>
											</div>
											<h3 class="post_title"><a href="{$xipblgpst.url}">{$xipblgpst.title}</a></h3>
											<div class="post_content">
											{if isset($xipblgpst.excerpt) && !empty($xipblgpst.excerpt)}
												<p>{$xipblgpst.excerpt|truncate:150:'...'|strip_tags}</p>
											{else}
												<p>{$xipblgpst.content|truncate:150:'...'|escape:'html':'UTF-8'}</p>
											{/if}
											</div>
										
									</div>
							
							</div>
						</article>
					</div>
				</div>
			{/foreach}
		</div>
		<div class="owl-buttons">
			<div class="owl-prev prevBlog_post_inner"><i class="icon-chevron-left icon"></i></div>
			<div class="owl-next nextBlog_post_inner"><i class="icon-chevron-right icon"></i></div>
		</div>
		{else}
			<p>{l s='No Blog Post Found' d='Shop.Theme.Laberthemes'}</p>
		{/if}
		</div>
		
		</div>
	</div>
</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		var owl = $(".home_blog_post_inner");
		owl.owlCarousel({
			items : 3,
			itemsDesktop : [1199,2],
			itemsDesktopSmall : [991,2],
			itemsTablet: [767,2],
			itemsMobile : [480,1],
			rewindNav : false,
			autoPlay :  false,
			stopOnHover: false,
			pagination : false,
		});
		$(".nextBlog_post_inner").click(function(){
		owl.trigger('owl.next');
		})
		$(".prevBlog_post_inner").click(function(){
		owl.trigger('owl.prev');
		})
	});
</script>
{/if}