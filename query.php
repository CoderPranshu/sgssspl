<?php
error_reporting(0);
session_start();
include("includes/connection.php");

?>



<?php


//varible setting
 $name= $_REQUEST['name'];
 $email = $_REQUEST['Email'];
 $optiona = $_REQUEST['phone'];
 $optionb = $_REQUEST['question'];
 

 $subject = "Message from Contact form !";
 $to ="sgssservices@gmail.com";  // change receiving email id 
 $content = "Name : ". $name. 
 " \r\nContact email : ". $email. 
 "\r\nPhone No. : ".$optiona.
 "\r\nQuestion : ".$optionb
 ; 
 
 
 
 

// check input fields
if ( empty($name)|| empty($email))
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


 