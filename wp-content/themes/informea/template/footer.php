<?php
	function tengine_footer() {
		$mobile = new Mobile_Detect();
?>
		<div class="clear"></div>
	</div><!-- #main -->

	<div id="footer" role="contentinfo">
		<div id="colophon">
			<div class="left top-logos">
				<span>
					<?php _e('Organizations', 'informea'); ?>
				</span>

				<div class="clear"></div>

				<table cellspacing="0" cellpadding="0" border="none">
					<tr class="xyz">
						<td>
							<a target="_blank" href="http://www.un.org/" title="<?php _e('Visit United Nations website', 'informea'); ?>">
								<img src="<?php bloginfo('template_directory'); ?>/images/logos/un.png" alt="<?php _e('UN Logo', 'informea'); ?>"  title="<?php _e('Visit United Nations website', 'informea'); ?>" />
							</a>

							<p>
								&nbsp;
							</p>
						</td>
						<td>
							<a target="_blank" href="http://www.unep.org/" title="<?php _e('Visit United Nations Environment Programme website', 'informea'); ?>">
								<img src="<?php bloginfo('template_directory'); ?>/images/logos/UNEP.png" alt="<?php _e('UNEP Logo', 'informea'); ?>"  title="<?php _e('Visit United Nations Environment Programme website', 'informea'); ?>" />
							</a>
							<p>
								&nbsp;
							</p>
						</td>

					</tr>

					<tr>
						<td>
							<a target="_blank" href="http://www.fao.org/" title="<?php _e('Visit Visit Food and Agriculture Organization website website', 'informea'); ?>">
								<img src="<?php bloginfo('template_directory'); ?>/images/logos/FAO.png" alt="<?php _e('FAO Logo', 'informea'); ?>"  title="<?php _e('Visit Visit Food and Agriculture Organization website website', 'informea'); ?>" />
							</a>

							<p>
								&nbsp;
							</p>
						</td>
						<td>
							<a target="_blank" href="http://www.unesco.org/" title="<?php _e('Visit United Nations Educational, Scientific and Cultural Organization website', 'informea'); ?>">
								<img src="<?php bloginfo('template_directory'); ?>/images/logos/UNESCO.png" alt="<?php _e('UNESCO Logo', 'informea'); ?>" title="<?php _e('Visit United Nations Educational, Scientific and Cultural Organization website', 'informea'); ?>" />
							</a>

							<p>
								&nbsp;
							</p>
						</td>
					</tr>
				</table>
			</div>

			<div class="left logos-col-2 top-logos">
				<span>
					<?php _e('Participating Treaties', 'informea'); ?>
				</span>

				<div class="clear"></div>

				<table>
					<tr>
						<td>
							<a target="_blank" href="http://unfccc.int/" title="<?php _e('Visit United Nations Framework Convention on Climate Change website', 'informea'); ?>">
								<img src="<?php bloginfo('template_directory'); ?>/images/logos/UNFCCC.png" alt="<?php _e('UNFCCC Logo', 'informea'); ?>" title="<?php _e('Visit United Nations Framework Convention on Climate Change website', 'informea'); ?>" />
								<p>
									<?php _e('UNFCCC', 'informea'); ?>
								</p>
							</a>
						</td>
						<td>
							<a target="_blank" href="http://www.unccd.int/" title="<?php _e('Visit United Nations Convention to Combat Desertification website', 'informea'); ?>">
								<img src="<?php bloginfo('template_directory'); ?>/images/logos/UNCCCD.png" alt="<?php _e('UNCCD Logo', 'informea'); ?>" title="<?php _e('Visit United Nations Convention to Combat Desertification website', 'informea'); ?>" />
								<p>
									<?php _e('UNCCD', 'informea'); ?>
								</p>
							</a>
						</td>
						<td>
							<a target="_blank" href="http://ozone.unep.org/" title="<?php _e('Visit Ozone Secretariat website', 'informea'); ?>">
								<img src="<?php bloginfo('template_directory'); ?>/images/logos/ozone.png" alt="<?php _e('OZONE Logo', 'informea'); ?>" title="<?php _e('Visit Ozone Secretariat website', 'informea'); ?>" />
								<p>
									<?php _e('OZONE', 'informea'); ?>
								</p>
							</a>
						</td>
						<td>
							<a target="_blank" href="http://www.basel.int/" title="<?php _e('Visit Basel Convention website', 'informea'); ?>">
								<img src="<?php bloginfo('template_directory'); ?>/images/logos/Basel.png" alt="<?php _e('BASEL Logo', 'informea'); ?>" title="<?php _e('Visit Basel Convention website', 'informea'); ?>" />
								<p>
									<?php _e('BASEL', 'informea'); ?>
								</p>
							</a>
						</td>
						<?php if($mobile->isMobile()) { ?>
					</tr>
					<tr>
						<?php } ?>
						<td>
							<a target="_blank" href="http://www.pic.int/" title="<?php _e('Visit Rotterdam Convention website', 'informea'); ?>">
								<img src="<?php bloginfo('template_directory'); ?>/images/logos/Rotterdam.png" alt="<?php _e('Rotterdam Logo', 'informea'); ?>" title="<?php _e('Visit Rotterdam Convention website', 'informea'); ?>" />
								<p>
									<?php _e('ROTTERDAM', 'informea'); ?>
								</p>
							</a>
						</td>

						<td>
							<a target="_blank" href="http://chm.pops.int/" title="<?php _e('Visit Stockholm Convention website', 'informea'); ?>">
								<img src="<?php bloginfo('template_directory'); ?>/images/logos/Stockholm.png" alt="<?php _e('Stockholm Logo', 'informea'); ?>" title="<?php _e('Visit Stockholm Convention website', 'informea'); ?>" />
								<p>
									<?php _e('STOCKHOLM', 'informea'); ?>
								</p>
							</a>
						</td>

						<td>
							<a target="_blank" href="http://www.unep.org/" title="Visit United Nations Environment Programme website">
								<img class="footer-image-no-margin" src="<?php bloginfo('template_directory'); ?>/images/logos/UNEP.png" alt="Title here" title="Title here" />
								<p>
									<?php _e('BARCELONA', 'informea'); ?>
								</p>

							</a>

						</td>

					</tr>

					<tr>
						<td>
							<a target="_blank" href="http://www.cbd.int/" title="<?php _e('Visit Convention on Biological Diversit website', 'informea'); ?>">
								<img src="<?php bloginfo('template_directory'); ?>/images/logos/CBD.png" alt="<?php _e('CBD Logo', 'informea'); ?>" title="<?php _e('Visit Convention on Biological Diversit website', 'informea'); ?>" />
								<p>
									<?php _e('CBD', 'informea'); ?>
								</p>
							</a>
						</td>
						<td>
							<a target="_blank" href="http://www.cites.org/" title="<?php _e('Visit Convention on International Trade in Endangered Species website', 'informea'); ?>" >
								<img src="<?php bloginfo('template_directory'); ?>/images/logos/cites.png" alt="<?php _e('CITES Logo', 'informea'); ?>" title="<?php _e('Visit Convention on International Trade in Endangered Species website', 'informea'); ?>" />
								<p>
									<?php _e('CITES', 'informea'); ?>
								</p>
							</a>
						</td>
						<td>
							<a target="_blank" href="http://whc.unesco.org/" title="<?php _e('Visit UNESCO World Heritage Convention website', 'informea'); ?>">
								<img src="<?php bloginfo('template_directory'); ?>/images/logos/whc.png" alt="<?php _e('UNESCO Logo', 'informea'); ?>" title="<?php _e('Visit UNESCO World Heritage Convention website', 'informea'); ?>" />
								<p>
									<?php _e('WHC', 'informea'); ?>
								</p>
							</a>
						</td>
						<td>
							<a target="_blank" href="http://www.ramsar.org/" title="<?php _e('Visit Ramsar Convention website', 'informea'); ?>">
								<img src="<?php bloginfo('template_directory'); ?>/images/logos/RAMSAR.png" alt="<?php _e('Ramsar Logo', 'informea'); ?>" title="<?php _e('Visit Ramsar Convention website', 'informea'); ?>" />
								<p>
									<?php _e('Ramsar', 'informea'); ?>
								</p>
							</a>
						</td>
						<?php if($mobile->isMobile()) { ?>
					</tr>
					<tr>
						<?php } ?>
						<td>
							<a target="_blank" href="http://www.planttreaty.org/" title="<?php _e('Visit International Treaty on Genetic Resources for Food and Agriculture website', 'informea'); ?>">
								<img src="<?php bloginfo('template_directory'); ?>/images/logos/ITPGRFA.png" alt="<?php _e('ITPGRFA Logo', 'informea'); ?>" title="<?php _e('Visit International Treaty on Genetic Resources for Food and Agriculture website', 'informea'); ?>" />
								<p>
									<?php _e('ITPGRFA', 'informea'); ?>
								</p>
							</a>
						</td>

						<td>
							<a target="_blank" href="http://www.cms.int/" title="<?php _e('Visit Convention on Migratory Species website', 'informea'); ?>">
								<img src="<?php bloginfo('template_directory'); ?>/images/logos/CMS.png" alt="<?php _e('CMS Logo', 'informea'); ?>" title="<?php _e('Visit Convention on Migratory Species website', 'informea'); ?>" />
								<p>
									<?php _e('CMS', 'informea'); ?>
								</p>
							</a>


						</td>

						<td class="logos-sublinks">
							<table>
								<tr>
									<td>
										<a target="_blank" href="http://www.acap.aq/">
											<?php _e('ACAP', 'informea'); ?>
										</a>
										<a target="_blank" href="http://www.accobams.org/">
											<?php _e('ACCOBAMS', 'informea'); ?>
										</a>
										<a target="_blank" href="http://www.unep-aewa.org/">
											<?php _e('AEWA', 'informea'); ?>
										</a>
										<a target="_blank" href="http://www.ascobans.org/">
											<?php _e('ASCOBANS', 'informea'); ?>
										</a>
										<a target="_blank" href="http://www.eurobats.org/">
											<?php _e('EUROBATS', 'informea'); ?>
										</a>
										<a target="_blank" href="http://www.igcp.org/">
											<?php _e('GORILLAS', 'informea'); ?>
										</a>
										<a target="_blank" href="http://www.waddensea-secretariat.org/">
											<?php _e('WADDEN SEA SEALS', 'informea'); ?>
										</a>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>

			<div class="clear">&nbsp;</div>

			<div class="left bottom-logos">
				<a href="javascript:void(0); "><?php _e('Sitemap', 'informea'); ?></a>
				&middot;
				<a href="javascript:void(0); "><?php _e('Privacy', 'informea'); ?></a>
				&middot;
				<a href="<?php echo bloginfo('url'); ?>/disclaimer"><?php _e('Terms and Conditions', 'informea'); ?></a>
				&middot;
				<?php _e('portions copyright &copy; United Nations, FAO, UNEP, UNESCO', 'informea'); ?>
			</div>
			<div class="clear"></div>
			<?php
				/* A sidebar in the footer? Yep. You can can customize your footer with four columns of widgets. */
				get_sidebar( 'footer' );
			?>
		</div><!-- #colophon -->
	</div><!-- #footer -->
</div><!-- #wrapper -->
<?php
	}
?>
