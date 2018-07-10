<div id="user_info">
    <a href="myProfile.php">Moj profil</a>
    <div style="height: 40px; width: 1px; float: left; background: white"></div>
    <a href="index.php">Naslovna</a>
    <a href="spomenar.php">Upisi se u spomenar</a>
    <?php if(getSessionUser()->userType == 1){ ?> <a href="users.php">Korisnici</a> <?php } ?>
    <?php if(getSessionUser()->userType == 1){ ?><a href="addQuestion.php">Dodaj pitanje</a> <?php } ?>
    <a style="float: right; margin-right: 30px;" href="logout.php">Odjavi se</a>
</div>
