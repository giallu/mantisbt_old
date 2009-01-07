<?php
# Mantis - a php based bugtracking system

# Mantis is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# Mantis is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Mantis.  If not, see <http://www.gnu.org/licenses/>.

	/**
	 * OpenID Login page
	 * Used to link a valid OpenID to existing accounts, can create new ones on demand
	 *
	 * @package MantisBT
	 * @copyright Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	 * @copyright Copyright (C) 2002 - 2009  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	 * @link http://www.mantisbt.org
	 */
	 /**
	  * Mantis Core API's
	  */
	require_once( 'core.php' );

	if ( auth_is_user_authenticated() && !current_user_is_anonymous() ) {
		print_header_redirect( config_get( 'default_home_page' ) );
	}

	$t_core_path = config_get( 'core_path' );

	$f_openid = gpc_get_string( 'openid_identifier', '' );

	if ( $f_openid === '' ) {
		print_header_redirect( 'login_page.php' );
		exit;
	}
	if ( isset( $_GET['openid_mode'] ) && $_GET['openid_mode'] == "id_res" ) {
		require_once( 'Zend/OpenId/Consumer.php' );
		$consumer = new Zend_OpenId_Consumer();
		if ( $consumer->verify($_GET, $id) ) {
				$status = "VALID";
		}
	}

	# Login page shouldn't be indexed by search engines
	html_robots_noindex();

	html_page_top1();
	html_page_top2a();

?>

<!-- Login Form BEGIN -->
<div align="center">
<form name="login_form" method="post" action="openid_login.php">
<table class="width50" cellspacing="1">
<tr>
	<td colspan="2" class="form-title">
		<?php
			if ( !is_blank( $f_return ) ) {
			?>
				<input type="hidden" name="return" value="<?php echo string_html_specialchars( $f_return ) ?>" />
				<?php
			}
			echo lang_get( 'login_title' ) ?>
	</td>
</tr>
<tr class="row-1">
	<td class="category">OpenID</td>
	<td width="75%"><input size="32" type="text" name="openid_identifier" value="<?php echo $f_openid ?>" /></td>
<tr>
</tr>
<tr colspan="2" class="form-title">
	<td colspan="2">Please enter the login data to associate your OpenID</td>
<tr>
</tr>
<tr class="row-1">
	<td class="category" width="25%">
		<?php echo lang_get( 'username' ) ?>
	</td>
	<td width="75%">
		<input type="text" name="username" size="32" maxlength="<?php echo USERLEN;?>" />
	</td>
</tr>
<tr class="row-2">
	<td class="category">
		<?php echo lang_get( 'password' ) ?>
	</td>
	<td>
		<input type="password" name="password" size="16" maxlength="<?php echo PASSLEN;?>" />
	</td>
</tr>
<tr class="row-1">
	<td class="category">
		<?php echo lang_get( 'save_login' ) ?>
	</td>
	<td>
		<input type="checkbox" name="perm_login" />
	</td>
</tr>
	<td class="center" colspan="2">
		<input type="submit" class="button" value="<?php echo lang_get( 'login_button' ) ?>" />
	</td>
</tr>
</table>
</form>
<br />
<?php if ( config_get( 'openid_enabled' ) === ON ) { ?>
<form name="openid_login_form" method="post" action="openid_login.php">
<table class="width50" cellspacing="1">
<tr class="form-title">
	<td class="form-title" colspan="2"><?php echo lang_get( 'login_using_openid' ) ?></td>
</tr>
<tr class="row-1">
	<td class="category">OpenID</td>
	<td width="75%"><input size="32" type="text" name="openid_identifier" value="<?php echo $f_openid ?>" /></td>
<tr>
</tr>
	<td class="center" colspan="2">
		<input type="submit" name="openid_action" class="button" value="<?php echo lang_get( 'login_button' ) ?>" />
	</td>
</tr>

</table>
</form>
<?php } ?>

</div>


<?php
	echo '<br /><div align="center">';
	print_signup_link();
	echo '&nbsp;';
	print_lost_password_link();
	echo '</div>';

	#
	# Do some checks to warn administrators of possible security holes.
	# Since this is considered part of the admin-checks, the strings are not translated.
	#

	if ( config_get_global( 'admin_checks' ) == ON ) {

		# Warning, if plain passwords are selected
		if ( config_get( 'login_method' ) === PLAIN ) {
			echo '<div class="warning" align="center">', "\n";
			echo "\t", '<p><font color="red">', lang_get( 'warning_plain_password_authentication' ), '</font></p>', "\n";
			echo '</div>', "\n";
		}

		# Generate a warning if administrator/root is valid.
		$t_admin_user_id = user_get_id_by_name( 'administrator' );
		if ( $t_admin_user_id !== false ) {
			if ( user_is_enabled( $t_admin_user_id ) && auth_does_password_match( $t_admin_user_id, 'root' ) ) {
				echo '<div class="warning" align="center">', "\n";
				echo "\t", '<p><font color="red">', lang_get( 'warning_default_administrator_account_present' ), '</font></p>', "\n";
				echo '</div>', "\n";
			}
		}

		# Check if the admin directory is available and is readable.
		$t_admin_dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR;
		if ( is_dir( $t_admin_dir ) && is_readable( $t_admin_dir ) ) {
			echo '<div class="warning" align="center">', "\n";
			echo '<p><font color="red">', lang_get( 'warning_admin_directory_present' ), '</font></p>', "\n";
			echo '</div>', "\n";
				
			# since admin directory and db_upgrade lists are available check for missing db upgrades	
			# Check for db upgrade for versions < 1.0.0 using old upgrader
			$t_db_version = config_get( 'database_version' , 0 );
			# if db version is 0, we haven't moved to new installer.
			if ( $t_db_version == 0 ) {
				if ( db_table_exists( db_get_table( 'mantis_upgrade_table' ) ) ) {
					$query = "SELECT COUNT(*) from " . db_get_table( 'mantis_upgrade_table' ) . ";";
					$result = db_query_bound( $query );
					if ( db_num_rows( $result ) < 1 ) {
						$t_upgrade_count = 0;
					} else {
						$t_upgrade_count = (int)db_result( $result );
					}
				} else {
					$t_upgrade_count = 0;
				}

				if ( $t_upgrade_count > 0 ) { # table exists, check for number of updates
				
					# new config table database version is 0.
					# old upgrade tables exist. 
					# assume user is upgrading from <1.0 and therefore needs to update to 1.x before upgrading to 1.2
					echo '<div class="warning" align="center">';
					echo '<p><font color="red">', lang_get( 'error_database_version_out_of_date_1' ), '</font></p>';
					echo '</div>';
				} else {
					# old upgrade tables do not exist, yet config database_version is 0
					echo '<div class="warning" align="center">';
					echo '<p><font color="red">', lang_get( 'error_database_no_schema_version' ), '</font></p>';
					echo '</div>';
				}
			}

			# Check for db upgrade for versions > 1.0.0 using new installer and schema
			require_once( 'admin/schema.php' );
			$t_upgrades_reqd = sizeof( $upgrade ) - 1;

			if ( ( 0 < $t_db_version ) &&
					( $t_db_version != $t_upgrades_reqd ) ) {

				if ( $t_db_version < $t_upgrades_reqd ) {
					echo '<div class="warning" align="center">';
					echo '<p><font color="red">', lang_get( 'error_database_version_out_of_date_2' ), '</font></p>';
					echo '</div>';
				} else {
					echo '<div class="warning" align="center">';
					echo '<p><font color="red">', lang_get( 'error_code_version_out_of_date' ), '</font></p>';
					echo '</div>';
				}
			}
		}

	} # if 'admin_checks'
?>

<!-- Autofocus JS -->
<?php if ( ON == config_get( 'use_javascript' ) ) { ?>
<script type="text/javascript" language="JavaScript">
<!--
	window.document.login_form.username.focus();
// -->
</script>
<?php } ?>

<?php html_page_bottom1a( __FILE__ ) ?>
