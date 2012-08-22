<?php
	$advanced = get_request_value('advanced', false);
?>
<form id="search_index" action="<?php bloginfo('url'); ?>/search" method="get">
	<input type="hidden" name="q_tab" value="2" />
	<div id="index-search">
		<div class="title">
			<img src="<?php bloginfo('template_directory'); ?>/images/btn-explorer-transp.png" />
		</div>
		<div id="index-search-content">
			<div class="section">
				<div class="content">
					<div class="slabel search-text"><label for="">Free search text</label></div>
					<input type="text" id="q_freetext"  name="q_freetext" class="freetext" size="40" />
				</div>
				<div class="clear"></div>
			</div>

			<div class="section">
				<div class="content">
				<?php if(!$advanced) { ?>
					<div class="slabel">Topics</div>
					<div style="margin-left: 75px;">
						<input id="index_treaty_biodiversity" type="checkbox" checked="checked" />
						<label for="index_treaty_biodiversity">Biodiversity</label>

						<input id="index_treaty_chemicals" type="checkbox" checked="checked" />
						<label for="index_treaty_chemicals">Chemicals / Waste</label>

						<br />
						<input id="index_treaty_climate" type="checkbox" checked="checked" />
						<label for="index_treaty_climate">Climate / Ozone / Deserts</label>

						<input id="index_treaty_other" type="checkbox" checked="checked" />
						<label for="index_treaty_other">Other (Regional, etc)</label>
					<?php
						$ts = $search2->ui_get_treaties_ids();
						foreach($ts as $t_id) {
					?>
						<input type="checkbox" id="q_treaty_index_<?php echo $t_id; ?>" name="q_treaty[]" value="<?php echo $t_id; ?>" checked="checked" class="hidden" />
					<?php
						}
					?>
					</div>
				<?php } else { ?>
					<div class="slabel">Conventions</div>
					<div class="conventions">
						<?php include(dirname(__FILE__) . '/explorer/inc.treaties.php'); ?>
					</div>
				<?php } ?>
					<div class="clear"></div>
				</div>
			</div>

			<div class="section">
				<div class="content">
					<div class="slabel">Search in</div>
					<input type="checkbox" id="index_q_use_decisions" name="q_use_decisions" checked="checked" value="1" />
					<label for="index_q_use_decisions">Decisions &amp; Resolutions</label>

					<input type="checkbox" id="index_q_use_treaties" name="q_use_treaties" checked="checked" value="1" />
					<label for="index_q_use_treaties">Treaties</label>

					<input type="checkbox" id="index_q_use_meetings"  name="q_use_meetings" value="1" class="checkbox hidden" checked="checked">
					<label for="index_q_use_meetings" class="check-label label click-filters-use-events">Meetings</label>
					<div class="clear"></div>
				</div>
			</div>

			<?php if($advanced) { ?>
			<div class="section">
				<div class="content">
					<div class="slabel">Type keyword</div>
					<select id="q_term_index" name="q_term[]" multiple="multiple" class="hidden">
					<?php
						$terms_page = new Thesaurus(NULL);
						$terms = $terms_page->suggest_vocabulary_terms();
						$sterms = $search2->get_terms();
						foreach($terms as $term) {
								$search2->ui_write_option($term->id, $term->term, in_array(intval($term->id), $sterms));
						}
					?>
					</select>
					<span id="index_and_or_radiobuttons" class="hidden">
						<input type="radio" id="q_term_and_index" name="q_term_or" value="and" checked="checked">
						<label for="q_term_and_index">AND</label>

						<input type="radio" id="q_term_or_index" name="q_term_or" value="or">
						<label for="q_term_or_index">OR</label>
					</span>
					<div id="index-search-terms" class="clear"></div>
					<div class="clear"></div>
				</div>
			</div>
			<div class="section">
				<div class="content">
					<div class="slabel">Date</div>
					<?php include(dirname(__FILE__) . '/index.search.date.php'); ?>
				</div>
			</div>
			<?php } ?>
			<a class="button orange" style="float:right;padding-right:5px;" href="javascript:$('#search_index').submit();">
				<span>Search</span>
			</a>
		</div>
		<div class="search-button">
			<?php if(!$advanced) { ?>
			<a class="advanced-search left" href="<?php bloginfo('url');?>/?advanced=1">
				<span>Advanced search &raquo;</span>
			</a>
			<?php } else { ?>
			<a class="advanced-search left" href="<?php bloginfo('url');?>/">
				<span>Simple search &raquo;</span>
			</a>
			<?php } ?>
			<div class="clear"></div>
		</div>
	</div>
</form>
<div style="margin-top: 15px;"></div>
<?php function inject_index_explorer() { ?>
<script type="text/javascript">
	$(document).ready(function() {
		$( "#q_term_index" ).reusableComboBox({
			select : function(evt, ob) {
				var item = ob.item;
				var span_class = 'term-content span-term-' + item.value;
				if(!$('.span-term-'+item.value).length) {
					var label = item.text;
					if(label.length > 20) {
						label = label.substring(0, 17) + '...';
					}
					if($('#index-search-terms').text().length > 0) {
						$('#index_and_or_radiobuttons').show();
					}
					$('<div title="' + item.text + '">' + label + '</div>').attr({'class' : span_class})
						.append( "<a href='javascript:explorerIndexUIDeselectTerm(" + item.value + ");'><img class='closebutton' src='" + images_dir + "/s.gif' alt='' title='' /></a>" )
						.appendTo($('#index-search-terms'));
				}
				return false;
			}
		});

		$('#use_biodiversity_label').click(function() {
			var treaties = new Array(1 /* CBD */, 8 /* Cartagena */, 9 /* Nagoya */, 3 /* CITES */, 4 /* CMS */,
									10 /* AEWA */, 14/* ITPGRFA */, 18 /* Ramsar */, 16 /* WHC */);
			var check = $('#use_biodiversity').is(':checked');
			$.each(treaties, function(index, item) {
				$('#q_treaty_index_' + item).attr('checked', check);
			});
		});
		$('#use_chemicals_label').click(function() {
			var treaties = new Array(2 /* Basel */, 20 /* Rotterdam */, 5 /* Stockholm */);
			var check = $('#use_chemicals').is(':checked');
			$.each(treaties, function(index, item) {
				$('#q_treaty_index_' + item).attr('checked', check);
			});
		});
		$('#use_climate_label').click(function() {
			var treaties = new Array(15 /* UNFCCC */, 17 /* Kyoto Protocol */, 19 /* UNCCD */, 6 /* Vienna */, 7 /* Montreal */);
			var check = $('#use_climate').is(':checked');
			$.each(treaties, function(index, item) {
				$('#q_treaty_index_' + item).attr('checked', check);
			});
		});
	});

	function explorerIndexUIDeselectTerm(id){
		$('.span-term-'+id).remove();
		$('#q_term_index option:selected').each(function(idx, el) {
			if($(el).attr('value') == id) {
				$(el).removeAttr('selected');
			}
		});
	}
</script>
<?php
	}
	add_filter('js_inject', 'inject_index_explorer');
?>
