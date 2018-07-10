<?php
require_once 'database_connect.php';
require_once 'database_user.php';

if(isset($_POST['action']) && $_POST['action'] === "answer" ){

    //update u bazi korisnici, dohvati podatke i zovi metodu updateBestResult iz file database_user.php
    $userId = $_POST['userId'];
    $answer = $_POST['answer'];
    $questionId=$_POST['questionId'];
    updateAnswer($userId, $questionId, $answer);

    echo json_encode( $result );
    flush();

}
else if(isset($_GET['action']) && $_GET['action'] === "users"){  //dohvacamo sve korisnike

    $users = getUsers(); //dohvacamo korisnike iz baze podataka database_user.php i vrati ih
    echo json_encode( "korisnici" );
    flush();
}
else if(isset($_POST['action']) && $_POST['action'] === "makeAdmin"){  //napravi usera sa poslanim id adminom

    $userId = $_POST['user_id'];

    setUserAdmin($userId); //metoda je u database_user.php
    $users = getUsers();  //dobavi usere sa novim podacima i vrati ih nazad

    echo json_encode( $users );
    flush();
}
?>
