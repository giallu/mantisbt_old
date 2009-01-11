<?php
# Mantis - a php based bugtracking system
# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2009  Mantis Team   - mantisbt-dev@lists.sourceforge.net
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
# $Id$
# --------------------------------------------------------

/**
 * @package CoreAPI
 * @subpackage HTMLAPI
 *
 * These functions control the display of each page
 *
 * This is the call order of these functions, should you need to figure out
 * which to modify or which to leave out.
 *
 * html_page_top1
 * 	html_begin
 * 	html_head_begin
 * 	html_css
 * 	html_content_type
 * 	html_rss_link
 * 	(html_meta_redirect)
 * 	html_title
 * html_page_top2
 * 	html_page_top2a
 * 	html_head_end
 * 	html_body_begin
 * 	html_header
 * 	html_top_banner
 * 	html_login_info
 * 	(print_project_menu_bar)
 * 	print_menu
 *
 * ...Page content here...
 *
 * html_page_bottom1
 * 	(print_menu)
 * 	html_page_bottom1a
 * 	html_bottom_banner
 * 	html_footer
 * 	html_body_end
 * html_end
 */
$t_core_dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;

require_once( $t_core_dir . 'current_user_api.php' );
require_once( $t_core_dir . 'string_api.php' );
require_once( $t_core_dir . 'bug_api.php' );
require_once( $t_core_dir . 'project_api.php' );
require_once( $t_core_dir . 'helper_api.php' );
require_once( $t_core_dir . 'authentication_api.php' );
require_once( $t_core_dir . 'user_api.php' );
require_once( $t_core_dir . 'rss_api.php' );
require_once( $t_core_dir . 'wiki_api.php' );
require_once( $t_core_dir . 'php_api.php' );

$g_rss_feed_url = null;

$g_robots_meta = '';

# flag for error handler to skip header menus
$g_error_send_page_header = true;

# Projax library disabled by default.  It will be enabled if projax_api.php
# is included.  But it must be included after html_api.php
$g_enable_projax = false;

# --------------------
# Sets the url for the rss link associated with the current page.
# null: means no feed (default).
function html_set_rss_link( $p_rss_feed_url ) {
	if( OFF != config_get( 'rss_enabled' ) ) {
		global $g_rss_feed_url;
		$g_rss_feed_url = $p_rss_feed_url;
	}
}

# --------------------
# This method must be called before the html_page_top* methods.  It marks the page as not
# for indexing.
function html_robots_noindex() {
	global $g_robots_meta;
	$g_robots_meta = 'noindex,follow';
}

# --------------------
# Prints the link that allows auto-detection of the associated feed.
function html_rss_link() {
	global $g_rss_feed_url;

	if( $g_rss_feed_url !== null ) {
		echo '<link rel="alternate" type="application/rss+xml" title="RSS" href="', $g_rss_feed_url, '" />';
	}
}

# --------------------
# Print the part of the page that comes before meta redirect tags should
#  be inserted
function html_page_top1( $p_page_title = null ) {
	html_begin();
	html_head_begin();
	html_css();
	html_content_type();
	include( config_get( 'meta_include_file' ) );

	global $g_robots_meta;
	if ( !is_blank( $g_robots_meta ) ) {
		echo "\t", '<meta name="robots" content="', $g_robots_meta, '" />', "\n";
	}

	html_rss_link();

	$t_favicon_image = config_get( 'favicon_image' );
	if( !is_blank( $t_favicon_image ) ) {
		echo "\t", '<link rel="shortcut icon" href="', helper_mantis_url( $t_favicon_image ), '" type="image/x-icon" />', "\n";
	}

	// Advertise the availability of the browser search plug-ins.
	echo "\t", '<link rel="search" type="application/opensearchdescription+xml" title="MantisBT: Text Search" href="' . string_sanitize_url( 'browser_search_plugin.php?type=text', true) . '" />';
	echo "\t", '<link rel="search" type="application/opensearchdescription+xml" title="MantisBT: Issue Id" href="' . string_sanitize_url( 'browser_search_plugin.php?type=id', true) . '" />';

	html_title( $p_page_title );
	html_head_javascript();
}

# --------------------
# Print the part of the page that comes after meta tags, but before the
#  actual page content
function html_page_top2() {
	html_page_top2a();

	if( !db_is_connected() ) {
		return;
	}

	if( auth_is_user_authenticated() ) {
		html_login_info();

		if( ON == config_get( 'show_project_menu_bar' ) ) {
			print_project_menu_bar();
			echo '<br />';
		}
	}
	print_menu();

	event_signal( 'EVENT_LAYOUT_CONTENT_BEGIN' );
}

# --------------------
# Print the part of the page that comes after meta tags and before the
#  actual page content, but without login info or menus.  This is used
#  directly during the login process and other times when the user may
#  not be authenticated
function html_page_top2a() {
	global $g_error_send_page_header;

	html_head_end();
	html_body_begin();
	$g_error_send_page_header = false;
	html_header();
	html_top_banner();
}

# --------------------
# Print the part of the page that comes below the page content
# $p_file should always be the __FILE__ variable. This is passed to show source
function html_page_bottom1( $p_file = null ) {
	if( !db_is_connected() ) {
		return;
	}

	event_signal( 'EVENT_LAYOUT_CONTENT_END' );

	if( config_get( 'show_footer_menu' ) ) {
		echo '<br />';
		print_menu();
	}

	html_page_bottom1a( $p_file );
}

# --------------------
# Print the part of the page that comes below the page content but leave off
#  the menu.  This is used during the login process and other times when the
#  user may not be authenticated.
function html_page_bottom1a( $p_file = null ) {
	if( null === $p_file ) {
		$p_file = basename( $_SERVER['PHP_SELF'] );
	}

	html_bottom_banner();
	html_footer( $p_file );
	html_body_end();
	html_end();
}

# --------------------
# (1) Print the document type and the opening <html> tag
function html_begin() {
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">', "\n";
	echo '<html>', "\n";
}

# --------------------
# (2) Begin the <head> section
function html_head_begin() {
	echo '<head>', "\n";
}

# --------------------
# (3) Print the content-type
function html_content_type() {
	echo "\t", '<meta http-equiv="Content-type" content="text/html;charset=', lang_get( 'charset' ), '" />', "\n";
}

# --------------------
# (4) Print the window title
function html_title( $p_page_title = null ) {
	$t_title = config_get( 'window_title' );
	echo "\t", '<title>';
	if( 0 == strlen( $p_page_title ) ) {
		echo string_display( $t_title );
	} else {
		if( 0 == strlen( $t_title ) ) {
			echo $p_page_title;
		} else {
			echo $p_page_title . ' - ' . string_display( $t_title );
		}
	}
	echo '</title>', "\n";
}

# --------------------
# (5) Print the link to include the css file
function html_css() {
	$t_css_url = config_get( 'css_include_file' );
	echo "\t", '<link rel="stylesheet" type="text/css" href="', helper_mantis_url( $t_css_url ), '" />', "\n";

	# fix for NS 4.x css
	echo "\t", '<script type="text/javascript" language="JavaScript"><!--', "\n";
	echo "\t\t", 'if(document.layers) {document.write("<style>td{padding:0px;}<\/style>")}', "\n";
	echo "\t", '// --></script>', "\n";
}

# --------------------
# (6) Print an HTML meta tag to redirect to another page
# This function is optional and may be called by pages that need a redirect.
# $p_time is the number of seconds to wait before redirecting.
# If we have handled any errors on this page and the 'stop_on_errors' config
#  option is turned on, return false and don't redirect.
#
# @param string The page to redirect: has to be a relative path
# @param integer seconds to wait for before redirecting
# @param boolean apply string_sanitize_url to passed url
# @return boolean
function html_meta_redirect( $p_url, $p_time = null, $p_sanitize = true ) {
	if( ON == config_get( 'stop_on_errors' ) && error_handled() ) {
		return false;
	}

	if( null === $p_time ) {
		$p_time = current_user_get_pref( 'redirect_delay' );
	}

	$t_url = config_get( 'path' );
	if( $p_sanitize ) {
		$t_url .= string_sanitize_url( $p_url );
	} else {
		$t_url .= $p_url;
	}

	echo "\t<meta http-equiv=\"Refresh\" content=\"$p_time;URL=$t_url\" />\n";

	return true;
}

# ---------------------
# (6a) Javascript...
function html_head_javascript() {
	if( ON == config_get( 'use_javascript' ) ) {
		echo "\t", '<script type="text/javascript" language="JavaScript" src="', helper_mantis_url( 'javascript/common.js' ), '">';
		echo '</script>', "\n";
		echo "\t", '<script type="text/JavaScript" src="', helper_mantis_url( 'javascript/ajax.js' ), '">';
		echo '</script>', "\n";

		global $g_enable_projax;

		if( $g_enable_projax ) {
			echo '<script type="text/javascript" src="', helper_mantis_url( 'javascript/projax/prototype.js' ), '"></script>';
			echo '<script type="text/javascript" src="', helper_mantis_url( 'javascript/projax/scriptaculous.js' ), '"></script>';
		}
	}
}

# --------------------
# (7) End the <head> section
function html_head_end() {
	event_signal( 'EVENT_LAYOUT_RESOURCES' );

	echo '</head>', "\n";
}

# --------------------
# (8) Begin the <body> section
function html_body_begin() {
	echo '<body>', "\n";

	event_signal( 'EVENT_LAYOUT_BODY_BEGIN' );
}

# --------------------
# (9) Print the title displayed at the top of the page
function html_header() {
	$t_title = config_get( 'page_title' );
	if( !is_blank( $t_title ) ) {
		echo '<div class="center"><span class="pagetitle">', string_display( $t_title ), '</span></div>', "\n";
	}
}

# --------------------
# (10) Print a user-defined banner at the top of the page if there is one.
function html_top_banner() {
	$t_page = config_get( 'top_include_page' );
	$t_logo_image = config_get( 'logo_image' );
	$t_logo_url = config_get( 'logo_url' );

	if( is_blank( $t_logo_image ) ) {
		$t_show_logo = false;
	} else {
		$t_show_logo = true;
		if( is_blank( $t_logo_url ) ) {
			$t_show_url = false;
		} else {
			$t_show_url = true;
		}
	}

	if( !is_blank( $t_page ) && file_exists( $t_page ) && !is_dir( $t_page ) ) {
		include( $t_page );
	} elseif( $t_show_logo ) {
		if( is_page_name( 'login_page' ) ) {
			$t_align = 'center';
		} else {
			$t_align = 'left';
		}

		echo '<div align="', $t_align, '">';
		if( $t_show_url ) {
			echo '<a href="', config_get( 'logo_url' ), '">';
		}
		echo '<img border="0" alt="Mantis Bugtracker" src="' . helper_mantis_url( config_get( 'logo_image' ) ) . '" />';
		if( $t_show_url ) {
			echo '</a>';
		}
		echo '</div>';
	}

	event_signal( 'EVENT_LAYOUT_PAGE_HEADER' );
}

# --------------------
# (11) Print the user's account information
# Also print the select box where users can switch projects
function html_login_info() {
	$t_username = current_user_get_field( 'username' );
	$t_access_level = get_enum_element( 'access_levels', current_user_get_access_level() );
	$t_now = date( config_get( 'complete_date_format' ) );
	$t_realname = current_user_get_field( 'realname' );

	echo '<table class="hide">';
	echo '<tr>';
	echo '<td class="login-info-left">';
	if( current_user_is_anonymous() ) {
		$t_return_page = $_SERVER['PHP_SELF'];
		if( isset( $_SERVER['QUERY_STRING'] ) ) {
			$t_return_page .= '?' . $_SERVER['QUERY_STRING'];
		}

		$t_return_page = string_url( $t_return_page );
		echo lang_get( 'anonymous' ) . ' | <a href="' . helper_mantis_url( 'login_page.php?return=' . $t_return_page ) . '">' . lang_get( 'login_link' ) . '</a>';
		if( config_get_global( 'allow_signup' ) == ON ) {
			echo ' | <a href="' . helper_mantis_url( 'signup_page.php' ) . '">' . lang_get( 'signup_link' ) . '</a>';
		}
	} else {
		echo lang_get( 'logged_in_as' ), ": <span class=\"italic\">", string_display( $t_username ), "</span> <span class=\"small\">";
		echo is_blank( $t_realname ) ? "($t_access_level)" : "(" . string_display( $t_realname ) . " - $t_access_level)";
		echo "</span>";
	}
	echo '</td>';
	echo '<td class="login-info-middle">';
	echo "<span class=\"italic\">$t_now</span>";
	echo '</td>';
	echo '<td class="login-info-right">';
	$t_show_project_selector = true;
	if( count( current_user_get_accessible_projects() ) == 1 ) {

		// >1
		$t_project_ids = current_user_get_accessible_projects();
		$t_project_id = (int) $t_project_ids[0];
		if( count( current_user_get_accessible_subprojects( $t_project_id ) ) == 0 ) {
			$t_show_project_selector = false;
		}
	}

	if( $t_show_project_selector ) {
		echo '<form method="post" name="form_set_project" action="' . helper_mantis_url( 'set_project.php' ) . '">';

		echo lang_get( 'email_project' ), ': ';
		if( ON == config_get( 'show_extended_project_browser' ) ) {
			print_extended_project_browser( helper_get_current_project_trace() );
		} else {
			if( ON == config_get( 'use_javascript' ) ) {
				echo '<select name="project_id" class="small" onchange="document.forms.form_set_project.submit();">';
			} else {
				echo '<select name="project_id" class="small">';
			}
			print_project_option_list( join( ';', helper_get_current_project_trace() ), true, null, true );
			echo '</select> ';
		}
		echo '<input type="submit" class="button-small" value="' . lang_get( 'switch' ) . '" />';
		echo '</form>';
	}
	if( OFF != config_get( 'rss_enabled' ) ) {

		# Link to RSS issues feed for the selected project, including authentication details.
		echo '<a href="' . rss_get_issues_feed_url() . '">';
		echo '<img src="' . helper_mantis_url( 'images/rss.png' ) . '" alt="' . lang_get( 'rss' ) . '" style="border-style: none; margin: 5px; vertical-align: middle;" />';
		echo '</a>';
	}

	echo '</td>';
	echo '</tr>';
	echo '</table>';
}

# --------------------
# (12) Print a user-defined banner at the bottom of the page if there is one.
function html_bottom_banner() {
	$t_page = config_get( 'bottom_include_page' );

	if( !is_blank( $t_page ) && file_exists( $t_page ) && !is_dir( $t_page ) ) {
		include( $t_page );
	}
}

# --------------------
# (13) Print the page footer information
function html_footer( $p_file ) {
	global $g_timer, $g_queries_array, $g_request_time;

	# If a user is logged in, update their last visit time.
	# We do this at the end of the page so that:
	#  1) we can display the user's last visit time on a page before updating it
	#  2) we don't invalidate the user cache immediately after fetching it
	#  3) don't do this on the password verification or update page, as it causes the
	#    verification comparison to fail
	if( auth_is_user_authenticated() && !( is_page_name( 'verify.php' ) || is_page_name( 'account_update.php' ) ) ) {
		$t_user_id = auth_get_current_user_id();
		user_update_last_visit( $t_user_id );
	}

	echo "\t", '<br />', "\n";
	echo "\t", '<hr size="1" />', "\n";

	echo '<table border="0" width="100%" cellspacing="0" cellpadding="0"><tr valign="top"><td>';
	if( ON == config_get( 'show_version' ) ) {
		$t_version_suffix = config_get_global( 'version_suffix' );
		echo "\t", '<span class="timer"><a href="http://www.mantisbt.org/" title="Free Web Based Bug Tracker">Mantis ', MANTIS_VERSION, ( $t_version_suffix ? " $t_version_suffix" : '' ), '</a>', '[<a href="http://www.mantisbt.org/"  title="Free Web Based Bug Tracker" target="_blank">^</a>]</span>', "\n";
	}
	echo "\t", '<address>Copyright &copy; 2000 - 2009 Mantis Group</address>', "\n";

	# only display webmaster email is current user is not the anonymous user
	if( !is_page_name( 'login_page.php' ) && !current_user_is_anonymous() ) {
		echo "\t", '<address><a href="mailto:', config_get( 'webmaster_email' ), '">', config_get( 'webmaster_email' ), '</a></address>', "\n";
	}

	event_signal( 'EVENT_LAYOUT_PAGE_FOOTER' );

	# print timings
	if( ON == config_get( 'show_timer' ) ) {
		$g_timer->print_times();
		echo sprintf( lang_get( 'memory_usage_in_kb' ), number_format( memory_get_peak_usage() / 1024 ) ), '<br />';
	}

	# print db queries that were run
	if( helper_show_queries() ) {
		$t_count = count( $g_queries_array );
		echo "\t", $t_count, ' ', lang_get( 'total_queries_executed' ), '<br />', "\n";
		$t_unique_queries = 0;
		$t_shown_queries = array();
		for( $i = 0;$i < $t_count;$i++ ) {
			if( !in_array( $g_queries_array[$i][0], $t_shown_queries ) ) {
				$t_unique_queries++;
				$g_queries_array[$i][3] = false;
				array_push( $t_shown_queries, $g_queries_array[$i][0] );
			} else {
				$g_queries_array[$i][3] = true;
			}
		}
		echo "\t", $t_unique_queries, ' ', lang_get( 'unique_queries_executed' ), '<br />', "\n";
		if( ON == config_get( 'show_queries_list' ) ) {
			echo "\t", '<table>', "\n";
			$t_total = 0;
			for( $i = 0;$i < $t_count;$i++ ) {
				$t_time = $g_queries_array[$i][1];
				$t_caller = $g_queries_array[$i][2];
				$t_total += $t_time;
				$t_style_tag = '';
				if( true == $g_queries_array[$i][3] ) {
					$t_style_tag = ' style="color: red;"';
				}
				echo "\t", '<tr valign="top"><td', $t_style_tag, '>', ( $i + 1 ), '</td>';
				echo '<td', $t_style_tag, '>', $t_time, '</td>';
				echo '<td', $t_style_tag, '><span style="color: gray;">', $t_caller, '</span><br />', string_html_specialchars( $g_queries_array[$i][0] ), '</td></tr>', "\n";
			}

			# @@@ Note sure if we should localize them given that they are debug info.  Will add if requested by users.
			echo "\t", '<tr><td></td><td>', $t_total, '</td><td>SQL Queries Total Time</td></tr>', "\n";
			echo "\t", '<tr><td></td><td>', round( microtime_float() - $g_request_time, 4 ), '</td><td>Page Request Total Time</td></tr>', "\n";
			echo "\t", '</table>', "\n";
		}
	}

	echo '</td><td>', "\n\t", '<div align="right">';
	echo '<a href="http://www.mantisbt.org" title="Free Web Based Bug Tracker"><img src="' . helper_mantis_url( 'images/mantis_logo_button.gif' ) . '" width="88" height="35" alt="Powered by Mantis Bugtracker" border="0" /></a>';
	echo '</div>', "\n", '</td></tr></table>', "\n";
}

# --------------------
# (14) End the <body> section
function html_body_end() {
	event_signal( 'EVENT_LAYOUT_BODY_END' );

	echo '</body>', "\n";
}

# --------------------
# (15) Print the closing <html> tag
function html_end() {
	echo '</html>', "\n";
}

# ##########################################################################
# HTML Menu API
# ##########################################################################

function prepare_custom_menu_options( $p_config ) {
	$t_custom_menu_options = config_get( $p_config );
	$t_options = array();

	foreach( $t_custom_menu_options as $t_custom_option ) {
		$t_access_level = $t_custom_option[1];
		if( access_has_project_level( $t_access_level ) ) {
			$t_caption = lang_get_defaulted( $t_custom_option[0] );
			$t_link = $t_custom_option[2];
			$t_options[] = "<a href=\"$t_link\">$t_caption</a>";
		}
	}

	return $t_options;
}

# --------------------
# Print the main menu
function print_menu() {
	if( auth_is_user_authenticated() ) {
		$t_protected = current_user_get_field( 'protected' );
		$t_current_project = helper_get_current_project();

		echo '<table class="width100" cellspacing="0">';
		echo '<tr>';
		echo '<td class="menu">';
		$t_menu_options = array();

		# Main Page
		$t_menu_options[] = '<a href="' . helper_mantis_url( 'main_page.php' ) . '">' . lang_get( 'main_link' ) . '</a>';

		# Plugin / Event added options
		$t_event_menu_options = event_signal( 'EVENT_MENU_MAIN_FRONT' );
		foreach( $t_event_menu_options as $t_plugin => $t_plugin_menu_options ) {
			foreach( $t_plugin_menu_options as $t_callback => $t_callback_menu_options ) {
				if( is_array( $t_callback_menu_options ) ) {
					$t_menu_options = array_merge( $t_menu_options, $t_callback_menu_options );
				} else {
					$t_menu_options[] = $t_callback_menu_options;
				}
			}
		}

		# My View
		$t_menu_options[] = '<a href="' . helper_mantis_url( 'my_view_page.php">' ) . lang_get( 'my_view_link' ) . '</a>';

		# View Bugs
		$t_menu_options[] = '<a href="' . helper_mantis_url( 'view_all_bug_page.php">' ) . lang_get( 'view_bugs_link' ) . '</a>';

		# Report Bugs
		if( access_has_project_level( config_get( 'report_bug_threshold' ) ) ) {
			$t_menu_options[] = string_get_bug_report_link();
		}

		# Changelog Page
		if( access_has_project_level( config_get( 'view_changelog_threshold' ) ) ) {
			$t_menu_options[] = '<a href="' . helper_mantis_url( 'changelog_page.php">' ) . lang_get( 'changelog_link' ) . '</a>';
		}

		# Roadmap Page
		if( access_has_project_level( config_get( 'roadmap_view_threshold' ) ) ) {
			$t_menu_options[] = '<a href="' . helper_mantis_url( 'roadmap_page.php">' ) . lang_get( 'roadmap_link' ) . '</a>';
		}

		# Summary Page
		if( access_has_project_level( config_get( 'view_summary_threshold' ) ) ) {
			$t_menu_options[] = '<a href="' . helper_mantis_url( 'summary_page.php">' ) . lang_get( 'summary_link' ) . '</a>';
		}

		# Project Documentation Page
		if( ON == config_get( 'enable_project_documentation' ) ) {
			$t_menu_options[] = '<a href="' . helper_mantis_url( 'proj_doc_page.php">' ) . lang_get( 'docs_link' ) . '</a>';
		}

		# Project Wiki
		if( wiki_enabled() ) {
			$t_menu_options[] = '<a href="' . helper_mantis_url( 'wiki.php?type=project&amp;id=' ) . $t_current_project . '">' . lang_get( 'wiki' ) . '</a>';
		}

		# Plugin / Event added options
		$t_event_menu_options = event_signal( 'EVENT_MENU_MAIN' );
		foreach( $t_event_menu_options as $t_plugin => $t_plugin_menu_options ) {
			foreach( $t_plugin_menu_options as $t_callback => $t_callback_menu_options ) {
				if( is_array( $t_callback_menu_options ) ) {
					$t_menu_options = array_merge( $t_menu_options, $t_callback_menu_options );
				} else {
					$t_menu_options[] = $t_callback_menu_options;
				}
			}
		}

		# Manage Users (admins) or Manage Project (managers) or Manage Custom Fields
		if( access_has_global_level( config_get( 'manage_site_threshold' ) ) ) {
			$t_link = helper_mantis_url( 'manage_overview_page.php' );
			$t_menu_options[] = "<a href=\"$t_link\">" . lang_get( 'manage_link' ) . '</a>';
		} else {
			$t_show_access = min( config_get( 'manage_user_threshold' ), config_get( 'manage_project_threshold' ), config_get( 'manage_custom_fields_threshold' ) );
			if( access_has_global_level( $t_show_access ) || access_has_any_project( $t_show_access ) ) {
				$t_current_project = helper_get_current_project();
				if( access_has_global_level( config_get( 'manage_user_threshold' ) ) ) {
					$t_link = helper_mantis_url( 'manage_user_page.php' );
				} else {
					if( access_has_project_level( config_get( 'manage_project_threshold' ), $t_current_project ) && ( $t_current_project <> ALL_PROJECTS ) ) {
						$t_link = helper_mantis_url( 'manage_proj_edit_page.php?project_id=' ) . $t_current_project;
					} else {
						$t_link = helper_mantis_url( 'manage_proj_page.php' );
					}
				}
				$t_menu_options[] = "<a href=\"$t_link\">" . lang_get( 'manage_link' ) . '</a>';
			}
		}

		# News Page
		if( access_has_project_level( config_get( 'manage_news_threshold' ) ) ) {

			# Admin can edit news for All Projects (site-wide)
			if(( ALL_PROJECTS != helper_get_current_project() ) || ( access_has_project_level( ADMINISTRATOR ) ) ) {
				$t_menu_options[] = '<a href="' . helper_mantis_url( 'news_menu_page.php">' ) . lang_get( 'edit_news_link' ) . '</a>';
			} else {
				$t_menu_options[] = '<a href="' . helper_mantis_url( 'login_select_proj_page.php">' ) . lang_get( 'edit_news_link' ) . '</a>';
			}
		}

		# Account Page (only show accounts that are NOT protected)
		if( OFF == $t_protected ) {
			$t_menu_options[] = '<a href="' . helper_mantis_url( 'account_page.php">' ) . lang_get( 'account_link' ) . '</a>';
		}

		# Add custom options
		$t_custom_options = prepare_custom_menu_options( 'main_menu_custom_options' );
		$t_menu_options = array_merge( $t_menu_options, $t_custom_options );
		if( config_get( 'time_tracking_enabled' ) && config_get( 'time_tracking_with_billing' ) && access_has_global_level( config_get( 'time_tracking_reporting_threshold' ) ) ) {
			$t_menu_options[] = '<a href="' . helper_mantis_url( 'billing_page.php">' ) . lang_get( 'time_tracking_billing_link' ) . '</a>';
		}

		# Logout (no if anonymously logged in)
		if( !current_user_is_anonymous() ) {
			$t_menu_options[] = '<a href="' . helper_mantis_url( 'logout_page.php">' ) . lang_get( 'logout_link' ) . '</a>';
		}
		echo implode( $t_menu_options, ' | ' );
		echo '</td>';
		echo '<td class="menu right nowrap">';
		echo '<form method="post" action="' . helper_mantis_url( 'jump_to_bug.php">' );

		if( ON == config_get( 'use_javascript' ) ) {
			$t_bug_label = lang_get( 'issue_id' );
			echo "<input type=\"text\" name=\"bug_id\" size=\"10\" class=\"small\" value=\"$t_bug_label\" onfocus=\"if (this.value == '$t_bug_label') this.value = ''\" onblur=\"if (this.value == '') this.value = '$t_bug_label'\" />&nbsp;";
		} else {
			echo "<input type=\"text\" name=\"bug_id\" size=\"10\" class=\"small\" />&nbsp;";
		}

		echo '<input type="submit" class="button-small" value="' . lang_get( 'jump' ) . '" />&nbsp;';
		echo '</form>';
		echo '</td>';
		echo '</tr>';
		echo '</table>';
	}
}

# --------------------
# Print the menu bar with a list of projects to which the user has access
function print_project_menu_bar() {
	$t_project_ids = current_user_get_accessible_projects();

	echo '<table class="width100" cellspacing="0">';
	echo '<tr>';
	echo '<td class="menu">';
	echo '<a href="' . helper_mantis_url( 'set_project.php?project_id=' . ALL_PROJECTS ) . '">' . lang_get( 'all_projects' ) . '</a>';

	foreach( $t_project_ids as $t_id ) {
		echo ' | <a href="' . helper_mantis_url( 'set_project.php?project_id=' . $t_id ) . ' ">' . string_display( project_get_field( $t_id, 'name' ) ) . '</a>';
		print_subproject_menu_bar( $t_id, $t_id . ';' );
	}

	echo '</td>';
	echo '</tr>';
	echo '</table>';
}

# --------------------
# Print the menu bar with a list of projects to which the user has access
function print_subproject_menu_bar( $p_project_id, $p_parents = '' ) {
	$t_subprojects = current_user_get_accessible_subprojects( $p_project_id );
	$t_char = ':';
	foreach( $t_subprojects as $t_subproject ) {
		echo $t_char . ' <a href="' . helper_mantis_url( 'set_project.php?project_id=' . $p_parents . $t_subproject ) . ' ">' . string_display( project_get_field( $t_subproject, 'name' ) ) . '</a>';
		print_subproject_menu_bar( $t_subproject, $p_parents . $t_subproject . ';' );
		$t_char = ',';
	}
}

# --------------------
# Print the menu for the graph summary section
function print_menu_graph() {
	if( config_get( 'use_jpgraph' ) ) {
		$t_icon_path = config_get( 'icon_path' );

		echo '<br />';
		echo '<a href="' . helper_mantis_url( 'summary_page.php' ) . '"><img src="' . $t_icon_path . 'synthese.gif" border="0" alt="" />' . lang_get( 'synthesis_link' ) . '</a> | ';
		echo '<a href="' . helper_mantis_url( 'summary_graph_imp_status.php' ) . '"><img src="' . $t_icon_path . 'synthgraph.gif" border="0" alt="" />' . lang_get( 'status_link' ) . '</a> | ';
		echo '<a href="' . helper_mantis_url( 'summary_graph_imp_priority.php' ) . '"><img src="' . $t_icon_path . 'synthgraph.gif" border="0" alt="" />' . lang_get( 'priority_link' ) . '</a> | ';
		echo '<a href="' . helper_mantis_url( 'summary_graph_imp_severity.php' ) . '"><img src="' . $t_icon_path . 'synthgraph.gif" border="0" alt="" />' . lang_get( 'severity_link' ) . '</a> | ';
		echo '<a href="' . helper_mantis_url( 'summary_graph_imp_category.php' ) . '"><img src="' . $t_icon_path . 'synthgraph.gif" border="0" alt="" />' . lang_get( 'category_link' ) . '</a> | ';
		echo '<a href="' . helper_mantis_url( 'summary_graph_imp_resolution.php' ) . '"><img src="' . $t_icon_path . 'synthgraph.gif" border="0" alt="" />' . lang_get( 'resolution_link' ) . '</a>';
	}
}

/**
 * Print the menu for the manage section
 *
 * @param string $p_page specifies the current page name so it's link can be disabled
 */
function print_manage_menu( $p_page = '' ) {
	$t_manage_user_page = 'manage_user_page.php';
	$t_manage_project_menu_page = 'manage_proj_page.php';
	$t_manage_custom_field_page = 'manage_custom_field_page.php';
	$t_manage_plugin_page = 'manage_plugin_page.php';
	$t_manage_config_page = 'adm_config_report.php';
	$t_manage_prof_menu_page = 'manage_prof_menu_page.php';

	switch( $p_page ) {
		case $t_manage_user_page:
			$t_manage_user_page = '';
			break;
		case $t_manage_project_menu_page:
			$t_manage_project_menu_page = '';
			break;
		case $t_manage_custom_field_page:
			$t_manage_custom_field_page = '';
			break;
		case $t_manage_config_page:
			$t_manage_config_page = '';
			break;
		case $t_manage_plugin_page:
			$t_manage_plugin_page = '';
			break;
		case $t_manage_prof_menu_page:
			$t_manage_prof_menu_page = '';
			break;
	}

	echo '<div align="center"><p>';
	if( access_has_global_level( config_get( 'manage_user_threshold' ) ) ) {
		print_bracket_link( helper_mantis_url( $t_manage_user_page ), lang_get( 'manage_users_link' ) );
	}
	if( access_has_project_level( config_get( 'manage_project_threshold' ) ) ) {
		print_bracket_link( helper_mantis_url( $t_manage_project_menu_page ), lang_get( 'manage_projects_link' ) );
	}
	if( access_has_global_level( config_get( 'manage_custom_fields_threshold' ) ) ) {
		print_bracket_link( helper_mantis_url( $t_manage_custom_field_page ), lang_get( 'manage_custom_field_link' ) );
	}
	if( access_has_global_level( config_get( 'manage_global_profile_threshold' ) ) ) {
		print_bracket_link( helper_mantis_url( $t_manage_prof_menu_page ), lang_get( 'manage_global_profiles_link' ) );
	}
	if( access_has_global_level( config_get( 'manage_plugin_threshold' ) ) ) {
		print_bracket_link( helper_mantis_url( $t_manage_plugin_page ), lang_get( 'manage_plugin_link' ) );
	}
	if( access_has_project_level( config_get( 'view_configuration_threshold' ) ) ) {
		print_bracket_link( helper_mantis_url( $t_manage_config_page ), lang_get( 'manage_config_link' ) );
	}

	# Plugin / Event added options
	$t_event_menu_options = event_signal( 'EVENT_MENU_MANAGE' );
	$t_menu_options = array();
	foreach( $t_event_menu_options as $t_plugin => $t_plugin_menu_options ) {
		foreach( $t_plugin_menu_options as $t_callback => $t_callback_menu_options ) {
			if( is_array( $t_callback_menu_options ) ) {
				$t_menu_options = array_merge( $t_menu_options, $t_callback_menu_options );
			} else {
				$t_menu_options[] = $t_callback_menu_options;
			}
		}
	}

	// Plugins menu items
	// TODO: this would be a call to print_pracket_link but the events returns cooked links so we cant
	foreach( $t_menu_options as $t_menu_item ) {
		echo '<span class="bracket-link">[&nbsp;';
		echo $t_menu_item;
		echo '&nbsp;]</span> ';
	}

	echo '</p></div>';
}

# --------------------
# Print the menu for the manage configuration section
# $p_page specifies the current page name so it's link can be disabled
function print_manage_config_menu( $p_page = '' ) {
	$t_configuration_report = 'adm_config_report.php';
	$t_permissions_summary_report = 'adm_permissions_report.php';
	$t_manage_work_threshold = 'manage_config_work_threshold_page.php';
	$t_manage_email = 'manage_config_email_page.php';
	$t_manage_workflow = 'manage_config_workflow_page.php';
	$t_manage_columns = 'manage_config_columns_page.php';

	switch( $p_page ) {
		case $t_configuration_report:
			$t_configuration_report = '';
			break;
		case $t_permissions_summary_report:
			$t_permissions_summary_report = '';
			break;
		case $t_manage_work_threshold:
			$t_manage_work_threshold = '';
			break;
		case $t_manage_email:
			$t_manage_email = '';
			break;
		case $t_manage_workflow:
			$t_manage_workflow = '';
			break;
		case $t_manage_columns:
			$t_manage_columns = '';
			break;
	}

	echo '<br /><div align="center">';
	if( access_has_project_level( config_get( 'view_configuration_threshold' ) ) ) {
		print_bracket_link( helper_mantis_url( $t_configuration_report ), lang_get_defaulted( 'configuration_report' ) );
		print_bracket_link( helper_mantis_url( $t_permissions_summary_report ), lang_get( 'permissions_summary_report' ) );
		print_bracket_link( helper_mantis_url( $t_manage_work_threshold ), lang_get( 'manage_threshold_config' ) );
		print_bracket_link( helper_mantis_url( $t_manage_workflow ), lang_get( 'manage_workflow_config' ) );
		print_bracket_link( helper_mantis_url( $t_manage_email ), lang_get( 'manage_email_config' ) );
		print_bracket_link( $t_manage_columns, lang_get( 'manage_columns_config' ) );
	}
	echo '</div>';
}

# --------------------
# Print the menu for the account section
# $p_page specifies the current page name so it's link can be disabled
function print_account_menu( $p_page = '' ) {
	$t_account_page = 'account_page.php';
	$t_account_prefs_page = 'account_prefs_page.php';
	$t_account_profile_menu_page = 'account_prof_menu_page.php';
	$t_account_sponsor_page = 'account_sponsor_page.php';
	$t_account_manage_columns_page = 'account_manage_columns_page.php';

	switch( $p_page ) {
		case $t_account_page:
			$t_account_page = '';
			break;
		case $t_account_prefs_page:
			$t_account_prefs_page = '';
			break;
		case $t_account_profile_menu_page:
			$t_account_profile_menu_page = '';
			break;
		case $t_account_sponsor_page:
			$t_account_sponsor_page = '';
			break;
		case $t_account_manage_columns_page:
			$t_account_manage_columns_page = '';
			break;
	}

	print_bracket_link( $t_account_page, lang_get( 'account_link' ) );
	print_bracket_link( $t_account_prefs_page, lang_get( 'change_preferences_link' ) );
	print_bracket_link( $t_account_manage_columns_page, lang_get( 'manage_columns_config' ) );

	if( access_has_project_level( config_get( 'add_profile_threshold' ) ) ) {
		print_bracket_link( helper_mantis_url( $t_account_profile_menu_page ), lang_get( 'manage_profiles_link' ) );
	}

	if(( config_get( 'enable_sponsorship' ) == ON ) && ( access_has_project_level( config_get( 'view_sponsorship_total_threshold' ) ) ) && !current_user_is_anonymous() ) {
		print_bracket_link( helper_mantis_url( $t_account_sponsor_page ), lang_get( 'my_sponsorship' ) );
	}
}

# --------------------
# Print the menu for the docs section
# $p_page specifies the current page name so it's link can be disabled
function print_doc_menu( $p_page = '' ) {
	$t_documentation_html = config_get( 'manual_url' );
	$t_proj_doc_page = 'proj_doc_page.php';
	$t_proj_doc_add_page = 'proj_doc_add_page.php';

	switch( $p_page ) {
		case $t_documentation_html:
			$t_documentation_html = '';
			break;
		case $t_proj_doc_page:
			$t_proj_doc_page = '';
			break;
		case $t_proj_doc_add_page:
			$t_proj_doc_add_page = '';
			break;
	}

	print_bracket_link( $t_documentation_html, lang_get( 'user_documentation' ) );
	print_bracket_link( helper_mantis_url( $t_proj_doc_page ), lang_get( 'project_documentation' ) );
	if( file_allow_project_upload() ) {
		print_bracket_link( helper_mantis_url( $t_proj_doc_add_page ), lang_get( 'add_file' ) );
	}
}

# --------------------
# Print the menu for the summary section
# $p_page specifies the current page name so it's link can be disabled
function print_summary_menu( $p_page = '' ) {
	echo '<div align="center">';
	print_bracket_link( 'print_all_bug_page.php', lang_get( 'print_all_bug_page_link' ) );

	if( config_get( 'use_jpgraph' ) != 0 ) {
		$t_summary_page = 'summary_page.php';
		$t_summary_jpgraph_page = 'summary_jpgraph_page.php';

		switch( $p_page ) {
			case $t_summary_page:
				$t_summary_page = '';
				break;
			case $t_summary_jpgraph_page:
				$t_summary_jpgraph_page = '';
				break;
		}

		print_bracket_link( helper_mantis_url( $t_summary_page ), lang_get( 'summary_link' ) );
		print_bracket_link( helper_mantis_url( $t_summary_jpgraph_page ), lang_get( 'summary_jpgraph_link' ) );
	}
	echo '</div>';
}

# =========================
# Candidates for moving to print_api
# =========================
# --------------------
# Print the color legend for the status colors
function html_status_legend() {
	echo '<br />';
	echo '<table class="width100" cellspacing="1">';
	echo '<tr>';

	$t_status_array = Mantis_Enum::getAssocArrayIndexedByValues( config_get( 'status_enum_string' ) );
	$t_status_names = Mantis_Enum::getAssocArrayIndexedByValues( lang_get( 'status_enum_string' ) );
	$enum_count = count( $t_status_array );

	# read through the list and eliminate unused ones for the selected project
	# assumes that all status are are in the enum array
	$t_workflow = config_get( 'status_enum_workflow' );
	if( !empty( $t_workflow ) ) {
		foreach( $t_status_array as $t_status => $t_name ) {
			if( !isset( $t_workflow[$t_status] ) || ( $t_workflow[$t_status] == '' ) ) {

				# drop elements that are not in the workflow
				unset( $t_status_array[$t_status] );
			}
		}
	}

	# draw the status bar
	$width = (int)( 100 / count( $t_status_array ) );
	foreach( $t_status_array as $t_status => $t_name ) {
		$t_val = $t_status_names[$t_status];
		$t_color = get_status_color( $t_status );

		echo "<td class=\"small-caption\" width=\"$width%\" bgcolor=\"$t_color\">$t_val</td>";
	}

	echo '</tr>';
	echo '</table>';
	if( ON == config_get( 'status_percentage_legend' ) ) {
		html_status_percentage_legend();
	}
}

# --------------------
# Print the legend for the status percentage
function html_status_percentage_legend() {

	$t_mantis_bug_table = db_get_table( 'mantis_bug_table' );
	$t_project_id = helper_get_current_project();
	$t_user_id = auth_get_current_user_id();

	# checking if it's a per project statistic or all projects
	$t_specific_where = helper_project_specific_where( $t_project_id, $t_user_id );

	$query = "SELECT status, COUNT(*) AS number
				FROM $t_mantis_bug_table
				WHERE $t_specific_where
				GROUP BY status";
	$result = db_query( $query );

	$t_bug_count = 0;
	$t_status_count_array = array();

	while( $row = db_fetch_array( $result ) ) {

		$t_status_count_array[$row['status']] = $row['number'];
		$t_bug_count += $row['number'];
	}

	$t_enum_values = Mantis_Enum::getValues( config_get( 'status_enum_string' ) );
	$enum_count = count( $t_enum_values );

	if( $t_bug_count > 0 ) {
		echo '<br />';
		echo '<table class="width100" cellspacing="1">';
		echo '<tr>';
		echo '<td class="form-title" colspan="' . $enum_count . '">' . lang_get( 'issue_status_percentage' ) . '</td>';
		echo '</tr>';
		echo '<tr>';

		foreach ( $t_enum_values as $t_status ) {
			$t_color = get_status_color( $t_status );

			if ( !isset( $t_status_count_array[$t_status] ) ) {
				$t_status_count_array[$t_status] = 0;
			}

			$width = round(( $t_status_count_array[$t_status] / $t_bug_count ) * 100 );

			if( $width > 0 ) {
				echo "<td class=\"small-caption-center\" width=\"$width%\" bgcolor=\"$t_color\">$width%</td>";
			}
		}

		echo '</tr>';
		echo '</table>';
	}
}

# --------------------
# Print an html button inside a form
function html_button( $p_action, $p_button_text, $p_fields = null, $p_method = 'post' ) {
	$p_action = urlencode( $p_action );
	$p_button_text = string_attribute( $p_button_text );
	if( null === $p_fields ) {
		$p_fields = array();
	}

	if( strtolower( $p_method ) == 'get' ) {
		$t_method = 'get';
	} else {
		$t_method = 'post';
	}

	echo "<form method=\"$t_method\" action=\"$p_action\">\n";

	foreach( $p_fields as $key => $val ) {
		$key = string_attribute( $key );
		$val = string_attribute( $val );

		echo "	<input type=\"hidden\" name=\"$key\" value=\"$val\" />\n";
	}

	echo "	<input type=\"submit\" class=\"button\" value=\"$p_button_text\" />\n";
	echo "</form>\n";
}

# --------------------
# Print a button to update the given bug
function html_button_bug_update( $p_bug_id ) {
	if( access_has_bug_level( config_get( 'update_bug_threshold' ), $p_bug_id ) ) {
		html_button( string_get_bug_update_page(), lang_get( 'update_bug_button' ), array( 'bug_id' => $p_bug_id ) );
	}
}

# --------------------
# Print Change Status to: button
#  This code is similar to print_status_option_list except
#   there is no masking, except for the current state
function html_button_bug_change_status( $p_bug_id ) {
	$t_bug_project_id = bug_get_field( $p_bug_id, 'project_id' );
	$t_bug_current_state = bug_get_field( $p_bug_id, 'status' );
	$t_current_access = access_get_project_level( $t_bug_project_id );

	$t_enum_list = get_status_option_list( $t_current_access, $t_bug_current_state, false, ( bug_get_field( $p_bug_id, 'reporter_id' ) == auth_get_current_user_id() && ( ON == config_get( 'allow_reporter_close' ) ) ) );

	if( count( $t_enum_list ) > 0 ) {

		# resort the list into ascending order after noting the key from the first element (the default)
		$t_default_arr = each( $t_enum_list );
		$t_default = $t_default_arr['key'];
		ksort( $t_enum_list );
		reset( $t_enum_list );

		echo "<form method=\"post\" action=\"bug_change_status_page.php\">";

		$t_button_text = lang_get( 'bug_status_to_button' );
		echo "<input type=\"submit\" class=\"button\" value=\"$t_button_text\" />";

		echo " <select name=\"new_status\">";

		# space at beginning of line is important
		foreach( $t_enum_list as $key => $val ) {
			echo "<option value=\"$key\" ";
			check_selected( $key, $t_default );
			echo ">$val</option>";
		}
		echo '</select>';

		$t_bug_id = string_attribute( $p_bug_id );
		echo "<input type=\"hidden\" name=\"bug_id\" value=\"$t_bug_id\" />\n";

		echo "</form>\n";
	}
}

# --------------------
# Print Assign To: combo box of possible handlers
function html_button_bug_assign_to( $p_bug_id ) {

	# make sure status is allowed of assign would cause auto-set-status
	$t_status = bug_get_field( $p_bug_id, 'status' );

	# workflow implementation

	if( ON == config_get( 'auto_set_status_to_assigned' ) && !bug_check_workflow( $t_status, config_get( 'bug_assigned_status' ) ) ) {

		# workflow
		return;
	}

	# make sure current user has access to modify bugs.
	if( !access_has_bug_level( config_get( 'update_bug_assign_threshold', config_get( 'update_bug_threshold' ) ), $p_bug_id ) ) {
		return;
	}

	$t_reporter_id = bug_get_field( $p_bug_id, 'reporter_id' );
	$t_handler_id = bug_get_field( $p_bug_id, 'handler_id' );
	$t_current_user_id = auth_get_current_user_id();
	$t_new_status = ( ON == config_get( 'auto_set_status_to_assigned' ) ) ? config_get( 'bug_assigned_status' ) : $t_status;

	$t_options = array();
	$t_default_assign_to = null;

	if(( $t_handler_id != $t_current_user_id ) && ( access_has_bug_level( config_get( 'handle_bug_threshold' ), $p_bug_id, $t_current_user_id ) ) ) {
		$t_options[] = array(
			$t_current_user_id,
			'[' . lang_get( 'myself' ) . ']',
		);
		$t_default_assign_to = $t_current_user_id;
	}

	if(( $t_handler_id != $t_reporter_id ) && user_exists( $t_reporter_id ) && ( access_has_bug_level( config_get( 'handle_bug_threshold' ), $p_bug_id, $t_reporter_id ) ) ) {
		$t_options[] = array(
			$t_reporter_id,
			'[' . lang_get( 'reporter' ) . ']',
		);

		if( $t_default_assign_to === null ) {
			$t_default_assign_to = $t_reporter_id;
		}
	}

	echo "<form method=\"post\" action=\"bug_assign.php\">";

	$t_button_text = lang_get( 'bug_assign_to_button' );
	echo "<input type=\"submit\" class=\"button\" value=\"$t_button_text\" />";

	echo " <select name=\"handler_id\">";

	# space at beginning of line is important

	$t_already_selected = false;

	foreach( $t_options as $t_entry ) {
		$t_id = string_attribute( $t_entry[0] );
		$t_caption = string_attribute( $t_entry[1] );

		# if current user and reporter can't be selected, then select the first
		# user in the list.
		if( $t_default_assign_to === null ) {
			$t_default_assign_to = $t_id;
		}

		echo "<option value=\"$t_id\" ";

		if(( $t_id == $t_default_assign_to ) && !$t_already_selected ) {
			check_selected( $t_id, $t_default_assign_to );
			$t_already_selected = true;
		}

		echo ">$t_caption</option>";
	}

	# allow un-assigning if already assigned.
	if( $t_handler_id != 0 ) {
		echo "<option value=\"0\"></option>";
	}

	$t_project_id = bug_get_field( $p_bug_id, 'project_id' );

	# 0 means currently selected
	print_assign_to_option_list( 0, $t_project_id );
	echo "</select>";

	$t_bug_id = string_attribute( $p_bug_id );
	echo "<input type=\"hidden\" name=\"bug_id\" value=\"$t_bug_id\" />\n";

	echo "</form>\n";
}

# --------------------
# Print a button to move the given bug to a different project
function html_button_bug_move( $p_bug_id ) {
	$t_status = bug_get_field( $p_bug_id, 'status' );

	if( access_has_bug_level( config_get( 'move_bug_threshold' ), $p_bug_id ) ) {
		html_button( 'bug_actiongroup_page.php', lang_get( 'move_bug_button' ), array( 'bug_arr[]' => $p_bug_id, 'action' => 'MOVE' ) );
	}
}

# --------------------
# Print a button to move the given bug to a different project
function html_button_bug_create_child( $p_bug_id ) {
	if( access_has_bug_level( config_get( 'update_bug_threshold' ), $p_bug_id ) ) {
		html_button( string_get_bug_report_url(), lang_get( 'create_child_bug_button' ), array( 'm_id' => $p_bug_id ) );
	}
}

# --------------------
# Print a button to reopen the given bug
function html_button_bug_reopen( $p_bug_id ) {
	$t_status = bug_get_field( $p_bug_id, 'status' );
	$t_reopen_status = config_get( 'bug_reopen_status' );
	$t_project = bug_get_field( $p_bug_id, 'project_id' );

	if( access_has_bug_level( config_get( 'reopen_bug_threshold' ), $p_bug_id ) || (( bug_get_field( $p_bug_id, 'reporter_id' ) == auth_get_current_user_id() ) && ( ON == config_get( 'allow_reporter_reopen' ) ) ) ) {
		html_button( 'bug_change_status_page.php', lang_get( 'reopen_bug_button' ), array( 'bug_id' => $p_bug_id, 'new_status' => $t_reopen_status, 'reopen_flag' => ON ) );
	}
}

# --------------------
# Print a button to monitor the given bug
function html_button_bug_monitor( $p_bug_id ) {
	if( access_has_bug_level( config_get( 'monitor_bug_threshold' ), $p_bug_id ) ) {
		html_button( 'bug_monitor.php', lang_get( 'monitor_bug_button' ), array( 'bug_id' => $p_bug_id, 'action' => 'add' ) );
	}
}

# --------------------
# Print a button to unmonitor the given bug
#  no reason to ever disallow someone from unmonitoring a bug
function html_button_bug_unmonitor( $p_bug_id ) {
	html_button( 'bug_monitor.php', lang_get( 'unmonitor_bug_button' ), array( 'bug_id' => $p_bug_id, 'action' => 'delete' ) );
}

# --------------------
# Print a button to delete the given bug
function html_button_bug_delete( $p_bug_id ) {
	if( access_has_bug_level( config_get( 'delete_bug_threshold' ), $p_bug_id ) ) {
		html_button( 'bug_actiongroup_page.php', lang_get( 'delete_bug_button' ), array( 'bug_arr[]' => $p_bug_id, 'action' => 'DELETE' ) );
	}
}

# --------------------
# Print a button to create a wiki page
function html_button_wiki( $p_bug_id ) {
	if( wiki_enabled() ) {
		if( access_has_bug_level( config_get( 'update_bug_threshold' ), $p_bug_id ) ) {
			html_button( 'wiki.php', lang_get_defaulted( 'Wiki' ), array( 'id' => $p_bug_id, 'type' => 'issue' ), 'get' );
		}
	}
}

# --------------------
# Print all buttons for view bug pages
function html_buttons_view_bug_page( $p_bug_id ) {
	$t_resolved = config_get( 'bug_resolved_status_threshold' );
	$t_status = bug_get_field( $p_bug_id, 'status' );
	$t_readonly = bug_is_readonly( $p_bug_id );

	echo '<table><tr class="vcenter">';
	if( !$t_readonly ) {
		# UPDATE button
		echo '<td class="center">';
		html_button_bug_update( $p_bug_id );
		echo '</td>';

		# ASSIGN button
		echo '<td class="center">';
		html_button_bug_assign_to( $p_bug_id );
		echo '</td>';

		# Change State button
		echo '<td class="center">';
		html_button_bug_change_status( $p_bug_id );
		echo '</td>';
	}

	# MONITOR/UNMONITOR button
	echo '<td class="center">';
	if( !current_user_is_anonymous() ) {
		if( user_is_monitoring_bug( auth_get_current_user_id(), $p_bug_id ) ) {
			html_button_bug_unmonitor( $p_bug_id );
		} else {
			html_button_bug_monitor( $p_bug_id );
		}
	}
	echo '</td>';

	if( !$t_readonly ) {
		# CREATE CHILD button
		echo '<td class="center">';
		html_button_bug_create_child( $p_bug_id );
		echo '</td>';
	}

	if( $t_resolved <= $t_status ) {
		# resolved is not the same as readonly
		echo '<td class="center">';

		# REOPEN button
		html_button_bug_reopen( $p_bug_id );
		echo '</td>';
	}

	if( !$t_readonly ) {

		# MOVE button
		echo '<td class="center">';
		html_button_bug_move( $p_bug_id );
		echo '</td>';

		# DELETE button
		echo '<td class="center">';
		html_button_bug_delete( $p_bug_id );
		echo '</td>';		
	}

	helper_call_custom_function( 'print_bug_view_page_custom_buttons', array( $p_bug_id ) );

	echo '</tr></table>';
}
