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

require_once( 'core.php' );
$t_core_path = config_get( 'core_path' );
require_once( $t_core_path.'current_user_api.php' );
require_once( $t_core_path.'vote_api.php' );
require_once( $t_core_path.'project_api.php' );

if ( current_user_is_anonymous() ) {
	access_denied();
}

$t_current_user_id = auth_get_current_user_id();
$t_resolved = config_get( 'bug_resolved_status_threshold' );
$f_show_all = gpc_get_bool( 'show_all', false );

# start the page
html_page_top1( lang_get( 'my_votes' ) );
html_page_top2();

$t_votes = vote_get_user_votes();

# get all information for issues ready for display to user
$t_votes_info = array();
foreach($t_votes as $t_vote)
{
	$t_issue = bug_get($t_vote['issue_id']);
	if ( ($t_issue->status < $t_resolved) || $f_show_all )
	{
		$t_project_name = project_get_name($t_issue->project_id);
		$t_votes_info[] = array('vote'=>$t_vote, 'issue'=>$t_issue, 'project_name'=>$t_project_name);
	}
}

?>

<br />
<table class="width100" cellspacing="1">
<tr>
	<td class="form-title">
		<?php echo lang_get( 'my_votes' ) ?>
	</td>
	<td class="right">
		<?php print_account_menu( 'account_voting_page.php' ) ?>
	</td>
</tr>
</table>



<table class="bugList">
	<caption>
		<?php echo lang_get( 'own_voted' ) ?>
	</caption>
	<thead>
	<tr>
		<th><?php echo lang_get( 'email_bug' ) ?></th>
		<th><?php echo lang_get( 'vote_weight' ) ?></th>
		<th><?php echo lang_get( 'vote_num_voters' ) ?></th>
		<th><?php echo lang_get( 'vote_balance' ) ?></th>
		<th><?php echo lang_get( 'email_project' ) ?></th>
		<th><?php echo lang_get( 'email_status' ) ?></th>
		<th><?php echo lang_get( 'email_summary' ) ?></th>
	</tr>
	</thead>
	<?php
	if (is_array($t_votes_info) && count($t_votes_info)>0){
	?>
	<?php foreach($t_votes_info as $t_vote_info){ ?>
	<tr bgcolor="<?php echo get_status_color( $t_vote_info['issue']->status )?>">
		<td>
			<a href="<?php echo string_get_bug_view_url( $t_vote_info['vote']['issue_id'] );?>"><?php echo bug_format_id( $t_vote_info['vote']['issue_id'] );?></a>
		</td>
		<td class="right">
			<?php echo ($t_vote_info['vote']['weight']>0)?('+'.$t_vote_info['vote']['weight']):$t_vote_info['vote']['weight'] ?>
		</td>
		<td class="right">
			<?php echo $t_vote_info['issue']->votes_num_voters ?>
		</td>
		<td class="right">
			<?php
			$t_balance = $t_vote_info['issue']->votes_positive - $t_vote_info['issue']->votes_negative;
			echo ($t_balance>0)?('+'.$t_balance):$t_balance; 
			?>
		</td>
		<td class="center">
			<?php echo $t_vote_info['project_name']; ?>
		</td>
		<td class="center">
			<?php echo string_attribute( get_enum_element( 'status', $t_vote_info['issue']->status ) ); ?>
		</td>
		<td>
			<?php
			echo string_display_line( $t_vote_info['issue']->summary );
			if ( VS_PRIVATE == $t_vote_info['issue']->view_state ) {
				printf( ' <img src="%s" alt="(%s)" title="%s" />', $t_icon_path . 'protected.gif', lang_get( 'private' ), lang_get( 'private' ) );
			}
			?>
		</td>
	</tr>
	<?php } }else{ ?>
	<tr><td colspan="7" class="center"><?php echo lang_get('no_votes') ?></td></tr>
	<?php } ?>
	<tfoot>
		<tr>
			<td colspan="2">
				<?php echo lang_get( 'votes_used' ) ?> = <?php echo vote_used_votes() ?>
			</td>
			<td colspan="5">
				<?php 
				$t_votes_available = vote_available_votes();
				if ($t_votes_available == VOTES_UNLIMITED_VOTES)
				{
					echo lang_get('vote_unlimited');
				}
				else
				{
					echo $t_votes_available;
				}
				?> 
				<?php echo lang_get( 'votes_remain' ) ?>
			</td>
		</tr>
	</tfoot>
</table>

<div align="center">
<?php
	html_button ( 'account_voting_page.php', 
		lang_get( ( $f_show_all ? 'voting_hide' : 'voting_show' ) ), 
		array( 'show_all' => ( $f_show_all ? 0 : 1 ) ) );
?>
</div>

<?php html_page_bottom1( __FILE__ ) ?>
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

require_once( 'core.php' );
$t_core_path = config_get( 'core_path' );
require_once( $t_core_path.'current_user_api.php' );
require_once( $t_core_path.'vote_api.php' );
require_once( $t_core_path.'project_api.php' );

if ( current_user_is_anonymous() ) {
	access_denied();
}

$t_current_user_id = auth_get_current_user_id();
$t_resolved = config_get( 'bug_resolved_status_threshold' );
$f_show_all = gpc_get_bool( 'show_all', false );

# start the page
html_page_top1( lang_get( 'my_votes' ) );
html_page_top2();

$t_votes = vote_get_user_votes();

# get all information for issues ready for display to user
$t_votes_info = array();
foreach($t_votes as $t_vote)
{
	$t_issue = bug_get($t_vote['issue_id']);
	if ( ($t_issue->status < $t_resolved) || $f_show_all )
	{
		$t_project_name = project_get_name($t_issue->project_id);
		$t_votes_info[] = array('vote'=>$t_vote, 'issue'=>$t_issue, 'project_name'=>$t_project_name);
	}
}

?>

<br />
<table class="width100" cellspacing="1">
<tr>
	<td class="form-title">
		<?php echo lang_get( 'my_votes' ) ?>
	</td>
	<td class="right">
		<?php print_account_menu( 'account_voting_page.php' ) ?>
	</td>
</tr>
</table>



<table class="bugList">
	<caption>
		<?php echo lang_get( 'own_voted' ) ?>
	</caption>
	<thead>
	<tr>
		<th><?php echo lang_get( 'email_bug' ) ?></th>
		<th><?php echo lang_get( 'vote_weight' ) ?></th>
		<th><?php echo lang_get( 'vote_num_voters' ) ?></th>
		<th><?php echo lang_get( 'vote_balance' ) ?></th>
		<th><?php echo lang_get( 'email_project' ) ?></th>
		<th><?php echo lang_get( 'email_status' ) ?></th>
		<th><?php echo lang_get( 'email_summary' ) ?></th>
	</tr>
	</thead>
	<?php
	if (is_array($t_votes_info) && count($t_votes_info)>0){
	?>
	<?php foreach($t_votes_info as $t_vote_info){ ?>
	<tr bgcolor="<?php echo get_status_color( $t_vote_info['issue']->status )?>">
		<td>
			<a href="<?php echo string_get_bug_view_url( $t_vote_info['vote']['issue_id'] );?>"><?php echo bug_format_id( $t_vote_info['vote']['issue_id'] );?></a>
		</td>
		<td class="right">
			<?php echo ($t_vote_info['vote']['weight']>0)?('+'.$t_vote_info['vote']['weight']):$t_vote_info['vote']['weight'] ?>
		</td>
		<td class="right">
			<?php echo $t_vote_info['issue']->votes_num_voters ?>
		</td>
		<td class="right">
			<?php
			$t_balance = $t_vote_info['issue']->votes_positive - $t_vote_info['issue']->votes_negative;
			echo ($t_balance>0)?('+'.$t_balance):$t_balance; 
			?>
		</td>
		<td class="center">
			<?php echo $t_vote_info['project_name']; ?>
		</td>
		<td class="center">
			<?php echo string_attribute( get_enum_element( 'status', $t_vote_info['issue']->status ) ); ?>
		</td>
		<td>
			<?php
			echo string_display_line( $t_vote_info['issue']->summary );
			if ( VS_PRIVATE == $t_vote_info['issue']->view_state ) {
				printf( ' <img src="%s" alt="(%s)" title="%s" />', $t_icon_path . 'protected.gif', lang_get( 'private' ), lang_get( 'private' ) );
			}
			?>
		</td>
	</tr>
	<?php } }else{ ?>
	<tr><td colspan="7" class="center"><?php echo lang_get('no_votes') ?></td></tr>
	<?php } ?>
	<tfoot>
		<tr>
			<td colspan="2">
				<?php echo lang_get( 'votes_used' ) ?> = <?php echo vote_used_votes() ?>
			</td>
			<td colspan="5">
				<?php 
				$t_votes_available = vote_available_votes();
				if ($t_votes_available == VOTES_UNLIMITED_VOTES)
				{
					echo lang_get('vote_unlimited');
				}
				else
				{
					echo $t_votes_available;
				}
				?> 
				<?php echo lang_get( 'votes_remain' ) ?>
			</td>
		</tr>
	</tfoot>
</table>

<div align="center">
<?php
	html_button ( 'account_voting_page.php', 
		lang_get( ( $f_show_all ? 'voting_hide' : 'voting_show' ) ), 
		array( 'show_all' => ( $f_show_all ? 0 : 1 ) ) );
?>
</div>

<?php html_page_bottom1( __FILE__ ) ?>
