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
require_once( dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'core.php' );

access_ensure_global_level( ADMINISTRATOR );

# --------------------
function helper_table_row_count( $p_table ) {
	$t_table = $p_table;
	$query = "SELECT COUNT(*) FROM $t_table";
	$result = db_query_bound( $query );
	$t_users = db_result( $result );

	return $t_users;
}

# --------------------
function print_table_stats( $p_table_name ) {
	$t_count = helper_table_row_count( $p_table_name );
	echo "$p_table_name = $t_count records<br />";
}

echo '<html><head><title>Mantis Database Statistics</title></head><body>';

echo '<h1>Mantis Database Statistics</h1>';

foreach( db_get_table_list() as $t_table ) {
	if( db_table_exists( $t_table ) ) {
		print_table_stats( $t_table );
	}
}

echo '</body></html>';
