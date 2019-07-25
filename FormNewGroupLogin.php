<?php
//Group
include $_SERVER['DOCUMENT_ROOT']."/admin20/database/connection.php";

$query = "SELECT * FROM `bedge_data_GroupName` WHERE `GroupID` = " . $_GET['GID'];
$row = mysqli_fetch_array(mysqli_query($con,$query));
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
 <link rel="shortcut icon" href="favicon.ico" >
 <link rel="shortcut icon" href="preview_16x16.png" >
 <link rel="icon" href="favicon.ico" type="image/gif" >
 
<title>Buyer's Edge</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-19778023-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body topmargin="0" leftmargin="0">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr align="left" valign="top">
          <td width="20" height="20">&nbsp;</td>
          <td height="20">&nbsp;</td>
          <td width="20" height="20">&nbsp;</td>
        </tr>
        <tr align="left" valign="top">
          <td width="20">&nbsp;</td>
          <td><table class="svcs" width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
              <tr> 
                <td height="20" align="left" valign="top" class="largebold">New Login and/or Password</font></td>
              </tr>
              <tr>
                <td class="largebold" align="left" valign="top"><img src="img/shim_25x10.gif" width="25" height="10"></td>
              </tr>
              <tr> 
                <td align="left" valign="top"> <table class="medium" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr> 
                      <td><img src="img/bg_subhead_left.jpg" height="20"></td>
                      <td width="99%" background="img/bg_subhead_bottop.jpg" class="subhead">&nbsp;LOGIN DETAILS:</td>
                      <td><img src="img/bg_subhead_right.jpg"></td>
                    </tr>
                  </table>
                  <table class="medium" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <form action="view/InsertGroupLogin.php" method="post" name="InsertGroupLogin">
                      <input type="hidden" name="GroupID" value="<?php echo $row['GroupID']; ?>">
                      <tr> 
                        <td colspan="3">&nbsp;</td>
                      </tr>
                      <tr> 
                        <th width="20%" height="35" align="left"><font size=2 face="Arial">&nbsp;Group:</font></th>
                        <td colspan="2">&nbsp;<?php echo $row['GroupName']; ?></td>
                      </tr>
                      <tr> 
                        <th width="20%" height="35" align="left"><font size=2 face="Arial">&nbsp;Username/Group #:</font></th>
                        <td>&nbsp;<input type="text" name="Username" size="25"></td>
                      </tr>
                      <tr> 
                        <th width="20%" height="34" align="left"><font size=2 face="Arial">&nbsp;Password:</font></th>
                        <td>&nbsp;<input type="text" name="Password1" size="25"></td>
                      </tr>
                      <tr>
                        <th width="20%" height="34" align="left"><font size=2 face="Arial">&nbsp;Group Type:</font></th>
                        <td>
                          <select name="groupType">
                          <?php  
                            $query = "SELECT * FROM `bedge_usergroups` WHERE 1";
                            $response = mysqli_query($con,$query);
                            
                            while($row = mysqli_fetch_array($response)){
                              echo "<option value='" . $row['id'] . "'>" . $row['title'] . "</option>";
                            }
                          ?>
                          </select>
                        </td>
                      </tr>
                      <tr> 
                        <td>&nbsp;</td>
                      </tr>
                      <tr> 
                        <th width="20%" height="32" align="left"><font size=2 face="Arial">&nbsp;</font></th>				
						<td width="20%"><input type="submit" value="Submit"></td>
                   		</form>
						<form action="DetailsGroup.php?" method="get">
						<td width="60%">
						<input type="submit" value="Never Mind">
						<input type="hidden" name="GID" value="<?php echo $row['GroupID']; ?>">
						</td>
						</form>
                      </tr>
                  </table></td>
              </tr>
            </table></td>
          <td width="20">&nbsp;</td>
        </tr>
        <tr align="left" valign="top">
          <td width="20" height="20">&nbsp;</td>
          <td height="20">&nbsp;</td>
          <td width="20" height="20">&nbsp;</td>
        </tr>
      </table></td>
  </tr>
</table>
</body>
</html>
