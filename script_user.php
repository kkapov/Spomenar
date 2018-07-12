<?php
require_once 'database_connect.php';
require_once 'database_user.php';

else if(isset($_POST['action']) && $_POST['action'] === "users"){  //dohvacamo sve korisnike

    $users = getUsers(); //dohvacamo korisnike iz baze podataka database_user.php i vrati ih
    echo json_encode( $users );
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
