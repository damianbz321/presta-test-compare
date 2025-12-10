<style>
    .laberUserInfo .laber-user-info .signin{
        background: #ABCC35!important;
    }
</style>
<div id="_desktop_user_info" class="pull-right">
	<div class="laberUserInfo dropdown js-dropdown ">
		<div class="expand-more" data-toggle="dropdown">
			<div class="laberUser">
				<p class="nameuser">
					<i class="icon_user icon-users"></i>
					{if $logged} 
						<span>Witaj!</span> 
						<a class="account"
							href="{$my_account_url}"
							title="{$customerName}"
							rel="nofollow">
							{$customerName}
						</a>
					{else}
					<span>Zaloguj się / Zarejestruj się</span>
					{/if}
				</p>
			</div>
		</div>
		<div class="laber-user-info dropdown-menu">
		  <div class="user-info">
			
			{if $logged}
			<div class="signin">
			  <a class="logout"
				href="{$logout_url}"
				rel="nofollow">
				Wyloguj się
			  </a>
			</div>
			<a class="laberMyAccount" href="{$urls.pages.my_account}">Moje konto</a>
			{else}
				<div class="signin">
				<a href="{$my_account_url}"
				title="Zaloguj się na swoje konto"
				rel="nofollow">
					Zaloguj się
				</a>
				<span>Nowy klient! Zacznij tutaj.</span>
				</div>
				<a class="register" href="{$urls.pages.register}">
					Zarejestruj się
				</a>
			{/if}
		  </div>
		</div>
	</div>
</div>