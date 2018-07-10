<?php   // komuniciranje s bazom vezanom uz korisnika
    require_once ('model/User.php');
    require_once ('user_session.php');

function registration($user, $password) { //funkciji prosljedujemo kriptirani password i user object

    global $connection;

    try {
        $statement = $connection->prepare("SELECT * FROM users WHERE name=:name OR email=:email");  //gledamo u bazi je li slobodno korisnicko ime i e-mail
        $statement->bindParam(':name', $user->name, PDO::PARAM_STR);
        $statement->bindParam(':email', $user->email, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchObject();
        if ($result) {
            if($result->name == $user->name){
                return "Korisničko ime je zauzeto.";
            }else{
                return "Email je već zauzet.";
            }
        }else{     //ako je slobodno spremamo podatke u bazu
            $user->id = $connection->lastInsertId();
            $statement = $connection->prepare("INSERT INTO users
                                                (id, name, password, email, userType) VALUES
                                                (:id,:name, :password, :email, :userType)");
            $statement->bindParam(':id', $user->id, PDO::PARAM_INT);
            $statement->bindParam(':name', $user->name, PDO::PARAM_STR);
            $statement->bindParam(':password', $password, PDO::PARAM_STR);
            $statement->bindParam(':email', $user->email, PDO::PARAM_STR);
            $statement->bindParam(':userType', $user->userType, PDO::PARAM_INT);
            $statement->execute();

            $user->id = $connection->lastInsertId();    //pridjelimo id novostvorenom korisniku (id je mysql sam stvorio)
            setSessionUser($user);  //dodamo usera u session (user_session.php)
            header("Location: index.php");
        }
    }
    catch(PDOException $e) {
        echo $e;
    }
}
function login($nameOrEmail, $password) {  //funkciji prosljedujemo kriptirani password i name or email

    global $connection;

    try {
        $statement = $connection->prepare("SELECT * FROM users WHERE name=:name OR email=:email");  //dohvacanje korisnika sa poslanim usernameom ili emailom iz baze
        $statement->bindParam(':name', $nameOrEmail, PDO::PARAM_STR);
        $statement->bindParam(':email', $nameOrEmail, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchObject();    //spremamo dohvacene podatke u $result
        if (!$result) { //ako ne postoji korisnik s tim username ili emailom
            return "Korisničko ime ne postoji u bazi.";
        }else if ( $result->password !==  $password) {  //ako postoji korisnik s tim username ili mailom, ali kriptirana sifra se ne poklapa s podacima iz baze
            return "Lozinka netočna";
        }else{  //puni objekt iz user.php podacima iz baze
            $user = new User();
            $user->id = $result->id;
            $user->name = $result->user;
            $user->email = $result->email;
            $user->userType = $result->userType;
            setSessionUser($user);  //spremi objekt u session (user_session.php)
            header("Location: index.php");  //ulogirali smo se i preusmjeravamo se na index.php
        }
    }
    catch(PDOException $e) {
        echo $e;
    }
}

function getUsers() {  //dohvaćamo sve registrirane korisnike

    global $connection;
    try {
         $statement = $connection->prepare("SELECT * FROM users WHERE id !=:id"); //dohvaćamo sve ostale korisnike
         $statement->bindParam(':id', getSessionUser()->id, PDO::PARAM_INT);
         $statement->execute();

        $userArray = Array();
        while($item = $statement->fetchObject()){ // ide po redovima i popunjava object user i doda je ga u array
            array_push($userArray, $item);
        }
        return $userArray;
    }
    catch(PDOException $e) {
        echo $e;
    }
}

function setUserAdmin($userId) { // postavi korisnika za administratora

    global $connection;

    try {
        $statement = $connection->prepare("UPDATE users SET userType = 1 WHERE id = :userId");
        $statement->bindParam(':userId', $userId,PDO::PARAM_INT);
        $statement->execute();
    }
    catch(PDOException $e) {
        echo $e;
    }
}

function updateAnswer($userId,$questionId, $answer) { //spremanje odgovora

    global $connection;

  /*  try{
        $statement = $connection->prepare("SELECT * FROM answers WHERE id=:id AND questionId=:questionId");
        $statement->bindParam(':id', $userId, PDO::PARAM_INT);
        $statement->bindParam(':questionId', $questionId, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchObject();

        if($result){
            $answerId = $result->id;
            $statement = $connection->prepare("UPDATE answers SET textAnswer = :textAnswer WHERE id = :answer_id");
            $statement->bindParam(':answer_id', $answerId, PDO::PARAM_INT);
            $statement->bindParam(':textAnswer', $answer, PDO::PARAM_STR);
            $statement->execute();
        }else{
            $time = time();
            $statement = $connection->prepare("INSERT INTO answers (questionId, id, textAnswer) VALUES (:questionId, :user_id, :textAnswer)");
            $statement->bindParam(':questionId', $questionId, PDO::PARAM_INT);
            $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $statement->bindParam(':textAnswer', $answer, PDO::PARAM_STR);
            $statement->execute();
        }

    }*/


   try {
      $statement = $connection->prepare("INSERT INTO answers
                                        (questionId, id, textAnswer) VALUES
                                        (:questionId, :id, :textAnswer)");
      $statement->bindParam(':questionId', $questionId, PDO::PARAM_INT);
      $statement->bindParam(':id', $userId, PDO::PARAM_INT);
      $statement->bindParam(':textAnswer', $answer, PDO::PARAM_STR);
      $statement->execute();
    }
     catch(PDOException $e) {
        echo $e;
    }

}
