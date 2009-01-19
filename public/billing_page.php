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
	access_ensure_global_level( config_get( 'time_tracking_reporting_threshold' ) );

/*
	compress_enable();
*/
?>
<?php html_page_top1( lang_get( 'time_tracking_billing_link' )  ) ?>
<?php html_page_top2() ?>

<br />

<?php
	$t_mantis_dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
?>
	<!-- Jump to Bugnote add form -->
<?php
	# Work break-down
	include( $t_mantis_dir . 'billing_inc.php' );
	
	html_page_bottom1( __FILE__ );
?>
