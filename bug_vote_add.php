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

if ( vote_is_enabled() )
{
	$f_bug_id		= gpc_get_int( 'bug_id' );
	$f_weight		= gpc_get_int( 'vote_weight' );
	$t_user_id  = auth_get_current_user_id();

	access_ensure_bug_level( config_get( 'voting_place_vote_threshold' ), $f_bug_id, $t_user_id );

	if (!vote_exists($f_bug_id, $t_user_id)){
		vote_add($f_bug_id, $f_weight, $t_user_id);
	}
	print_successful_redirect_to_bug($f_bug_id);
}
else
{
	trigger_error( ERROR_VOTING_NOT_ENABLED, ERROR );
}
