<div class="wrap">
	<h1>Happy WP SCSS Compiler</h1>
	<p>
		<span class="dashicons dashicons-laptop"></span> Version <?php echo esc_html($version) ?><br />
		<!-- <span class="dashicons dashicons-heart"></span> Developped by <a href="https://mkey.fr" target="_blank">Happy Monkey</a><br /> -->
		<span class="dashicons dashicons-editor-help"></span> Help & Issues: <a href="https://wordpress.org/support/plugin/happy-scss-compiler/" target="_blank">Wordpress Plugin Support</a><br />
		<span class="dashicons dashicons-lightbulb"></span> Ideas for improvement: <a href="mailto:dev@happy-monkey.fr?subject=SCSS Compiler: I have an idea!"">dev@happy-monkey.fr</a>
	</p>
	
	<div class="tab-content">
		<?php
		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->settings_tab1_key;
			?>
			<div class="wrap">
				<?php $this->plugin_options_tabs(); ?>
				
				<?php // Advanced Paths
				if($tab == $this->settings_tab3_key): ?>
				
					<h2><?php echo $this->title_icon ?> Paths from Wordpress root folder</h2>
					<p>Fill the two textareas with paths from the root folder, one per line.<br />Each line must match the equivalent line in the other textarea (SCSS -> CSS).</p>
					<form action="/wp-admin/admin-post.php" method="post">
						<input type="hidden" name="action" value="advancedpaths" />
						
						<div id="adv_path_container">
							<div class="adv_path_textarea_column">
								<label for="hm_wp_scss__adv_path_scss">SCSS paths:</label>
								<textarea name="hm_wp_scss__adv_path_scss" id="hm_wp_scss__adv_path_scss" placeholder="/wp-content/plugins/my-plugin/scss/&#10;/wp-content/themes/my-theme/assets/scss/&#10;..."><?php echo get_option( "hm_wp_scss__adv_path_scss" ) ?? '' ?></textarea>
							</div>
							<div id="adv_path_arrow"><span class="dashicons dashicons-arrow-right-alt"></span></div>
							<div class="adv_path_textarea_column">
								<label for="hm_wp_scss__adv_path_css">CSS paths:</label>
								<textarea name="hm_wp_scss__adv_path_css" id="hm_wp_scss__adv_path_css" placeholder="/wp-content/plugins/my-plugin/css/&#10;/wp-content/themes/my-theme/assets/css/&#10;..."><?php echo get_option( "hm_wp_scss__adv_path_css" ) ?? '' ?></textarea>
							</div>
						</div>
						<input type="submit" value="Enregistrer" class="button button-primary">
					</form>
				
				<?php // Import / Export
				elseif($tab == $this->settings_tab2_key): ?>
				
					<h2><?php echo $this->title_icon ?> Export Settings</h2>
					<p>Here you can export your settings from Happy WP SCSS, either to use them on another site or to save them.</p>
					<form action="/wp-admin/admin-post.php" method="post">
						<input type="hidden" name="action" value="dljson" />
						<input type="submit" value="Download Settings" class="button button-primary">
					</form>
					
					<h2><?php echo $this->title_icon ?> Import Settings</h2>
					<p>
						Import settings file previously exported with this plugin.<br />
						<strong>Warning: all your current settings will be replaced.</strong>
					</p>
					<form action="/wp-admin/admin-post.php" method="post" enctype="multipart/form-data">
						<input type="hidden" name="action" value="importsettings" />
						<input type="file" name="import_field" /><br /><br />
						<input type="submit" value="Import Settings" class="button button-primary">
					</form>
				
				<?php else: ?>
				
					<form method="post" action="options.php">
						<?php wp_nonce_field( 'update-options' ); ?>
						<?php settings_fields( $tab ); ?>
						<?php do_settings_sections( $tab ); ?>
						<?php submit_button(); ?>
					</form>
				
				<?php endif; ?>

			</div>
		</div>
	
	
</div>