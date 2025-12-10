tinymce.PluginManager.add('bootstrapaccordion', function(editor, url) {
	// Add a button that opens a window
	editor.addButton('bootstrapaccordion', {
		text: ' Accordion',
		icon: 'guicon-alert guicon guicon-alert',
		onclick: accordionDialog,
		stateSelector: 'div.bootstrap-accordion *'
	});
	
	function accordionDialog() {
			var accordionTitle, accordionContent, accordionIn, current, win;
			
			editor.windowManager.open({
				title: 'Accordion',
				url: url + '/dialog.html',
				width: 600,
				height: 400,
				buttons: [
					{
						text: 'Insert',
						onclick: function() {
							
							var timestamp = new Date().getTime();
							
							var accordionTitle = win.getContentWindow().document.getElementById('accordion-title').value;
							var accordionContent = win.getContentWindow().tinyMCE.activeEditor.getContent({format : 'raw'});
							var accordionIn = win.getContentWindow().document.getElementById('accordion-open').value == 'Yes' ? ' in' : '';

							var accordion =
								'<div class="panel panel-default bootstrap-accordion">' +
									'<div class="panel-heading" role="tab" id="heading-' + timestamp + '">' +
										'<h4 class="panel-title">' +
											'<a role="button" class="bootstrap-accordion-title" data-toggle="collapse" data-parent="#accordion" href="#collapse-' + timestamp + '">' +
												accordionTitle +
											'</a>' +
										'</h4>' +
									'</div>' +
									'<div id="collapse-' + timestamp + '" class="panel-collapse collapse' + accordionIn + '" role="tabpanel">' +
										'<div class="panel-body bootstrap-accordion-content">' +
											accordionContent +
										'</div>'
									'</div>'
								'</div>';

							if(current.length > 0) {
								current.remove();
							}

							editor.insertContent(accordion);

							win.close();
						}
					},

					{text: 'Close', onclick: 'close'}
				]
			});
			
			// Top most window object
			win = editor.windowManager.getWindows()[0];
			current = $(editor.selection.getNode()).closest('.bootstrap-accordion');
			
			win.getContentWindow().document.body.onload = function() {
				if(current.length > 0) {
					win.getContentWindow().document.getElementById('accordion-title').value = current.find('.bootstrap-accordion-title').html();
					win.getContentWindow().tinyMCE.activeEditor.setContent(current.find('.bootstrap-accordion-content').html());
					win.getContentWindow().document.getElementById('accordion-open').value = (current.find('.panel-collapse.collapse.in').length > 0 ? 'Yes' : 'No');
				}
			}
		}
	
});
