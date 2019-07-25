<?php

defined('_JEXEC') or die;

class plgAuthenticationcustomAuthentication extends JPlugin
{

	public function onUserAuthenticate($credentials, $options, &$response)
	{
		//print_r($options);
		$response->type = 'Joomla';
		
		//echo md5(strtolower($credentials['password']));
	    
       // Joomla does not like blank passwords
		if (empty($credentials['password']))
		{
			$response->status        = JAuthentication::STATUS_FAILURE;
			$response->error_message = JText::_('JGLOBAL_AUTH_EMPTY_PASS_NOT_ALLOWED');

			return;
		}
		// Get a database object
		if($options['action']=="core.login.site")
		{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('id, password,loginCount')
			->from('#__users')
			->where('username=' . $db->quote($credentials['username']))
			->where('password=' .$db->quote($credentials['password']));
		}
		else
		{
			$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('id, password')
			->from('#__users')
			->where('username=' . $db->quote($credentials['username']));
			
		}
		//echo $query;
		$db->setQuery($query);
		$result = $db->loadObjectList();
		//print_r($result);
		
		if ($result)
		{
		  
		  //get the correct user login from clear text database
		  
		 // include ("connection.php");   //include "/database/connection.php";       >JMB 4/5/2019 
		 include $_SERVER['DOCUMENT_ROOT']."/database/connection.php";
		
		 
		  $sql = "SELECT * FROM `bedge_data_GroupLoginLookup` WHERE `Username` = '" . $credentials['username'] . "' and `Password1` = '" .$credentials['password']."'";
		  $query=mysqli_query($con,$sql);		 
		 $response1 = mysqli_fetch_array($query);
		  //get correct username/password combination(non-unique usernames in database) from joomla database
		  if(count($result) > 1){
		    foreach($result as $userEntry){
		      if($credentials['password'] == $userEntry->password){
		        $result = $userEntry;
		        break;
		      }
		    }
		  } else {
		    $result = $result[0];
		  }
				
				 //login user using case insensitive password

			$match = JUserHelper::verifyPassword(strtolower($credentials['password']), $result->password, $result->id);
			//if no match check if password is case in-sensitive

			if($match != true){		  
			//echo "crpassword:".$credentials['password']."<br>";
			//echo "password:".$result->password;
			$resultpassword=md5($result->password);
			if($options['action']=="core.login.site")
				{
				$match = JUserHelper::verifyPassword($credentials['password'], $resultpassword, $result->id);
                }
				else
				{
					$match = JUserHelper::verifyPassword($credentials['password'], $result->password, $result->id);
				}
	      }
				//echo "match:".$match; 

			if ($match === true)
			{
				

				// Bring this in line with the rest of the system
				$user               = JUser::getInstance($result->id);
				$response->email    = $user->email;
				$response->fullname = $user->name;
				
				if($options['action']=="core.login.site")
				{
					
					include $_SERVER['DOCUMENT_ROOT']."/database/connection.php";
					
				$app = JFactory::getApplication();
				$jinput = $app->input;
				$jcookie  = $jinput->cookie;

			    $jcookie->set( 'userid', $result->id, 0); // Set cookie data ($name, $value, $expire) $expire == 0: cookie lifetime is of browser session.
				
				/*Custom Bedge_user Lastlogindate and Logincount add functionality start*/
				
				$logincount=$result->loginCount;

				$newlogincount=number_format($logincount+1);
			
				$uid=$result->id;
				
				$sql = "UPDATE `bedge_users` SET `lastvisitDate` = now(), `loginCount` = \"$newlogincount\" WHERE `id` = " . $uid;
				if (mysqli_query($con, $sql)) {
					echo "Record updated successfully";
				} else {
					echo "Error updating record: " . mysqli_error($con);
					}
				//mysqli_close($con);
				
				
				/*Custom Bedge_user Lastlogindate and Logincount functionality end*/
				
				
				/*Custom Bedge_data_GroupLoginLookup Lastlogindate and Logincount functionality start*/
				
				$username=$credentials['username'];
				$password=$credentials['password'];
				
				$sqlbdg = "SELECT  * FROM `bedge_data_GroupLoginLookup` WHERE `UserName` = '" . $username."' AND `Password1`='".$password."'";
				$result = mysqli_query($con,$sqlbdg);
	            $rbdg = mysqli_num_rows($result);
				$resultbdg=mysqli_fetch_array($result);
				//print_r($resultbdg);
				if($rbdg>0)
				{
					$oldlogincountbdg=$resultbdg['LoginCount'];
					$GroupLoginID=$resultbdg['GroupLoginID'];
					$newlogincountbdg=number_format($oldlogincountbdg+1);
					$sql = "UPDATE `bedge_data_GroupLoginLookup` SET `LastLogin` = now(), `LoginCount` = \"$newlogincountbdg\" WHERE `GroupLoginID` = " . $GroupLoginID;
				if (mysqli_query($con, $sql)) {
					echo "Record updated successfully";
				} else {
					echo "Error updating record: " . mysqli_error($con);
					}
					
				}//mysql number of rows bedge_data_GroupLoginLookup condition end
				
				/*Custom Bedge_data_GroupLoginLookup Lastlogindate and Logincount functionality end*/
				
				
				/*Custom bedge_data_GroupLoginMonthYearLookup LastLogin,LoginCount,LoginMonth,LoginYear,Loginmonthyear and Longinmonthyearcount code start*/
    
				$username=$credentials['username'];
				$password=$credentials['password'];
				
				$sqlbdgll = "SELECT  * FROM `bedge_data_GroupLoginMonthYearLookup` WHERE `UserName` = '" . $username."' AND `Password1`='".$password."'";
				$resultbdgroupll = mysqli_query($con,$sqlbdgll);
	            
				$rbdgll = mysqli_num_rows($resultbdgroupll);
				$resultbdgll=mysqli_fetch_array($resultbdgroupll);	
				
				if($rbdgll >0)
				{
					//print_r($resultbdgll);
					
					$oldlogincountbdgll=$resultbdgll['LoginMonthYearCount'];					
					$GroupLoginMonthYearLookupID=$resultbdgll['GroupLoginMonthYearLookupID'];
					$newlogincountbdgll=number_format($oldlogincountbdgll+1);
					
					$now = new \DateTime('now');
					$date=$now->format('d');
					$Loginmonth = $now->format('n');
					$Loginyear = $now->format('Y');
					$Loginmonthyear= $Loginmonth.$Loginyear;
					$sql = "UPDATE `bedge_data_GroupLoginMonthYearLookup` SET `LastLogin` = now(), `LoginCount` = \"$newlogincountbdgll\", `LoginMonth` = \"$Loginmonth\", `LoginYear` = \"$Loginyear\", `LoginMonthYear` = \"$Loginmonthyear\",`LoginMonthYearCount` = \"$newlogincountbdgll\" WHERE `GroupLoginMonthYearLookupID` = " . $GroupLoginMonthYearLookupID;
				if (mysqli_query($con, $sql)) {
					echo "Record updated successfully";
				} else {
					echo "Error updating record: " . mysqli_error($con);
					}
					mysqli_close($con);
					
                }//mysql number of rows bedge_data_GroupLoginMonthYearLookup condition end			
				
				
				/*Custom bedge_data_GroupLoginMonthYearLookup LastLogin,LoginCount,LoginMonth,LoginYear,Loginmonthyear and Longinmonthyearcount code end*/
				
				
				}
				

				if (JFactory::getApplication()->isAdmin())
				{
					$response->language = $user->getParam('admin_language');
				}
				else
				{
					$response->language = $user->getParam('language');
				}

				$response->status        = JAuthentication::STATUS_SUCCESS;
				$response->error_message = '';
			    
			}
			else
			{

				// Invalid password
				$response->status        = JAuthentication::STATUS_FAILURE;
				$response->error_message = JText::_('JGLOBAL_AUTH_INVALID_PASS');
			}
		}
		else
		{
			// Invalid user
			$response->status        = JAuthentication::STATUS_FAILURE;
			$response->error_message = JText::_('JGLOBAL_AUTH_NO_USER');
		}
		// Check the two factor authentication
		if ($response->status == JAuthentication::STATUS_SUCCESS)
		{

			require_once JPATH_ADMINISTRATOR . '/components/com_users/helpers/users.php';

			$methods = UsersHelper::getTwoFactorMethods();

			if (count($methods) <= 1)
			{
				// No two factor authentication method is enabled
				return;
			}

			require_once JPATH_ADMINISTRATOR . '/components/com_users/models/user.php';

			$model = new UsersModelUser;

			// Load the user's OTP (one time password, a.k.a. two factor auth) configuration
			if (!array_key_exists('otp_config', $options))
			{
				$otpConfig             = $model->getOtpConfig($result->id);
				$options['otp_config'] = $otpConfig;
			}
			else
			{
				$otpConfig = $options['otp_config'];
			}

			// Check if the user has enabled two factor authentication
			if (empty($otpConfig->method) || ($otpConfig->method == 'none'))
			{
				// Warn the user if they are using a secret code but they have not
				// enabed two factor auth in their account.
				if (!empty($credentials['secretkey']))
				{
					try
					{
						$app = JFactory::getApplication();

						$this->loadLanguage();

						$app->enqueueMessage(JText::_('PLG_AUTH_JOOMLA_ERR_SECRET_CODE_WITHOUT_TFA'), 'warning');
					}
					catch (Exception $exc)
					{
						// This happens when we are in CLI mode. In this case
						// no warning is issued
						return;
					}
				}

				return;
			}

			// Load the Joomla! RAD layer
			if (!defined('FOF_INCLUDED'))
			{
				include_once JPATH_LIBRARIES . '/fof/include.php';
			}

			// Try to validate the OTP
			FOFPlatform::getInstance()->importPlugin('twofactorauth');

			$otpAuthReplies = FOFPlatform::getInstance()->runPlugins('onUserTwofactorAuthenticate', array($credentials, $options));

			$check = false;

			/*
			 * This looks like noob code but DO NOT TOUCH IT and do not convert
			 * to in_array(). During testing in_array() inexplicably returned
			 * null when the OTEP begins with a zero! o_O
			 */
			if (!empty($otpAuthReplies))
			{
				foreach ($otpAuthReplies as $authReply)
				{
					$check = $check || $authReply;
				}
			}

			// Fall back to one time emergency passwords
			if (!$check)
			{
				// Did the user use an OTEP instead?
				if (empty($otpConfig->otep))
				{
					if (empty($otpConfig->method) || ($otpConfig->method == 'none'))
					{
						// Two factor authentication is not enabled on this account.
						// Any string is assumed to be a valid OTEP.

						return;
					}
					else
					{
						/*
						 * Two factor authentication enabled and no OTEPs defined. The
						 * user has used them all up. Therefore anything they enter is
						 * an invalid OTEP.
						 */
						return;
					}
				}

				// Clean up the OTEP (remove dashes, spaces and other funny stuff
				// our beloved users may have unwittingly stuffed in it)
				$otep  = $credentials['secretkey'];
				$otep  = filter_var($otep, FILTER_SANITIZE_NUMBER_INT);
				$otep  = str_replace('-', '', $otep);
				$check = false;

				// Did we find a valid OTEP?
				if (in_array($otep, $otpConfig->otep))
				{
					// Remove the OTEP from the array
					$otpConfig->otep = array_diff($otpConfig->otep, array($otep));

					$model->setOtpConfig($result->id, $otpConfig);

					// Return true; the OTEP was a valid one
					$check = true;
				}
			}

			if (!$check)
			{
				$response->status        = JAuthentication::STATUS_FAILURE;
				$response->error_message = JText::_('JGLOBAL_AUTH_INVALID_SECRETKEY');
			}
		}
	}
}
