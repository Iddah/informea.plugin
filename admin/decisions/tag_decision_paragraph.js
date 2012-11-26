var dialog = null;
var current_paragraph = null;
var clipboard = '';
$().ready(function() {
	$('.tooltip, .location-link').tipsy({gravity: 's'});

	dialog = $('#tdialog')
		.dialog(
		{
			title : 'Decision paragraph breakdown &amp; tagging',
			modal: true, autoOpen : false,
			minWidth : 600,
			minHeight : 400,
			buttons: [
				{
					text: "Save this paragraph",
					click: function() {
						var tags = [];
						$('#keywords').find('option').each(function() {
							if($(this).attr('selected') == 'selected') {
								tags.push(this.value);
							}
						});
						var data = {
							id_decision: id_decision,
							paragraph: $('#tdialog_para').val(),
							tags: tags
						}
						// Ajax call to save the tagged paragraph
						$.ajax({
							url: edit_decision_ajaxurl,
							dataType: 'json',
							data: data,
							type: 'POST',
							success: function(data) {
								// alert(data.message);
								window.location.reload();
							},
							error: function(data) {
								alert('An error has occurred, please request support with this message : ' + data.responseText);
							}
						});
						clipboard = '';
						$(this).dialog('close');
					}
				},
				{
					text: "Cancel",
					click: function() { clipboard = ''; $(this).dialog('close'); }
				}
			]
		});
	// dialog.dialog('open');

	var synMenu = new dhtmlXMenuObject();
	synMenu.setIconsPath(imagePath); // Global variable
	synMenu.renderAsContextMenu();
	synMenu.addNewChild(synMenu.topId, 0, 'r_add_tag', 'Tag this paragraph', false, 'add.png');
	synMenu.addNewChild(synMenu.topId, 1, 'r_remove_tag', 'Remove tags', false, 'delete.png');

	$('#decision_content').bind('contextmenu', function(e) {
		if($.browser.msie) { e = $.event.fix(e); }
		clipboard = getBrowserSelectedText();
		if(clipboard != '') {
			synMenu.setItemEnabled('r_add_tag');
			synMenu.setItemDisabled('r_remove_tag');
			synMenu.showContextMenu(e.pageX, e.pageY);
		} else {
			if($(e.target).hasClass('tagged-paragraph')) {
				current_paragraph = $(e.target).attr('id');
				synMenu.setItemDisabled('r_add_tag');
				synMenu.setItemEnabled('r_remove_tag');
				synMenu.showContextMenu(e.pageX, e.pageY);
			}
		}
		return false;
	});

	synMenu.attachEvent('onClick', function (id, zoneId, casState) {
		if('r_add_tag' == id) {
			openTaggingDialog();
		}
		if('r_remove_tag' == id) {
			// Ajax call to save the tagged paragraph
			$.ajax({
				url: delete_decision_paragraph_ajaxurl,
				dataType: 'json',
				data: {
					id_decision: id_decision,
					id_paragraph: current_paragraph,
				},
				type: 'POST',
				success: function(data) {
					// console.log(data);
					window.location.reload();
				},
				error: function(data) {
					alert('An error has occurred, please request support with this message : ' + data.responseText);
				}
			});
		}
	});
});


function openTaggingDialog() {
	if(clipboard == 0) {
		alert('There\'s not selected text. Please select the paragraph first');
		return;
	}
	$('#tdialog_para').val(clipboard);
	if(clipboard.length > 300) {
		clipboard = clipboard.substring(0, 300) + '...';
	}
	$('#tdialog_pc').text(clipboard);
	$('#keywords').find('option').each(function() {
		$(this).removeAttr('selected');
	});

	dialog.dialog('open');
}

function getBrowserSelectedText() {
	var txt = '';
	if (window.getSelection) {
		txt = window.getSelection().toString();
	} else if (document.getSelection) {
		txt = document.getSelection();
	} else if (document.selection) {
		txt = document.selection.createRange().text;
	} else return;
	return txt;
}
