<?php
    require_once ('user_session.php');  //koristimo metode iz tih fileova u zagradama (mogli smo i jednostavno napisati funkcije iz fileova
//                                          u ovaj file, ali ovako nam je prakticnije jer cemo ih koristit vise puta
    require_once ('database_connect.php');
    require_once('database_user.php');

    if(isUserLogined() != -1){ //ako smo vec prijavljeni, preusmjeravamo usera s ovog filea na file index.php
        header("Location: index.php");
    }

    $errors = array();

    if(isset($_POST['prijava']))
    {                                                                            //ako smo stisnuli prijava izvrši ovu petlju
        if(!isset($_POST['user']) || strlen($_POST['user']) < 1){
            array_push($errors, "Unesite ime");
        }
        if(!isset($_POST['pass']) || strlen($_POST['pass']) < 1 ){
            array_push($errors, "Unesite lozinku");
        }
        if(sizeof($errors) == 0){   //ako nema grešaka, dobavljamo podatke iz forme, zovemo metodu login koja se nalazi u database_user.php da provjerimo
            //ako postoji korisnik s tim username i passwordom u bazi, ako ne postoji, dodajemo gresku u polje gresaka $errors

            $userNameOrEmail = htmlentities($_POST['user']);
            $password = htmlentities($_POST['pass']);
            $cryptedPassword = sha1($password);

            $returnMessage = login($userNameOrEmail, $cryptedPassword);
            if(strlen($returnMessage) > 0){
                array_push($errors, $returnMessage);
            }
        }
    }

?>

<?php
    $title = "Prijava"; //naslov u kartici
    require_once "header.php";
?>

    <div id="error" >
        <?php
        if(sizeof($errors) != 0){
            foreach ($errors as $error){
                echo $error . "</br>";
            }
        }
        ?>
    </div>
    <div id="center">
        <div id="login">  <!-- div s login elementima-->

            <h1>Prijava</h1>

            <form method="post" action="login.php">  <!-- preko posta preko buttona "Prijava" saljemo podatke iz forme u ovaj isti file-->
                <table>

                    <tr>
                        <td><label for="user">Korisnicko ime:</label></td>
                        <td><input name="user" type="text" id="user" placeholder="Korisnicko ime"></td>
                    </tr>

                    <tr>
                        <td><label for="pass">Lozinka:</label></td>
                        <td><input name="pass" type="password" id="pass" placeholder="Lozinka"></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td><input type="submit" value="Prijava" name="prijava"></td>
                    </tr>

                </table>
            </form>

            <a href="registration.php" style="margin-left: 158px" >Nemaš korisnički račun? Registriraj se!</a>

        </div>

    </div>
</div>
