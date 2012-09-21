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
<ul class="list-dropdown treaty_contacts">
<?php
	foreach($countries_contacts as $country) {
		$contacts = (isset($all_contacts[$country->id_country])) ? $all_contacts[$country->id_country] : array();
?>
	<li id="treaty-<?php echo $country->id_country; ?>">
		<a name="contact-bookmark-<?php echo $country->id_country; ?>"></a>
		<a href="javascript:void(0);" class="left closed list-item-title-click">
			<div class="list-item-title">
				<img class="left" title="<?php echo $country->country_name; ?>"
					alt="<?php _e('Country flag', 'informea'); ?>"
					src="<?php bloginfo('url'); ?>/<?php echo $country->country_flag_medium; ?>" />
				<div class="left"><?php echo $country->country_name; ?></div>
			</div>
		</a>
		<div class="list-item-content hidden">
		<?php foreach($contacts as $contact) { ?>
			<div class="padding-5px">
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
			</div>
		<?php } ?>
		</div>
		<div class="clear"></div>
	</li>
	<?php } ?>
</ul>
<?php } else {
	_e('No focal points recorded for this treaty ... yet.', 'informea');
} ?>
