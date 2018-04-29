<?php

//if sesion is login give outo atribute to a object 
// example echo  User->email;
if($session->is_logged_in()) {
$id = $_SESSION['user_id']; 
$user = User::find_by_id($id);
}
?>