<?php
# Mantis - a php based bugtracking system

# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2007  Mantis Team   - mantisbt-dev@lists.sourceforge.net

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

	# --------------------------------------------------------
	# $Id: $
	# --------------------------------------------------------

$t_core_dir = dirname( __FILE__ ).DIRECTORY_SEPARATOR;
require_once( $t_core_dir . 'current_user_api.php' );
require_once( $t_core_dir . 'history_api.php' );
require_once( $t_core_dir . 'bug_api.php' );
require_once( $t_core_dir . 'user_api.php' );


/**
 * vote_add
 * 
 * @param integer $p_issue_id issue primary key
 * @param integer $p_weight impact of vote
 * @param integer $p_user_id user primary key
 */
function vote_add( $p_issue_id, $p_weight, $p_user_id = null )
{
	if ( $p_issue_id < 1 )
	{
		error_parameters( $p_issue_id );
		trigger_error( ERROR_BUG_NOT_FOUND , ERROR );
	}
	
	$t_issue_project = bug_get_field($p_issue_id, 'project_id');
	$t_vote_max_weight = vote_max_weight( $t_issue_project, $p_user_id );
	$t_unlimited = (VOTES_UNLIMITED_VOTES == $t_vote_max_weight);

	if ( ( $p_weight > $t_vote_max_weight && !$t_unlimited ) || $p_weight == 0 )
	{
		trigger_error( ERROR_VOTING_OVER_LIMIT, ERROR );
	}
	
	$t_mantis_bug_votes_table	= db_get_table( 'mantis_bug_votes_table' );

	$query = "INSERT INTO $t_mantis_bug_votes_table
		          		( issue_id, user_id, weight )
		          	 VALUES
		          		( " . db_param(0) . ", " . db_param(1) . ", " . db_param(2) . " )";
	db_query_bound( $query, Array( (int)$p_issue_id, (int)$p_user_id, (int)$p_weight ) );

	#update issue counters to keep in sync
	vote_updates_counters_for_issue( $p_issue_id );
	
	# log vote history
	history_log_event_special( $p_issue_id, BUGVOTE_ADDED );
	bug_update_date($p_issue_id);
}

/**
 * vote_delete
 *
 * @param integer $p_issue_id
 * @param integer $p_user_id
 */
function vote_delete( $p_issue_id, $p_user_id )
{
	if ( $p_issue_id < 1 )
	{
		error_parameters( $p_issue_id );
		trigger_error( ERROR_BUG_NOT_FOUND , ERROR );
	}
	if ( $p_user_id < 1 )
	{
		error_parameters( $p_user_id );
		trigger_error( ERROR_USER_NOT_FOUND , ERROR );
	}
	
	$t_mantis_bug_votes_table	= db_get_table( 'mantis_bug_votes_table' );

	# now remove vote from voting table
	$query = "DELETE FROM $t_mantis_bug_votes_table WHERE issue_id = " . db_param(0) . " AND user_id = " . db_param(1);
	db_query_bound($query, Array( (int)$p_issue_id, (int)$p_user_id ));
	
	#update issue counters to keep bug table in sync
	vote_updates_counters_for_issue( $p_issue_id );
	
	# log vote history
	history_log_event_special( $p_issue_id, BUGVOTE_DELETED );
	bug_update_date($p_issue_id);
}

/**
 * vote_delete_issue_votes
 * Deleting an issue should delete all associated votes.
 * This should only be called post issue delete
 * 
 * @param integer $p_issue_id issue primary key
 */
function vote_delete_issue_votes( $p_issue_id )
{
	if ( $p_issue_id < 1 )
	{
		error_parameters( $p_issue_id );
		trigger_error( ERROR_BUG_NOT_FOUND , ERROR );
	}
	
	$t_mantis_bug_votes_table	= db_get_table( 'mantis_bug_votes_table' );
	$query = "DELETE FROM $t_mantis_bug_votes_table WHERE issue_id = " . db_param(0);
	db_query_bound($query, Array( (int)$p_issue_id ));
}

/**
 * vote_delete_user_votes
 * Deleting a user should delete all associated votes.
 *
 * @param integer $p_user_id user primary key
 */
function vote_delete_user_votes( $p_user_id )
{
	if ( $p_user_id < 1 )
	{
		error_parameters( $p_user_id );
		trigger_error( ERROR_USER_NOT_FOUND , ERROR );
	}
	
	$votes = vote_get_user_votes( null, true, $p_user_id );
	foreach($votes as $vote)
	{
		vote_delete($vote['issue_id'], $p_user_id);
	}
}

/**
 * vote_get_user_votes
 *
 * @param integer $p_project_id
 * @param boolean $p_include_resolved
 * @param integer $p_user_id
 * @return array issues and thier weight array('issue_id'=>$p_issue_id, 'weight'=>$p_weight); 
 */
function vote_get_user_votes ($p_project_id = null, $p_include_resolved = true, $p_user_id = null)
{
	if ($p_user_id === null)
	{
		$p_user_id = auth_get_current_user_id();
	}
	
	$t_mantis_bug_votes_table	= db_get_table( 'mantis_bug_votes_table' );
	
	$query = "SELECT issue_id, weight FROM $t_mantis_bug_votes_table WHERE user_id = " . db_param(0);
	$result = db_query_bound($query, Array($p_user_id));

	$users = array();
	while ( $row = db_fetch_array( $result ) ) {

		$t_resolved = bug_is_resolved($row['issue_id']);
		
		if ($p_include_resolved || !$t_resolved )
		{
			
			if ( $p_project_id === null || $p_project_id == ALL_PROJECTS )
			{
				$users[] = $row;
			}
			else
			{
				$t_issue_project = bug_get_field($row['issue_id'], 'project_id');
				if ( $t_issue_project == $p_project_id )
				{
					$users[] = $row;
				}
			}
		}
	}
	return $users;
}

/**
 * vote_get_issue_votes
 * returns an array of user ids, weight
 * 
 * @param integer $p_issue_id issue primary key
 * @return array users and thier vote weight array('user_id'=>$p_user_id, 'weight'=>$p_weight); 
 */
function vote_get_issue_votes( $p_issue_id )
{
	if ( $p_issue_id < 1 )
	{
		error_parameters( $p_issue_id );
		trigger_error( ERROR_BUG_NOT_FOUND , ERROR );
	}
	
	$t_mantis_bug_votes_table	= db_get_table( 'mantis_bug_votes_table' );
	$query = "SELECT user_id, weight FROM $t_mantis_bug_votes_table WHERE issue_id = " . db_param(0);
	$result = db_query_bound($query, Array($p_issue_id));
	$t_issue_votes = array();
	while ( $row = db_fetch_array( $result ) ) {
		$t_issue_votes[] = $row;
	}
	return $t_issue_votes;
}

/**
 * vote_is_enabled
 * check whether voting is enabled on the given project
 * 
 * @param integer $p_project_id
 * @return boolean
 */
function vote_is_enabled( $p_project_id = null )
{
	if ($p_project_id === null)
	{
		$p_project_id = helper_get_current_project();
	}
	$t_enabled = ( config_get( 'voting_enabled', null, null, $p_project_id ) == ON );
	return $t_enabled;
}

/**
 * vote_can_vote
 * whether or not the user is allowed to vote on an issue
 *
 * @param integer $p_issue_id
 * @param integer $p_user_id
 * @return boolean
 */
function vote_can_vote( $p_issue_id, $p_user_id = null )
{
	$t_can_vote = access_has_bug_level( config_get( 'voting_place_vote_threshold' ),$p_issue_id , $p_user_id ); 
	return $t_can_vote;
}

/**
 * vote_can_view_vote_details
 * whether or not the user is allowed to view vote details
 *
 * @param integer $p_issue_id
 * @param integer $p_user_id
 * @return boolean
 */
function vote_can_view_vote_details( $p_issue_id, $p_user_id = null )
{
	$t_has_level = ( access_has_bug_level( config_get( 'voting_view_user_votes_threshold' ), $p_issue_id , $p_user_id ) );
	return $t_has_level;
}

/**
 * vote_exists
 * whether the user has placed a vote on a given issue or not
 * @param integer $p_issue_id
 * @param integer $p_user_id
 * @return boolean
 */
function vote_exists ( $p_issue_id, $p_user_id )
{
	$t_mantis_bug_votes_table	= db_get_table( 'mantis_bug_votes_table' );
	$query 	= "SELECT COUNT(*)
		          	FROM $t_mantis_bug_votes_table
		          	WHERE issue_id=" . db_param(0) . " AND user_id = " . db_param(1);
		$result	= db_query_bound( $query, Array( $p_issue_id, $p_user_id ) );

		if ( 0 == db_result( $result ) ) {
			return false;
		} else {
			return true;
		}
}

/**
 * vote_max_votes
 * the maximum number of votes the given user can cast across all issues
 * 
 * @param integer $p_user_id
 * @return integer
 */
function vote_max_votes( $p_user_id )
{
	$t_default_num_votes = config_get('voting_default_num_votes');
	
	if (is_array($t_default_num_votes))
	{
		ksort($t_default_num_votes); # relies on user levels being numeric
		$t_user_level = user_get_access_level( $p_user_id );
		foreach($t_default_num_votes as $t_vote_level => $t_votes)
		{
			if ($t_user_level >= $t_vote_level)
			{
				$t_num_votes = $t_votes;
				break;
			}
		}
	}
	else
	{
		$t_num_votes = intval($t_default_num_votes);
	}

	return $t_num_votes;
}

/**
 * vote_available_votes
 * the number of available votes a user can still cast on a given project
 * note this may also return VOTES_UNLIMITED_VOTES so you should always test for this return value
 * 
 * @param integer $p_project_id
 * @param integer $p_user_id
 * @return integer number of available votes, also VOTES_UNLIMITED_VOTES
 */
function vote_available_votes( $p_project_id = null, $p_user_id = null )
{
	if ($p_user_id === null)
	{
		$p_user_id = auth_get_current_user_id();
	}
	
	$t_max_votes = vote_max_votes( $p_user_id );
	
	
	if ($t_max_votes == 0)
	{
		return VOTES_UNLIMITED_VOTES;
	}
	else
	{
		$t_used_votes = vote_used_votes( $p_project_id, $p_user_id );
		
		$t_available_votes = $t_max_votes -  $t_used_votes;
		return $t_available_votes;
	}
}

/**
 * vote_used_votes
 * the number of votes already cast on a given project that are not resolved
 * 
 * @param integer $p_project_id
 * @param integer $p_user_id
 * @return integer
 */
function vote_used_votes( $p_project_id = null, $p_user_id = null )
{

	if ($p_user_id === null)
	{
		$p_user_id = auth_get_current_user_id();
	}
	
	if ($p_project_id === null)
	{
		$p_project_id = helper_get_current_project();
	}
	
	$t_per_project = config_get('voting_per_project');
	
	if ($t_per_project == ON)
	{
		$t_votes = vote_get_user_votes( $p_project_id, false, $p_user_id );	
	}
	else
	{
		$t_votes = vote_get_user_votes( ALL_PROJECTS , false, $p_user_id );
	}
	
	$t_weight_used = 0;
	foreach($t_votes as $t_vote)
	{
		if ($t_vote['weight']>0)
		{
			$t_weight_used += $t_vote['weight'];
		}
		else
		{
			$t_weight_used -= $t_vote['weight'];
		}
	}
	return $t_weight_used;
}

/**
 * vote_max_weight
 * the maximum weight a user can cast on a single vote right now - note this is different from vote_available_votes
 * takes in to consideration how many votes a user has remaining
 * returning whichever is the lesser, your available votes or you max vote weight
 * 
 * @param integer $p_project_id
 * @param integer $p_user_id
 * @return integer
 */
function vote_max_weight( $p_project_id = null, $p_user_id = null )
{
	if ($p_project_id === null)
	{
		$p_project_id = helper_get_current_project();
	}
	
	if ($p_user_id === null)
	{
		$p_user_id = auth_get_current_user_id();
	}
	
	$t_available_votes = vote_available_votes( $p_project_id, $p_user_id );
	$t_voting_max_vote_weight = config_get('voting_max_vote_weight');
	if (is_array($t_voting_max_vote_weight))
	{
		ksort($t_voting_max_vote_weight); # relies on user levels being numeric
		$t_user_level = user_get_access_level( $p_user_id );
		
		#find your maximum applicable voting weight 
		foreach($t_voting_max_vote_weight as $t_level => $t_max)
		{
			if ($t_user_level >= $t_level)
			{
				$t_voting_max_vote_weight = $t_max;
			}
		}
	}
	
	# return whichever is the lesser, your available votes or you max vote weight
	$t_voting_max_vote_weight = ($t_available_votes > $t_voting_max_vote_weight)?$t_voting_max_vote_weight:$t_available_votes;
	
	return $t_voting_max_vote_weight;
}

/**
 * vote_updates_counters_for_issue
 * updates a given issue/bug vote weight counters
 * should be called post any changes to votes on an issue
 * 
 * @param integer $p_issue_id
 */
function vote_updates_counters_for_issue( $p_issue_id )
{
	$c_issue_id   	= db_prepare_int( $p_issue_id );
	$t_issue_table	= db_get_table( 'mantis_bug_votes_table' );
	
	$t_bug = bug_get( $p_issue_id );
	
	$query 	= "SELECT COUNT(*) as voteCount
	          	FROM $t_issue_table
	          	WHERE issue_id=" . db_param();
	$t_count_result	= db_query_bound( $query, Array( $c_issue_id ) );
	$t_bug->votes_num_voters = (int)$t_count_result->fields['voteCount'];
	
	$query 	= "SELECT SUM(weight) as voteWeight
	          	FROM $t_issue_table
	          	WHERE weight > 0 AND issue_id=" . db_param();
	$t_positive_result	= db_query_bound( $query, Array( $c_issue_id ) );
	$t_bug->votes_positive = (int)$t_positive_result->fields['voteWeight'];
	          	
	$query 	= "SELECT SUM(weight) as voteWeight
	          	FROM $t_issue_table
	          	WHERE weight < 0 AND issue_id=" . db_param();
	$t_negative_result	= db_query_bound( $query, Array( $c_issue_id ) );
	$t_bug->votes_negative = (int)$t_negative_result->fields['voteWeight'];
	
	bug_update( $p_issue_id, $t_bug, false, true );
}
