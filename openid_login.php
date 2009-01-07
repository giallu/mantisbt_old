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
	 * This is the action page for the open id login.
	 *
	 * @package MantisBT
	 * @copyright Copyright (C) 2002 - 2009  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	 * @link http://www.mantisbt.org
	 */
	 /**
	  * Mantis Core API's
	  */
	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );


	if ( config_get( 'openid_enabled' ) !== ON ) {
		# if open id is disabled, then re-direct back to the login page.
		print_header_redirect( 'login_page.php' );
		exit;
	}

	require_once( 'Zend/OpenId/Consumer.php' );

	$status = "";
	if ( isset( $_POST['openid_action'] ) &&
		$_POST['openid_action'] == "Login" &&
		! empty($_POST['openid_identifier'] ) ) {
		$consumer = new Zend_OpenId_Consumer();
		if ( !$consumer->login( $_POST['openid_identifier'] ) ) {
			$status = "OpenID login failed.<br>";
		}
	} else if ( isset( $_GET['openid_mode'] ) ) {
		if ( $_GET['openid_mode'] == "id_res" ) {
			$consumer = new Zend_OpenId_Consumer();
			if ( $consumer->verify($_GET, $t_openid) ) {
				$status = 'VALID';
			} else {
				$status = 'INVALID';
			}
		} else if ( $_GET['openid_mode'] == "cancel" ) {
			$status = 'CANCELED';
		}
	}
	
	if ( $status !== 'VALID' ) {
		print_header_redirect( 'login_page.php' );
		exit;
	}

	$t_user_id = user_get_id_by_openid( $t_openid );
	
	if ( $t_user_id === false ) {
		# openID was valid, but no user is associated with it
		# redirect to login page, asking to fill in the login form or create a new user
		print_header_redirect( 'openid_login_page.php?openid_identifier=' . rawurlencode( $t_openid ) );
		exit;
	}
	else {
		$t_username = user_get_field( $t_user_id, 'username' );

	# try to acquire user data
	$t_display_name = (string)$t_auth_info->profile->displayName;
	$t_email = (string)$t_auth_info->profile->email;
	$t_identifier = (string)$t_auth_info->profile->identifier;
	$t_url = (string)$t_auth_info->profile->url;
	$t_primary_key = (string)$t_auth_info->profile->primaryKey;

	if ( $t_user_id == 0 ) {
		# Use the email address for getting matching ids.
		$t_map = user_get_id_name_map_by_email( $t_email );

		# If no matches, then create a new user.
		if ( $t_map === false || ( count( $t_map ) == 0 ) ) {

			# Only create users if signup is allowed.			
			if ( config_get( 'allow_signup' ) == ON ) {
				# If display name is available to us and is different than user name, then use display name
				# as user real name, otherwise leave the real name blank.
				if ( $t_username == $t_display_name ) {
					$t_real_name = '';
				} else {
					$t_real_name = $t_display_name;
				}
	
				# If the preferred username is not available, then add a number suffix.
				$t_suffix_count = 0;
				$t_original_username = $t_username;
				while ( !user_is_name_unique( $t_username ) ) {
					$t_suffix_count++;
					$t_username = $t_original_username . $t_suffix_count;
				}
	
				# Create random password
				$t_seed = $t_email . $t_username;
				$t_password = auth_generate_random_password( $t_seed );
	
				# Create the user
				$t_cookie = user_create( $t_username, $t_password, $t_email, /* access_level */ null, /* $p_protected */ false, /* $p_enabled */ true, $t_real_name, /* $p_send_verification_email */ false );
				if ( $t_cookie !== false ) {
					$t_user_id = user_get_id_by_name( $t_username );
				}
			}
		} else {
			# create a map with the username as the key and the id as the value.
			$t_map_name_as_key = array_flip( $t_map );

			# match for user name / email will take 1st priority 
			if ( isset( $t_map_name_as_key[$t_username] ) ) {
				$t_user_id = (integer)$t_map_name_as_key[$t_username];
			} else {
				# this will return the matching user with the highest access level.
				$t_user_id = user_get_id_by_email( $t_email );
			}
			
			# for protected users, don't associate them with open id (at least for now)
			if ( user_is_protected( $t_user_id ) ) {
				$t_user_id = 0;
			}
		}

	}
	}
	
	# log in the matched user id (if any).
	if ( $t_user_id == 0 ) {
		$t_logged_in = false;
	} else {
		# The login will fail if the user is disabled.
		$t_logged_in = auth_attempt_login( $t_username, null );
	}

	if ( $t_logged_in ) {
		$t_redirect_url = config_get( 'default_home_page' );
	} else {
		$t_redirect_url = 'login_page.php';		
	}

	print_header_redirect( $t_redirect_url );
