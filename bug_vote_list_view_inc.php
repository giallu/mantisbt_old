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

# This include file prints out the list of users that have voted for the current
# bug.	$f_bug_id must be set to the bug id
$t_core_path = config_get( 'core_path' );
require_once( $t_core_path . 'vote_api.php' );
require_once( $t_core_path . 'collapse_api.php' );


$t_voting_enabled = vote_is_enabled();
$t_current_user_id = auth_get_current_user_id();

#
# Determine whether the voting section should be shown.
#

if ($t_voting_enabled) {
	
	$t_votes = vote_get_issue_votes( $f_bug_id );

	$t_votes_exist = count( $t_votes ) > 0;
	$t_can_view_vote_details = vote_can_view_vote_details($f_bug_id, $t_current_user_id);
	$t_can_vote = vote_can_vote($f_bug_id, $t_current_user_id);

	$t_show_votes = $t_votes_exist || $t_can_vote;
	
	$t_total_positive = bug_get_field( $f_bug_id, 'votes_positive' );
	$t_total_negative = bug_get_field( $f_bug_id, 'votes_negative' );
	$t_total_votes = $t_total_positive - $t_total_negative;
	
	$t_total_voters = bug_get_field( $f_bug_id, 'votes_num_voters' );
	
	$t_button_text = lang_get('vote_cast_button');
	$t_bug_id = string_attribute( $f_bug_id );
	
	$t_voting_weight_options = config_get( 'voting_weight_options' );
	asort($t_voting_weight_options);
	$t_voting_weight_default = config_get( 'voting_weight_default' );
	
	$t_issue_project = bug_get_field( $f_bug_id, 'project_id');
	$t_max_votes = vote_max_votes( $t_current_user_id );
	$t_used_votes = vote_used_votes( $t_issue_project );
	$t_unlimited = (VOTES_UNLIMITED_VOTES == $t_max_votes);
	
	$t_available_votes = vote_available_votes( $t_issue_project, $t_current_user_id );
	$t_voting_max_vote_weight = vote_max_weight( $t_issue_project, $t_current_user_id );
	
	$t_voting_per_project = config_get( 'voting_per_project' );
	
} else {
	$t_show_votes = false;
}
?>
<?php if ( $t_show_votes ) { # Voting Box	?>

<a name="votings" id="votings"></a>
<br />

<?php collapse_open( 'voting' );?>

<table class="width100" cellspacing="1">
	<tr>
		<td class="form-title" colspan="2">
		<?php collapse_icon( 'voting' ); ?>
		<?php echo lang_get('voting_this_issue') ?> 
	</td>
	</tr>

	<tr class="row-1">
		<td class="category" width="15%">Vote on issue</td>
		<td>
		<?php
		if ( $t_can_vote ) {
			if (vote_exists($f_bug_id, $t_current_user_id) ) { #show 'remove my vote' button
									
				if (bug_is_resolved($f_bug_id)  )
				{		
					echo lang_get('voted_and_resolved');
				}
				else if(bug_get_field($f_bug_id,'status') == ASSIGNED)
				{
					echo lang_get('voted_and_assigned');
				}
				else
				{
					html_button( 'bug_vote_delete.php',
									 lang_get( 'vote_delete_button' ),
									 array( 'bug_id' => $f_bug_id, 'action' => 'DELETE' ) );
				}
			
			}
			else {  # show 'add vote' button
			?>
				
			<form method="post" action="bug_vote_add.php">
			<?php if ( $t_available_votes > 0 || $t_unlimited ){ ?>
			<input type="submit" class="button" value="<?php echo $t_button_text ;?>" />
			<select name="vote_weight">
			<?php 
			foreach($t_voting_weight_options as $t_option_key => $t_option_value){
				$t_vote_cost = ($t_option_value>0)?$t_option_value:-$t_option_value; #normalise the weight
				if ( ( $t_voting_max_vote_weight >= $t_vote_cost || $t_unlimited ) && $t_vote_cost != 0){
			?>
				<option value="<?php echo $t_option_value?>"<?php echo($t_voting_weight_default==$t_option_value)?' selected':'' ?>><?php echo $t_option_key?></option>
			<?php }} ?>
			</select>
			
			<? } # available_votes>0 ?>
			
			(<?php 
			echo $t_used_votes ;
			if ( !$t_unlimited )
			{
				echo '/' . $t_max_votes;
			} 
			?> <?php echo lang_get('votes_used')?>, 
			<?php
			if ($t_available_votes == VOTES_UNLIMITED_VOTES)
			{
				echo lang_get('vote_unlimited');
			}
			else
			{
				echo $t_available_votes;
			}		 
			?> 
			<?php echo lang_get('votes_remain');?>)
			<input type="hidden" name="bug_id" value="<?php echo $t_bug_id; ?>" />
			</form>		
		<? 
			} #end vote_exists
		} #end can_vote 
		?>
		</td>
	</tr>
	<?php if ( $t_votes_exist ) {	?>
	<tr>
	<td class="category" width="15%">Summary</td>
	<td>
		<?php echo lang_get('votes_positive') ?> = <?php echo $t_total_positive;?><br>
		<?php echo lang_get('votes_negative') ?> = <?php echo $t_total_negative;?><br>
		<?php echo lang_get('vote_balance') ?> = <?php echo $t_total_votes; ?><br>
		<?php echo lang_get('vote_num_voters')?> = <?php echo $t_total_voters; ?>
		
	</td>
	</tr>
	
	<?php if ($t_can_view_vote_details){ ?>
	<tr class="row-2">
		<td class="category" width="15%">Voters List</td>
		<td>
		<?php	foreach($t_votes as $userVote){ ?>
			<div class="userVote">
				<?php echo user_get_name($userVote['user_id']) ?> <?php echo ($userVote['weight']>=1)?'+'.$userVote['weight']:$userVote['weight'] ?>
			</div>
		<?php } ?>
		</td>
	</tr>
	<?php
		} #end view_vote_details 
	} #end votes_exist
	?>
</table>

<?php collapse_closed( 'voting' ); ?>

<table class="width100" cellspacing="1">
	<tr>
		<td class="form-title">
		<?php collapse_icon( 'voting' );	?>
		<?php echo lang_get('voting_this_issue') ?> <span style="font-weight: normal;">(<?php echo lang_get('vote_balance') ?> = <?php echo $t_total_votes ?>, <?php echo lang_get('vote_num_voters')?> = <?php echo $t_total_voters ?>)</span>
		</td>
	</tr>
</table>

<?php	
	collapse_end( 'voting' );
} # If voting enabled
?>
