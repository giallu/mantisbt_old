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
 * @package CoreAPI
 * @subpackage FilterAPI
 * @copyright Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
 * @copyright Copyright (C) 2002 - 2009  Mantis Team   - mantisbt-dev@lists.sourceforge.net
 * @link http://www.mantisbt.org
 */

/**
 *  Add a filter to the database for the current user
 * @param int $p_project_id
 * @param bool $p_is_public
 * @param string $p_name
 * @param string $p_filter_string
 * @return int
 */
function filter_db_set_for_current_user( $p_project_id, $p_is_public, $p_name, $p_filter_string ) {
	$t_user_id = auth_get_current_user_id();
	$c_project_id = db_prepare_int( $p_project_id );
	$c_is_public = db_prepare_bool( $p_is_public, false );

	$t_filters_table = db_get_table( 'mantis_filters_table' );

	# check that the user can save non current filters (if required)
	if(( ALL_PROJECTS <= $c_project_id ) && ( !is_blank( $p_name ) ) && ( !access_has_project_level( config_get( 'stored_query_create_threshold' ) ) ) ) {
		return -1;
	}

	# ensure that we're not making this filter public if we're not allowed
	if( !access_has_project_level( config_get( 'stored_query_create_shared_threshold' ) ) ) {
		$c_is_public = db_prepare_bool( false );
	}

	# Do I need to update or insert this value?
	$query = "SELECT id FROM $t_filters_table
					WHERE user_id=" . db_param() . "
					AND project_id=" . db_param() . "
					AND name=" . db_param();
	$result = db_query_bound( $query, Array( $t_user_id, $c_project_id, $p_name ) );

	if( db_num_rows( $result ) > 0 ) {
		$row = db_fetch_array( $result );

		$query = "UPDATE $t_filters_table
					  SET is_public=" . db_param() . ",
						filter_string=" . db_param() . "
					  WHERE id=" . db_param();
		db_query_bound( $query, Array( $c_is_public, $p_filter_string, $row['id'] ) );

		return $row['id'];
	} else {
		$query = "INSERT INTO $t_filters_table
						( user_id, project_id, is_public, name, filter_string )
					  VALUES
						( " . db_param() . ", " . db_param() . ", " . db_param() . ", " . db_param() . ", " . db_param() . " )";
		db_query_bound( $query, Array( $t_user_id, $c_project_id, $c_is_public, $p_name, $p_filter_string ) );

		# Recall the query, we want the filter ID
		$query = "SELECT id
						FROM $t_filters_table
						WHERE user_id=" . db_param() . "
						AND project_id=" . db_param() . "
						AND name=" . db_param();
		$result = db_query_bound( $query, Array( $t_user_id, $c_project_id, $p_name ) );

		if( db_num_rows( $result ) > 0 ) {
			$row = db_fetch_array( $result );
			return $row['id'];
		}

		return -1;
	}
}

/**
 *  This function returns the filter string that is
 *  tied to the unique id parameter. If the user doesn't
 *  have permission to see this filter, the function
 *  returns null
 * @param int $p_filter_id
 * @param int $p_user_id
 * @return mixed
 */
function filter_db_get_filter( $p_filter_id, $p_user_id = null ) {
	global $g_cache_filter_db_filters;
	$t_filters_table = db_get_table( 'mantis_filters_table' );
	$c_filter_id = db_prepare_int( $p_filter_id );

	if( isset( $g_cache_filter_db_filters[$p_filter_id] ) ) {
		if( $g_cache_filter_db_filters[$p_filter_id] === false ) {
			return null;
		}
		return $g_cache_filter_db_filters[$p_filter_id];
	}

	if( null === $p_user_id ) {
		$t_user_id = auth_get_current_user_id();
	} else {
		$t_user_id = $p_user_id;
	}

	$query = "SELECT *
				  FROM $t_filters_table
				  WHERE id=" . db_param();
	$result = db_query_bound( $query, Array( $c_filter_id ) );

	if( db_num_rows( $result ) > 0 ) {
		$row = db_fetch_array( $result );

		if( $row['user_id'] != $t_user_id ) {
			if( $row['is_public'] != true ) {
				return null;
			}
		}

		# check that the user has access to non current filters
		if(( ALL_PROJECTS <= $row['project_id'] ) && ( !is_blank( $row['name'] ) ) && ( !access_has_project_level( config_get( 'stored_query_use_threshold', $row['project_id'], $t_user_id ) ) ) ) {
			return null;
		}

		$g_cache_filter_db_filters[$p_filter_id] = $row['filter_string'];
		return $row['filter_string'];
	} else {
		$g_cache_filter_db_filters[$p_filter_id] = false;
		return false;
	}
}

/**
 * @param int $p_project_id
 * @param int $p_user_id
 * @return int
 */
function filter_db_get_project_current( $p_project_id, $p_user_id = null ) {
	$t_filters_table = db_get_table( 'mantis_filters_table' );
	$c_project_id = db_prepare_int( $p_project_id );
	$c_project_id = $c_project_id * -1;

	if( null === $p_user_id ) {
		$c_user_id = auth_get_current_user_id();
	} else {
		$c_user_id = db_prepare_int( $p_user_id );
	}

	# we store current filters for each project with a special project index
	$query = "SELECT *
				  FROM $t_filters_table
				  WHERE user_id=" . db_param() . "
					AND project_id=" . db_param() . "
					AND name=" . db_param();
	$result = db_query_bound( $query, Array( $c_user_id, $c_project_id, '' ) );

	if( db_num_rows( $result ) > 0 ) {
		$row = db_fetch_array( $result );
		return $row['id'];
	}

	return null;
}

/**
 *  Query for the filter name using the filter id
 * @param int $p_filter_id
 * @return string
 */
function filter_db_get_name( $p_filter_id ) {
	$t_filters_table = db_get_table( 'mantis_filters_table' );
	$c_filter_id = db_prepare_int( $p_filter_id );

	$query = "SELECT *
				  FROM $t_filters_table
				  WHERE id=" . db_param();
	$result = db_query_bound( $query, Array( $c_filter_id ) );

	if( db_num_rows( $result ) > 0 ) {
		$row = db_fetch_array( $result );

		if( $row['user_id'] != auth_get_current_user_id() ) {
			if( $row['is_public'] != true ) {
				return null;
			}
		}

		return $row['name'];
	}

	return null;
}

/**
 *  Check if the current user has permissions to delete the stored query
 * @param $p_filter_id
 * @return bool
 */
function filter_db_can_delete_filter( $p_filter_id ) {
	$t_filters_table = db_get_table( 'mantis_filters_table' );
	$c_filter_id = db_prepare_int( $p_filter_id );
	$t_user_id = auth_get_current_user_id();

	# Administrators can delete any filter
	if( access_has_global_level( ADMINISTRATOR ) ) {
		return true;
	}

	$query = "SELECT id
				  FROM $t_filters_table
				  WHERE id=" . db_param() . "
				  AND user_id=" . db_param() . "
				  AND project_id!=" . db_param();

	$result = db_query_bound( $query, Array( $c_filter_id, $t_user_id, -1 ) );

	if( db_num_rows( $result ) > 0 ) {
		return true;
	}

	return false;
}

/**
 *  Delete the filter specified by $p_filter_id
 * @param $p_filter_id
 * @return bool
 */
function filter_db_delete_filter( $p_filter_id ) {
	$t_filters_table = db_get_table( 'mantis_filters_table' );
	$c_filter_id = db_prepare_int( $p_filter_id );
	$t_user_id = auth_get_current_user_id();

	if( !filter_db_can_delete_filter( $c_filter_id ) ) {
		return false;
	}

	$query = "DELETE FROM $t_filters_table
				  WHERE id=" . db_param();
	$result = db_query_bound( $query, Array( $c_filter_id ) );

	if( db_affected_rows( $result ) > 0 ) {
		return true;
	}

	return false;
}

/**
 *  Delete all the unnamed filters
 */
function filter_db_delete_current_filters() {
	$t_filters_table = db_get_table( 'mantis_filters_table' );
	$t_all_id = ALL_PROJECTS;

	$query = "DELETE FROM $t_filters_table
					WHERE project_id<=" . db_param() . "
					AND name=" . db_param();
	$result = db_query_bound( $query, Array( $t_all_id, '' ) );
}


/**
 * @param int $p_project_id
 * @param int $p_user_id
 * @return mixed
 */
function filter_db_get_available_queries( $p_project_id = null, $p_user_id = null ) {
	$t_filters_table = db_get_table( 'mantis_filters_table' );
	$t_overall_query_arr = array();

	if( null === $p_project_id ) {
		$t_project_id = helper_get_current_project();
	} else {
		$t_project_id = db_prepare_int( $p_project_id );
	}

	if( null === $p_user_id ) {
		$t_user_id = auth_get_current_user_id();
	} else {
		$t_user_id = db_prepare_int( $p_user_id );
	}

	# If the user doesn't have access rights to stored queries, just return
	if( !access_has_project_level( config_get( 'stored_query_use_threshold' ) ) ) {
		return $t_overall_query_arr;
	}

	# Get the list of available queries. By sorting such that public queries are
	# first, we can override any query that has the same name as a private query
	# with that private one
	$query = "SELECT * FROM $t_filters_table
					WHERE (project_id='$t_project_id'
					OR project_id='0')
					AND name!=''
					ORDER BY is_public DESC, name ASC";
	$result = db_query( $query );
	$query_count = db_num_rows( $result );

	for( $i = 0;$i < $query_count;$i++ ) {
		$row = db_fetch_array( $result );
		if(( $row['user_id'] == $t_user_id ) || db_prepare_bool( $row['is_public'] ) ) {
			$t_overall_query_arr[$row['id']] = $row['name'];
		}
	}

	$t_overall_query_arr = array_unique( $t_overall_query_arr );
	asort( $t_overall_query_arr );

	return $t_overall_query_arr;
}
