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

	if ( !function_exists( 'curl_setopt' ) ) {
		echo "curl extension not installed.";
		exit;
	}

	if ( !function_exists( 'simplexml_load_string' ) ) {
		echo "simplexml extension not installed.";
		exit;
	}
	
	$f_token = gpc_get_string( 'token', '' );

	# The token will be blank if the user pressed cancel and doesn't login with user id.
	# In this case or if open id is disabled, then re-direct back to the login page.
	if ( is_blank( $f_token ) || !MantisOpenId::isEnabled() ) {
		print_header_redirect( 'login_page.php' );
		exit;
	}

	$t_api_key = config_get( 'openid_api_key' );
	$t_url = 'https://' . config_get( 'openid_site_name' ) . '.rpxnow.com/';

	$rpx = new RPX( $t_api_key, $t_url );

	try
	{
		$t_auth_info = $rpx->auth_info( $f_token );
	}
	catch (Exception $ex)
	{
		echo $ex->getMessage();
		exit;
	}

	$t_display_name = (string)$t_auth_info->profile->displayName;
	$t_email = (string)$t_auth_info->profile->email;
	$t_identifier = (string)$t_auth_info->profile->identifier;
	$t_username = (string)$t_auth_info->profile->preferredUsername;
	$t_url = (string)$t_auth_info->profile->url;
	$t_primary_key = (string)$t_auth_info->profile->primaryKey;

	# ignore mapping
	#if ( !is_blank( $t_primary_key ) ) {
	#	$rpx->unmap( $t_identifier, $t_primary_key );
	#	$t_primary_key = '';
	#}
	
	$t_user_id = 0;

	# if the user is already associated with the open id used for login, then use the mapped user id.
	if ( !is_blank( $t_primary_key ) ) {
		$t_user_id = (integer)$t_primary_key;

		if ( !user_exists( $t_user_id ) ) {
			# user was deleted from the database, do the unmapping.
			try
			{
				$rpx->unmap( $t_identifier, $t_primary_key );
			}
			catch (Exception $ex)
			{
				// the unmapping is cleanup work, if fails, then continue.
			}

			$t_user_id = 0;
		} else {
			$t_username = user_get_field( $t_user_id, 'username' );
		}
	}

	# Either there was no previous mapping or the previous mapping was referring to a deleted user.
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

		# If a user id is found/created, then update rpxnow.		
		if ( $t_user_id != 0 ) {
			try
			{
				# register the mapping from open id to user id in rpxnow so next time it is
				# available without any database lookups.
				$rpx->map( $t_identifier, $t_user_id );
			}
			catch (Exception $ex)
			{
				echo $ex->getMessage();
				exit;
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
