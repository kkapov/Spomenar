<?php
    require_once 'user_session.php';
    if(isUserLogined() === -1){     //ako user nije logiran, prebacimo ga na login
        header("Location: login.php");
    }

    $title = "Naslovna";
    require_once "header.php";
?>

<div id="center">

    <?php
        require_once "user_info.php";   //alatna traka
    ?>

    <div id="main">

        <p style="text-align: center; width: 100%; font-size: 30px; margin-top: 20px;"> Dobro došli </p>
        <p style="text-align: center; width: 100%; margin-top: 20px;">
        Spomenar se sastoji različitih tipova pitanja. Na svako pitanje možete vidjeti i ostale odgovore. Nema točnog odgovora. Uživajte!
 </p>


    </div>

</div>
