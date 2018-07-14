<?php
/*
If you would like to edit this file, copy it to your current theme's directory and edit it there.
Theme My Login will always look in your theme's directory first, before using this default template.
*/
?>
<div class='sign-in'>
	<?php _e('Sign into your account','pluginhunt'); ?>
</div>
<div class='clear'></div>
<div class="tml tml-login login-container" id="theme-my-login<?php $template->the_instance(); ?>">
	<?php $template->the_action_template_message( 'login' ); ?>
	<?php $template->the_errors(); ?>
	<div class='ph_socials ph_reg col-md-4 left-part vcenter'>
		<div class="left-container">

			<div class='pyraneed-login-logo'>
					<a href='<?php echo esc_url( home_url( '/' ) ); ?>' title='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>' rel='home'><img class="login-img" src='<?php echo get_stylesheet_directory_uri(); ?>/assets/img/pyraneed-login-logo.png' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'></a>
			</div>
			<form name="loginform" id="loginform<?php $template->the_instance(); ?>" action="<?php $template->the_action_url( 'login' ); ?>" method="post">
				<p class="tml-user-login-wrap">
					<input type="text" name="log" id="user_login<?php $template->the_instance(); ?>" autocomplete="off" class="input" placeholder="E-mail" value="<?php $template->the_posted_value( 'log' ); ?>" size="20" />
				</p>

				<p class="tml-user-pass-wrap">
					<input type="password" name="pwd" id="user_pass<?php $template->the_instance(); ?>" autocomplete="off" class="input" placeholder="Password" value="" size="20" autocomplete="off" />
				</p>

				<?php do_action( 'login_form' ); ?>

				<div class="tml-rememberme-submit-wrap">
					<p class="tml-rememberme-wrap">
						<input name="rememberme" type="checkbox" id="rememberme<?php $template->the_instance(); ?>" value="forever" />
						<label for="rememberme<?php $template->the_instance(); ?>"><?php esc_attr_e( 'Remember Me', 'pluginhunt' ); ?></label>
					</p>

					<p class="tml-submit-wrap">
						<input type="submit" name="wp-submit" id="wp-submit<?php $template->the_instance(); ?>" value="<?php esc_attr_e( 'Log In', 'pluginhunt' ); ?>" />
						<input type="hidden" name="redirect_to" value="<?php $template->the_redirect_url( 'login' ); ?>" />
						<input type="hidden" name="instance" value="<?php $template->the_instance(); ?>" />
						<input type="hidden" name="action" value="login" />
					</p>
				</div>
			</form>
			<?php $template->the_action_links( array( 'login' => false ) ); ?>
			

		</div>
	</div>
<div class="col-md-8 right-part">
</div>

</div>
