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
	 * @package MantisBT
	 * @copyright Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	 * @copyright Copyright (C) 2002 - 2009  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	 * @link http://www.mantisbt.org
	 */
	 /**
	  * Mantis Core API's
	  */
	require_once( 'core.php' );
	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'compress_api.php' );
	require_once( $t_core_path.'filter_api.php' );
	require_once( $t_core_path.'current_user_api.php' );
	require_once( $t_core_path.'bug_api.php' );
	require_once( $t_core_path.'string_api.php' );
	require_once( $t_core_path.'date_api.php' );
	require_once( $t_core_path.'rss_api.php' );

	auth_ensure_user_authenticated();

	$t_query_arr = filter_db_get_available_queries();

	# Special case: if we've deleted our last query, we have nothing to show here.
	if ( sizeof( $t_query_arr ) < 1 ) {
		print_header_redirect( 'view_all_bug_page.php' );
	}

	compress_enable();

	html_page_top1();
	html_page_top2();

	$t_use_query_url = 'view_all_set.php?type=3&amp;source_query_id=';
	$t_delete_query_url = 'query_delete_page.php?source_query_id=';
	
	$t_rss_enabled = config_get( 'rss_enabled' );
?>
<br />
<div align="center">
<table class="width75" cellspacing="0">
<?php
	$t_column_count = 0;
	$t_max_column_count = 2;

	foreach( $t_query_arr as $t_id => $t_name ) {
		if ( $t_column_count == 0 ) {
			print '<tr ' . helper_alternate_class() . '>';
		}

		print '<td>';

		if ( OFF != $t_rss_enabled ) {
			# Use the "new" RSS link style.
			print_rss( rss_get_issues_feed_url( null, null, $t_id ), lang_get( 'rss' ) );
			echo ' ';
		}

		print '<a href="' . $t_use_query_url . db_prepare_int( $t_id ) . '">' . string_display( $t_name ) . '</a>';

		if ( filter_db_can_delete_filter( $t_id ) ) {
			echo ' ';
			print_button( $t_delete_query_url . db_prepare_int( $t_id ), lang_get( 'delete_query' ) );
		}

		print '</td>';

		$t_column_count++;
		if ( $t_column_count == $t_max_column_count ) {
			print '</tr>';
			$t_column_count = 0;
		}
	}

	# Tidy up this row
	if ( ( $t_column_count > 0 ) && ( $t_column_count < $t_max_column_count ) ) {
		for ( $i = $t_column_count; $i < $t_max_column_count; $i++ ) {
			print '<td>&nbsp;</td>';
		}
		print '</tr>';
	}
?>
</table>
</div>
<?php html_page_bottom1( __FILE__ ) ?>
