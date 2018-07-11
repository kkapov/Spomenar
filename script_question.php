<?php

require_once 'database_connect.php';
require_once 'database_question.php';

if(isset($_GET['action'])){ //skripta za dobaljanje pitanja, dobavi sva pitanja za proslijedjenu kategoriju
    //i vrati ta pitanja (pitanja dobavi preko file-a database_question.php)
   $message = getQuestions();
    sendJSONandExit($message);

}
function sendJSONandExit( $message )
{
    // Kao izlaz skripte pošalji $message u JSON formatu i prekini izvođenje.
    header( 'Content-type:application/json;charset=utf-8' );
    echo json_encode( $message );
    flush();
    exit( 0 );
}

?>
