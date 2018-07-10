<?php
    require_once 'user_session.php';
    if(isUserLogined() === -1){
        header("Location: login.php");
    }
    if(isUserLogined() != 1){
        header("Location: index.php");
    }

    require_once 'database_connect.php';
    require_once 'database_user.php';

?>

<?php
    $title = "Korisnici"; //pogledati login.php
    require_once "header.php";
?>

<div id="center">

    <?php
        require_once "user_info.php"; //pogledati index.php
    ?>

    <div id="main">

        <div id="categoryAndUseMain">

            <h3> Korisnici </h3>

            <table id="users">
            </table>

        </div>

    </div>

</div>

<script>

    var users;
    getUsers();

    function getUsers(){ //dohvati korisnike preko ajaxa i pozovi metodu za prikazivanje usera
        $.ajax({
            url : "script_user.php",

            data :
                {
                   action : "users"
                },
            type: "POST",

            dataType : "json",
            success : function(data)
            {
              console.log(data);
                users = data;
                showUsers();
            },
            error : function (xhr, status, errorThrown){
                alert("Ne mogu se dohvatiti korisnici");
            }

        });

    }

    function makeAdmin(userId){ //napravi usera adminom i onda ponovo ispisi usera sa tim novim podacima

        $.ajax({
            url : "script_user.php",

            data :
                {
                    action : "makeAdmin",
                    user_id: userId
                },

            type: "POST",

            dataType : "json",

            success : function(data)
            {
                users = data;
                showUsers();
            },
            error : function (xhr, status, errorThrown){
                alert("greska");
            }

        });

    }

    $("body").on("click", "button.makeAdmin", function () { //dodaj click listener svim make admin buttonima

        makeAdmin($(this).val())  //val() sadrzava id od korisnika (pogledaj showusers metodu)

    });

    function showUsers() {

        $("#users").empty();  //prvo isprazni table, onda idi kroz polje korisnika, ispisi ime u prvu kolonu, a ako korisnik nije admin u drugu kolonu (td)
        // dodaj button za postavljanje user kao admina a ako je, onda je drugi td prazan

        for(var i = 0; i < users.length; i++){
            var tr = $("<tr></tr>");
            var td = $("<td>" + users[i].name + "</td>");
            td.css("width", "300px");
            var td2 = $("<td></td>");
            if(users[i].userType != 1){
                var button = $("<button class='makeAdmin'>Napravi adminom</button>");
                button.val(users[i].id);
                button.css("margin-left", "17px");
                td2.append(button);
            }
            tr.append(td);
            tr.append(td2);
            $("#users").append(tr);
        }
    }

</script>
