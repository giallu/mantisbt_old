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

$t_core_dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;

/**
 *  Get a permalink for the current active filter.  The results of using these fields by other users
 *  can be inconsistent with the original results due to fields like "Myself", "Current Project",
 *  and due to access level.
 * @param array $p_custom_filter
 * @return string the search.php?xxxx or an empty string if no criteria applied.
 */
function filter_get_url( $p_custom_filter ) {
	$t_query = array();

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_PROJECT_ID] ) ) {
		$t_project_id = $p_custom_filter[FILTER_PROPERTY_PROJECT_ID];

		if( count( $t_project_id ) == 1 && $t_project_id[0] == META_FILTER_CURRENT ) {
			$t_project_id = array(
				helper_get_current_project(),
			);
		}

		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_PROJECT_ID, $t_project_id );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_FREE_TEXT] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_FREE_TEXT, $p_custom_filter[FILTER_PROPERTY_FREE_TEXT] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_CATEGORY] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_CATEGORY, $p_custom_filter[FILTER_PROPERTY_CATEGORY] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_REPORTER_ID] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_REPORTER_ID, $p_custom_filter[FILTER_PROPERTY_REPORTER_ID] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_STATUS_ID] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_STATUS_ID, $p_custom_filter[FILTER_PROPERTY_STATUS_ID] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_MONITOR_USER_ID] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_MONITOR_USER_ID, $p_custom_filter[FILTER_PROPERTY_MONITOR_USER_ID] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_HANDLER_ID] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_HANDLER_ID, $p_custom_filter[FILTER_PROPERTY_HANDLER_ID] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_NOTE_USER_ID] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_NOTE_USER_ID, $p_custom_filter[FILTER_PROPERTY_NOTE_USER_ID] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_SEVERITY_ID] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_SEVERITY_ID, $p_custom_filter[FILTER_PROPERTY_SEVERITY_ID] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_RESOLUTION_ID] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_RESOLUTION_ID, $p_custom_filter[FILTER_PROPERTY_RESOLUTION_ID] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_PRIORITY_ID] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_PRIORITY_ID, $p_custom_filter[FILTER_PROPERTY_PRIORITY_ID] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_VIEW_STATE_ID] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_VIEW_STATE_ID, $p_custom_filter[FILTER_PROPERTY_VIEW_STATE_ID] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_SHOW_STICKY_ISSUES] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_SHOW_STICKY_ISSUES, $p_custom_filter[FILTER_PROPERTY_SHOW_STICKY_ISSUES] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_PRODUCT_VERSION] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_PRODUCT_VERSION, $p_custom_filter[FILTER_PROPERTY_PRODUCT_VERSION] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_PRODUCT_BUILD] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_PRODUCT_BUILD, $p_custom_filter[FILTER_PROPERTY_PRODUCT_BUILD] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_FIXED_IN_VERSION] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_FIXED_IN_VERSION, $p_custom_filter[FILTER_PROPERTY_FIXED_IN_VERSION] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_TARGET_VERSION] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_TARGET_VERSION, $p_custom_filter[FILTER_PROPERTY_TARGET_VERSION] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_SORT_FIELD_NAME] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_SORT_FIELD_NAME, $p_custom_filter[FILTER_PROPERTY_SORT_FIELD_NAME] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_SORT_DIRECTION] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_SORT_DIRECTION, $p_custom_filter[FILTER_PROPERTY_SORT_DIRECTION] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_SEARCH_ISSUES_PER_PAGE] ) ) {
		if( $p_custom_filter[FILTER_SEARCH_ISSUES_PER_PAGE] != config_get( 'default_limit_view' ) ) {
			$t_query[] = filter_encode_field_and_value( FILTER_PROPERTY_ISSUES_PER_PAGE, $p_custom_filter[FILTER_SEARCH_ISSUES_PER_PAGE] );
		}
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_HIGHLIGHT_CHANGED] ) ) {
		if( $p_custom_filter[FILTER_PROPERTY_HIGHLIGHT_CHANGED] != config_get( 'default_show_changed' ) ) {
			$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_HIGHLIGHT_CHANGED, $p_custom_filter[FILTER_PROPERTY_HIGHLIGHT_CHANGED] );
		}
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_HIDE_STATUS_ID] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_HIDE_STATUS_ID, $p_custom_filter[FILTER_PROPERTY_HIDE_STATUS_ID] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_NOT_ASSIGNED] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_NOT_ASSIGNED, $p_custom_filter[FILTER_PROPERTY_NOT_ASSIGNED] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_FILTER_BY_DATE] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_FILTER_BY_DATE, $p_custom_filter[FILTER_PROPERTY_FILTER_BY_DATE] );

		# The start and end dates are only applicable if filter by date is set.
		if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_START_DAY] ) ) {
			$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_START_DAY, $p_custom_filter[FILTER_PROPERTY_START_DAY] );
		}

		if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_END_DAY] ) ) {
			$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_END_DAY, $p_custom_filter[FILTER_PROPERTY_END_DAY] );
		}

		if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_START_MONTH] ) ) {
			$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_START_MONTH, $p_custom_filter[FILTER_PROPERTY_START_MONTH] );
		}

		if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_END_MONTH] ) ) {
			$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_END_MONTH, $p_custom_filter[FILTER_PROPERTY_END_MONTH] );
		}

		if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_START_YEAR] ) ) {
			$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_START_YEAR, $p_custom_filter[FILTER_PROPERTY_START_YEAR] );
		}

		if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_END_YEAR] ) ) {
			$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_END_YEAR, $p_custom_filter[FILTER_PROPERTY_END_YEAR] );
		}
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_RELATIONSHIP_TYPE] ) ) {
		if( $p_custom_filter[FILTER_PROPERTY_RELATIONSHIP_TYPE] != -1 ) {
			$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_RELATIONSHIP_TYPE, $p_custom_filter[FILTER_PROPERTY_RELATIONSHIP_TYPE] );
		}
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_RELATIONSHIP_BUG] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_RELATIONSHIP_BUG, $p_custom_filter[FILTER_PROPERTY_RELATIONSHIP_BUG] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_PLATFORM] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_PLATFORM, $p_custom_filter[FILTER_PROPERTY_PLATFORM] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_OS] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_OS, $p_custom_filter[FILTER_PROPERTY_OS] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_OS_BUILD] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_OS_BUILD, $p_custom_filter[FILTER_PROPERTY_OS_BUILD] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_TAG_STRING] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_TAG_STRING, $p_custom_filter[FILTER_PROPERTY_TAG_STRING] );
	}

	if( !filter_field_is_any( $p_custom_filter[FILTER_PROPERTY_TAG_SELECT] ) ) {
		$t_query[] = filter_encode_field_and_value( FILTER_SEARCH_TAG_SELECT, $p_custom_filter[FILTER_PROPERTY_TAG_SELECT] );
	}

	if( isset( $p_custom_filter['custom_fields'] ) ) {
		foreach( $p_custom_filter['custom_fields'] as $t_custom_field_id => $t_custom_field_values ) {
			if( !filter_field_is_any( $t_custom_field_values ) ) {
				$t_query[] = filter_encode_field_and_value( 'custom_field_' . $t_custom_field_id, $t_custom_field_values );
			}
		}
	}

	if( count( $t_query ) > 0 ) {
		$t_query_str = implode( $t_query, '&amp;' );
		$t_url = config_get( 'path' ) . 'search.php?' . $t_query_str;
	} else {
		$t_url = '';
	}

	return $t_url;
}

/**
 *  Encodes a field and it's value for the filter URL.  This handles the URL encoding
 *  and arrays.
 * @param string $p_field_name The field name.
 * @param string $p_field_value The field value (can be an array)
 * @return string url encoded string
 */
function filter_encode_field_and_value( $p_field_name, $p_field_value ) {
	$t_query_array = array();
	if( is_array( $p_field_value ) ) {
		$t_count = count( $p_field_value );
		if( $t_count > 1 ) {
			foreach( $p_field_value as $t_value ) {
				$t_query_array[] = urlencode( $p_field_name . '[]' ) . '=' . urlencode( $t_value );
			}
		}
		elseif( $t_count == 1 ) {
			$t_query_array[] = urlencode( $p_field_name ) . '=' . urlencode( $p_field_value[0] );
		}
	} else {
		$t_query_array[] = urlencode( $p_field_name ) . '=' . urlencode( $p_field_value );
	}

	return implode( $t_query_array, '&amp;' );
}

# ==========================================================================
# GENERAL FUNCTIONS                            						      =
# ==========================================================================
/**
 *  Checks the supplied value to see if it is an ANY value.
 * @param string $p_field_value - The value to check.
 * @return bool true for "ANY" values and false for others.  "ANY" means filter criteria not active.
 */
function filter_field_is_any( $p_field_value ) {
	if( is_array( $p_field_value ) ) {
		if( count( $p_field_value ) == 0 ) {
			return true;
		}

		foreach( $p_field_value as $t_value ) {
			if(( META_FILTER_ANY == $t_value ) && ( is_numeric( $t_value ) ) ) {
				return true;
			}
		}
	} else {
		if( is_string( $p_field_value ) && is_blank( $p_field_value ) ) {
			return true;
		}

		if( is_bool( $p_field_value ) && !$p_field_value ) {
			return true;
		}

		if(( META_FILTER_ANY == $p_field_value ) && ( is_numeric( $p_field_value ) ) ) {
			return true;
		}
	}

	return false;
}

/**
     *  Checks the supplied value to see if it is a NONE value.
     * @param string $p_field_value - The value to check.
     * @return bool true for "NONE" values and false for others.
 * @todo is a check for these necessary?  if ( ( $t_filter_value === 'none' ) || ( $t_filter_value === '[none]' ) )
     */
function filter_field_is_none( $p_field_value ) {
	if( is_array( $p_field_value ) ) {
		foreach( $p_field_value as $t_value ) {
			if(( META_FILTER_NONE == $t_value ) && ( is_numeric( $t_value ) ) ) {
				return true;
			}
		}
	} else {
		if( is_string( $p_field_value ) && is_blank( $p_field_value ) ) {
			return false;
		}

		if(( META_FILTER_NONE == $p_field_value ) && ( is_numeric( $p_field_value ) ) ) {
			return true;
		}
	}

	return false;
}

/**
 *  Checks the supplied value to see if it is a MYSELF value.
 * @param string $p_field_value - The value to check.
 * @return bool true for "MYSELF" values and false for others.
 */
function filter_field_is_myself( $p_field_value ) {
	return( META_FILTER_MYSELF == $p_field_value ? TRUE : FALSE );
}

/**
     * @param $p_count
     * @param $p_per_page
     * @return int
     */
function filter_per_page( $p_filter, $p_count, $p_per_page ) {
	$p_per_page = (( NULL == $p_per_page ) ? (int) $p_filter[FILTER_PROPERTY_ISSUES_PER_PAGE] : $p_per_page );
	$p_per_page = (( 0 == $p_per_page || -1 == $p_per_page ) ? $p_count : $p_per_page );

	return (int) abs( $p_per_page );
}

/**
     *  Use $p_count and $p_per_page to determine how many pages to split this list up into.
     *  For the sake of consistency have at least one page, even if it is empty.
     * @param $p_count
     * @param $p_per_page
     * @return $t_page_count
     */
function filter_page_count( $p_count, $p_per_page ) {
	$t_page_count = ceil( $p_count / $p_per_page );
	if( $t_page_count < 1 ) {
		$t_page_count = 1;
	}
	return $t_page_count;
}

/**
     *  Checks to make sure $p_page_number isn't past the last page.
     *  and that $p_page_number isn't before the first page
     *   @param $p_page_number
     *   @param $p_page_count
     */
function filter_valid_page_number( $p_page_number, $p_page_count ) {
	if( $p_page_number > $p_page_count ) {
		$p_page_number = $p_page_count;
	}

	if( $p_page_number < 1 ) {
		$p_page_number = 1;
	}
	return $p_page_number;
}

/**
     *  Figure out the offset into the db query, offset is which record to start querying from
     * @param int $p_page_number
     * @param int $p_per_page
     * @return int
     */
function filter_offset( $p_page_number, $p_per_page ) {
	return(( (int) $p_page_number -1 ) * (int) $p_per_page );
}

/**
 *  Make sure that our filters are entirely correct and complete (it is possible that they are not).
 *  We need to do this to cover cases where we don't have complete control over the filters given.s
 * @param array $p_filter_arr
 * @return mixed
 * @todo function needs to be abstracted
 */
function filter_ensure_valid_filter( $p_filter_arr ) {

	# extend current filter to add information passed via POST
	if( !isset( $p_filter_arr['_version'] ) ) {
		$p_filter_arr['_version'] = config_get( 'cookie_version' );
	}
	$t_cookie_vers = (int) substr( $p_filter_arr['_version'], 1 );
	if( substr( config_get( 'cookie_version' ), 1 ) > $t_cookie_vers ) {

		# if the version is old, update it
		$p_filter_arr['_version'] = config_get( 'cookie_version' );
	}
	if( !isset( $p_filter_arr['_view_type'] ) ) {
		$p_filter_arr['_view_type'] = gpc_get_string( 'view_type', 'simple' );
	}
	if( !isset( $p_filter_arr[FILTER_PROPERTY_ISSUES_PER_PAGE] ) ) {
		$p_filter_arr[FILTER_PROPERTY_ISSUES_PER_PAGE] = gpc_get_int( FILTER_PROPERTY_ISSUES_PER_PAGE, config_get( 'default_limit_view' ) );
	}
	if( !isset( $p_filter_arr[FILTER_PROPERTY_HIGHLIGHT_CHANGED] ) ) {
		$p_filter_arr[FILTER_PROPERTY_HIGHLIGHT_CHANGED] = config_get( 'default_show_changed' );
	}
	if( !isset( $p_filter_arr[FILTER_PROPERTY_SHOW_STICKY_ISSUES] ) ) {
		$p_filter_arr[FILTER_PROPERTY_SHOW_STICKY_ISSUES] = config_get( 'show_sticky_issues' );
	}
	if( !isset( $p_filter_arr[FILTER_PROPERTY_SORT_FIELD_NAME] ) ) {
		$p_filter_arr[FILTER_PROPERTY_SORT_FIELD_NAME] = "last_updated";
	}
	if( !isset( $p_filter_arr[FILTER_PROPERTY_SORT_DIRECTION] ) ) {
		$p_filter_arr[FILTER_PROPERTY_SORT_DIRECTION] = "DESC";
	}

	if( !isset( $p_filter_arr[FILTER_PROPERTY_PLATFORM] ) ) {
		$p_filter_arr[FILTER_PROPERTY_PLATFORM] = array(
			0 => META_FILTER_ANY,
		);
	}

	if( !isset( $p_filter_arr[FILTER_PROPERTY_OS] ) ) {
		$p_filter_arr[FILTER_PROPERTY_OS] = array(
			0 => META_FILTER_ANY,
		);
	}

	if( !isset( $p_filter_arr[FILTER_PROPERTY_OS_BUILD] ) ) {
		$p_filter_arr[FILTER_PROPERTY_OS_BUILD] = array(
			0 => META_FILTER_ANY,
		);
	}

	if( !isset( $p_filter_arr[FILTER_PROPERTY_PROJECT_ID] ) ) {
		$p_filter_arr[FILTER_PROPERTY_PROJECT_ID] = array(
			0 => META_FILTER_CURRENT,
		);
	}

	if( !isset( $p_filter_arr[FILTER_PROPERTY_START_MONTH] ) ) {
		$p_filter_arr[FILTER_PROPERTY_START_MONTH] = gpc_get_string( FILTER_PROPERTY_START_MONTH, date( 'm' ) );
	}
	if( !isset( $p_filter_arr[FILTER_PROPERTY_START_DAY] ) ) {
		$p_filter_arr[FILTER_PROPERTY_START_DAY] = gpc_get_string( FILTER_PROPERTY_START_DAY, 1 );
	}
	if( !isset( $p_filter_arr[FILTER_PROPERTY_START_YEAR] ) ) {
		$p_filter_arr[FILTER_PROPERTY_START_YEAR] = gpc_get_string( FILTER_PROPERTY_START_YEAR, date( 'Y' ) );
	}
	if( !isset( $p_filter_arr[FILTER_PROPERTY_END_MONTH] ) ) {
		$p_filter_arr[FILTER_PROPERTY_END_MONTH] = gpc_get_string( FILTER_PROPERTY_END_MONTH, date( 'm' ) );
	}
	if( !isset( $p_filter_arr[FILTER_PROPERTY_END_DAY] ) ) {
		$p_filter_arr[FILTER_PROPERTY_END_DAY] = gpc_get_string( FILTER_PROPERTY_END_DAY, date( 'd' ) );
	}
	if( !isset( $p_filter_arr[FILTER_PROPERTY_END_YEAR] ) ) {
		$p_filter_arr[FILTER_PROPERTY_END_YEAR] = gpc_get_string( FILTER_PROPERTY_END_YEAR, date( 'Y' ) );
	}
	if( !isset( $p_filter_arr[FILTER_PROPERTY_FREE_TEXT] ) ) {
		$p_filter_arr[FILTER_PROPERTY_FREE_TEXT] = '';
	}
	if( !isset( $p_filter_arr[FILTER_PROPERTY_NOT_ASSIGNED] ) ) {
		$p_filter_arr[FILTER_PROPERTY_NOT_ASSIGNED] = gpc_get_bool( FILTER_PROPERTY_NOT_ASSIGNED, false );
	}
	if( !isset( $p_filter_arr[FILTER_PROPERTY_FILTER_BY_DATE] ) ) {
		$p_filter_arr[FILTER_PROPERTY_FILTER_BY_DATE] = gpc_get_bool( FILTER_PROPERTY_FILTER_BY_DATE, false );
	}
	if( !isset( $p_filter_arr[FILTER_PROPERTY_VIEW_STATE_ID] ) ) {
		$p_filter_arr[FILTER_PROPERTY_VIEW_STATE_ID] = gpc_get( FILTER_PROPERTY_VIEW_STATE_ID, '' );
	}
	elseif( filter_field_is_any( $p_filter_arr[FILTER_PROPERTY_VIEW_STATE_ID] ) ) {
		$p_filter_arr[FILTER_PROPERTY_VIEW_STATE_ID] = META_FILTER_ANY;
	}
	if( !isset( $p_filter_arr[FILTER_PROPERTY_RELATIONSHIP_TYPE] ) ) {
		$p_filter_arr[FILTER_PROPERTY_RELATIONSHIP_TYPE] = gpc_get_int( FILTER_PROPERTY_RELATIONSHIP_TYPE, -1 );
	}
	if( !isset( $p_filter_arr[FILTER_PROPERTY_RELATIONSHIP_BUG] ) ) {
		$p_filter_arr[FILTER_PROPERTY_RELATIONSHIP_BUG] = gpc_get_int( FILTER_PROPERTY_RELATIONSHIP_BUG, 0 );
	}
	if( !isset( $p_filter_arr[FILTER_PROPERTY_TARGET_VERSION] ) ) {
		$p_filter_arr[FILTER_PROPERTY_TARGET_VERSION] = META_FILTER_ANY;
	}
	if( !isset( $p_filter_arr[FILTER_PROPERTY_TAG_STRING] ) ) {
		$p_filter_arr[FILTER_PROPERTY_TAG_STRING] = gpc_get_string( FILTER_PROPERTY_TAG_STRING, '' );
	}
	if( !isset( $p_filter_arr[FILTER_PROPERTY_TAG_SELECT] ) ) {
		$p_filter_arr[FILTER_PROPERTY_TAG_SELECT] = gpc_get_string( FILTER_PROPERTY_TAG_SELECT, '' );
	}

	$t_custom_fields = custom_field_get_ids();

	# @@@ (thraxisp) This should really be the linked ids, but we don't know the project
	$f_custom_fields_data = array();
	if( is_array( $t_custom_fields ) && ( sizeof( $t_custom_fields ) > 0 ) ) {
		foreach( $t_custom_fields as $t_cfid ) {
			if( is_array( gpc_get( 'custom_field_' . $t_cfid, null ) ) ) {
				$f_custom_fields_data[$t_cfid] = gpc_get_string_array( 'custom_field_' . $t_cfid, META_FILTER_ANY );
			} else {
				$f_custom_fields_data[$t_cfid] = gpc_get_string( 'custom_field_' . $t_cfid, META_FILTER_ANY );
				$f_custom_fields_data[$t_cfid] = array(
					$f_custom_fields_data[$t_cfid],
				);
			}
		}
	}

	# validate sorting
	$t_fields = helper_get_columns_to_view();
	$t_n_fields = count( $t_fields );
	for( $i = 0;$i < $t_n_fields;$i++ ) {
		if( isset( $t_fields[$i] ) && in_array( $t_fields[$i], array( 'selection', 'edit', 'bugnotes_count', 'attachment' ) ) ) {
			unset( $t_fields[$i] );
		}
	}
	$t_sort_fields = split( ',', $p_filter_arr['sort'] );
	$t_dir_fields = split( ',', $p_filter_arr['dir'] );
	for( $i = 0;$i < 2;$i++ ) {
		if( isset( $t_sort_fields[$i] ) ) {
			$t_drop = false;
			$t_sort = $t_sort_fields[$i];
			if( strpos( $t_sort, 'custom_' ) === 0 ) {
				if( false === custom_field_get_id_from_name( substr( $t_sort, strlen( 'custom_' ) ) ) ) {
					$t_drop = true;
				}
			} else {
				if( !in_array( $t_sort, $t_fields ) ) {
					$t_drop = true;
				}
			}
			if( !in_array( $t_dir_fields[$i], array( "ASC", "DESC" ) ) ) {
				$t_drop = true;
			}
			if( $t_drop ) {
				unset( $t_sort_fields[$i] );
				unset( $t_dir_fields[$i] );
			}
		}
	}
	if( count( $t_sort_fields ) > 0 ) {
		$p_filter_arr['sort'] = implode( ',', $t_sort_fields );
		$p_filter_arr['dir'] = implode( ',', $t_dir_fields );
	} else {
		$p_filter_arr['sort'] = "last_updated";
		$p_filter_arr['dir'] = "DESC";
	}

	# validate or filter junk from other fields
	$t_multi_select_list = array(
		FILTER_PROPERTY_CATEGORY => 'string',
		FILTER_PROPERTY_SEVERITY_ID => 'int',
		FILTER_PROPERTY_STATUS_ID => 'int',
		FILTER_PROPERTY_REPORTER_ID => 'int',
		FILTER_PROPERTY_HANDLER_ID => 'int',
		FILTER_PROPERTY_NOTE_USER_ID => 'int',
		FILTER_PROPERTY_RESOLUTION_ID => 'int',
		FILTER_PROPERTY_PRIORITY_ID => 'int',
		FILTER_PROPERTY_PRODUCT_BUILD => 'string',
		FILTER_PROPERTY_PRODUCT_VERSION => 'string',
		FILTER_PROPERTY_HIDE_STATUS_ID => 'int',
		FILTER_PROPERTY_FIXED_IN_VERSION => 'string',
		FILTER_PROPERTY_TARGET_VERSION => 'string',
		FILTER_PROPERTY_MONITOR_USER_ID => 'int',
		'show_profile' => 'int',
	);
	foreach( $t_multi_select_list as $t_multi_field_name => $t_multi_field_type ) {
		if( !isset( $p_filter_arr[$t_multi_field_name] ) ) {
			if( FILTER_PROPERTY_HIDE_STATUS_ID == $t_multi_field_name ) {
				$p_filter_arr[$t_multi_field_name] = array(
					config_get( 'hide_status_default' ),
				);
			}
			elseif( 'custom_fields' == $t_multi_field_name ) {
				$p_filter_arr[$t_multi_field_name] = array(
					$f_custom_fields_data,
				);
			} else {
				$p_filter_arr[$t_multi_field_name] = array(
					META_FILTER_ANY,
				);
			}
		} else {
			if( !is_array( $p_filter_arr[$t_multi_field_name] ) ) {
				$p_filter_arr[$t_multi_field_name] = array(
					$p_filter_arr[$t_multi_field_name],
				);
			}
			$t_checked_array = array();
			foreach( $p_filter_arr[$t_multi_field_name] as $t_filter_value ) {
				$t_filter_value = stripslashes( $t_filter_value );
				if(( $t_filter_value === 'any' ) || ( $t_filter_value === '[any]' ) ) {
					$t_filter_value = META_FILTER_ANY;
				}
				if(( $t_filter_value === 'none' ) || ( $t_filter_value === '[none]' ) ) {
					$t_filter_value = META_FILTER_NONE;
				}
				if( 'string' == $t_multi_field_type ) {
					$t_checked_array[] = db_prepare_string( $t_filter_value );
				}
				elseif( 'int' == $t_multi_field_type ) {
					$t_checked_array[] = db_prepare_int( $t_filter_value );
				}
				elseif( 'array' == $t_multi_field_type ) {
					$t_checked_array[] = $t_filter_value;
				}
			}
			$p_filter_arr[$t_multi_field_name] = $t_checked_array;
		}
	}

	if( is_array( $t_custom_fields ) && ( sizeof( $t_custom_fields ) > 0 ) ) {
		foreach( $t_custom_fields as $t_cfid ) {
			if( !isset( $p_filter_arr['custom_fields'][$t_cfid] ) ) {
				$p_filter_arr['custom_fields'][$t_cfid] = array(
					META_FILTER_ANY,
				);
			} else {
				if( !is_array( $p_filter_arr['custom_fields'][$t_cfid] ) ) {
					$p_filter_arr['custom_fields'][$t_cfid] = array(
						$p_filter_arr['custom_fields'][$t_cfid],
					);
				}
				$t_checked_array = array();
				foreach( $p_filter_arr['custom_fields'][$t_cfid] as $t_filter_value ) {
					$t_filter_value = stripslashes( $t_filter_value );
					if(( $t_filter_value === 'any' ) || ( $t_filter_value === '[any]' ) ) {
						$t_filter_value = META_FILTER_ANY;
					}
					$t_checked_array[] = db_prepare_string( $t_filter_value );
				}
				$p_filter_arr['custom_fields'][$t_cfid] = $t_checked_array;
			}
		}
	}

	# all of our filter values are now guaranteed to be there, and correct.
	return $p_filter_arr;
}

/**
 *  Get the standard filter that is to be used when no filter was previously saved.
 *  When creating specific filters, this can be used as a basis for the filter, where
 *  specific entries can be overridden.
 * @return mixed
 */
function filter_get_default() {
	$t_hide_status_default = config_get( 'hide_status_default' );
	$t_default_show_changed = config_get( 'default_show_changed' );

	$t_filter = array(
		FILTER_PROPERTY_CATEGORY => Array(
			'0' => META_FILTER_ANY,
		),
		FILTER_PROPERTY_SEVERITY_ID => Array(
			'0' => META_FILTER_ANY,
		),
		FILTER_PROPERTY_STATUS_ID => Array(
			'0' => META_FILTER_ANY,
		),
		FILTER_PROPERTY_HIGHLIGHT_CHANGED => $t_default_show_changed,
		FILTER_PROPERTY_REPORTER_ID => Array(
			'0' => META_FILTER_ANY,
		),
		FILTER_PROPERTY_HANDLER_ID => Array(
			'0' => META_FILTER_ANY,
		),
		FILTER_PROPERTY_PROJECT_ID => Array(
			'0' => META_FILTER_CURRENT,
		),
		FILTER_PROPERTY_RESOLUTION_ID => Array(
			'0' => META_FILTER_ANY,
		),
		FILTER_PROPERTY_PRODUCT_BUILD => Array(
			'0' => META_FILTER_ANY,
		),
		FILTER_PROPERTY_PRODUCT_VERSION => Array(
			'0' => META_FILTER_ANY,
		),
		FILTER_PROPERTY_HIDE_STATUS_ID => Array(
			'0' => $t_hide_status_default,
		),
		FILTER_PROPERTY_MONITOR_USER_ID => Array(
			'0' => META_FILTER_ANY,
		),
		FILTER_PROPERTY_SORT_FIELD_NAME => 'last_updated',
		FILTER_PROPERTY_SORT_DIRECTION => 'DESC',
		FILTER_PROPERTY_ISSUES_PER_PAGE => config_get( 'default_limit_view' ),
	);

	return filter_ensure_valid_filter( $t_filter );
}

/**
 *  Deserialize filter string
 * @param string $p_serialized_filter
 * @return mixed $t_filter array
 * @see filter_ensure_valid_filter
 */
function filter_deserialize( $p_serialized_filter ) {
	if( is_blank( $p_serialized_filter ) ) {
		return false;
	}

	# check to see if new cookie is needed
	$t_setting_arr = explode( '#', $p_serialized_filter, 2 );
	if(( $t_setting_arr[0] == 'v1' ) || ( $t_setting_arr[0] == 'v2' ) || ( $t_setting_arr[0] == 'v3' ) || ( $t_setting_arr[0] == 'v4' ) ) {

		# these versions can't be salvaged, they are too old to update
		return false;
	}

	# We shouldn't need to do this anymore, as filters from v5 onwards should cope with changing
	# filter indices dynamically
	$t_filter_array = array();
	if( isset( $t_setting_arr[1] ) ) {
		$t_filter_array = unserialize( $t_setting_arr[1] );
	} else {
		return false;
	}
	if( $t_filter_array['_version'] != config_get( 'cookie_version' ) ) {

		# if the version is not new enough, update it using defaults
		return filter_ensure_valid_filter( $t_filter_array );
	}

	return $t_filter_array;
}

/**
 *  Check if the filter cookie exists and is of the correct version.
 * @return bool
 */
function filter_is_cookie_valid() {
	$t_view_all_cookie_id = gpc_get_cookie( config_get( 'view_all_cookie' ), '' );
	$t_view_all_cookie = filter_db_get_filter( $t_view_all_cookie_id );

	# check to see if the cookie does not exist
	if( is_blank( $t_view_all_cookie ) ) {
		return false;
	}

	# check to see if new cookie is needed
	$t_setting_arr = explode( '#', $t_view_all_cookie, 2 );
	if(( $t_setting_arr[0] == 'v1' ) || ( $t_setting_arr[0] == 'v2' ) || ( $t_setting_arr[0] == 'v3' ) || ( $t_setting_arr[0] == 'v4' ) ) {
		return false;
	}

	# We shouldn't need to do this anymore, as filters from v5 onwards should cope with changing
	# filter indices dynamically
	$t_filter_cookie_arr = array();
	if( isset( $t_setting_arr[1] ) ) {
		$t_filter_cookie_arr = unserialize( $t_setting_arr[1] );
	} else {
		return false;
	}
	if( $t_filter_cookie_arr['_version'] != config_get( 'cookie_version' ) ) {
		return false;
	}

	return true;
}

/**
 *  Get the array fields specified by $p_filter_id
 *  using the cached row if it's available
 * @param int $p_filter_id
 * @return mixed a filter row
 */
function filter_get_row( $p_filter_id ) {
	return filter_cache_row( $p_filter_id );
}

/**
 *  Get the value of the filter field specified by filter id and field name
 * @param int $p_filter_id
 * @param string $p_field_name
 * @return string
 */
function filter_get_field( $p_filter_id, $p_field_name ) {
	$row = filter_get_row( $p_filter_id );

	if( isset( $row[$p_field_name] ) ) {
		return $row[$p_field_name];
	} else {
		error_parameters( $p_field_name );
		trigger_error( ERROR_DB_FIELD_NOT_FOUND, WARNING );
		return '';
	}
}

/**
 *  Add sort parameters to the query clauses
 * @param array $p_filter
 * @param bool $p_show_sticky
 * @param array $p_query_clauses
 * @return array $p_query_clauses
 */
function filter_get_query_sort_data( &$p_filter, $p_show_sticky, $p_query_clauses ) {
	$t_bug_table = db_get_table( 'mantis_bug_table' );
	$t_custom_field_string_table = db_get_table( 'mantis_custom_field_string_table' );

	# if sort is blank then default the sort and direction.  This is to fix the
	# symptoms of #3953.  Note that even if the main problem is fixed, we may
	# have to keep this code for a while to handle filters saved with this blank field.
	if( is_blank( $p_filter[FILTER_PROPERTY_SORT_FIELD_NAME] ) ) {
		$p_filter[FILTER_PROPERTY_SORT_FIELD_NAME] = 'last_updated';
		$p_filter[FILTER_PROPERTY_SORT_DIRECTION] = 'DESC';
	}

	$p_query_clauses['order'] = array();
	$t_sort_fields = split( ',', $p_filter[FILTER_PROPERTY_SORT_FIELD_NAME] );
	$t_dir_fields = split( ',', $p_filter[FILTER_PROPERTY_SORT_DIRECTION] );

	if(( 'on' == $p_filter[FILTER_PROPERTY_SHOW_STICKY_ISSUES] ) && ( NULL !== $p_show_sticky ) ) {
		$p_query_clauses['order'][] = "sticky DESC";
	}

	for( $i = 0;$i < count( $t_sort_fields );$i++ ) {
		$c_sort = db_prepare_string( $t_sort_fields[$i] );
		$c_dir = 'DESC' == $t_dir_fields[$i] ? 'DESC' : 'ASC';

		if( !in_array( $t_sort_fields[$i], array_slice( $t_sort_fields, $i + 1 ) ) ) {

			# if sorting by a custom field
			if( strpos( $c_sort, 'custom_' ) === 0 ) {
				$t_custom_field = substr( $c_sort, strlen( 'custom_' ) );
				$t_custom_field_id = custom_field_get_id_from_name( $t_custom_field );

				$c_cf_alias = str_replace( ' ', '_', $t_custom_field );
				$t_cf_table_alias = $t_custom_field_string_table . '_' . $t_custom_field_id;
				$t_cf_select = "$t_cf_table_alias.value $c_cf_alias";

				# check to be sure this field wasn't already added to the query.
				if( !in_array( $t_cf_select, $p_query_clauses['select'] ) ) {
					$p_query_clauses['select'][] = $t_cf_select;
					$p_query_clauses['join'][] = "LEFT JOIN $t_custom_field_string_table $t_cf_table_alias ON $t_bug_table.id  = $t_cf_table_alias.bug_id AND $t_cf_table_alias.field_id = $t_custom_field_id";
				}

				$p_query_clauses['order'][] = "$c_cf_alias $c_dir";
			} else {
				if ( 'last_updated' == $c_sort ) {
					$c_sort = "$t_bug_table.last_updated";
				}
				$p_query_clauses['order'][] = $c_sort . " " . $c_dir;
			}
		}
	}

	# add basic sorting if necessary
	if( !in_array( 'last_updated', $t_sort_fields ) ) {
		$p_query_clauses['order'][] = "$t_bug_table.last_updated DESC";
	}
	if( !in_array( 'date_submitted', $t_sort_fields ) ) {
		$p_query_clauses['order'][] = $t_bug_table . '.date_submitted DESC';
	}

	return $p_query_clauses;
}

/**
     *  Remove any duplicate values in certain elements of query_clauses
     *  Do not loop over query clauses as some keys may contain valid duplicate values.
     *  We basically want unique values for just the base query elements select, from, and join
     *  'where' and 'where_values' key should not have duplicates as that is handled earlier and applying
     *  array_unique here could cause problems with the query.
     * @param $p_query_clauses
     * @return $p_query_clauses
     */
function filter_unique_query_clauses( $p_query_clauses ) {
	$p_query_clauses['select'] = array_unique( $p_query_clauses['select'] );
	$p_query_clauses['from'] = array_unique( $p_query_clauses['from'] );
	$p_query_clauses['join'] = array_unique( $p_query_clauses['join'] );
	return $p_query_clauses;
}

/**
     *  Build a query with the query clauses array, query for bug count and return the result
     * @param array $p_query_clauses
     * @return int
     */
function filter_get_bug_count( $p_query_clauses ) {
	$t_bug_table = db_get_table( 'mantis_bug_table' );
	$p_query_clauses = filter_unique_query_clauses( $p_query_clauses );
	$t_select_string = "SELECT Count( DISTINCT $t_bug_table.id ) as idcnt ";
	$t_from_string = " FROM " . implode( ', ', $p_query_clauses['from'] );
	$t_join_string = (( count( $p_query_clauses['join'] ) > 0 ) ? implode( ' ', $p_query_clauses['join'] ) : '' );
	$t_where_string = (( count( $p_query_clauses['where'] ) > 0 ) ? 'WHERE ' . implode( ' AND ', $p_query_clauses['where'] ) : '' );
	$t_result = db_query_bound( "$t_select_string $t_from_string $t_join_string $t_where_string", $p_query_clauses['where_values'] );
	return db_result( $t_result );
}

/**
 * @todo Had to make all these parameters required because we can't use
 *  call-time pass by reference anymore.  I really preferred not having
 *  to pass all the params in if you didn't want to, but I wanted to get
 *  rid of the errors for now.  If we can think of a better way later
 *  (maybe return an object) that would be great.
 *
 * @param int $p_page_number the page you want to see (set to the actual page on return)
 * @param int $p_per_page the number of bugs to see per page (set to actual on return)
 *      -1   indicates you want to see all bugs
 *      null indicates you want to use the value specified in the filter
 * @param int $p_page_count you don't need to give a value here, the number of pages will be stored here on return
 * @param int $p_bug_count you don't need to give a value here, the number of bugs will be stored here on return
 * @param mixed $p_custom_filter Filter to use.
 * @param int $p_project_id project id to use in filtering.
 * @param int $p_user_id user id to use as current user when filtering.
 * @param bool $p_show_sticky get sticky issues only.
 */
function filter_get_bug_rows( &$p_page_number, &$p_per_page, &$p_page_count, &$p_bug_count, $p_custom_filter = null, $p_project_id = null, $p_user_id = null, $p_show_sticky = null ) {
	log_event( LOG_FILTERING, 'START NEW FILTER QUERY' );

	$t_bug_table = db_get_table( 'mantis_bug_table' );
	$t_bug_text_table = db_get_table( 'mantis_bug_text_table' );
	$t_bugnote_table = db_get_table( 'mantis_bugnote_table' );
	$t_category_table = db_get_table( 'mantis_category_table' );
	$t_custom_field_string_table = db_get_table( 'mantis_custom_field_string_table' );
	$t_bugnote_text_table = db_get_table( 'mantis_bugnote_text_table' );
	$t_project_table = db_get_table( 'mantis_project_table' );
	$t_bug_monitor_table = db_get_table( 'mantis_bug_monitor_table' );
	$t_limit_reporters = config_get( 'limit_reporters' );
	$t_bug_relationship_table = db_get_table( 'mantis_bug_relationship_table' );
	$t_report_bug_threshold = config_get( 'report_bug_threshold' );
	$t_where_param_count = 0;

	$t_current_user_id = auth_get_current_user_id();

	if( null === $p_user_id ) {
		$t_user_id = $t_current_user_id;
	} else {
		$t_user_id = $p_user_id;
	}

	$c_user_id = db_prepare_int( $t_user_id );

	if( null === $p_project_id ) {

		# @@@ If project_id is not specified, then use the project id(s) in the filter if set, otherwise, use current project.
		$t_project_id = helper_get_current_project();
	} else {
		$t_project_id = $p_project_id;
	}

	if( $p_custom_filter === null ) {

		# Prefer current_user_get_bug_filter() over user_get_filter() when applicable since it supports
		# cookies set by previous version of the code.
		if( $t_user_id == $t_current_user_id ) {
			$t_filter = current_user_get_bug_filter();
		} else {
			$t_filter = user_get_bug_filter( $t_user_id, $t_project_id );
		}
	} else {
		$t_filter = $p_custom_filter;
	}

	$t_filter = filter_ensure_valid_filter( $t_filter );

	if( false === $t_filter ) {
		return false;

		# signify a need to create a cookie
		# @@@ error instead?
	}

	$t_view_type = $t_filter['_view_type'];
	$t_where_clauses = array(
		"$t_project_table.enabled = " . db_param(),
		"$t_project_table.id = $t_bug_table.project_id",
	);
	$t_where_params = array(
		1,
	);
	$t_select_clauses = array(
		"$t_bug_table.*",
		"$t_bug_table.last_updated",
		"$t_bug_table.date_submitted",
	);

	$t_join_clauses = array();
	$t_from_clauses = array();

	// normalize the project filtering into an array $t_project_ids
	if( 'simple' == $t_view_type ) {
		log_event( LOG_FILTERING, 'Simple Filter' );
		$t_project_ids = array(
			$t_project_id,
		);
		$t_include_sub_projects = true;
	} else {
		log_event( LOG_FILTERING, 'Advanced Filter' );
		if( !is_array( $t_filter[FILTER_PROPERTY_PROJECT_ID] ) ) {
			$t_project_ids = array(
				db_prepare_int( $t_filter[FILTER_PROPERTY_PROJECT_ID] ),
			);
		} else {
			$t_project_ids = array_map( 'db_prepare_int', $t_filter[FILTER_PROPERTY_PROJECT_ID] );
		}

		$t_include_sub_projects = (( count( $t_project_ids ) == 1 ) && ( $t_project_ids[0] == META_FILTER_CURRENT ) );
	}

	log_event( LOG_FILTERING, 'project_ids = @P' . implode( ', @P', $t_project_ids ) );
	log_event( LOG_FILTERING, 'include sub-projects = ' . ( $t_include_sub_projects ? '1' : '0' ) );

	// if the array has ALL_PROJECTS, then reset the array to only contain ALL_PROJECTS.
	// replace META_FILTER_CURRENT with the actualy current project id.
	$t_all_projects_found = false;
	$t_new_project_ids = array();
	foreach( $t_project_ids as $t_pid ) {
		if( $t_pid == META_FILTER_CURRENT ) {
			$t_pid = $t_project_id;
		}

		if( $t_pid == ALL_PROJECTS ) {
			$t_all_projects_found = true;
			log_event( LOG_FILTERING, 'all projects selected' );
			break;
		}

		// filter out inaccessible projects.
		if( !access_has_project_level( VIEWER, $t_pid, $t_user_id ) ) {
			continue;
		}

		$t_new_project_ids[] = $t_pid;
	}

	$t_projects_query_required = true;
	if( $t_all_projects_found ) {
		if( user_is_administrator( $t_user_id ) ) {
			log_event( LOG_FILTERING, 'all projects + administrator, hence no project filter.' );
			$t_projects_query_required = false;
		} else {
			$t_project_ids = user_get_accessible_projects( $t_user_id );
		}
	} else {
		$t_project_ids = $t_new_project_ids;
	}

	if( $t_projects_query_required ) {

		// expand project ids to include sub-projects
		if( $t_include_sub_projects ) {
			$t_top_project_ids = $t_project_ids;

			foreach( $t_top_project_ids as $t_pid ) {
				log_event( LOG_FILTERING, 'Getting sub-projects for project id @P' . $t_pid );
				$t_project_ids = array_merge( $t_project_ids, user_get_all_accessible_subprojects( $t_user_id, $t_pid ) );
			}

			$t_project_ids = array_unique( $t_project_ids );
		}

		// if no projects are accessible, then return an empty array.
		if( count( $t_project_ids ) == 0 ) {
			log_event( LOG_FILTERING, 'no accessible projects' );
			return array();
		}

		log_event( LOG_FILTERING, 'project_ids after including sub-projects = @P' . implode( ', @P', $t_project_ids ) );

		// this array is to be populated with project ids for which we only want to show public issues.  This is due to the limited
		// access of the current user.
		$t_public_only_project_ids = array();

		// this array is populated with project ids that the current user has full access to.
		$t_private_and_public_project_ids = array();

		$t_access_required_to_view_private_bugs = config_get( 'private_bug_threshold' );
		foreach( $t_project_ids as $t_pid ) {
			if( access_has_project_level( $t_access_required_to_view_private_bugs, $t_pid, $t_user_id ) ) {
				$t_private_and_public_project_ids[] = $t_pid;
			} else {
				$t_public_only_project_ids[] = $t_pid;
			}
		}

		log_event( LOG_FILTERING, 'project_ids (with public/private access) = @P' . implode( ', @P', $t_private_and_public_project_ids ) );
		log_event( LOG_FILTERING, 'project_ids (with public access) = @P' . implode( ', @P', $t_public_only_project_ids ) );

		$t_count_private_and_public_project_ids = count( $t_private_and_public_project_ids );
		if( $t_count_private_and_public_project_ids == 1 ) {
			$t_private_and_public_query = "( $t_bug_table.project_id = " . $t_private_and_public_project_ids[0] . " )";
		}
		elseif( $t_count_private_and_public_project_ids > 1 ) {
			$t_private_and_public_query = "( $t_bug_table.project_id in (" . implode( ', ', $t_private_and_public_project_ids ) . ") )";
		} else {
			$t_private_and_public_query = null;
		}

		$t_count_public_only_project_ids = count( $t_public_only_project_ids );
		$t_public_view_state_check = "( ( $t_bug_table.view_state = " . VS_PUBLIC . " ) OR ( $t_bug_table.reporter_id = $t_user_id ) )";
		if( $t_count_public_only_project_ids == 1 ) {
			$t_public_only_query = "( ( $t_bug_table.project_id = " . $t_public_only_project_ids[0] . " ) AND $t_public_view_state_check )";
		}
		elseif( $t_count_public_only_project_ids > 1 ) {
			$t_public_only_query = "( ( $t_bug_table.project_id in (" . implode( ', ', $t_public_only_project_ids ) . ") ) AND $t_public_view_state_check )";
		} else {
			$t_public_only_query = null;
		}

		// both queries can't be null, so we either have one of them or both.

		if( $t_private_and_public_query === null ) {
			$t_project_query = $t_public_only_query;
		} elseif( $t_public_only_query === null ) {
			$t_project_query = $t_private_and_public_query;
		} else {
			$t_project_query = "( $t_public_only_query OR $t_private_and_public_query )";
		}

		log_event( LOG_FILTERING, 'project query = ' . $t_project_query );
		array_push( $t_where_clauses, $t_project_query );
	}

	# view state
	$t_view_state = db_prepare_int( $t_filter[FILTER_PROPERTY_VIEW_STATE_ID] );
	if( !filter_field_is_any( $t_filter[FILTER_PROPERTY_VIEW_STATE_ID] ) ) {
		$t_view_state_query = "($t_bug_table.view_state=" . db_param() . ")";
		log_event( LOG_FILTERING, 'view_state query = ' . $t_view_state_query );
		$t_where_params[] = $t_view_state;
		array_push( $t_where_clauses, $t_view_state_query );
	} else {
		log_event( LOG_FILTERING, 'no view_state query' );
	}

	# reporter
	if( !filter_field_is_any( $t_filter[FILTER_PROPERTY_REPORTER_ID] ) ) {
		$t_clauses = array();

		foreach( $t_filter[FILTER_PROPERTY_REPORTER_ID] as $t_filter_member ) {
			if( filter_field_is_none( $t_filter_member ) ) {
				array_push( $t_clauses, "0" );
			} else {
				$c_reporter_id = db_prepare_int( $t_filter_member );
				if( filter_field_is_myself( $c_reporter_id ) ) {
					array_push( $t_clauses, $c_user_id );
				} else {
					array_push( $t_clauses, $c_reporter_id );
				}
			}
		}

		if( 1 < count( $t_clauses ) ) {
			$t_reporter_query = "( $t_bug_table.reporter_id in (" . implode( ', ', $t_clauses ) . ") )";
		} else {
			$t_reporter_query = "( $t_bug_table.reporter_id=$t_clauses[0] )";
		}

		log_event( LOG_FILTERING, 'reporter query = ' . $t_reporter_query );
		array_push( $t_where_clauses, $t_reporter_query );
	} else {
		log_event( LOG_FILTERING, 'no reporter query' );
	}

	# limit reporter
	# @@@ thraxisp - access_has_project_level checks greater than or equal to,
	#   this assumed that there aren't any holes above REPORTER where the limit would apply
	#
	if(( ON === $t_limit_reporters ) && ( !access_has_project_level( REPORTER + 1, $t_project_id, $t_user_id ) ) ) {
		$c_reporter_id = $c_user_id;
		$t_where_params[] = $c_reporter_id;
		array_push( $t_where_clauses, "($t_bug_table.reporter_id=" . db_param() . ")" );
	}

	# handler
	if( !filter_field_is_any( $t_filter[FILTER_PROPERTY_HANDLER_ID] ) ) {
		$t_clauses = array();

		foreach( $t_filter[FILTER_PROPERTY_HANDLER_ID] as $t_filter_member ) {
			if( filter_field_is_none( $t_filter_member ) ) {
				array_push( $t_clauses, 0 );
			} else {
				$c_handler_id = db_prepare_int( $t_filter_member );
				if( filter_field_is_myself( $c_handler_id ) ) {
					array_push( $t_clauses, $c_user_id );
				} else {
					array_push( $t_clauses, $c_handler_id );
				}
			}
		}

		if( 1 < count( $t_clauses ) ) {
			$t_handler_query = "( $t_bug_table.handler_id in (" . implode( ', ', $t_clauses ) . ") )";
		} else {
			$t_handler_query = "( $t_bug_table.handler_id=$t_clauses[0] )";
		}

		log_event( LOG_FILTERING, 'handler query = ' . $t_handler_query );
		array_push( $t_where_clauses, $t_handler_query );
	} else {
		log_event( LOG_FILTERING, 'no handler query' );
	}

	# category
	if( !filter_field_is_any( $t_filter[FILTER_PROPERTY_CATEGORY] ) ) {
		$t_clauses = array();

		foreach( $t_filter[FILTER_PROPERTY_CATEGORY] as $t_filter_member ) {
			if( filter_field_is_none( $t_filter_member ) ) {
			} else {
				array_push( $t_clauses, $t_filter_member );
			}
		}

		if( 1 < count( $t_clauses ) ) {
			$t_where_tmp = array();
			foreach( $t_clauses as $t_clause ) {
				$t_where_tmp[] = db_param();
				$t_where_params[] = $t_clause;
			}
			array_push( $t_where_clauses, "( $t_bug_table.category_id in ( SELECT id FROM $t_category_table WHERE name in (" . implode( ', ', $t_where_tmp ) . ") ) )" );
		} else {
			$t_where_params[] = $t_clauses[0];
			array_push( $t_where_clauses, "( $t_bug_table.category_id in ( SELECT id FROM $t_category_table WHERE name=" . db_param() . ") )" );
		}
	}

	# severity
	if( !filter_field_is_any( $t_filter[FILTER_PROPERTY_SEVERITY_ID] ) ) {
		$t_clauses = array();

		foreach( $t_filter[FILTER_PROPERTY_SEVERITY_ID] as $t_filter_member ) {
			$c_show_severity = db_prepare_int( $t_filter_member );
			array_push( $t_clauses, $c_show_severity );
		}
		if( 1 < count( $t_clauses ) ) {
			$t_where_tmp = array();
			foreach( $t_clauses as $t_clause ) {
				$t_where_tmp[] = db_param();
				$t_where_params[] = $t_clause;
			}
			array_push( $t_where_clauses, "( $t_bug_table.severity in (" . implode( ', ', $t_where_tmp ) . ") )" );
		} else {
			$t_where_params[] = $t_clauses[0];
			array_push( $t_where_clauses, "( $t_bug_table.severity=" . db_param() . " )" );
		}
	}

	# show / hide status
	# take a list of all available statuses then remove the ones that we want hidden, then make sure
	# the ones we want shown are still available
	$t_desired_statuses = array();
	$t_available_statuses = Mantis_Enum::getValues( config_get( 'status_enum_string' ) );

	if( 'simple' == $t_filter['_view_type'] ) {

		# simple filtering: if showing any, restrict by the hide status value, otherwise ignore the hide
		$t_any_found = false;
		$t_this_status = $t_filter[FILTER_PROPERTY_STATUS_ID][0];
		$t_this_hide_status = $t_filter[FILTER_PROPERTY_HIDE_STATUS_ID][0];

		if( filter_field_is_any( $t_this_status ) ) {
			foreach( $t_available_statuses as $t_this_available_status ) {
				if( $t_this_hide_status > $t_this_available_status ) {
					$t_desired_statuses[] = $t_this_available_status;
				}
			}
		} else {
			$t_desired_statuses[] = $t_this_status;
		}
	} else {
		# advanced filtering: ignore the hide
		if( filter_field_is_any( $t_filter[FILTER_PROPERTY_STATUS_ID] ) ) {
			$t_desired_statuses = array();
		} else {
			foreach( $t_filter[FILTER_PROPERTY_STATUS_ID] as $t_this_status ) {
				$t_desired_statuses[] = $t_this_status;
			}
		}
	}

	if( count( $t_desired_statuses ) > 0 ) {
		$t_clauses = array();

		foreach( $t_desired_statuses as $t_filter_member ) {
			$c_show_status = db_prepare_int( $t_filter_member );
			array_push( $t_clauses, $c_show_status );
		}
		if( 1 < count( $t_clauses ) ) {
			$t_where_tmp = array();
			foreach( $t_clauses as $t_clause ) {
				$t_where_tmp[] = db_param();
				$t_where_params[] = $t_clause;
			}
			array_push( $t_where_clauses, "( $t_bug_table.status in (" . implode( ', ', $t_where_tmp ) . ") )" );
		} else {
			$t_where_params[] = $t_clauses[0];
			array_push( $t_where_clauses, "( $t_bug_table.status=" . db_param() . " )" );
		}
	}

	# resolution
	if( !filter_field_is_any( $t_filter[FILTER_PROPERTY_RESOLUTION_ID] ) ) {
		$t_clauses = array();

		foreach( $t_filter[FILTER_PROPERTY_RESOLUTION_ID] as $t_filter_member ) {
			$c_show_resolution = db_prepare_int( $t_filter_member );
			array_push( $t_clauses, $c_show_resolution );
		}
		if( 1 < count( $t_clauses ) ) {
			$t_where_tmp = array();
			foreach( $t_clauses as $t_clause ) {
				$t_where_tmp[] = db_param();
				$t_where_params[] = $t_clause;
			}
			array_push( $t_where_clauses, "( $t_bug_table.resolution in (" . implode( ', ', $t_where_tmp ) . ") )" );
		} else {
			$t_where_params[] = $t_clauses[0];
			array_push( $t_where_clauses, "( $t_bug_table.resolution=" . db_param() . " )" );
		}
	}

	# priority
	if( !filter_field_is_any( $t_filter[FILTER_PROPERTY_PRIORITY_ID] ) ) {
		$t_clauses = array();

		foreach( $t_filter[FILTER_PROPERTY_PRIORITY_ID] as $t_filter_member ) {
			$c_show_priority = db_prepare_int( $t_filter_member );
			array_push( $t_clauses, $c_show_priority );
		}
		if( 1 < count( $t_clauses ) ) {
			$t_where_tmp = array();
			foreach( $t_clauses as $t_clause ) {
				$t_where_tmp[] = db_param();
				$t_where_params[] = $t_clause;
			}
			array_push( $t_where_clauses, "( $t_bug_table.priority in (" . implode( ', ', $t_where_tmp ) . ") )" );
		} else {
			$t_where_params[] = $t_clauses[0];
			array_push( $t_where_clauses, "( $t_bug_table.priority=" . db_param() . " )" );
		}
	}

	# product build
	if( !filter_field_is_any( $t_filter[FILTER_PROPERTY_PRODUCT_BUILD] ) ) {
		$t_clauses = array();

		foreach( $t_filter[FILTER_PROPERTY_PRODUCT_BUILD] as $t_filter_member ) {
			$t_filter_member = stripslashes( $t_filter_member );
			if( filter_field_is_none( $t_filter_member ) ) {
				array_push( $t_clauses, '' );
			} else {
				$c_show_build = db_prepare_string( $t_filter_member );
				array_push( $t_clauses, $c_show_build );
			}
		}
		if( 1 < count( $t_clauses ) ) {
			$t_where_tmp = array();
			foreach( $t_clauses as $t_clause ) {
				$t_where_tmp[] = db_param();
				$t_where_params[] = $t_clause;
			}
			array_push( $t_where_clauses, "( $t_bug_table.build in (" . implode( ', ', $t_where_tmp ) . ") )" );
		} else {
			$t_where_params[] = $t_clauses[0];
			array_push( $t_where_clauses, "( $t_bug_table.build=" . db_param() . " )" );
		}
	}

	# product version
	if( !filter_field_is_any( $t_filter[FILTER_PROPERTY_PRODUCT_VERSION] ) ) {
		$t_clauses = array();

		foreach( $t_filter[FILTER_PROPERTY_PRODUCT_VERSION] as $t_filter_member ) {
			$t_filter_member = stripslashes( $t_filter_member );
			if( filter_field_is_none( $t_filter_member ) ) {
				array_push( $t_clauses, '' );
			} else {
				$c_show_version = db_prepare_string( $t_filter_member );
				array_push( $t_clauses, $c_show_version );
			}
		}

		if( 1 < count( $t_clauses ) ) {
			$t_where_tmp = array();
			foreach( $t_clauses as $t_clause ) {
				$t_where_tmp[] = db_param();
				$t_where_params[] = $t_clause;
			}
			array_push( $t_where_clauses, "( $t_bug_table.version in (" . implode( ', ', $t_where_tmp ) . ") )" );
		} else {
			$t_where_params[] = $t_clauses[0];
			array_push( $t_where_clauses, "( $t_bug_table.version=" . db_param() . " )" );
		}
	}

	# profile
	if( !filter_field_is_any( $t_filter['show_profile'] ) ) {
		$t_clauses = array();

		foreach( $t_filter['show_profile'] as $t_filter_member ) {
			$t_filter_member = stripslashes( $t_filter_member );
			if( filter_field_is_none( $t_filter_member ) ) {
				array_push( $t_clauses, "0" );
			} else {
				$c_show_profile = db_prepare_int( $t_filter_member );
				array_push( $t_clauses, "$c_show_profile" );
			}
		}
		if( 1 < count( $t_clauses ) ) {
			$t_where_tmp = array();
			foreach( $t_clauses as $t_clause ) {
				$t_where_tmp[] = db_param();
				$t_where_params[] = $t_clause;
			}
			array_push( $t_where_clauses, "( $t_bug_table.profile_id in (" . implode( ', ', $t_where_tmp ) . ") )" );
		} else {
			$t_where_params[] = $t_clauses[0];
			array_push( $t_where_clauses, "( $t_bug_table.profile_id=" . db_param() . " )" );
		}
	}

	# platform
	if( !filter_field_is_any( $t_filter[FILTER_PROPERTY_PLATFORM] ) ) {
		$t_clauses = array();

		foreach( $t_filter[FILTER_PROPERTY_PLATFORM] as $t_filter_member ) {
			$t_filter_member = stripslashes( $t_filter_member );
			if( filter_field_is_none( $t_filter_member ) ) {
				array_push( $t_clauses, '' );
			} else {
				$c_platform = db_prepare_string( $t_filter_member );
				array_push( $t_clauses, $c_platform );
			}
		}

		if( 1 < count( $t_clauses ) ) {
			$t_where_tmp = array();
			foreach( $t_clauses as $t_clause ) {
				$t_where_tmp[] = db_param();
				$t_where_params[] = $t_clause;
			}
			array_push( $t_where_clauses, "( $t_bug_table.platform in (" . implode( ', ', $t_where_tmp ) . ") )" );
		} else {
			$t_where_params[] = $t_clauses[0];
			array_push( $t_where_clauses, "( $t_bug_table.platform = " . db_param() . " )" );
		}
	}

	# os
	if( !filter_field_is_any( $t_filter[FILTER_PROPERTY_OS] ) ) {
		$t_clauses = array();

		foreach( $t_filter[FILTER_PROPERTY_OS] as $t_filter_member ) {
			$t_filter_member = stripslashes( $t_filter_member );
			if( filter_field_is_none( $t_filter_member ) ) {
				array_push( $t_clauses, '' );
			} else {
				$c_os = db_prepare_string( $t_filter_member );
				array_push( $t_clauses, $c_os );
			}
		}

		if( 1 < count( $t_clauses ) ) {
			$t_where_tmp = array();
			foreach( $t_clauses as $t_clause ) {
				$t_where_tmp[] = db_param();
				$t_where_params[] = $t_clause;
			}
			array_push( $t_where_clauses, "( $t_bug_table.os in (" . implode( ', ', $t_where_tmp ) . ") )" );
		} else {
			$t_where_params[] = $t_clauses[0];
			array_push( $t_where_clauses, "( $t_bug_table.os = " . db_param() . " )" );
		}
	}

	# os_build
	if( !filter_field_is_any( $t_filter[FILTER_PROPERTY_OS_BUILD] ) ) {
		$t_clauses = array();

		foreach( $t_filter[FILTER_PROPERTY_OS_BUILD] as $t_filter_member ) {
			$t_filter_member = stripslashes( $t_filter_member );
			if( filter_field_is_none( $t_filter_member ) ) {
				array_push( $t_clauses, '' );
			} else {
				$c_os_build = db_prepare_string( $t_filter_member );
				array_push( $t_clauses, $c_os_build );
			}
		}

		if( 1 < count( $t_clauses ) ) {
			$t_where_tmp = array();
			foreach( $t_clauses as $t_clause ) {
				$t_where_tmp[] = db_param();
				$t_where_params[] = $t_clause;
			}
			array_push( $t_where_clauses, "( $t_bug_table.os_build in (" . implode( ', ', $t_where_tmp ) . ") )" );
		} else {
			$t_where_params[] = $t_clauses[0];
			array_push( $t_where_clauses, "( $t_bug_table.os_build = " . db_param() . " )" );
		}
	}

	# date filter
	if(( 'on' == $t_filter[FILTER_PROPERTY_FILTER_BY_DATE] ) && is_numeric( $t_filter[FILTER_PROPERTY_START_MONTH] ) && is_numeric( $t_filter[FILTER_PROPERTY_START_DAY] ) && is_numeric( $t_filter[FILTER_PROPERTY_START_YEAR] ) && is_numeric( $t_filter[FILTER_PROPERTY_END_MONTH] ) && is_numeric( $t_filter[FILTER_PROPERTY_END_DAY] ) && is_numeric( $t_filter[FILTER_PROPERTY_END_YEAR] ) ) {

		$t_start_string = $t_filter[FILTER_PROPERTY_START_YEAR] . "-" . $t_filter[FILTER_PROPERTY_START_MONTH] . "-" . $t_filter[FILTER_PROPERTY_START_DAY] . " 00:00:00";
		$t_end_string = $t_filter[FILTER_PROPERTY_END_YEAR] . "-" . $t_filter[FILTER_PROPERTY_END_MONTH] . "-" . $t_filter[FILTER_PROPERTY_END_DAY] . " 23:59:59";

		$t_where_params[] = $t_start_string;
		$t_where_params[] = $t_end_string;
		array_push( $t_where_clauses, "($t_bug_table.date_submitted BETWEEN " . db_param() . " AND " . db_param() . " )" );
	}

	# fixed in version
	if( !filter_field_is_any( $t_filter[FILTER_PROPERTY_FIXED_IN_VERSION] ) ) {
		$t_clauses = array();

		foreach( $t_filter[FILTER_PROPERTY_FIXED_IN_VERSION] as $t_filter_member ) {
			$t_filter_member = stripslashes( $t_filter_member );
			if( filter_field_is_none( $t_filter_member ) ) {
				array_push( $t_clauses, '' );
			} else {
				$c_fixed_in_version = db_prepare_string( $t_filter_member );
				array_push( $t_clauses, $c_fixed_in_version );
			}
		}
		if( 1 < count( $t_clauses ) ) {
			$t_where_tmp = array();
			foreach( $t_clauses as $t_clause ) {
				$t_where_tmp[] = db_param();
				$t_where_params[] = $t_clause;
			}
			array_push( $t_where_clauses, "( $t_bug_table.fixed_in_version in (" . implode( ', ', $t_where_tmp ) . ") )" );
		} else {
			$t_where_params[] = $t_clauses[0];
			array_push( $t_where_clauses, "( $t_bug_table.fixed_in_version=" . db_param() . " )" );
		}
	}

	# target version
	if( !filter_field_is_any( $t_filter[FILTER_PROPERTY_TARGET_VERSION] ) ) {
		$t_clauses = array();

		foreach( $t_filter[FILTER_PROPERTY_TARGET_VERSION] as $t_filter_member ) {
			$t_filter_member = stripslashes( $t_filter_member );
			if( filter_field_is_none( $t_filter_member ) ) {
				array_push( $t_clauses, '' );
			} else {
				$c_target_version = db_prepare_string( $t_filter_member );
				array_push( $t_clauses, $c_target_version );
			}
		}

		# echo var_dump( $t_clauses ); exit;
		if( 1 < count( $t_clauses ) ) {
			$t_where_tmp = array();
			foreach( $t_clauses as $t_clause ) {
				$t_where_tmp[] = db_param();
				$t_where_params[] = $t_clause;
			}
			array_push( $t_where_clauses, "( $t_bug_table.target_version in (" . implode( ', ', $t_where_tmp ) . ") )" );
		} else {
			$t_where_params[] = $t_clauses[0];
			array_push( $t_where_clauses, "( $t_bug_table.target_version=" . db_param() . " )" );
		}
	}

	# users monitoring a bug
	if( !filter_field_is_any( $t_filter[FILTER_PROPERTY_MONITOR_USER_ID] ) ) {
		$t_clauses = array();
		$t_table_name = 'user_monitor';
		array_push( $t_join_clauses, "LEFT JOIN $t_bug_monitor_table $t_table_name ON $t_table_name.bug_id = $t_bug_table.id" );

		foreach( $t_filter[FILTER_PROPERTY_MONITOR_USER_ID] as $t_filter_member ) {
			$c_user_monitor = db_prepare_int( $t_filter_member );
			if( filter_field_is_myself( $c_user_monitor ) ) {
				array_push( $t_clauses, $c_user_id );
			} else {
				array_push( $t_clauses, $c_user_monitor );
			}
		}
		if( 1 < count( $t_clauses ) ) {
			$t_where_tmp = array();
			foreach( $t_clauses as $t_clause ) {
				$t_where_tmp[] = db_param();
				$t_where_params[] = $t_clause;
			}
			array_push( $t_where_clauses, "( $t_table_name.user_id in (" . implode( ', ', $t_where_tmp ) . ") )" );
		} else {
			$t_where_params[] = $t_clauses[0];
			array_push( $t_where_clauses, "( $t_table_name.user_id=" . db_param() . " )" );
		}
	}

	# bug relationship
	$t_any_found = false;
	$c_rel_type = $t_filter[FILTER_PROPERTY_RELATIONSHIP_TYPE];
	$c_rel_bug = $t_filter[FILTER_PROPERTY_RELATIONSHIP_BUG];
	if( -1 == $c_rel_type || 0 == $c_rel_bug ) {
		$t_any_found = true;
	}
	if( !$t_any_found ) {
		# use the complementary type
		$t_comp_type = relationship_get_complementary_type( $c_rel_type );
		$t_clauses = array();
		$t_table_name = 'relationship';
		array_push( $t_join_clauses, "LEFT JOIN $t_bug_relationship_table $t_table_name ON $t_table_name.destination_bug_id = $t_bug_table.id" );
		array_push( $t_join_clauses, "LEFT JOIN $t_bug_relationship_table ${t_table_name}2 ON ${t_table_name}2.source_bug_id = $t_bug_table.id" );

		// get reverse relationships
		$t_where_params[] = $t_comp_type;
		$t_where_params[] = $c_rel_bug;
		$t_where_params[] = $c_rel_type;
		$t_where_params[] = $c_rel_bug;
		array_push( $t_clauses, "($t_table_name.relationship_type=" . db_param() . " AND $t_table_name.source_bug_id=" . db_param() . ")" );
		array_push( $t_clauses, "($t_table_name" . "2.relationship_type=" . db_param() . " AND $t_table_name" . "2.destination_bug_id=" . db_param() . ")" );
		array_push( $t_where_clauses, '(' . implode( ' OR ', $t_clauses ) . ')' );
	}

	# tags
	$c_tag_string = trim( $t_filter[FILTER_PROPERTY_TAG_STRING] );
	$c_tag_select = trim( $t_filter[FILTER_PROPERTY_TAG_SELECT] );
	if( is_blank( $c_tag_string ) && !is_blank( $c_tag_select ) && $c_tag_select != 0 ) {
		$t_tag = tag_get( $c_tag_select );
		$c_tag_string = $t_tag['name'];
	}

	if( !is_blank( $c_tag_string ) ) {
		$t_tags = tag_parse_filters( $c_tag_string );

		if( count( $t_tags ) ) {

			$t_tags_all = array();
			$t_tags_any = array();
			$t_tags_none = array();

			foreach( $t_tags as $t_tag_row ) {
				switch( $t_tag_row['filter'] ) {
					case 1:
						$t_tags_all[] = $t_tag_row;
						break;
					case 0:
						$t_tags_any[] = $t_tag_row;
						break;
					case -1:
						$t_tags_none[] = $t_tag_row;
						break;
				}
			}

			if( 0 < $t_filter[FILTER_PROPERTY_TAG_SELECT] && tag_exists( $t_filter[FILTER_PROPERTY_TAG_SELECT] ) ) {
				$t_tags_any[] = tag_get( $t_filter[FILTER_PROPERTY_TAG_SELECT] );
			}

			$t_bug_tag_table = db_get_table( 'mantis_bug_tag_table' );

			if( count( $t_tags_all ) ) {
				$t_clauses = array();
				foreach( $t_tags_all as $t_tag_row ) {
					array_push( $t_clauses, "$t_bug_table.id IN ( SELECT bug_id FROM $t_bug_tag_table WHERE $t_bug_tag_table.tag_id = $t_tag_row[id] )" );
				}
				array_push( $t_where_clauses, '(' . implode( ' AND ', $t_clauses ) . ')' );
			}

			if( count( $t_tags_any ) ) {
				$t_clauses = array();
				foreach( $t_tags_any as $t_tag_row ) {
					array_push( $t_clauses, "$t_bug_tag_table.tag_id = $t_tag_row[id]" );
				}
				array_push( $t_where_clauses, "$t_bug_table.id IN ( SELECT bug_id FROM $t_bug_tag_table WHERE ( " . implode( ' OR ', $t_clauses ) . ') )' );
			}

			if( count( $t_tags_none ) ) {
				$t_clauses = array();
				foreach( $t_tags_none as $t_tag_row ) {
					array_push( $t_clauses, "$t_bug_tag_table.tag_id = $t_tag_row[id]" );
				}
				array_push( $t_where_clauses, "$t_bug_table.id NOT IN ( SELECT bug_id FROM $t_bug_tag_table WHERE ( " . implode( ' OR ', $t_clauses ) . ') )' );
			}
		}
	}

	# note user id
	if( !filter_field_is_any( $t_filter[FILTER_PROPERTY_NOTE_USER_ID] ) ) {
		$t_bugnote_table_alias = 'mbnt';
		$t_clauses = array();
		array_push( $t_from_clauses, "$t_bugnote_table  $t_bugnote_table_alias" );
		array_push( $t_where_clauses, "( $t_bug_table.id = $t_bugnote_table_alias.bug_id )" );

		foreach( $t_filter[FILTER_PROPERTY_NOTE_USER_ID] as $t_filter_member ) {
			$c_note_user_id = db_prepare_int( $t_filter_member );
			if( filter_field_is_myself( $c_note_user_id ) ) {
				array_push( $t_clauses, $c_user_id );
			} else {
				array_push( $t_clauses, $c_note_user_id );
			}
		}
		if( 1 < count( $t_clauses ) ) {
			$t_where_tmp = array();
			foreach( $t_clauses as $t_clause ) {
				$t_where_tmp[] = db_param();
				$t_where_params[] = $t_clause;
			}
			array_push( $t_where_clauses, "( $t_bugnote_table_alias.reporter_id in (" . implode( ', ', $t_where_tmp ) . ") )" );
		} else {
			$t_where_params[] = $t_clauses[0];
			array_push( $t_where_clauses, "( $t_bugnote_table_alias.reporter_id=" . db_param() . " )" );
		}
	}

	# custom field filters
	if( ON == config_get( 'filter_by_custom_fields' ) ) {

		# custom field filtering
		# @@@ At the moment this gets the linked fields relating to the current project
		#     It should get the ones relating to the project in the filter or all projects
		#     if multiple projects.
		$t_custom_fields = custom_field_get_linked_ids( $t_project_id );

		foreach( $t_custom_fields as $t_cfid ) {
			$t_field_info = custom_field_cache_row( $t_cfid, true );
			if( !$t_field_info['filter_by'] ) {
				continue;

				# skip this custom field it shouldn't be filterable
			}

			$t_custom_where_clause = '';

			# Ignore all custom filters that are not set, or that are set to '' or "any"
			if( !filter_field_is_any( $t_filter['custom_fields'][$t_cfid] ) ) {
				$t_def = custom_field_get_definition( $t_cfid );
				$t_table_name = $t_custom_field_string_table . '_' . $t_cfid;

				# We need to filter each joined table or the result query will explode in dimensions
				# Each custom field will result in a exponential growth like Number_of_Issues^Number_of_Custom_Fields
				# and only after this process ends (if it is able to) the result query will be filtered
				# by the WHERE clause and by the DISTINCT clause
				$t_cf_join_clause = "LEFT JOIN $t_custom_field_string_table $t_table_name ON $t_table_name.bug_id = $t_bug_table.id AND $t_table_name.field_id = $t_cfid ";

				if( $t_def['type'] == CUSTOM_FIELD_TYPE_DATE ) {
					switch( $t_filter['custom_fields'][$t_cfid][0] ) {
						case CUSTOM_FIELD_DATE_ANY:
							break;
						case CUSTOM_FIELD_DATE_NONE:
							array_push( $t_join_clauses, $t_cf_join_clause );
							$t_custom_where_clause = '(( ' . $t_table_name . '.bug_id is null) OR ( ' . $t_table_name . '.value = 0)';
							break;
						case CUSTOM_FIELD_DATE_BEFORE:
							array_push( $t_join_clauses, $t_cf_join_clause );
							$t_custom_where_clause = '(( ' . $t_table_name . '.value != 0 AND (' . $t_table_name . '.value+0) < ' . ( $t_filter['custom_fields'][$t_cfid][2] ) . ')';
							break;
						case CUSTOM_FIELD_DATE_AFTER:
							array_push( $t_join_clauses, $t_cf_join_clause );
							$t_custom_where_clause = '( (' . $t_table_name . '.value+0) > ' . ( $t_filter['custom_fields'][$t_cfid][1] + 1 );
							break;
						default:
							array_push( $t_join_clauses, $t_cf_join_clause );
							$t_custom_where_clause = '( (' . $t_table_name . '.value+0) BETWEEN ' . $t_filter['custom_fields'][$t_cfid][1] . ' AND ' . $t_filter['custom_fields'][$t_cfid][2];
							break;
					}
				} else {

					array_push( $t_join_clauses, $t_cf_join_clause );

					$t_filter_array = array();
					foreach( $t_filter['custom_fields'][$t_cfid] as $t_filter_member ) {
						$t_filter_member = stripslashes( $t_filter_member );
						if( filter_field_is_none( $t_filter_member ) ) {

							# coerce filter value if selecting META_FILTER_NONE so it will match empty fields
							$t_filter_member = '';

							# but also add those _not_ present in the custom field string table
							array_push( $t_filter_array, "$t_bug_table.id NOT IN (SELECT bug_id FROM $t_custom_field_string_table WHERE field_id=$t_cfid)" );
						}

						switch( $t_def['type'] ) {
							case CUSTOM_FIELD_TYPE_MULTILIST:
							case CUSTOM_FIELD_TYPE_CHECKBOX:
								$t_where_params[] = '%|' . $t_filter_member . '|%';
								array_push( $t_filter_array, db_helper_like( "$t_table_name.value" ) );
								break;
							default:
								array_push( $t_filter_array, "$t_table_name.value = '" . db_prepare_string( $t_filter_member ) . "'" );
						}
					}
					$t_custom_where_clause .= '(' . implode( ' OR ', $t_filter_array );
				}
				if( !is_blank( $t_custom_where_clause ) ) {
					array_push( $t_where_clauses, $t_custom_where_clause . ')' );
				}
			}
		}
	}

	# Text search
	if( !is_blank( $t_filter[FILTER_PROPERTY_FREE_TEXT] ) ) {
		$c_search = '%' . $t_filter[FILTER_PROPERTY_FREE_TEXT] . '%';
		$t_textsearch_where_clause = '(' . db_helper_like( 'summary' ) . ' OR ' . db_helper_like( "$t_bug_text_table.description" ) . ' OR ' . db_helper_like( "$t_bug_text_table.steps_to_reproduce" ) . ' OR ' . db_helper_like( "$t_bug_text_table.additional_information" ) . ' OR ' . db_helper_like( "$t_bugnote_text_table.note" );

		$t_where_params[] = $c_search;
		$t_where_params[] = $c_search;
		$t_where_params[] = $c_search;
		$t_where_params[] = $c_search;
		$t_where_params[] = $c_search;

		if( is_numeric( $t_filter[FILTER_PROPERTY_FREE_TEXT] ) ) {
			$c_search_int = (int) $t_filter[FILTER_PROPERTY_FREE_TEXT];
			$t_textsearch_where_clause .= " OR $t_bug_table.id = " . db_param();
			$t_textsearch_where_clause .= " OR $t_bugnote_table.id = " . db_param();
			$t_where_params[] = $c_search_int;
			$t_where_params[] = $c_search_int;
		}
		$t_textsearch_where_clause .= " )";

		# add text query elements to arrays
		$t_from_clauses[] = "$t_bug_text_table";
		$t_where_clauses[] = "$t_bug_table.bug_text_id = $t_bug_text_table.id";
		$t_where_clauses[] = $t_textsearch_where_clause;
		$t_join_clauses[] = " LEFT JOIN $t_bugnote_table ON $t_bug_table.id = $t_bugnote_table.bug_id";
		$t_join_clauses[] = " LEFT JOIN $t_bugnote_text_table ON $t_bugnote_table.bugnote_text_id = $t_bugnote_text_table.id";
	}

	# End text search

	$t_from_clauses[] = $t_project_table;
	$t_from_clauses[] = $t_bug_table;

	$t_query_clauses['select'] = $t_select_clauses;
	$t_query_clauses['from'] = $t_from_clauses;
	$t_query_clauses['join'] = $t_join_clauses;
	$t_query_clauses['where'] = $t_where_clauses;
	$t_query_clauses['where_values'] = $t_where_params;
	$t_query_clauses = filter_get_query_sort_data( $t_filter, $p_show_sticky, $t_query_clauses );

	# assigning to $p_* for this function writes the values back in case the caller wants to know
	# Get the total number of bugs that meet the criteria.
	$p_bug_count = filter_get_bug_count( $t_query_clauses );
	if( 0 == $p_bug_count ) {
		return array();
	}
	$p_per_page = filter_per_page( $t_filter, $p_bug_count, $p_per_page );
	$p_page_count = filter_page_count( $p_bug_count, $p_per_page );
	$p_page_number = filter_valid_page_number( $p_page_number, $p_page_count );
	$t_offset = filter_offset( $p_page_number, $p_per_page );
	$t_query_clauses = filter_unique_query_clauses( $t_query_clauses );
	$t_select_string = "SELECT DISTINCT " . implode( ', ', $t_query_clauses['select'] );
	$t_from_string = " FROM " . implode( ', ', $t_query_clauses['from'] );
	$t_order_string = " ORDER BY " . implode( ', ', $t_query_clauses['order'] );
	$t_join_string = count( $t_query_clauses['join'] ) > 0 ? implode( ' ', $t_query_clauses['join'] ) : '';
	$t_where_string = count( $t_query_clauses['where'] ) > 0 ? 'WHERE ' . implode( ' AND ', $t_query_clauses['where'] ) : '';
	$t_result = db_query_bound( "$t_select_string $t_from_string $t_join_string $t_where_string $t_order_string", $t_query_clauses['where_values'], $p_per_page, $t_offset );
	$t_row_count = db_num_rows( $t_result );

	$t_id_array_lastmod = array();
	for( $i = 0;$i < $t_row_count;$i++ ) {
		$t_row = db_fetch_array( $t_result );
		$t_id_array_lastmod[] = (int) $t_row['id'];
		$t_rows[] = $t_row;
	}

	return filter_cache_result( $t_rows, $t_id_array_lastmod );
}

/**
 *  Cache the filter results with bugnote stats for later use
 * @param array $p_rows results of the filter query
 * @param array $p_id_array_lastmod array of bug ids
 * @return array
 */
function filter_cache_result( $p_rows, $p_id_array_lastmod ) {
	$t_bugnote_table = db_get_table( 'mantis_bugnote_table' );

	$t_id_array_lastmod = array_unique( $p_id_array_lastmod );
	$t_where_string = "WHERE $t_bugnote_table.bug_id in (" . implode( ", ", $t_id_array_lastmod ) . ")";
	$t_query = "SELECT DISTINCT bug_id,MAX(last_modified) as last_modified, COUNT(last_modified) as count FROM $t_bugnote_table $t_where_string GROUP BY bug_id";

	# perform query
	$t_result = db_query_bound( $t_query );
	$t_row_count = db_num_rows( $t_result );
	for( $i = 0;$i < $t_row_count;$i++ ) {
		$t_row = db_fetch_array( $t_result );
		$t_stats[$t_row['bug_id']] = $t_row;
	}

	$t_rows = array();
	foreach( $p_rows as $t_row ) {
		if( !isset( $t_stats[$t_row['id']] ) ) {
			$t_rows[] = bug_cache_database_result( $t_row, false );
		} else {
			$t_rows[] = bug_cache_database_result( $t_row, $t_stats[$t_row['id']] );
		}
	}
	return $t_rows;
}
# ==========================================================================
# CACHING
# ==========================================================================
/**
 * @internal SECURITY NOTE: cache globals are initialized here to prevent them
 *      being spoofed if register_globals is turned on.
 * 	We cache filter requests to reduce the number of SQL queries
 * @global mixed $g_cache_filter
 * @global mixed $g_cache_filter_db_filters
 */
$g_cache_filter = array();
$g_cache_filter_db_filters = array();

/**
 *  Cache a filter row if necessary and return the cached copy
 *  If the second parameter is true (default), trigger an error
 *  if the filter can't be found.  If the second parameter is
 *  false, return false if the filter can't be found.
 * @param int $p_filter_id
 * @param bool $p_trigger_errors
 * @return mixed
 */
function filter_cache_row( $p_filter_id, $p_trigger_errors = true ) {
	global $g_cache_filter;

	$c_filter_id = db_prepare_int( $p_filter_id );

	$t_filters_table = db_get_table( 'mantis_filters_table' );

	if( isset( $g_cache_filter[$c_filter_id] ) ) {
		return $g_cache_filter[$c_filter_id];
	}

	$query = 'SELECT *
				  FROM ' . $t_filters_table . '
				  WHERE id=' . db_param();
	$result = db_query_bound( $query, Array( $c_filter_id ) );

	if( 0 == db_num_rows( $result ) ) {
		if( $p_trigger_errors ) {
			error_parameters( $p_filter_id );
			trigger_error( ERROR_FILTER_NOT_FOUND, ERROR );
		} else {
			return false;
		}
	}

	$row = db_fetch_array( $result );

	$g_cache_filter[$c_filter_id] = $row;

	return $row;
}

/**
 *  Clear the filter cache (or just the given id if specified)
 * @param int $p_filter_id
 * @return bool
 */
function filter_clear_cache( $p_filter_id = null ) {
	global $g_cache_filter;

	if( null === $p_filter_id ) {
		$g_cache_filter = array();
	} else {
		$c_filter_id = db_prepare_int( $p_filter_id );
		unset( $g_cache_filter[$c_filter_id] );
	}

	return true;
}
