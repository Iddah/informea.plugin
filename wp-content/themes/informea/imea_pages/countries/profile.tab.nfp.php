<?php
$showall = get_request_boolean('showall', false);
$id_contact = get_request_int('id_contact', null);
function js_inject_country_details_nfp() {
?>
<script type="text/javascript">
$(document).ready(function() {
	if(window.location.hash) {
		var bookmark = window.location.hash.substr(18, window.location.hash.length - 18);
		var parent_div_id = "#treaty-" + bookmark;
		$(parent_div_id + " .list-item-content").show();
	}
});
</script>
<?php
}
add_action('js_inject', 'js_inject_country_details_nfp');
echo "<strong>Disclaimer: </strong>Please note that the focal points of the remaining conventions will be accessible before February 2012";
if(count($treaties_contacts)) {
?>
<ul class="list-dropdown">
<?php
	$showall_div = $showall ? 'visible' : 'hidden';
	foreach($treaties_contacts as $treaty) {
		$contacts = (isset($all_contacts[$treaty->id_treaty])) ? $all_contacts[$treaty->id_treaty] : array();
?>
	<li class="collapsible even list-item" id="treaty-<?php echo $treaty->id_treaty; ?>">
		<a name="contact-bookmark-<?php echo $treaty->id_treaty; ?>"></a>
		<a href="javascript:void(0);" class="left closed list-item-title-click" style="height: 50px;">
			<div class="list-item-title">
				<img class="left mea-logo" title="<?php echo $treaty->short_title; ?>" alt="<?php _e('Convention logo', 'informea'); ?>" src="<?php echo $treaty->logo_medium; ?>">
				<div class="left mea-name"><?php echo $treaty->short_title; ?></div>
			</div>
		</a>
		<div class="<?php echo $showall_div; ?> list-item-content country-nfp-item">
		<div class="clear"></div>
		<?php
		foreach($contacts as $contact) {
			$highlight = ($contact->id == $id_contact) ? 'highlight' : '';
		?>
			<div class="<?php echo $highlight; ?> padding-5px">
				<a name="contact-<?php echo $contact->id; ?>"></a>
				<div class="contact-name">
					<?php echo $contact->prefix; ?> <?php echo $contact->first_name; ?> <?php echo $contact->last_name; ?>
				</div>
				<?php if($contact->position !== NULL) { ?>
				<em><?php echo $contact->position; ?></em>
				<br />
				<?php } ?>

				<?php if($contact->department !== NULL) { ?>
				<em><?php echo $contact->department; ?></em>
				<br />
				<?php } ?>

				<?php if($contact->institution !== NULL) { ?>
				<?php echo $contact->institution; ?>
				<br />
				<?php } ?>

				<?php if($contact->address !== NULL) { ?>
				<div class="contact-address">
					<strong><?php _e('Address:', 'informea'); ?></strong> <?php echo replace_enter_br($contact->address); ?>
				</div>
				<?php } ?>

				<?php if($contact->telephone !== NULL) { ?>
				<div>
					<strong><?php _e('Phone:', 'informea'); ?></strong> <?php echo $contact->telephone; ?>
				</div>
				<?php } ?>

				<?php if($contact->fax !== NULL) { ?>
				<div>
					<strong><?php _e('Fax:', 'informea'); ?></strong> <?php echo $contact->fax; ?>
				</div>
				<?php } ?>
				<?php if (!empty($contact->email)){ ?>
					<a href="<?php echo bloginfo('url'); ?>/countries/<?php echo $country->id; ?>/sendmail/<?php echo $contact->id?>/<?php echo $treaty->id_treaty?>" class="tooltip cnt-contact-link" title="<?php _e('Contact this person', 'informea'); ?>"><?php _e('Contact via e-mail', 'informea'); ?></a>&nbsp;&nbsp;
				<?php } ?>
				<a href="<?php echo bloginfo('url'); ?>/vcard?id_contact=<?php echo $contact->id?>" class="tooltip cnt-contact-link" title="<?php _e('Download vCard', 'informea'); ?>"><?php _e('Download vcard', 'informea'); ?></a>
			</div>
		<?php } ?>
		</div>
	</li>
	<?php } ?>
</ul>
<?php } ?>

