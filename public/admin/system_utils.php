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
 * @todo FIXME: Looks like "From", "to", and "Copy" need i18n. Possibly more in this file.
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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Mantis Administration - System Utilities</title>
<link rel="stylesheet" type="text/css" href="admin.css" />
</head>
<body>

<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
	<tr class="top-bar">
		<td class="links">
			[ <a href="index.php">Back to Mantis Administration</a> ]
		</td>
		<td class="title">
			System Utilities
		</td>
	</tr>
</table>
<br /><br />

<table width="80%" bgcolor="#222222" border="0" cellpadding="10" cellspacing="1">
	<tr><td bgcolor=\"#e8e8e8\" colspan=\"2\"><span class=\"title\">Upgrade Utilities</span></td></tr>

	<!-- # Headings -->
	<tr bgcolor="#ffffff"><th width="70%">Description</th><th width="30%">Execute</th></tr>

	<!-- each row links to an upgrade
		move database bug attachments to disk -->
	<tr bgcolor="#ffffff"><td>Move attachments stored in database schema to disk files.</td><td><center>
	<?php html_button( 'move_db2disk.php', 'Move Attachments to Disk', array( 'doc' => 'attachment' ) );?>
	</center></td></tr>

	<!-- move database project files to disk -->
	<tr bgcolor="#ffffff"><td>Move project files stored in database schema to disk.</td><td><center>
	<?php html_button( 'move_db2disk.php', 'Move Project Files to Disk', array( 'doc' => 'project' ) );?>
	</center></td></tr>

	<!-- move custom field content to standard field -->
	<tr bgcolor="#ffffff"><td>Copy Custom Field to Standard Field.</td><td><center>
	<form method="post" action="copy_field.php">
		From
		<SELECT name="source_id">
			<?php
				$t_custom_ids = custom_field_get_ids();
foreach( $t_custom_ids as $t_id ) {
	printf( "<OPTION VALUE=\"%d\">%s", $t_id, custom_field_get_field( $t_id, 'name' ) );
}
?>
		</SELECT> to
		<SELECT name="dest_id">
			<?php
/** @todo should be expanded and configurable */
// list matches exact field name from database
$t_dest_ids = array(
	'fixed_in_version',
);
foreach( $t_dest_ids as $t_id ) {
	printf( "<OPTION VALUE=\"%s\">%s", $t_id, $t_id );
}
?>
		</SELECT>
	<input type="submit" class="button" value="Copy" />
	</form>
	</center></td></tr>

	<!-- Database Statistics -->
	<tr bgcolor="#ffffff"><td>Show database statistics.</td><td><center>
	<?php html_button( 'db_stats.php', 'Display', array() );?>
	</center></td></tr>

</table>
</body>
</html>
