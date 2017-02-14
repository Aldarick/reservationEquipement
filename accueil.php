<?php
// $Id: day.php 2216 2011-12-29 15:32:47Z cimorrison $

require_once "defaultincludes.inc";
require_once "mincals.inc";
require_once "functions_table.inc";

// Get non-standard form variables
$timetohighlight = get_form_var('timetohighlight', 'int');

// Check the user is authorised for this page
checkAuthorised();

// form the room parameter for use in query strings.    We want to preserve room information
// if possible when switching between views
$room_param = (empty($room)) ? "" : "&amp;room=$room";

$timestamp = mktime(12, 0, 0, $month, $day, $year);

// print the page header
print_header($day, $month, $year, $area, isset($room) ? $room : "");

echo "<div id=\"dwm_header\" class=\"screenonly\">\n";


// Show all rooms
echo make_room_select_html_dayphp_accueil('day.php', $room, $year, $month, $day);






require_once "trailer.inc";
?>
