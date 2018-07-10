<?php

require_once 'database_connect.php';
require_once 'database_question.php';

if(isset($_GET['action'])){ //skripta za dobaljanje pitanja, dobavi sva pitanja za proslijedjenu kategoriju
    //i vrati ta pitanja (pitanja dobavi preko file-a database_question.php)
   $message = getQuestions();
   echo json_encode( $message );
   flush();

}


?>
