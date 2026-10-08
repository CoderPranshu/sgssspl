<?php
error_reporting(0);
session_start();
include("includes/connection.php");

?>



<?php


//varible setting
 $name= $_REQUEST['name'];
 

 $subject = "Message from Newsletter form !";
 $to ="info@sgsspl.com";  // change receiving email id 
 $content = "Name : ". $name ; 
 
 
 
 

// check input fields
if ( empty($name))
{
echo"<script type='text/javascript'>alert('Error!');
    window.history.go(-1);
    </script>";
}
else 
{ mail($to,$subject,$content);

    echo"<script type='text/javascript'>alert('Your message sent succesfully  ');
    window.location = 'index.php';
   
    </script>";
}


?>


 