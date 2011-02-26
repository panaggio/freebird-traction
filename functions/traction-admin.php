<?php
	/*
		Class Definition
	*/
	if (!class_exists( 'JestroCore' )) {
		class JestroCore {

			var $themename = "Jestro";
			var $themeurl = "http://thethemefoundry.com/";
			var $shortname = "jestro_themes";
			var $options = array();

			/* PHP4 Compatible Constructor */
			function JestroCore () {
				add_action( 'init', array(&$this, 'printAdminScripts' ));
				add_action( 'admin_menu', array(&$this, 'addAdminPage' ));
			}

			/* Add Custom CSS & JS */
			function printAdminScripts () {
					if ( isset( $_GET['page'] ) && $_GET['page'] == basename(__FILE__) ) {
					wp_enqueue_style( 'jestro', get_bloginfo( 'template_directory' ).'/functions/stylesheets/admin.css' );
					wp_enqueue_script( 'jestro', get_bloginfo( 'template_directory' ).'/functions/javascripts/admin.js', array( 'jquery' ));
					wp_enqueue_script( 'farbtastic' );
					wp_enqueue_style( 'farbtastic' );
				}
			}

			/* Process Input and Add Options Page*/
			function addAdminPage() {
				// global $themename, $shortname, $options;
				if ( isset( $_GET['page'] ) && $_GET['page'] == basename(__FILE__) ) {
					if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'save' ) {
						foreach ($this->options as $value) {
							if ( array_key_exists('id', $value) ) {
								if ( isset( $_REQUEST[ $value['id'] ] ) ) {
									update_option( $value['id'], $_REQUEST[ $value['id'] ] );
								}
							}
						}
						foreach ($this->options as $value) {
							if ( array_key_exists('id', $value) ) {
								if ( isset( $_REQUEST[ $value['id'] ] ) ) {
									update_option( $value['id'], $_REQUEST[ $value['id'] ] );
								} else {
									delete_option( $value['id'] );
								}
							}
						}
						header("Location: themes.php?page=".basename(__FILE__)."&saved=true");
						die;
					} else if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'reset' ) {
						foreach ($this->options as $value) {
							if ( array_key_exists('id', $value) ) {
								delete_option( $value['id'] );
							}
						}
						header("Location: themes.php?page=".basename(__FILE__)."&reset=true");
						die;
					}
				}
				add_theme_page($this->themename." Options", $this->themename." Options", 'edit_theme_options', basename(__FILE__), array(&$this, 'adminPage' ));
			}

			/* Output of the Admin Page */
			function adminPage () {
				// global $themename, $shortname, $options;
				if ( isset( $_REQUEST['saved'] ) && $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>' . $this->themename . __( ' settings saved!', $this->domain ). '</strong></p></div>';
				if ( isset( $_REQUEST['reset'] ) && $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>' . $this->themename . __( ' settings reset.', $this->domain ). '</strong></p></div>'; ?>

<div id="v-options">
	<div class="v-top clear">
		<h1 class="v-trial"><?php echo $this->themename; ?> <?php _e( 'Options', $this->domain ); ?></h1>
		<div class="v-logo"><a href="http://thethemefoundry.com/">The Theme Foundry</a></div>
	</div>
	<div id="vop-header"><p><strong><?php _e( 'Need help?', $this->domain ); ?></strong> <a href="http://thethemefoundry.com/tutorials/<?php echo $this->domain; ?>/"><?php _e( 'Read the tutorials' , $this->domain ); ?></a> <?php _e( 'or visit the <a href="http://thethemefoundry.com/forums/">support forums.</a>', $this->domain ); ?></p></div>
	<div class="v-notice">
		<h3><?php _e( 'Go PRO!', $this->domain ); ?></h3>
		<p><?php _e( 'You are using the free trial version of ' . $this->themename . '. Upgrade to ' . $this->themename . ' PRO for extra features, lifetime theme updates, dedicated support, and comprehensive theme tutorials.', $this->domain ); ?></p>
		<p><a href="http://thethemefoundry.com/<?php echo $this->domain; ?>/"><?php _e( 'Learn more about ' . $this->themename . ' PRO &rarr;', $this->domain ); ?></a></p>
	</div>
	<div id="vop-body">
		<form method="post">
<?php
				for ($i = 0; $i < count($this->options); $i++) :
					switch ($this->options[$i]["type"]) :

						case "subhead":
							if ($i != 0) { ?>
		<div class="v-save-button submit">
			<input type="hidden" name="action" value="save" />
			<input class="button-primary" type="submit" value="<?php _e( 'Save changes', $this->domain ); ?>" name="save"/>
		</div><!--end v-save-button-->
	</div>
</div><!--end v-option--><?php } ?>
<div class="v-option">
	<h3><?php echo $this->options[$i]["name"]; ?></h3>
	<div class="v-option-body clear">
		<?php if ( isset( $this->options[$i]["notice"] ) ) $notice = $this->options[$i]["notice"]; else $notice = ''; ?>
		<?php if ($notice != '' ) { ?>
			<p class="notice"><?php echo $notice; ?></p>
		<?php } ?>
						<?php
							break;

					case "checkbox":
						?>
		<?php if ( isset( $this->options[$i]["pro"] ) ) $pro = $this->options[$i]["pro"]; else $pro = false; ?>
		<div class="v-field check clear <?php if ( $pro == 'true' ) echo 'pro'; ?>">
			<div class="v-field-d"><span><?php echo $this->options[$i]["desc"]; ?></span></div>
			<input id="<?php echo $this->options[$i]["id"]; ?>" type="checkbox" name="<?php echo $this->options[$i]["id"]; ?>" value="true"<?php echo (get_option($this->options[$i]['id'])) ? ' checked="checked"' : ''; ?> />
			<label for="<?php echo $this->options[$i]["id"]; ?>"><?php echo $this->options[$i]["name"]; ?></label>
		</div><!--end v-field check-->
						<?php
							break;

						case "radio":
							?>
		<?php if ( isset( $this->options[$i]["pro"] ) ) $pro = $this->options[$i]["pro"]; else $pro = false; ?>
		<div class="v-field radio clear <?php if ( $pro == 'true' ) echo 'pro'; ?>">
			<div class="v-field-d"><span><?php echo $this->options[$i]["desc"]; ?></span></div>
				<?php
				$radio_setting = get_option($this->options[$i]['id']);
				$checked = '';
				foreach ($this->options[$i]['options'] as $key => $val) :
					if ($radio_setting != '' && $key == get_option($this->options[$i]['id']) ) {
						$checked = ' checked="checked"';
					} else {
						if ($key == $this->options[$i]['std']){
							$checked = 'checked="checked"';
						}
					}
					?>
				<input type="radio" name="<?php echo $this->options[$i]['id']; ?>" value="<?php echo $key; ?>"<?php echo $checked; ?> /><?php echo $val; ?><br />
				<?php endforeach; ?>
			<label for="<?php echo $this->options[$i]["id"]; ?>"><?php echo $this->options[$i]["name"]; ?></label>
		</div><!--end v-field radio-->
						<?php
							break;

						case "text":
							?>
		<?php if ( isset( $this->options[$i]["pro"] ) ) $pro = $this->options[$i]["pro"]; else $pro = false; ?>
		<div class="v-field text clear <?php if ( $pro == 'true' ) echo 'pro'; ?>">
			<div class="v-field-d"><span><?php echo $this->options[$i]["desc"]; ?></span></div>
			<label for="<?php echo $this->options[$i]["id"]; ?>"><?php echo $this->options[$i]["name"]; ?></label>
			<input id="<?php echo $this->options[$i]["id"]; ?>" type="text" name="<?php echo $this->options[$i]["id"]; ?>" value="<?php echo stripslashes((get_option($this->options[$i]["id"]) != '') ? get_option($this->options[$i]["id"]) : $this->options[$i]["std"]); ?>" />
		</div><!--end v-field text-->
						<?php
							break;

						case "colorpicker":
							?>
		<?php if ( isset( $this->options[$i]["pro"] ) ) $pro = $this->options[$i]["pro"]; else $pro = false; ?>
		<div class="v-field colorpicker clear <?php if ( $pro == 'true' ) echo 'pro'; ?>">
			<div class="v-field-d"><span><?php echo $this->options[$i]["desc"]; ?></span></div>
			<label for="<?php echo $this->options[$i]["id"]; ?>"><?php echo $this->options[$i]["name"]; ?> <a href="#<?php echo $this->options[$i]["id"]; ?>_colorpicker" onclick="toggleColorpicker (this, '<?php echo $this->options[$i]["id"]; ?>', 'open', '<?php _e( 'show color picker', $this->domain ); ?>', '<?php _e( 'hide color picker', $this->domain ); ?>' )"><?php _e( 'show color picker', $this->domain ); ?></a></label>
			<div id="<?php echo $this->options[$i]["id"]; ?>_colorpicker" class="colorpicker_container"></div>
			<input id="<?php echo $this->options[$i]["id"]; ?>" type="text" name="<?php echo $this->options[$i]["id"]; ?>" value="<?php echo (get_option($this->options[$i]["id"]) != '') ? get_option($this->options[$i]["id"]) : $this->options[$i]["std"]; ?>" />
		</div><!--end v-field colorpicker-->
						<?php
							break;

						case "select":
							?>
		<?php if ( isset( $this->options[$i]["pro"] ) ) $pro = $this->options[$i]["pro"]; else $pro = false; ?>
		<div class="v-field select clear <?php if ( $pro == 'true' ) echo 'pro'; ?>">
			<div class="v-field-d"><span><?php echo $this->options[$i]["desc"]?></span></div>
			<label for="<?php echo $this->options[$i]["id"]; ?>"><?php echo $this->options[$i]["name"]; ?></label>
			<select id="<?php echo $this->options[$i]["id"]; ?>" name="<?php echo $this->options[$i]["id"]; ?>">
				<?php
					foreach ($this->options[$i]["options"] as $key => $val) :
						if (get_option($this->options[$i]["id"]) == '' || is_null(get_option($this->options[$i]["id"]))) : ?>
					<option value="<?php echo $key; ?>"<?php echo ($key == $this->options[$i]['std']) ? ' selected="selected"' : ''; ?>><?php echo $val; ?></option>
						<?php else : ?>
					<option value="<?php echo $key; ?>"<?php echo get_option($this->options[$i]["id"]) == $key ? ' selected="selected"' : ''; ?>><?php echo $val; ?></option>
					<?php
						endif;
					endforeach;
				?>
			</select>
		</div><!--end v-field select-->
						<?php
							break;

						case "textarea":
							?>
		<?php if ( isset( $this->options[$i]["pro"] ) ) $pro = $this->options[$i]["pro"]; else $pro = false; ?>
		<div class="v-field textarea clear <?php if ( $pro == 'true' ) echo 'pro'; ?>">
			<div class="v-field-d"><span><?php echo $this->options[$i]["desc"]?></span></div>
			<label for="<?php echo $this->options[$i]["id"]; ?>"><?php echo $this->options[$i]["name"]?></label>
			<textarea id="<?php echo $this->options[$i]["id"]; ?>" name="<?php echo $this->options[$i]["id"]; ?>"<?php echo ($this->options[$i]["options"] ? ' rows="'.$this->options[$i]["options"]["rows"].'" cols="'.$this->options[$i]["options"]["cols"].'"' : ''); ?>><?php
				echo ( get_option($this->options[$i]['id']) != '') ? stripslashes(get_option($this->options[$i]['id'])) : stripslashes($this->options[$i]['std']);
			?></textarea>
		</div><!--end vop-v-field textarea-->
						<?php
							break;

					endswitch;
				endfor;
			?>
					<div class="v-save-button submit">
						<input type="submit" value="<?php _e( 'Save changes', $this->domain ); ?>" name="save"/>
					</div><!--end v-save-button-->
				</div>
			</div>
			<div class="v-saveall-button submit">
				<input class="button-primary" type="submit" value="<?php _e( 'Save all changes', $this->domain ); ?>" name="save"/>
			</div>
			</form>
			<div class="v-reset-button submit">
				<form method="post">
					<input type="hidden" name="action" value="reset" />
					<input class="v-reset" type="submit" value="<?php _e( 'Reset all options', $this->domain ); ?>" name="reset"/>
				</form>
			</div>

			<script type="text/javascript">
				<?php
					for ($i = 0; $i < count($this->options); $i++) :
						if ($this->options[$i]['type'] == 'colorpicker' ):
				?>
						jQuery("#<?php echo $this->options[$i]["id"]; ?>_colorpicker").farbtastic("#<?php echo $this->options[$i]["id"]; ?>");
				<?php
						endif;
					endfor;
				?>
					jQuery( '.colorpicker_container' ).hide();
					jQuery("div.v-field.pro input, div.v-field.pro select, div.v-field.pro textarea").attr("disabled", "disabled");
			</script>
	</div><!--end vop-body-->
</div><!--end v-options-->
			<?php
			}
		}
	}
?>