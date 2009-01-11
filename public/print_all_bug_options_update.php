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
	 * Updates printing prefs then redirect to print_all_bug_page_page.php
	 * @package MantisBT
	 * @copyright Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	 * @copyright Copyright (C) 2002 - 2009  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	 * @link http://www.mantisbt.org
	 */
	 /**
	  * Mantis Core API's
	  */
	require_once( 'core.php' );
	require( 'print_all_bug_options_inc.php' );

	# helper_ensure_post();

	auth_ensure_user_authenticated();

	$f_user_id		= gpc_get_int( 'user_id' );
	$f_redirect_url	= gpc_get_string( 'redirect_url' );

	# the check for the protected state is already done in the form, there is
	# no need to duplicate it here.

	# get the fields list
	$t_field_name_arr = get_field_names();
	$field_name_count = count($t_field_name_arr);

	# check the checkboxes
	for ($i=0 ; $i <$field_name_count ; $i++) {
		$t_name='print_'.strtolower(str_replace(' ','_',$t_field_name_arr[$i]));
		$t_flag = gpc_get( $t_name, null );

		if ( $t_flag === null ) {
			$t_prefs_arr[$i] = 0;
		} else {
			$t_prefs_arr[$i] = 1;
		}
	}

	# get user id
	$t_user_id = $f_user_id;

	$c_export = implode('',$t_prefs_arr);

	# update preferences
	$t_user_print_pref_table = db_get_table( 'mantis_user_print_pref_table' );
	$query = "UPDATE $t_user_print_pref_table
			SET print_pref=" . db_param() . "
			WHERE user_id=" . db_param();

	$result = db_query_bound( $query, Array( $c_export, $t_user_id ) );

	html_page_top1();
	html_meta_redirect( $f_redirect_url );
	html_page_top2();
	echo '<br /><div align="center">';

	if ( $result ) {
		print lang_get( 'operation_successful' );
	} else {
		print error_string( ERROR_GENERIC );
	}

	echo '<br />';
	print_bracket_link( $f_redirect_url, lang_get( 'proceed' ) );
	echo '<br /></div>';
	html_page_bottom1( __FILE__ );
?>
