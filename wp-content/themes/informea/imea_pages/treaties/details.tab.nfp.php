<?php
function js_inject_treaty_nfp() {
	global $id_treaty;
?>
<script type="text/javascript">
$(document).ready(function() {
	if(window.location.hash) {
		bookmark = window.location.hash.substr(18,window.location.hash.length-18);
		parent_div_id = "#treaty-"+bookmark;
		$(parent_div_id+" .list-item-content-details").show();
		$(parent_div_id+" .toggle-treaty img").attr("src","<?php bloginfo('template_directory'); ?>/images/collapse.gif");
	}
});
</script>
<?php
}
add_action('js_inject', 'js_inject_treaty_nfp');
?>
<?php if(count($countries_contacts)) { ?>
<ul id="nfp-countries" class="list-dropdown hidden list-view" style="display: block;">
<?php
	foreach($countries_contacts as $country) {
		$contacts = (isset($all_contacts[$country->id_country])) ? $all_contacts[$country->id_country] : array();
?>
	<li id="treaty-<?php echo $country->id_country; ?>" class="collapsible  even">
		<a name="contact-bookmark-<?php echo $country->id_country; ?>"></a>
		<div class="left list-item-content">
			<div class="list-item-top-details">
				<div class="left list-item-action">
					<a id="toggle-treaty-<?php echo $country->id_country; ?>" class="toggle-treaty" href="javascript:void(0); ">
						<img src="<?php bloginfo('template_directory'); ?>/images/expand.gif" alt="Compress" title="<?php _e('Compress content', 'informea'); ?>" />
					</a>
				</div>

				<div class="left list-item-logo-nfp">
					<a href="<?php echo bloginfo('url'); ?>/countries/<?php echo $country->id_country; ?>">
						<img title="<?php echo $country->country_name; ?>" alt="<?php echo $country->country_name; ?>" src="<?php bloginfo('url'); ?>/<?php echo $country->country_flag; ?>"-->
					</a>
				</div>

				<div class="left list-item-title" style="width:auto;">
					<a id="toggle-nfp-<?php echo $country->id_country; ?>" class="location-link toggle-treaty" href="javascript:void(0);" title="See focal points for <?php echo $country->country_name; ?>">
						<?php echo $country->country_name; ?>
					</a>
					<a class="location-link" href="<?php echo bloginfo('url'); ?>/countries/<?php echo $country->id_country; ?>" title="Go to <?php echo $country->country_name; ?> country profile">
						<img src="<?php bloginfo('template_directory'); ?>/images/external.png" />
					</a>
				</div>
				<div class="clear"></div>
			</div>

			<div class="list-item-content-details hidden country-nfp">
				<ul>
				<?php foreach($contacts as $contact) { ?>
					<li class="nfp-details">
						<div class="contact-name">
							<?php echo $contact->prefix; ?> <?php echo $contact->first_name; ?> <?php echo $contact->last_name; ?>
						</div>
					<?php
						if($contact->position !== NULL) {
							echo '<em>' . $contact->position . '</em><br />';
						}

						if($contact->department !== NULL) {
							echo '<em>' . $contact->department . '</em><br />';
						}
						if($contact->institution !== NULL) {
							echo '<em>' . $contact->institution . '</em><br />';
						}
					?>
						<?php if($contact->address !== NULL) { ?>
						<div class="nfp-address">
							<strong><?php _e('Address:', 'informea'); ?></strong> <?php echo replace_enter_br($contact->address); ?>
						</div>
						<?php } ?>

						<?php if($contact->telephone !== NULL) { ?>
						<div class="nfp-phone">
							<strong><?php _e('Phone:', 'informea'); ?></strong> <?php echo $contact->telephone; ?>
						</div>
						<?php } ?>

						<?php if($contact->fax !== NULL) { ?>
						<div class="nfp-fax">
							<strong><?php _e('Fax:', 'informea'); ?></strong> <?php echo $contact->fax; ?>
						</div>
						<?php } ?>
						<?php if (!empty($contact->email)){ ?>
							<a class="button blue" href="<?php bloginfo('url'); ?>/treaties/<?php echo $treaty->id; ?>/sendmail/<?php echo $contact->id?>/<?php echo $country->id_country?>" class="tooltip cnt-contact-link" title="<?php _e('Contact this person', 'informea'); ?>">
								<span><?php _e('Contact via e-mail', 'informea'); ?></span>
							</a>
						<?php } ?>
						<a class="button blue" href="<?php echo bloginfo('url'); ?>/vcard?id_contact=<?php echo $contact->id?>" class="tooltip cnt-contact-link" title="<?php _e('Download vcard', 'informea'); ?>">
							<span><?php _e('Download vcard', 'informea'); ?></span>
						</a>
					</li>
				<?php } ?>
			</ul>
		</div>
		<div class="clear"></div>
	</li>
	<?php } ?>
</ul>
<?php } else {
	_e('No focal points recorded for this treaty ... yet.', 'informea');
} ?>
