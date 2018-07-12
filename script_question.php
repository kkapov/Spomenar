<?php

require_once 'database_connect.php';
require_once 'database_question.php';

if(isset($_GET['action']) && $_GET['action'] ==="get_questions" ){ //skripta za dohvaćanje pitanja, dohvati sva pitanja
                                                                //i vrati ta pitanja (pitanja dobavi preko file-a database_question.php)
   $message = getQuestions();
    sendJSONandExit($message);
}

if (isset($_GET['action']) && $_GET['action']=== "allanswers")
{
  $questionId=$_GET['questionId'];
  $message=getAnswersForQuestions($questionId);
  sendJSONandExit($message);
}

if(isset($_POST['action']) && $_POST['action'] === "answer" ){

    //update u bazi korisnici, dohvati podatke i zovi metodu updateBestResult iz file database_user.php
    $userId = $_POST['userId'];
    $answer = $_POST['answer'];
    $questionId=$_POST['questionId'];
    updateAnswer($userId, $questionId, $answer);

    echo json_encode( "success" );
    flush();

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
