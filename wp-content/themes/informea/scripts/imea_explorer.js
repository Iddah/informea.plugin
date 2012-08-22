/**
 * Global variables - images_dir - URL path to the theme's images
 */

/**
 * Resets all the explorer fields to their original values. This happens
 * due to Firefox which keeps data some form fields (terms <select>)
 * after page refresh, so form is in inconsistent state.
 */
function reset_explorer_form() {
	$("#q_term_explorer").each(function(){
		$("#q_term_explorer option").removeAttr("selected");
	});

}

/**
 * Show one category and hide the others
 */
function show_category(section_name) {
	var sections = ['all-search', 'treaties-search', 'events-search'];
	$.each(sections, function(index, current_section) {
		var item_content = $('.' + current_section + '-content');
		var icon = $('.' + current_section + ' .search-section-title .search-explorer-toggle-icon');
		if(section_name == current_section) {
			if(!item_content.is(':visible')) {
				item_content.slideDown();
				icon.attr('src', images_dir + 'search-box/minus.png');
			} else {
				item_content.slideUp();
				icon.attr('src', images_dir + 'search-box/plus.png');
			}
		} else {
			item_content.slideUp();
			icon.attr('src', images_dir + 'search-box/plus.png');
		}
	});
}

// Expand collapse categories in MEA Explorer
$(document).ready(function() {
	$('.search-section-title').click(function(e) {
		e.preventDefault();
		var parent = $(this).parent().attr('class').split(' ')[1];
		if(parent == 'documents-search') { // TODO Official documents not implemented yet

			return false;
		}
		show_category(parent);
		return false;
	});

	// MEA Explorer search forms handling
	$('#explorer_q_freetext').click(function() {
		if($(this).attr('value') == freetext_default) {
			$(this).attr('value', '');
		}
	});
	$('#explorer_q_freetext').focusout(function() {
		if($(this).attr('value') == '') {
			$(this).attr('value', freetext_default);
		}
	});
	$( "#q_term_explorer" ).combobox();
	//$( "#q_term_explorer" ).toggle();
	reset_explorer_form();

	setupExplorerTreaties();
});


function setupExplorerTreaties() {
	$('.explorer_all_treaties').click(function(e) {
		if(!$(e.target).is(':checked')) {
			$('.explorer_all_treaties_items').find('[type=checkbox]').removeAttr('checked');
		} else {
			$('.explorer_all_treaties_items').find('[type=checkbox]').attr('checked', 'checked');
		}
	});

	$('.explorer_all_treaties_items .explorer-treaty-click-children').click(function(e) {
		if($(e.target).is(':checked')) {
			$(e.target).siblings('ul.sublist').find('[type=checkbox]').attr('checked', 'checked');
		} else {
			$(e.target).siblings('ul.sublist').find('[type=checkbox]').removeAttr('checked');
		}
	});

	// Index page
	$('#index_treaty_biodiversity').click(function() {
		$(arr_treaties_biodiversity).each(function(idx, id) {
			if($('#index_treaty_biodiversity').is(':checked')) { $('#q_treaty_index_' + id).attr('checked', 'checked'); } else { $('#q_treaty_index_' + id).removeAttr('checked'); }
		});
	});
	$('#index_treaty_chemicals').click(function() {
		$(arr_treaties_chemicals).each(function(idx, id) {
			if($('#index_treaty_chemicals').is(':checked')) { $('#q_treaty_index_' + id).attr('checked', 'checked'); } else { $('#q_treaty_index_' + id).removeAttr('checked'); }
		});
	});
	$('#index_treaty_climate').click(function() {
		$(arr_treaties_climate).each(function(idx, id) {
			if($('#index_treaty_climate').is(':checked')) { $('#q_treaty_index_' + id).attr('checked', 'checked'); } else { $('#q_treaty_index_' + id).removeAttr('checked'); }
		});
	});
	$('#index_treaty_other').click(function() {
		$(arr_treaties_other).each(function(idx, id) {
			if($('#index_treaty_other').is(':checked')) { $('#q_treaty_index_' + id).attr('checked', 'checked'); } else { $('#q_treaty_index_' + id).removeAttr('checked'); }
		});
	});
}


function explorerUISelectTerm(id, name) {
	var span_class = 'term-content span-term-' + id;
	if(!$('.span-term-'+id).length) {
		if($('.selected-terms-holder').text().length > 0) {
			$('#explorer_and_or_radiobuttons').show();
		}
		$('<div>' + name + '</div>').attr({'class' : span_class})
			.append( "<a href='javascript:explorerUIDeselectTerm(" + id + ");'><img class='closebutton' src='" + images_dir + "/s.gif' alt='' title='' /></a>" )
			.appendTo($('.selected-terms-holder'));
	}
}

function explorerUIDeselectTerm(id){
	$('.span-term-'+id).remove();
	var selected = $( '#q_term_explorer option:selected' );
	for(var i = 0; i < selected.length; i++) {
		var opt = selected[i];
		if(opt.value == id) {
			opt.selected = false;
			break;
		}
	}
}


(function( $ ) {
	$.widget( "ui.combobox", {
		_create: function(customClass) {
			var self = this,
				select = this.element.hide(),
				selected = select.children( ":selected" ),
				value = selected.val() ? selected.text() : "";
			// var input = this.input = $( "<input>" ).insertAfter( select ).val( value )
			var input = this.input = $( "<input>" ).insertAfter( select )
				.autocomplete({
					delay: 0,
					minLength: 0,
					source: function( request, response ) {
						var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
						response( select.children( "option" ).map(function() {
							var text = $( this ).text();
							if ( this.value && ( !request.term || matcher.test(text) ) )
								return {
									label: text.replace(
										new RegExp(
											"(?![^&;]+;)(?!<[^<>]*)(" +
											$.ui.autocomplete.escapeRegex(request.term) +
											")(?![^<>]*>)(?![^&;]+;)", "gi"
										), "<strong>$1</strong>" ),
									value: text,
									option: this
								};
						}) );
					},
					select: function( event, ui ) {
						ui.item.option.selected = true;
						self._trigger( "selected", event, {
							item: ui.item.option
						});
						explorerUISelectTerm(ui.item.option.value, ui.item.option.text);
						ui.item.value = '';
					},
					change: function( event, ui ) {
						if ( !ui.item ) {
							var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" );
							var valid = false;
							select.children( "option" ).each(function() {
								if ( $( this ).text().match( matcher ) ) {
									this.selected = valid = true;
									return false;
								}
							});
							if ( !valid ) {
								// remove invalid value, as it didn't match anything
								$( this ).val( "" );
								select.val( "" );
								input.data( "autocomplete" ).term = "";
								return false;
							}
						}
					}
				})
				.addClass( "ui-widget ui-widget-content ui-corner-left " + (typeof(customClass) != 'undefined' ? customClass : '') );

			input.data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( "<a>" + item.label + "</a>" )
					.appendTo( ul );
			};

			this.button = $( "<button type='button'>&nbsp;</button>" )
				.attr( "tabIndex", -1 )
				.attr( "title", "Show All Terms" )
				.insertAfter( input )
				.button({
					icons: {
						primary: "ui-icon-triangle-1-s"
					},
					text: false
				})
				.removeClass( "ui-corner-all" )
				.addClass( "ui-corner-right ui-button-icon" )
				.click(function() {
					// close if already visible
					if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
						input.autocomplete( "close" );
						return;
					}
					// work around a bug (likely same cause as #5265)
					$( this ).blur();
					// pass empty string as value to search for, displaying all results
					input.autocomplete( "search", "" );
					input.focus();
				});
		},

		destroy: function() {
			this.input.remove();
			this.button.remove();
			this.element.show();
			$.Widget.prototype.destroy.call( this );
		}
	});
})( jQuery );

// Reusable combobox
(function( $ ) {
	$.widget( "ui.reusableComboBox", {
		_create: function(customClass) {
			var self = this,
				select = this.element.hide(),
				selected = select.children( ":selected" ),
				value = selected.val() ? selected.text() : "";
				// var input = this.input = $( "<input>" ).insertAfter( select ).val( value )
				var input = this.input = $( "<input>" ).insertAfter( select )
				.autocomplete({
					delay: 0,
					minLength: 0,
					source: function( request, response ) {
						var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
						response( select.children( "option" ).map(function() {
							var text = $( this ).text();
							if ( this.value && ( !request.term || matcher.test(text) ) )
								return {
									label: text.replace(
										new RegExp(
											"(?![^&;]+;)(?!<[^<>]*)(" +
											$.ui.autocomplete.escapeRegex(request.term) +
											")(?![^<>]*>)(?![^&;]+;)", "gi"
										), "<strong>$1</strong>" ),
									value: text,
									option: this
								};
						}) );
					},
					select: function( event, ui ) {
						ui.item.option.selected = true;
						self._trigger( "select", event, {
							item: ui.item.option
						});
						ui.item.value = '';
					},
					change: function( event, ui ) {
						if ( !ui.item ) {
							var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" );
							var valid = false;
							select.children( "option" ).each(function() {
								if ( $( this ).text().match( matcher ) ) {
									this.selected = valid = true;
									return false;
								}
							});
							if ( !valid ) {
								// remove invalid value, as it didn't match anything
								$( this ).val( "" );
								select.val( "" );
								input.data( "autocomplete" ).term = "";
								return false;
							}
						}
					}
				})
				.addClass( "ui-widget ui-widget-content ui-corner-left " + (typeof(customClass) != 'undefined' ? customClass : '') );

			input.data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( "<a>" + item.label + "</a>" )
					.appendTo( ul );
			};

			this.button = $( "<button type='button'>&nbsp;</button>" )
				.attr( "tabIndex", -1 )
				.attr( "title", "Show All Terms" )
				.insertAfter( input )
				.button({
					icons: {
						primary: "ui-icon-triangle-1-s"
					},
					text: false
				})
				.removeClass( "ui-corner-all" )
				.addClass( "ui-corner-right ui-button-icon" )
				.click(function() {
					// close if already visible
					if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
						input.autocomplete( "close" );
						return;
					}
					// work around a bug (likely same cause as #5265)
					$( this ).blur();
					// pass empty string as value to search for, displaying all results
					input.autocomplete( "search", "" );
					input.focus();
				});
		},

		destroy: function() {
			this.input.remove();
			this.button.remove();
			this.element.show();
			$.Widget.prototype.destroy.call( this );
		}
	});
})( jQuery );
