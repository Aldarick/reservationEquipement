<?php

/* authValidateUser($user, $pass)
 * 
 * Checks if the specified username/password pair are valid
 * 
 * $user  - The user name
 * $pass  - The password
 * 
 * Returns:
 *   0        - The pair are invalid or do not exist
 *   non-zero - The pair are valid
 */
function authValidateUser($user, $pass)
{
  // Check if we do not have a username/password
  // User can always bind to LDAP anonymously with empty password,
  // therefore we need to block empty password here...
  if (!isset($user) || !isset($pass) || strlen($pass)==0)
  {
    return 0;
  }

  $object = array();
  $object['pass'] = $pass;

  //return authLdapAction("authValidateUserCallback", $user, $object);
  
  return connection_serveur($user, $pass);
}


function connection_serveur($user, $pass)
{
global $ds;
global $ds2;
global $login;
global $login_new;
global $givenname;

global $ldap_host;
global $ldap_port;



	if ($user!='')
	{
		if (isset($pass))
		{ 
        $login=$user;
		$pwd=$pass;
		//$serveur_ad_ip="155.132.189.2";
        //$serveur_ad_ip="155.132.189.130";
		//$serveur_ad_port="389";
        
        $serveur_ad_ip= $ldap_host;
        $serveur_ad_port= $ldap_port;
        
		$pseudoutil="cn=parcours AD,cn=users,dc=olympe,dc=local";
		$passeutil="ridLd@p";
			if (!(connection_ad($serveur_ad_ip,$serveur_ad_port,$pseudoutil,$passeutil)))
			{
 			return false;
 			}
 			else
			{
				if (isset($login_new))
				{
				$pseudoutil2=$login_new;
	 			$passeutil2=$pwd;
					if(connection_ad2($serveur_ad_ip,$serveur_ad_port,$pseudoutil2,$passeutil2))
					{
					$_SESSION['login'] = $login; 
                    return true;
					}
			 		else
					{
					//exit;
			 		}
				}
				else
				{
				//exit;
				}
			}
		}
	}
}

function connection_ad($ip, $port, $user,$pwd)
{
global $ds;
global $ds2;
global $login;
global $login_new;
global $givenname;

global $firstName;
global $lastName;

$correct=false;
$connection=ldap_connect($ip,$port) ;
$ds=$connection;
ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
	if(!$connection)
	{
 	//exit;
 	}
	else
 	{
  	$liaison=ldap_bind($connection,$user,$pwd);
  		if($liaison)
		{
	 	$correct=true;
	 	$filter = "(&(objectClass=user)(samaccountname=".$login.")(cn=*))";
	 	$sr=ldap_search($ds, "cn=users,dc=olympe,dc=local", "$filter");
	 	$info = ldap_get_entries($ds, $sr);
	 	$login_new=$info[0]["dn"];
        
        //test
        
        $firstName = $info[0]["givenname"][0];
        $lastName = $info[0]["sn"][0];
        
        checkIfUserExists($login);
        

// sn nom famille
// cn: nom complet pre + nom
//givenname: pre
		}
	}
ldap_close($connection);
return $correct;
} 


function connection_ad2($ip,$port,$user,$pwd)
{
global $ds;
global $ds2;
global $login;
global $login_new;
global $givenname;
$correct=false;
$connection=ldap_connect($ip,$port) ;
$ds2=$connection;
ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
	if(!$connection)
	{
 	//exit;
 	}
 	else
 	{
  	$liaison=@ldap_bind($connection,$user,$pwd);
  		if($liaison)
		{
		 $correct=true;
		}
	}
ldap_close($connection);
return $correct;
}


function authGetUserLevel($user)
{
  global $tbl_users;
  // User not logged in, user level '0'
  if(!isset($user))
  {
    return 0;
  }
  
  $result = sql_query1("SELECT level FROM $tbl_users WHERE name='" . strtolower(addslashes($user)) . "' LIMIT 1");
  if ($result == -1)
  {
    $sql_error = sql_error();
    if (!empty($sql_error))
    {
      // It's possible that sql_query1 returned -1 because there were no matching rows,
      // so we only trigger an error if there was a genuine SQL error.
      trigger_error($sql_error, E_USER_WARNING);
    }
    return 0;
  }

  return $result;
}

function user_can_maintenance($room_id, $user) {

    global $tbl_users, $tbl_room;
  // User not logged in, user level '0'
  if(!isset($user))
  {
    return False;
  }
  
  $admin = -1;
  $coadmin = -1;
  
  $sql = "SELECT U1.name, U2.name 
          FROM $tbl_room R, $tbl_users U1, $tbl_users U2
          WHERE U1.id = R.admin_id
          AND U2.id = R.coadmin_id
          AND R.id = $room_id";
  //error_log("Requete : " . $sql, 3, "C:/xampp/htdocs/reservation_salles_blanches/log.log");
  $res = sql_query($sql);
  if (! $res)
  {
    trigger_error(sql_error(), E_USER_WARNING);
    fatal_error(FALSE, get_vocab("fatal_db_error"));

  }
  if (sql_count($res) == 1)
  {
    $row = sql_row_keyed($res, 0);
    $admin_id = $row['admin'];
    $coadmin_id = $row['coadmin'];
  }
  sql_free($res);
  
  // return true if the user is the admin, the coadmin, or an administrator
  return ($user == $admin || $user == $coadmin || authGetUserLevel($user) >= 2); 

}

/////////      ACCOUNT CREATION ON FIRST CONNECTION //////////


/*
 * Checks if the user exists. If he doesn't, it will call
 * the function to create it
 *
 */
function checkIfUserExists($user){

    if(!isset($user))
  {
    return 0;
  }
  
  global $tbl_users;
  $operation = "SELECT COUNT(*) FROM $tbl_users WHERE name='" . strtolower(addslashes($user)). "'";
  $result = sql_query1($operation);
  
  if($result == 0){
      createUser($user);
  }
}
  
function createUser($user){
    global $tbl_users;
    global $firstName;
    global $lastName;
    

    $operation = "INSERT INTO $tbl_users (`id`, `level`, `name`, `prenom`, `nom`, `password`, `email`) VALUES (NULL, '1', '$user', '$firstName', '$lastName', 'uselessPASS', NULL)";
    
    
    $r = sql_command($operation);
    if ($r == -1)
    {
        trigger_error(sql_error(), E_USER_WARNING);
      print_header(0, 0, 0, "", "");
      print_footer(TRUE);
    
    }
    
  
  }

?>