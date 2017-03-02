<?php
class User 
{
	function checkUser($fnm,$lnm,$email) 
	{
        $query = mysql_query("SELECT * FROM `userss` WHERE email = '$email'");
        $result = mysql_fetch_array($query);
        if (!empty($result)) {
            # User is already present
        } else {
            #user not present. Insert a new Record
            $query = mysql_query("INSERT INTO `userss` (fnm,lnm,email) VALUES ('$fnm', '$lnm','$email')");
			
            $query = mysql_query("SELECT * FROM `userss` WHERE email='$email'");
            $result = mysql_fetch_array($query);
            return $result;
        }
        return $result;
    }


}
?>