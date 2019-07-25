<?php
include $_SERVER['DOCUMENT_ROOT']."/admin20/database/connection.php";

$Username = addslashes($_POST['Username']);
$Password = addslashes($_POST['Password1']);

//write the new record
$columns = "`GroupID`, `Username`, `Password1`, `Status`, `EnteredBy`, `CreationDate`";
$values = "\"" . $_POST['GroupID'] . "\", \"$Username\", \"$Password\",  \"Active\", \"" . $_SESSION['fullname'] . "\", now()";
$query = "INSERT INTO `bedge_data_GroupLoginLookup` ($columns) VALUES ($values)";
mysqli_query($con,$query);

//get the record that was just written
$query = "SELECT * FROM `bedge_data_GroupLoginLookup` order by `GroupLoginID` desc";
$RSGLL = mysqli_fetch_array(mysqli_query($con,$query));

//write the new record
$columns = "`GroupLoginID`, `GroupID`, `LastLogin`, `CreationDate`";
$values = "\"" . $RSGLL['GroupLoginID'] . "\", \"" . $RSGLL['GroupID'] . "\", now(), now()"; 
$query = "INSERT INTO `bedge_data_GroupLoginMonthYearLookup` ($columns) VALUES ($values)";
mysqli_query($con,$query);

//create record in joomla login table
$columns = "`username`, `password`, `registerDate`";
$values = "\"" . $Username . "\", md5(\"$Password\") , now()";
$query = "INSERT INTO `bedge_users` ($columns) VALUES ($values)";
mysqli_query($con,$query);

//get id from just created user
$query = "SELECT `id` FROM `bedge_users` WHERE `username` = \"$Username\" AND `password` = md5(\"$Password\")";
$uid = mysqli_fetch_array(mysqli_query($con,$query));

//create entry in usergroup map
$columns = "`user_id`, `group_id`";
$values = $uid[0] . ", " . $_POST['groupType'];
$query = "INSERT INTO `bedge_user_usergroup_map` ($columns) VALUES ($values)";
mysqli_query($con,$query);

//nextpage
header('Location: DetailsGroup.php?GID=' . $RSGLL['GroupID']);
?>
