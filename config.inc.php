<?php

// $Id: config.inc.php 2211 2011-12-24 09:27:00Z cimorrison $

/**************************************************************************
 *   MRBS Configuration File
 *   Configure this file for your site.
 *   You shouldn't have to modify anything outside this file
 *   (except for the lang.* files, eg lang.en for English, if
 *   you want to change text strings such as "Meeting Room
 *   Booking System", "room" and "area").
 **************************************************************************/

/**********
 * Timezone
 **********/
 
// The timezone your meeting rooms run in. It is especially important
// to set this if you're using PHP 5 on Linux. In this configuration
// if you don't, meetings in a different DST than you are currently
// in are offset by the DST offset incorrectly.
//
// Note that timezones can be set on a per-area basis, so strictly speaking this
// setting should be in areadefaults.inc.php, but as it is so important to set
// the right timezone it is included here.
//
// When upgrading an existing installation, this should be set to the
// timezone the web server runs in.  See the INSTALL document for more information.
//
// A list of valid timezones can be found at http://php.net/manual/timezones.php
// The following line must be uncommented by removing the '//' at the beginning
$timezone = "Europe/Paris";

// If you are using iCalendar notifications of bookings (see the mail settings below)
// then the iCalendar attachment includes a definition of your timezone in 
// VTIMEZONE format.   This defines the timezone, including the rules for Daylight
// Saving Time transitions.    This information is included in the MRBS distribution.
// However, as governments can change the rules periodically, MRBS will check from
// time to time to see if there is a later version available on the web.   If your
// site prevents external access to the web, this check will time out.  However
// you can avoid the timeout and stop MRBS checking for up to date versions by
// setting $zoneinfo_update = FALSE;
$zoneinfo_update = FALSE;

/*******************
 * Database settings
 ******************/
// Which database system: "pgsql"=PostgreSQL, "mysql"=MySQL,
// "mysqli"=MySQL via the mysqli PHP extension
$dbsys = "mysql";
// Hostname of database server. For pgsql, can use "" instead of localhost
// to use Unix Domain Sockets instead of TCP/IP.
$db_host = "localhost";
// Database name:
$db_database = "mrbs";
// Database login user name:
$db_login = "rieublandou";
// Database login password:
$db_password = 'mrbs1604';
// Prefix for table names.  This will allow multiple installations where only
// one database is available
$db_tbl_prefix = "mrbs_";
// Uncomment this to NOT use PHP persistent (pooled) database connections:
// $db_nopersist = 1;


/* Add lines from systemdefaults.inc.php and areadefaults.inc.php below here
   to change the default configuration. Do _NOT_ modify systemdefaults.inc.php
   or areadefaults.inc.php.  */

$auth["session"] = "cookie"; // How to get and keep the user ID. One of
           // "http" "php" "cookie" "ip" "host" "nt" "omni"
           // "remote_user"

$auth["type"] = "ldap2"; // How to validate the user/password. One of "none"
                          // "config" "db" "db_ext" "pop3" "imap" "ldap" "nis"
                          // "nw" "ext".

// Configuration parameters for 'cookie' session scheme

// The encryption secret key for the session tokens. You are strongly
// advised to change this if you use this session scheme
$auth["session_cookie"]["secret"] = "This isn't a very good secret!";
// The expiry time of a session, in seconds. Set to 0 to use session cookies
$auth["session_cookie"]["session_expire_time"] = (60*60*24*30); // 30 days
// Whether to include the user's IP address in their session cookie.
// Increases security, but could cause problems with proxies/dynamic IP
// machines
$auth["session_cookie"]["include_ip"] = FALSE;


// Configuration parameters for 'php' session scheme

// The expiry time of a session, in seconds
// N.B. Long session expiry times rely on PHP not retiring the session
// on the server too early. If you only want session cookies to be used,
// set this to 0.
$auth["session_php"]["session_expire_time"] = (60*60*24*30); // 30 days


// Cookie path override. If this value is set it will be used by the
// 'php' and 'cookie' session schemes to override the default behaviour
// of automatically determining the cookie path to use
//$cookie_path_override = '/mrbs/';

// The list of administrators (can modify other peoples settings).
//
// This list is not needed when using the 'db' authentication scheme EXCEPT
// when upgrading from a pre-MRBS 1.4.2 system that used db authentication.
// Pre-1.4.2 the 'db' authentication scheme did need this list.   When running
// edit_users.php for the first time in a 1.4.2 system or later, with an existing
// users list in the database, the system will automatically add a field to
// the table for access rights and give admin rights to those users in the database
// for whom admin rights are defined here.   After that this list is ignored.
unset($auth["admin"]);              // Include this when copying to config.inc.php
$auth["admin"][] = "127.0.0.1";     // localhost IP address. Useful with IP sessions.
$auth["admin"][] = "sysadmin"; // A user name from the user list. Useful 
                                    // with most other session schemes.
//$auth["admin"][] = "10.0.0.1";
//$auth["admin"][] = "10.0.0.2";
//$auth["admin"][] = "10.0.0.3";

// 'auth_config' user database
// Format: $auth["user"]["name"] = "password";
$auth["user"]["sysadmin"] = "sysadmin";
$auth["user"]["alice"] = "a";
$auth["user"]["bob"] = "b";

// 'session_http' configuration settings
$auth["realm"]  = "mrbs";

// 'session_remote_user' configuration settings
//$auth['remote_user']['login_link'] = '/login/link.html';
//$auth['remote_user']['logout_link'] = '/logout/link.html';

// 'auth_ext' configuration settings
$auth["prog"]   = "";
$auth["params"] = "";

// 'auth_db' configuration settings
// The highest level of access (0=none; 1=user; 2+=admin).    Used in edit_users.php
// In the future we might want a higher level of granularity, eg to distinguish between
// different levels of admin
$max_level = 2;
// The lowest level of admin allowed to view other users
$min_user_viewing_level = 2;
// The lowest level of admin allowed to edit other users
$min_user_editing_level = 2;






// 'auth_ldap' configuration settings

// Where is the LDAP server.
// This can be an array.
$ldap_host = "155.132.189.130";

// If you have a non-standard LDAP port, you can define it here.
// This can be an array.
$ldap_port = 389;




// General settings
// If you want only administrators to be able to make and delete bookings,
// set this variable to TRUE
$auth['only_admin_can_book'] = FALSE;
// If you want only administrators to be able to make repeat bookings,
// set this variable to TRUE
$auth['only_admin_can_book_repeat'] = FALSE;
// If you want only administrators to be able to make bookings spanning
// more than one day, set this variable to TRUE.
$auth['only_admin_can_book_multiday'] = FALSE;
// If you want only administrators to be able to select multiple rooms
// on the booking form then set this to TRUE.  (It doesn't stop ordinary users
// making separate bookings for the same time slot, but it does slow them down).
$auth['only_admin_can_select_multiroom'] = FALSE;
// If you don't want ordinary users to be able to see the other users'
// details then set this to TRUE.  (Only relevant when using 'db' authentication]
$auth['only_admin_can_see_other_users'] = TRUE;
// If you want to prevent the public (ie un-logged in users) from
// being able to view bookings, set this variable to TRUE
$auth['deny_public_access'] = FALSE;
// Set to TRUE if you want admins to be able to perform bulk deletions
// on the Report page.  (It also only shows up if JavaScript is enabled)
$auth['show_bulk_delete'] = FALSE;


/*******************
 * Themes
 *******************/

// Choose a theme for the MRBS.   The theme controls two aspects of the look and feel:
//   (a) the styling:  the most commonly changed colours, dimensions and fonts have been 
//       extracted from the main CSS file and put into the styling.inc file in the appropriate
//       directory in the Themes directory.   If you want to change the colour scheme, you should
//       be able to do it by changing the values in the theme file.    More advanced styling changes
//       can be made by changing the rules in the CSS file.
//   (b) the header:  the header.inc file which contains the function used for producing the header.
//       This enables organisations to plug in their own header functions quite easily, in cases where
//       the desired corporate look and feel cannot be changed using the CSS alone and the mark-up
//       itself needs to be changed.
//
//  MRBS will look for the files "styling.inc" and "header.inc" in the directory Themes/$theme and
//  if it can't find them will use the files in Themes/default.    A theme directory can contain
//  a replacement styling.inc file or a replacement header.inc file or both.

// Available options are:

// "default"        Default MRBS theme
// "classic126"     Same colour scheme as MRBS 1.2.6

$theme = "thales3";



/*********************************
 * Site identification information
 *********************************/
$mrbs_admin = "Administrateur de l'application";
$mrbs_admin_email = "hugues.rieublandou@3-5lab.fr";
// NOTE:  there are more email addresses in $mail_settings below.    You can also give
// email addresses in the format 'Full Name <address>', for example:
// $mrbs_admin_email = 'Booking System <admin_email@your.org>';
// if the name section has any "peculiar" characters in it, you will need
// to put the name in double quotes, e.g.:
// $mrbs_admin_email = '"Bloggs, Joe" <admin_email@your.org>';

// The company name is mandatory.   It is used in the header and also for email notifications.
// The company logo, additional information and URL are all optional.

$mrbs_company = "III-V Lab";   // This line must always be uncommented ($mrbs_company is used in various places)

// Uncomment this next line to use a logo instead of text for your organisation in the header
$mrbs_company_logo = "logo.png";    // name of your logo file.   This example assumes it is in the MRBS directory

// Uncomment this next line for supplementary information after your company name or logo
//$mrbs_company_more_info = "You can put additional information here";  // e.g. "XYZ Department"

// Uncomment this next line to have a link to your organisation in the header
//$mrbs_company_url = "http://3-5lab.fr/";

// This is to fix URL problems when using a proxy in the environment.
// If links inside MRBS appear broken, then specify here the URL of
// your MRBS root directory, as seen by the users. For example:
// $url_base =  "http://webtools.uab.ericsson.se/oam";
// It is also recommended that you set this if you intend to use email
// notifications, to ensure that the correct URL is displayed in the
// notification.
$url_base = "http://intranet/reservation_salles_blanches";


/******************
 * Booking policies
 ******************/

// Most booking policies can be configured on a per-area basis, so these variables
// appear in the areadefaults.inc.php file.

// The settings below are global policy settings

// Set a maximum duration for bookings
$max_duration_enabled = FALSE; // Set to TRUE if you want to enforce a maximum duration
$max_duration_secs = 60*60*2;  // (seconds) - when using "times"
$max_duration_periods = 2;     // (periods) - when using "periods"


/******************
 * Display settings
 ******************/

// [These are all variables that control the appearance of pages and could in time
//  become per-user settings]

// Start of week: 0 for Sunday, 1 for Monday, etc.
$weekstarts = 1;

// Days of the week that should be hidden from display
// 0 for Sunday, 1 for Monday, etc.
// For example, if you want Saturdays and Sundays to be hidden set $hidden_days = array(0,6);
//
// By default the hidden days will be removed completely from the main table in the week and month
// views.   You can alternatively arrange for them to be shown as narrow, greyed-out columns
// by editing the CSS file.   Look for $column_hidden_width in mrbs.css.php.
//
// [Note that although they are hidden from display in the week and month views, they 
// can still be booked from the edit_entry form and you can display the bookings by
// jumping straight into the day view from the date selector.]
$hidden_days = array();

// Trailer date format: 0 to show dates as "Jul 10", 1 for "10 Jul"
$dateformat = 0;

// Time format in pages. 0 to show dates in 12 hour format, 1 to show them
// in 24 hour format
$twentyfourhour_format = 1;

// The number of years back and ahead the date selectors should go
$year_range['back'] = 0;
$year_range['ahead'] = 6;

// Formats used for dates and times.   For formatting options
// see http://php.net/manual/function.strftime.php
$strftime_format['date']         = "%A %d %B %Y";  // Used in Day view
$strftime_format['dayname']      = "%A";           // Used in Month view and edit_entry form
$strftime_format['dayname_cal']  = "%a";           // Used in mini calendars
$strftime_format['month_cal']    = "%B";           // Used in mini calendars
$strftime_format['mon']          = "%b";           // Used in date selectors
$strftime_format['ampm']         = "%p";
$strftime_format['time12']       = "%I:%M%p";      // 12 hour clock
$strftime_format['time24']       = "%H:%M";        // 24 hour clock
$strftime_format['datetime']     = "%c";           // Used in Help
$strftime_format['datetime12']   = "%I:%M:%S%p - %A %d %B %Y";  // 12 hour clock
$strftime_format['datetime24']   = "%H:%M:%S - %A %d %B %Y";    // 24 hour clock
// If you prefer dates as "10 Jul" instead of "Jul 10" ($dateformat = TRUE in
// MRBS 1.4.5 and earlier) then use
// $strftime_format['daymonth']     = "%d %b";
$strftime_format['daymonth']     = "%b %d";        // Used in trailer
$strftime_format['monyear']      = "%b %Y";        // Used in trailer
$strftime_format['monthyear']    = "%B %Y";        // Used in Month view

// Whether or not to display the timezone
$display_timezone = FALSE;

// Results per page for searching:
$search["count"] = 20;

// Page refresh time (in seconds). Set to 0 to disable
$refresh_rate = 0;

// Refresh rate (in seconds) for Ajax checking of valid bookings on the edit_entry page
// Set to 0 to disable
$ajax_refresh_rate = 10;

// Trailer type.   FALSE gives a trailer complete with links to days, weeks and months before
// and after the current date.    TRUE gives a simpler trailer that just has links to the
// current day, week and month.
$simple_trailer = FALSE;

// should areas be shown as a list or a drop-down select box?
//$area_list_format = "list";
$area_list_format = "select";

// Entries in monthly view can be shown as start/end slot, brief description or
// both. Set to "description" for brief description, "slot" for time slot and
// "both" for both. Default is "both", but 6 entries per day are shown instead
// of 12.
$monthly_view_entries_details = "both";

// To view weeks in the bottom (trailer.inc) as week numbers (42) instead of
// 'first day of the week' (13 Oct), set this to TRUE.  Will also give week
// numbers in the month view
$view_week_number = TRUE;

// To display week numbers in the mini-calendars, set this to true. The week
// numbers are only accurate if you set $weekstarts to 1, i.e. set the
// start of the week to Monday
$mincals_week_numbers = TRUE;

// To display times on the x-axis (along the top) and rooms or days on the y-axis (down the side)
// set to TRUE;   the default/traditional version of MRBS has rooms (or days) along the top and
// times along the side.    Transposing the table can be useful if you have a large number of
// rooms and not many time slots.
$times_along_top = FALSE;

// To display the row labels (times, rooms or days) on the right hand side as well as the 
// left hand side in the day and week views, set to TRUE;
// (was called $times_right_side in earlier versions of MRBS)
$row_labels_both_sides = FALSE;

// To display the column headers (times, rooms or days) on the bottom of the table as
// well as the top in the day and week views, set to TRUE;
$column_labels_both_ends = FALSE;

// To display the mini caldandars at the bottom of the day week and month views
// set this value to TRUE
$display_calendar_bottom = FALSE; 

// Define default starting view (month, week or day)
// Default is day
$default_view = "day";

// Define default room to start with (used by index.php)
// Room numbers can be determined by looking at the Edit or Delete URL for a
// room on the admin page.
// Default is 0
$default_room = 0;

// Define clipping behaviour for the cells in the day and week views.
// Set to TRUE if you want the cells in the day and week views to be clipped.   This
// gives a table where all the rows have the same hight, regardless of content.
// Alternatively set to FALSE if you want the cells to expand to fit the content.
// (FALSE not supported in IE6 and IE7 due to their incomplete CSS support)
$clipped = TRUE;                

// Define clipping behaviour for the cells in the month view.                           
// Set to TRUE if you want the cells in the month view to scroll if there are too
// many bookings to display; set to FALSE if you want the table cell to expand to
// accommodate the bookings.   (NOTE: (1) scrolling doesn't work in IE6 and so the table
// cell will always expand in IE6.  (2) In IE8 Beta 2 scrolling doesn't work either and
// the cell content is clipped when $month_cell_scrolling is set to TRUE.)
$month_cell_scrolling = TRUE;

// Define the maximum length of a string that can be displayed in an admin table cell
// (eg the rooms and users lists) before it is truncated.  (This is necessary because 
// you don't want a cell to contain for example a 2 kbyte text string, which could happen
// with user defined fields).
$max_content_length = 20;  // characters

// The maximum length of a database field for which a text input can be used on a form
// (eg when editing a user or room).  If longer than this a text area will be used.
$text_input_max = 70;  // characters

// For inputs that have autocomplete options, eg the area and room match inputs on
// the report page, we can define how many characters need to be input before the 
// options are displayed.  This enables us to prevent a huge long list of options
// being presented.   We define the breakpoints in an array.   For example if we set
// $autocomplete_length_breaks = array(25, 250, 2500); this means that if the number of options
// is less than 25 then they will be displayed when 0 characters are input, ie the input
// receives focus.   If the number of options is less than 250 then they will be displayed
// when 1 character is input and so on.    The array can be as long as you like.   If it
// is empty then the options are displayed when 0 characters are input.
$autocomplete_length_breaks = array(25, 250, 2500);


/*******************
 * Calendar settings
 *******************/

// This setting controls whether to use "clock" or "times" based intervals
// (FALSE and the default) or user defined periods (TRUE).   

// $enable_periods is settable on a per-area basis.

$enable_periods = FALSE;  // Default value for new areas

// Resolution - what blocks can be booked, in seconds.
// Default is half an hour: 1800 seconds.
$resolution = (30 * 60);  // DEFAULT VALUE FOR NEW AREAS

// If the following variable is set to TRUE, the resolution of bookings
// is forced to be the value of $resolution, rather than the resolution set
// for the area in the database.
$force_resolution = FALSE;

// Default duration - default length (in seconds) of a booking.
// Defaults to (60 * 60) seconds, i.e. an hour
$default_duration = (60 * 60);  // DEFAULT VALUE FOR NEW AREAS
// Whether the "All Day" checkbox should be checked by default.  (Note
// that even if this is set to true, $default_duration should still
// be set as that is the duration that will be used when the All Day
// checkbox is unchecked)
$default_duration_all_day = FALSE;  // DEFAULT VALUE FOR NEW AREAS

// The default settings below (along with the 30 minute resolution above)
// give you 24 half-hourly slots starting at 07:00, with the last slot
// being 18:30 -> 19:00

// The beginning of the first slot of the day (DEFAULT VALUES FOR NEW AREAS)
$morningstarts         = 8;   // must be integer in range 0-23
$morningstarts_minutes = 0;   // must be integer in range 0-59

// The beginning of the last slot of the day (DEFAULT VALUES FOR NEW AREAS)
$eveningends           = 18;  // must be integer in range 0-23
$eveningends_minutes   = 0;   // must be integer in range 0-59

/******************
 * Booking policies
 ******************/

// If the variables below are set to TRUE, MRBS will force a minimum and/or maximum advance
// booking time on ordinary users (admins can make bookings for whenever they like).   The
// minimum advance booking time allows you to set a policy saying that users must book
// at least so far in advance.  The maximum allows you to set a policy saying that they cannot
// book more than so far in advance.  How the times are determined depends on whether Periods
// or Times are being used.   The min_book_ahead settings also apply to the deletion of bookings
// (to prevent users deleting bookings that have taken place and trying to avoid being charged; if
// it's a booking in the future past the max_book_ahead time then presumaly nobody minds if the
// booking is deleted)

// DEFAULT VALUES FOR NEW AREAS
$min_book_ahead_enabled = FALSE;    // set to TRUE to enforce a minimum advance booking time
$max_book_ahead_enabled = FALSE;    // set to TRUE to enforce a maximum advance booking time

// The advance booking limits are measured in seconds and are set by the two variables below.
// The relevant time for determining whether a booking is allowed is the start time of the
// booking.  Values may be negative: for example setting $min_book_ahead_secs = -300 means
// that users cannot book more than 5 minutes in the past.

// DEFAULT VALUES FOR NEW AREAS
$min_book_ahead_secs = 0;           // (seconds) cannot book in the past
$max_book_ahead_secs = 60*60*24*7;  // (seconds) no more than one week ahead

// NOTE:  If you are using periods, MRBS has no notion of when the periods occur during the
// day, and so cannot impose policies of the kind "users must book at least one period
// in advance".    However it can impose policies such as "users must book at least
// one day in advance".   The two values above are rounded down to the nearest whole 
// number of days when using periods.   For example 86401 will be rounded down to 86400
// (one day) and 1 will be rounded down to 0.
//
// As MRBS does not know when the periods occur in the day, there is no way of specifying, for example,
// that bookings must be made at least 24 hours in advance.    Setting $min_book_ahead_secs=86400
// will allow somebody to make a booking at 11:59 pm for the first period the next day, which
// which may occur at 8.00 am.



/************************
 * Miscellaneous settings
 ************************/
 
// PRIVATE BOOKINGS SETTINGS

// These settings are all set per area through MRBS.   These are the default
// settings that are used when a new area is created.

// Only administrators or the person who booked a private event can see
// details of the event.  Everyone else just sees that the time/period
// is booked on the schedule.

$private_enabled = FALSE;  // DEFAULT VALUE FOR NEW AREAS
           // Display checkbox in entry page to make
           // the booking private.

$private_mandatory = FALSE;  // DEFAULT VALUE FOR NEW AREAS
           // If TRUE all new/edited entries will 
           // use the value from $private_default when saved.
           // If checkbox is displayed it will be disabled.
           
$private_default = FALSE;  // DEFAULT VALUE FOR NEW AREAS
           // Set default value for "Private" flag on new/edited entries.
           // Used if the $private_enabled checkbox is displayed
           // or if $private_mandatory is set.

$private_override = "none";  // DEFAULT VALUE FOR NEW AREAS
           // Override default privacy behavior. 
           // "none" - Private flag on entry is used
           // "private" - ALL entries are treated as private regardless
           //             of private flag on the entry.
           // "public" - NO entry is treated as private, regardless of
           //            private flag on the entry.
           // Overrides $private_default and $private_mandatory
           // Consider your users' expectations of privacy before
           // changing to "public" or from "private" to "none"

 
// SETTINGS FOR APPROVING BOOKINGS - PER-AREA

// These settings control whether bookings made by ordinary users need to be
// approved by an admin.   The settings here are the default settings for new
// areas.  The settings for individual areas can be changed from within MRBS.

$approval_enabled = FALSE;  // Set to TRUE to enable booking approval

// Set to FALSE if you don't want users to be able to send reminders
// to admins when bookings are still awaiting approval.
$reminders_enabled = FALSE;


// SETTINGS FOR BOOKING CONFIRMATION

// Allows bookings to be marked as "tentative", ie not yet 100% certain,
// and confirmed later.   Useful if you want to reserve a slot but at the same
// time let other people know that there's a possibility it may not be needed.
$confirmation_enabled = TRUE;

// The default confirmation status for new bookings.  (TRUE: confirmed, FALSE: tentative)
// Only used if $confirmation_enabled is TRUE.   If $confirmation_enabled is 
// FALSE, then all new bookings are confirmed automatically.
$confirmed_default = TRUE;



/**********************************************
 * Email settings
 **********************************************/

// WHO TO EMAIL
// ------------
// The following settings determine who should be emailed when a booking is made,
// edited or deleted (though the latter two events depend on the "When" settings below).
// Set to TRUE or FALSE as required
// (Note:  the email addresses for the room and area administrators are set from the
// edit_area_room.php page in MRBS)
$mail_settings['admin_on_bookings']      = TRUE;  // the addresses defined by $mail_settings['recipients'] below
$mail_settings['area_admin_on_bookings'] = TRUE;  // the area administrator
$mail_settings['room_admin_on_bookings'] = TRUE;  // the room administrator
$mail_settings['booker']                 = FALSE;  // the person making the booking
$mail_settings['book_admin_on_approval'] = FALSE;  // the booking administrator when booking approval is enabled
                                                   // (which is the MRBS admin, but this setting allows MRBS
                                                   // to be extended to have separate booking approvers)     

// WHEN TO EMAIL
// -------------
// These settings determine when an email should be sent.
// Set to TRUE or FALSE as required
//
// (Note:  (a) the variables $mail_settings['admin_on_delete'] and
// $mail_settings['admin_all'], which were used in MRBS versions 1.4.5 and
// before are now deprecated.   They are still supported for reasons of backward
// compatibility, but they may be withdrawn in the future.  (b)  the default 
// value of $mail_settings['on_new'] is TRUE for compatibility with MRBS 1.4.5
// and before, where there was no explicit config setting, but mails were always sent
// for new bookings if there was somebody to send them to)

$mail_settings['on_new']    = TRUE;   // when an entry is created
$mail_settings['on_change'] = FALSE;  // when an entry is changed
$mail_settings['on_delete'] = FALSE;  // when an entry is deleted


// WHAT TO EMAIL
// -------------
// These settings determine what should be included in the email
// Set to TRUE or FALSE as required
$mail_settings['details']   = FALSE; // Set to TRUE if you want full booking details;
                                     // otherwise you just get a link to the entry
$mail_settings['html']      = FALSE; // Set to true if you want HTML mail
$mail_settings['icalendar'] = FALSE; // Set to TRUE to include iCalendar details
                                     // which can be imported into a calendar.  (Note:
                                     // iCalendar details will not be sent for areas
                                     // that use periods as there isn't a mapping between
                                     // periods and time of day, so the calendar would not
                                     // be able to import the booking)

// HOW TO EMAIL - LANGUAGE
// -----------------------------------------

// Set the language used for emails (choose an available lang.* file).
$mail_settings['admin_lang'] = 'fr';   // Default is 'en'.


// HOW TO EMAIL - ADDRESSES
// ------------------------
// The email addresses of the MRBS administrator are set in the config file, and
// those of the room and area administrators are set though the edit_area_room.php
// in MRBS.    But if you have set $mail_settings['booker'] above to TRUE, MRBS will
// need the email addresses of ordinary users.   If you are using the "db" 
// authentication method then MRBS will be able to get them from the users table.  But
// if you are using any other authentication scheme then the following settings allow
// you to specify a domain name that will be appended to the username to produce a
// valid email address (eg "@domain.com").
$mail_settings['domain'] = '';
// If you use $mail_settings['domain'] above and username returned by mrbs contains extra
// strings appended like domain name ('username.domain'), you need to provide
// this extra string here so that it will be removed from the username.
$mail_settings['username_suffix'] = '';


// HOW TO EMAIL - BACKEND
// ----------------------
// Set the name of the backend used to transport your mails. Either 'mail',
// 'smtp' or 'sendmail'. Default is 'mail'.
$mail_settings['admin_backend'] = 'mail';

/*******************
 * Sendmail settings
 */
 
// Set the path of the Sendmail program (only used with "sendmail" backend).
// Default is '/usr/bin/sendmail'
$sendmail_settings['path'] = '/usr/bin/sendmail';
// Set additional Sendmail parameters (only used with "sendmail" backend).
// (example "-t -i"). Default is ''
$sendmail_settings['args'] = '';

/*******************
 * SMTP settings
 */

// These settings are only used with the "smtp" backend"
$smtp_settings['host'] = 'localhost';  // SMTP server
$smtp_settings['port'] = 25;           // SMTP port number
$smtp_settings['auth'] = FALSE;        // Whether to use SMTP authentication
$smtp_settings['username'] = '';       // Username (if using authentication)
$smtp_settings['password'] = '';       // Password (if using authentication)


// EMAIL - MISCELLANEOUS
// ---------------------

// Set the email address of the From field. Default is 'admin_email@your.org'
$mail_settings['from'] = 'admin_email@your.org';

// Set the recipient email. Default is 'admin_email@your.org'. You can define
// more than one recipient like this "john@doe.com,scott@tiger.com"
$mail_settings['recipients'] = 'admin_email@your.org';

// Set email address of the Carbon Copy field. Default is ''. You can define
// more than one recipient (see 'recipients')
$mail_settings['cc'] = '';

// Set to TRUE if you want the cc addresses to be appended to the to line.
// (Some email servers are configured not to send emails if the cc or bcc
// fields are set)
$mail_settings['treat_cc_as_to'] = FALSE;

// The filename to be used for iCalendar attachments.   Will always have the
// extension '.ics'
$mail_settings['ics_filename'] = "booking";

// Set this to TRUE if you want MRBS to output debug information to the browser
// when you are sending email.   If you are not getting emails it can be helpful
// by telling you (a) whether the mail functions are being called in the first place
//(b) whether there are addresses to send email to and (c) the result of the mail
// sending operation.
$mail_settings['debug'] = TRUE;
// Set this to TRUE if you do not want any email sent, whatever the rest of the settings.
// This is a global setting that will override anything else.   Useful when testing MRBS.
$mail_settings['disabled'] = TRUE;
 
 
/**********
 * Language
 **********/

// Set this to 1 to disable the automatic language changing MRBS performs
// based on the user's browser language settings. It will ensure that
// the language displayed is always the value of $default_language_tokens,
// as specified below
$disable_automatic_language_changing = 1;

// Set this to a different language specifier to default to different
// language tokens. This must equate to a lang.* file in MRBS.
// e.g. use "fr" to use the translations in "lang.fr" as the default
// translations.  [NOTE: it is only necessary to change this if you
// have disabled automatic language changing above]
$default_language_tokens = "fr";



?>
