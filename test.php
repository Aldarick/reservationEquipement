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
    authLdapDebug("Empty username or password passed");
    return 0;
  }

  $object = array();
  $object['pass'] = $pass;

  //return authLdapAction("authValidateUserCallback", $user, $object);
  
  return connection_serveur($user, $pass)
}


function connection_serveur($user, $pass)
{
global $ds;
global $ds2;
global $login;
global $login_new;
global $givenname;
	if (!(isset($user) && $user!=''))
	{
		if (isset($pass))
		{ 
        $login=$user;
		$pwd=$pwd;
		//$serveur_ad_ip="155.132.189.2";
$serveur_ad_ip="155.132.189.130";
		$serveur_ad_port="389";
		$pseudoutil="cn=parcours AD,cn=users,dc=olympe,dc=local";
		$passeutil="ridLd@p";
			if (!(connection_ad($serveur_ad_ip,$serveur_ad_port,$pseudoutil,$passeutil)))
			{
 			echo "<br><br><DIV style='color: red'><b>Authentification echouée sur le serveur. </b><br></DIV>";
 			//echo '<DIV style="color: red"><b>Merci de contacter </b><a href="mailto:myriam.oudart@3-5lab.fr ?subject=Publications sur intranet">Myriam Oudart</a><br><br></DIV>';
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
					}
			 		else
					{
			 		echo "<br><DIV style='color: red'><b>Authentification echouée car le nom et le mot de passe sont incorrects</b></div>";
					exit;
			 		}
				}
				else
				{
				echo '<br><DIV style="color: red"><b>Authentification echouée car le nom et le mot de passe sont incorrects. </b></div>';
				exit;
				}
			}
		}
	}
}

function connection_ad($ip,$port,$user,$pwd)
{
global $ds;
global $ds2;
global $login;
global $login_new;
global $givenname;
$correct=false;
$connection=ldap_connect($ip,$port) ;
$ds=$connection;
ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
	if(!$connection)
	{
 	echo "Probleme connection au serveur AD<br/>";
 	exit;
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
 	echo "Probleme connection au serveur AD<br/>";
 	exit;
 	}
 	else
 	{
  	$liaison=ldap_bind($connection,$user,$pwd);
  		if($liaison)
		{
		 $correct=true;
		}
	}
ldap_close($connection);
return $correct;
}