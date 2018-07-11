<?php   //koristimo session za usera; kada izademo iz igre bez odjave da nam user ostane i dalje zapamcen
require_once ('model/User.php');

session_start();

function setSessionUser ($user) {   //postavljanje aktivnog usera
    $_SESSION["user"] = $user;
}


function getSessionUser () {    //dobavljanje usera
    return $_SESSION["user"];
}

function isUserLogined(){   //ako user nije ulogiran, vracamo -1, a ako je, vraća 1 za admina, a 2 za obicnog igraca
    if(!isset($_SESSION['user'])){
        return -1;
    }
    $user = $_SESSION["user"];
    if($user){
        return $user->userType;
    }
    return -1;
}

?>
