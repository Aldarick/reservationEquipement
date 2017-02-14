<?php
// $Id: edit_materiau.php  2015-05-04 Rieublandou



require_once "defaultincludes.inc";
require_once "mrbs_sql.inc";


function create_field_entry_timezone()
{
  global $timezone, $zoneinfo_outlook_compatible;
  
  $special_group = "Others";
  
  echo "<div>\n";
  echo "<label for=\"area_timezone\">" . get_vocab("timezone") . ":</label>\n";

  // If possible we'll present a list of timezones that this server supports and
  // which also have a corresponding VTIMEZONE definition.
  // Otherwise we'll just have to let the user type in a timezone, which introduces
  // the possibility of an invalid timezone.
  if (function_exists('timezone_identifiers_list'))
  {
    $timezones = array();
    $timezone_identifiers = timezone_identifiers_list();
    foreach ($timezone_identifiers as $value)
    {
      if (strpos($value, '/') === FALSE)
      {
        // There are some timezone identifiers (eg 'UTC') on some operating
        // systems that don't fit the Continent/City model.   We'll put them
        // into the special group
        $continent = $special_group;
        $city = $value;
      }
      else
      {
        // Note: timezone identifiers can have three components, eg
        // America/Argentina/Tucuman.    To keep things simple we will
        // treat anything after the first '/' as a single city and
        // limit the explosion to two
        list($continent, $city) = explode('/', $value, 2);
      }
      // Check that there's a VTIMEZONE definition
      $tz_dir = ($zoneinfo_outlook_compatible) ? TZDIR_OUTLOOK : TZDIR;  
      $tz_file = "$tz_dir/$value.ics";
      // UTC is a special case because we can always produce UTC times in iCalendar
      if (($city=='UTC') || file_exists($tz_file))
      {
        $timezones[$continent][] = $city;
      }
    }
    
    echo "<select id=\"area_timezone\" name=\"area_timezone\">\n";
    foreach ($timezones as $continent => $cities)
    {
      if (count($cities) > 0)
      {
        echo "<optgroup label=\"" . htmlspecialchars($continent) . "\">\n";
        foreach ($cities as $city)
        {
          if ($continent == $special_group)
          {
            $timezone_identifier = $city;
          }
          else
          {
            $timezone_identifier = "$continent/$city";
          }
          echo "<option value=\"" . htmlspecialchars($timezone_identifier) . "\"" .
               (($timezone_identifier == $timezone) ? " selected=\"selected\"" : "") .
               ">" . htmlspecialchars($city) . "</option>\n";
        }
        echo "</optgroup>\n";
      }
    }
    echo "</select>\n";
  }
  // There is no timezone_identifiers_list() function so we'll just let the
  // user type in a timezone
  else
  {
    echo "<input id=\"area_timezone\" name=\"area_timezone\" value=\"" . htmlspecialchars($timezone) . "\">\n";
  }
  
  echo "</div>\n";
}

// Get non-standard form variables
$phase = get_form_var('phase', 'int');
$materiau = get_form_var('materiau', 'int');
$materiau_name = get_form_var('materiau_name', 'string');

$phase = get_form_var('phase', 'int');
$old_room = get_form_var('old_room', 'int');
$new_room = get_form_var('new_room', 'int');
$room_name = get_form_var('room_name', 'string');
$room_disabled = get_form_var('room_disabled', 'string');
$sort_key = get_form_var('sort_key', 'string');
$old_room_name = get_form_var('old_room_name', 'string');

$description = get_form_var('description', 'string');
$capacity = get_form_var('capacity', 'int');
$materiau_admin_email = get_form_var('materiau_admin_email', 'string');

$custom_html = get_form_var('custom_html', 'string');  // Used for both area and room, but you only ever have one or the other
$change_done = get_form_var('change_done', 'string');
$change_room = get_form_var('change_room', 'string');
$change_materiau = get_form_var('change_materiau', 'string');

$room = get_form_var('room', 'int');
 


// Get the information about the fields in the room table
$fields = sql_field_info($tbl_materiau);

// Get any user defined form variables
foreach($fields as $field)
{
  switch($field['nature'])
  {
    case 'character':
      $type = 'string';
      break;
    case 'integer':
      $type = 'int';
      break;
    // We can only really deal with the types above at the moment
    default:
      $type = 'string';
      break;
  }
  $var = VAR_PREFIX . $field['name'];
  $$var = get_form_var($var, $type);
  if (($type == 'int') && ($$var === ''))
  {
    unset($$var);
  }
}

// Check the user is authorised for this page
checkAuthorised();

// Also need to know whether they have admin rights
$user = getUserName();
$required_level = (isset($max_level) ? $max_level : 2);
$is_admin = (authGetUserLevel($user) >= $required_level);

// Done changing area or room information?

if (isset($change_done))
{
  if (!empty($materiau)) // Get the room the materiau is in
  {
    $room = mrbsGetMateriauRoom($materiau);
  }    
  $row = sql_row_keyed($res, 0);
  Header("Location: admin.php?day=$day&month=$month&year=$year&area=$area");
  exit();
}

// Intialise the validation booleans
$valid_email = TRUE;
$valid_resolution = TRUE;
$enough_slots = TRUE;
$valid_area = TRUE;
$valid_room_name = TRUE;



// PHASE 2
// -------
if ($phase == 2)
{
  // Unauthorised users shouldn't normally be able to reach Phase 2, but just in case
  // they have, check again that they are allowed to be here
  if (isset($change_materiau))
  {
    if (!$is_admin)
    {
      showAccessDenied($day, $month, $year, $area, "");
      exit();
    }
  }
  
  require_once "functions_mail.inc";

  // PHASE 2 (ROOM) - UPDATE THE DATABASE
  // ------------------------------------
    echo "<p> new : " . $new_room . "</p>"; 
    echo "<p> old : " . $old_room . "</p>"; 
  if ((isset($change_materiau) && !empty($materiau)) || 1 == 1)
  {
    // clean up the address list replacing newlines by commas and removing duplicates
    $materiau_admin_email = clean_address_list($materiau_admin_email);
    // put a space after each comma so that the list displays better
    $materiau_admin_email = str_replace(',', ', ', $materiau_admin_email);
    // validate the email addresses
    $valid_email = validate_email_list($materiau_admin_email);
  
    if (FALSE != $valid_email)
    {
      if (empty($capacity))
      {
        $capacity = 0;
      }
    
      // Acquire a mutex to lock out others who might be deleting the new room
      /*if (!sql_mutex_lock("$tbl_room"))
      {
        fatal_error(TRUE, get_vocab("failed_to_acquire"));
      }*/
      // Check the new room still exists
//      if (sql_query1("SELECT COUNT(*) FROM $tbl_room WHERE id=$new_room LIMIT 1") < 1)
//      {
//        $valid_room = FALSE;
//          echo "<p>invalid room</p>";
//      }
      // If so, check that the room name is not already used in the area
      // (only do this if you're changing the room name or the area - if you're
      // just editing the other details for an existing room we don't want to reject
      // the edit because the room already exists!)
      // [SQL escaping done by sql_syntax_casesensitive_equals()]
      elseif ( (($new_room != $old_room) || ($room_name != $old_room_name))
              && sql_query1("SELECT COUNT(*)
                               FROM $tbl_materiau
                              WHERE" . sql_syntax_casesensitive_equals("materiau_name", $materiau_name) . "
                                AND room_id=$new_room
                              LIMIT 1") > 0)
      {
        $valid_room_name = FALSE;
      }
      // If everything is still OK, update the databasae
      else
      {
        // Convert booleans into 0/1 (necessary for PostgreSQL)
        $room_disabled = (!empty($room_disabled)) ? 1 : 0;
        $sql = "UPDATE $tbl_materiau SET ";
        $n_fields = count($fields);
        $assign_array = array();
        foreach ($fields as $field)
        {
          if ($field['name'] != 'id')  // don't do anything with the id field
          {
            switch ($field['name'])
            {
              // first of all deal with the standard MRBS fields
              case 'room_id':
                $assign_array[] = "room_id=$old_room";
                break;
              case 'disabled':
                $assign_array[] = "disabled=$room_disabled";
                break;
              case 'materiau_name':
                $assign_array[] = "materiau_name='" . addslashes($materiau_name) . "'";
                break;
              case 'sort_key':
                $assign_array[] = "sort_key='" . addslashes($sort_key) . "'";
                break;
              case 'description':
                $assign_array[] = "description='" . addslashes($description) . "'";
                break;
              case 'materiau_admin_email':
                $assign_array[] = "materiau_admin_email='" . addslashes($materiau_admin_email) . "'";
                break;
              case 'custom_html':
                //$assign_array[] = "custom_html='" . addslashes($custom_html) . "'";
                break;
              // then look at any user defined fields
              default:
                $var = VAR_PREFIX . $field['name'];
                switch ($field['nature'])
                {
                  case 'integer':
                    if (!isset($$var) || ($$var === ''))
                    {
                      // Try and set it to NULL when we can because there will be cases when we
                      // want to distinguish between NULL and 0 - especially when the field
                      // is a genuine integer.
                      $$var = ($field['is_nullable']) ? 'NULL' : 0;
                    }
                    break;
                  default:
                    $$var = "'" . addslashes($$var) . "'";
                    break;
                }
                // Note that we don't have to escape or quote the fieldname
                // thanks to the restriction on custom field names
                $assign_array[] = $field['name'] . "=" . $$var;
                break;
            }
          }
        }
        $sql .= implode(", ", $assign_array) . " WHERE id=$materiau";
          
        echo "<p>" . $sql . "</p>"; 
        if (sql_command($sql) < 0)
        {
            echo "$room , $new_room";
          echo get_vocab("update_room_failed") . "<br>\n";
          trigger_error(sql_error(), E_USER_WARNING);
          fatal_error(FALSE, get_vocab("fatal_db_error"));
        }
        // if everything is OK, release the mutex and go back to
        // the admin page (for the new area)
        sql_mutex_unlock("$tbl_room");
        Header("Location: edit_area_room.php?change_room=1&phase=1&room=$room");
        exit();
      }
    
      // Release the mutex
      sql_mutex_unlock("$tbl_room");
    }
  }
    else{
        echo "<p> ça marche pas...</p>";
    }
}

// PHASE 1 - GET THE USER INPUT
// ----------------------------

print_header($day, $month, $year, isset($area) ? $area : "", isset($materiau) ? $materiau : "");



// Non-admins will only be allowed to view room details, not change them
// (We would use readonly instead of disabled, but it is not valid for some 
// elements, eg <select>)
$disabled = ($is_admin) ? "" : " disabled=\"disabled\"";

// THE ROOM FORM


if (isset($change_materiau) && !empty($materiau))
{
  $res = sql_query("SELECT * FROM $tbl_materiau WHERE id=$materiau LIMIT 1");
  if (! $res)
  {
    fatal_error(0, get_vocab("error_room") . $materiau . get_vocab("not_found"));
  }
  $row = sql_row_keyed($res, 0);
  
  echo "<h2>\n";
  echo ($is_admin) ? get_vocab("editmateriau") : get_vocab("viewmateriau");
  echo "</h2>\n";
  ?>
  <form class="form_general" id="edit_materiau" action="edit_materiau.php" method="post">
    <fieldset class="admin">
    <legend></legend>
  
      <fieldset>
      <legend></legend>
        <span class="error">
           <?php 
           // It's impossible to have more than one of these error messages, so no need to worry
           // about paragraphs or line breaks.
           echo ((FALSE == $valid_email) ? get_vocab('invalid_email') : "");
           echo ((FALSE == $valid_area) ? get_vocab('invalid_area') : "");
           echo ((FALSE == $valid_room_name) ? get_vocab('invalid_room_name') : "");
           ?>
        </span>
      </fieldset>
    
      <fieldset>
      <legend></legend>
      <input type="hidden" name="room" value="<?php echo $row["room_id"]?>">
      <input type="hidden" name="materiau" value="<?php echo $row["id"]?>">
      <?php
               
               
      $res = sql_query("SELECT R.id, room_name, area_name FROM $tbl_room R, $tbl_area A WHERE R.area_id = A.id");
      if (!$res)
      {
        trigger_error(sql_error(), E_USER_WARNING);
        fatal_error(FALSE, get_vocab("fatal_db_error"));
      }
      if (sql_count($res) == 0)
      {
        fatal_error(FALSE, get_vocab('norooms'));  // should not happen
      }
      
      // The room select box
      echo "<div>\n";
      echo "<label for=\"new_room\">" . get_vocab("room") . ":</label>\n";
      echo "<select id=\"new_room\" name=\"new_room\"disabled=\"disabled\">\n";
        for ($i = 0; ($row_room = sql_row_keyed($res, $i)); $i++)
        {
          echo "<option value=\"" . $row_room['id'] . "\"";
          if ($row_room['id'] == $row['room_id'])
          {
            echo " selected=\"selected\"";
          }
          echo ">" . htmlspecialchars($row_room['area_name']) . " - " . htmlspecialchars($row_room['room_name']) . "</option>\n";
        }  
      echo "</select>\n";
      echo "<input type=\"hidden\" name=\"old_room\" value=\"" . $row['room_id'] . "\">\n";
      echo "</div>\n";
      
      // First of all deal with the standard MRBS fields
    
    
      /*// materiau name
      echo "<div>\n";
      echo "<label for=\"materiau_name\">" . get_vocab("name") . ":</label>\n";
      echo "<input type=\"text\" id=\"materiau_name\" name=\"materiau_name\" value=\"" . htmlspecialchars($row["materiau_name"]) . "\"$disabled>\n";
      echo "<input type=\"hidden\" name=\"old_materiau_name\" value=\"" . htmlspecialchars($row["materiau_name"]) . "\">\n";
      echo "</div>\n";*/
      
      // Status (Enabled or Disabled)
      if ($is_admin)
      {
        echo "<div>\n";
        echo "<label title=\"" . get_vocab("disabled_materiau_note") . "\">" . get_vocab("status") . ":</label>\n";
        echo "<div class=\"group\">\n";
        echo "<label>\n";
        $checked = ($row['disabled']) ? "" : " checked=\"checked\"";
        echo "<input class=\"radio\" type=\"radio\" name=\"materiau_disabled\" value=\"0\"${checked}${disabled}>\n";
        echo get_vocab("enabled") . "</label>\n";
        echo "<label>\n";
        $checked = ($row['disabled']) ? " checked=\"checked\"" : "";
        echo "<input class=\"radio\" type=\"radio\" name=\"materiau_disabled\" value=\"1\"${checked}${disabled}>\n";
        echo get_vocab("disabled") . "</label>\n";
        echo "</div>\n";
        echo "</div>\n";
      }

//      // Sort key
//      if ($is_admin)
//      {
//        echo "<div>\n";
//        echo "<label for=\"sort_key\" title=\"" . get_vocab("sort_key_note") . "\">" . get_vocab("sort_key") . ":</label>\n";
//        echo "<input type=\"text\" id=\"sort_key\" name=\"sort_key\" value=\"" . htmlspecialchars($row["sort_key"]) . "\"$disabled>\n";
//        echo "</div>\n";
//      }

      // Description
      echo "<div>\n";
      echo "<label for=\"description\">" . get_vocab("description") . ":</label>\n";
      echo "<input type=\"text\" id=\"description\" name=\"description\" value=\"" . htmlspecialchars($row["description"]) . "\"$disabled>\n";
      echo "</div>\n";
      

      
      // materiau admin email
      echo "<div>\n";
      echo "<label for=\"materiau_admin_email\" title=\"" . get_vocab("email_list_note") . "\">" . get_vocab("materiau_admin_email") . ":</label>\n";
      echo "<textarea id=\"materiau_admin_email\" name=\"materiau_admin_email\" rows=\"4\" cols=\"40\"$disabled>" . htmlspecialchars($row["materiau_admin_email"]) . "</textarea>\n";
      echo "</div>\n";
      
//      // Custom HTML
//      if ($is_admin)
//      {
//        // Only show the raw HTML to admins.  Non-admins will see the rendered HTML
//        echo "<div>\n";
//        echo "<label for=\"room_custom_html\" title=\"" . get_vocab("custom_html_note") . "\">" . get_vocab("custom_html") . ":</label>\n";
//        echo "<textarea id=\"room_custom_html\" name=\"custom_html\" rows=\"4\" cols=\"40\"$disabled>\n";
//        echo htmlspecialchars($row['custom_html']);
//        echo "</textarea>\n";
//        echo "</div>\n";
//      }
    
//      // then look at any user defined fields  
//      foreach ($fields as $field)
//      {
//        if (!in_array($field['name'], $standard_fields['room']))
//        {
//          echo "<div>\n";
//          $label_text = get_loc_field_name($tbl_room, $field['name']);
//          $var_name = VAR_PREFIX . $field['name'];
//          echo "<label for=\"$var_name\">$label_text:</label>\n";
//          // Output a checkbox if it's a boolean or integer <= 2 bytes (which we will
//          // assume are intended to be booleans)
//          if (($field['nature'] == 'boolean') || 
//              (($field['nature'] == 'integer') && isset($field['length']) && ($field['length'] <= 2)) )
//          {
//            echo "<input type=\"checkbox\" class=\"checkbox\" " .
//                  "id=\"$var_name\" " .
//                  "name=\"$var_name\" " .
//                  "value=\"1\" " .
//                  ((!empty($row[$field['name']])) ? " checked=\"checked\"" : "") .
//                  "$disabled>\n";
//          }
//          // Output a textarea if it's a character string longer than the limit for a
//          // text input
//          elseif (($field['nature'] == 'character') && isset($field['length']) && ($field['length'] > $text_input_max))
//          {
//            echo "<textarea rows=\"8\" cols=\"40\" " .
//                  "id=\"$var_name\" " .
//                  "name=\"$var_name\" " .
//                  "$disabled>\n";
//            echo htmlspecialchars($row[$field['name']]);
//            echo "</textarea>\n";
//          }
//          // Otherwise output a text input
//          else
//          {
//            echo "<input type=\"text\" " .
//                  "id=\"$var_name\" " .
//                  "name=\"$var_name\" " .
//                  "value=\"" . htmlspecialchars($row[$field['name']]) . "\"" .
//                  "$disabled>\n";
//          }
//          echo "</div>\n";
//        }
//      }
      echo "</fieldset>\n";
    
      // Submit and Back buttons (Submit only if they're an admin)  
      echo "<fieldset class=\"submit_buttons\">\n";
      echo "<legend></legend>\n";
      echo "<div id=\"edit_area_room_submit_back\">\n";
      echo "<input class=\"submit\" type=\"submit\" name=\"change_done\" value=\"" . get_vocab("backadmin") . "\">\n";
      echo "</div>\n";
      if ($is_admin)
      { 
        echo "<div id=\"edit_area_room_submit_save\">\n";
        echo "<input type=\"hidden\" name=\"phase\" value=\"2\">";
        echo "<input class=\"submit\" type=\"submit\" name=\"change_materiau\" value=\"" . get_vocab("change") . "\">\n";
        echo "</div>\n";
      }
      echo "</fieldset>\n";
        
      ?>
    </fieldset>
  </form>   
      
  <?php
 
}

require_once "trailer.inc" ?>
