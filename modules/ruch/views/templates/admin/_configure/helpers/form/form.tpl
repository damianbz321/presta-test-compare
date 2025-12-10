{extends file="helpers/form/form.tpl"}

{block name="input"}
	{if $input.name == 'RUCH_API_ACTIONS'}
	{if $input.dev == 1}
    	<div id="ruch_api_actions" class="form-horizontal">
			<div id="ruch_msg_container"></div>
        	<table cellspacing="0" cellpadding="10" class="table">
            	<tbody>
                	<tr>
						<td>Test połączenia</td>
						<td><button class="btn btn-default pull-right" id="ruch_test1" type="button">Z cache WSDL</button></td>
						<td><button class="btn btn-default pull-right" id="ruch_test2" type="button">Bez cache WSDL</button></td>
						<td><button class="btn btn-default pull-right" id="ruch_test3" type="button">Curl</button></td>
                    </tr>
                </tbody>
            </table>
	    </div>
	    <script type="text/javascript">

	    var ruch_ajax_uri = '{$input.ajax_uri|escape:'htmlall':'UTF-8'}';
	    var ruch_token = '{$input.token|escape:'htmlall':'UTF-8'}';

	    $(document).ready(function(){
			$('#ruch_test1').live('click', function() {
				$('#ajax_running').slideDown();
				$('#ruch_msg_container').slideUp().html('');
		        $.ajax({
		            type: "POST",
		            async: true,
		            url: ruch_ajax_uri,
		            dataType: "json",
		            global: false,
		            contentType: "application/json; charset=utf-8",
		            data: JSON.stringify({
		            	'action': 'test1',
		            	'token': ruch_token,
		            	'url': $('#RUCH_API_URL').val(),
		            	'user': $('#RUCH_API_ID').val(),
		            	'pass': $('#RUCH_API_PASS').val()
		            }),
		            success: function(resp)
		            {
						if (resp.error)
						{
							$('#ruch_get_org').hide();
							$('#ruch_msg_container').hide().html('<p class="error alert alert-danger">'+resp.error+'</p>').slideDown();
						}
						else
						{
							$('#ruch_get_org').show();
							$('#ruch_msg_container').hide().html('<p class="alert alert-success">'+resp.ok+'</p>').slideDown();
						}
						$.scrollTo('#ruch_api_actions', 400, { offset: { top: -100 }});

		                $('#ajax_running').slideUp();
		            },
		            error: function(jqXHR, textStatus, errorThrown)
		            {
		            	if(jqXHR.status == 0) alert("{l s='Nieprawidłowa domena' mod='ruch'}");
		                $('#ajax_running').slideUp();
		            }
		        });
			});

			$('#ruch_test2').live('click', function() {
				$('#ajax_running').slideDown();
				$('#ruch_msg_container').slideUp().html('');
		        $.ajax({
		            type: "POST",
		            async: true,
		            url: ruch_ajax_uri,
		            dataType: "json",
		            global: false,
		            contentType: "application/json; charset=utf-8",
		            data: JSON.stringify({
		            	'action': 'test2',
		            	'token': ruch_token,
		            	'url': $('#RUCH_API_URL').val(),
		            	'user': $('#RUCH_API_ID').val(),
		            	'pass': $('#RUCH_API_PASS').val()
		            }),
		            success: function(resp)
		            {
						if (resp.error)
						{
							$('#ruch_get_org').hide();
							$('#ruch_msg_container').hide().html('<p class="error alert alert-danger">'+resp.error+'</p>').slideDown();
						}
						else
						{
							$('#ruch_get_org').show();
							$('#ruch_msg_container').hide().html('<p class="alert alert-success">'+resp.ok+'</p>').slideDown();
						}
						$.scrollTo('#ruch_api_actions', 400, { offset: { top: -100 }});

		                $('#ajax_running').slideUp();
		            },
		            error: function(jqXHR, textStatus, errorThrown)
		            {
		            	if(jqXHR.status == 0) alert("{l s='Nieprawidłowa domena' mod='ruch'}");
		                $('#ajax_running').slideUp();
		            }
		        });
			});

			$('#ruch_test3').live('click', function() {
				$('#ajax_running').slideDown();
				$('#ruch_msg_container').slideUp().html('');
		        $.ajax({
		            type: "POST",
		            async: true,
		            url: ruch_ajax_uri,
		            dataType: "json",
		            global: false,
		            contentType: "application/json; charset=utf-8",
		            data: JSON.stringify({
		            	'action': 'test3',
		            	'token': ruch_token,
		            	'url': $('#RUCH_API_URL').val(),
		            	'user': $('#RUCH_API_ID').val(),
		            	'pass': $('#RUCH_API_PASS').val()
		            }),
		            success: function(resp)
		            {
						if (resp.error)
						{
							$('#ruch_get_org').hide();
							$('#ruch_msg_container').hide().html('<p class="error alert alert-danger">'+resp.error+'</p>').slideDown();
						}
						else
						{
							$('#ruch_get_org').show();
							$('#ruch_msg_container').hide().html('<p class="alert alert-success">'+resp.ok+'</p>').slideDown();
						}
						$.scrollTo('#ruch_api_actions', 400, { offset: { top: -100 }});

		                $('#ajax_running').slideUp();
		            },
		            error: function(jqXHR, textStatus, errorThrown)
		            {
		            	if(jqXHR.status == 0) alert("{l s='Nieprawidłowa domena' mod='ruch'}");
		                $('#ajax_running').slideUp();
		            }
		        });
			});
	    });
	    
		</script>
	{/if}
	{else}
		{$smarty.block.parent}
    {/if}

{/block}
