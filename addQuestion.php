<?php
    require_once 'user_session.php';  //pocetak kao i addCategory.php
    if(isUserLogined() === -1){
        header("Location: login.php");
    }

    if(isUserLogined() != 1){
        header("Location: index.php");
    }

    require_once 'model/Question.php';
    require_once 'database_connect.php';
    require_once 'database_question.php';

    $errors = array();

    if(isset($_POST['submit'])){

        //provjera da li ima gresaka t forme
        if(!isset($_POST['questionType'])){
            array_push($errors, "Odaberite tip pitanja!");
        }
        if(!isset($_POST['questionText']) || strlen($_POST['questionText']) === 0){
            array_push($errors, "Unesite pitanje.");
        }

        $questionType = $_POST['questionType'];
        $fullPath = "";

        if($questionType === "2"){
            if(!isset($_POST['answer1']) || strlen($_POST['answer1']) === 0
                || !isset($_POST['answer2']) || strlen($_POST['answer2']) === 0
                || !isset($_POST['answer3']) || strlen($_POST['answer3']) === 0
                || !isset($_POST['answer4']) || strlen($_POST['answer4']) === 0){
                array_push($errors, "Unesite sve četiri opcije odgovora.");
            }
        }
        if($questionType === "4"){
            if(!isset($_POST['option1']) || strlen($_POST['option1']) === 0
                || !isset($_POST['option2']) || strlen($_POST['option2']) === 0){
                array_push($errors, "Unesite obje opcije odgovora.");
            }
          }

        //ako nema gresaka napravi object question i spremi u njega podatke i onda spremi te podatke u bazu podataka

        if(sizeof($errors) == 0){
            $question = new Question();
            $question->question = htmlentities($_POST['questionText']);
            $question->questionType = htmlentities($_POST['questionType']);

            $question->imageForQuestion = $fullPath;

            $answers = array();
            $correct = 0;

            if($question->questionType == '2'){
              $answers[0] = htmlentities($_POST['answer1']);
              $answers[1] = htmlentities($_POST['answer2']);
              $answers[2] = htmlentities($_POST['answer3']);
              $answers[3] = htmlentities($_POST['answer4']);
            }
            else if($question->questionType == '4'){
              $answers[0] = htmlentities($_POST['option1']);
              $answers[1] = htmlentities($_POST['option2']);
            }


            $returnMessage = addQuestion($question, $answers); //dodavanje pitanja u bazu - database_question.php
            if(strlen($returnMessage) > 0){
                array_push($errors, $returnMessage);
            }
        }
      }
?>

<?php
    $title = "Dodajte pitanje";
    require_once "header.php";
?>

<div id="center">

    <?php
        require_once "user_info.php";
    ?>

    <div id="main">

        <div id="error" >
            <?php
            if(sizeof($errors) != 0){
                foreach ($errors as $error){
                    echo $error . "</br>";
                }
            }
            ?>
        </div>

        <h3> Dodajte pitanje </h3>

        <form method="post" action="addQuestion.php" enctype="multipart/form-data"> <!-- pocetak forme za dodavanje pitanja -->
            <label for="questionType">Odaberite tip pitanja:</label>
            <select name="questionType" id="questionType" style="margin-left: 117px; width: 300px">
                <option disabled selected value> -- izaberite opciju -- </option>
                <option value="1">Samo pitanje</option>
                <option value="2">Pitanje sa ponuđenim odgovorima</option>
            <!--    <option value="3">Pitanje sa slikom</option> -->
                <option value="4">Ili - ili</option>
            </select>

            <table id="questionAreaTable">

            </table>
        </form>

    </div>

</div>

<script>
    $("#questionType").change(function() { //hvata event na promjenu selecta sa tipom pitanja
        $("#questionAreaTable").empty();  //prvo sve maknemo iz talbice, pa onda dodajemo elemente forme u tablicu

        var trQuestion = $("<tr></tr>");
        var tdQquestionTekstLabel = $("<td><p>Unesite pitanje</p></td>");
        var tdQuestionTekst = $("<td><textarea rows='5' colls='20' name='questionText' id='questionText' placeholder='Upišite pitanje'></textarea></td>");
        trQuestion.append(tdQquestionTekstLabel);
        trQuestion.append(tdQuestionTekst);
        $("#questionAreaTable").append(trQuestion);


        if($("#questionType option:selected").val() === "2"){

            var trAnswer1 = $("<tr></tr>");
            var trAnswer2 = $("<tr></tr>");
            var trAnswer3 = $("<tr></tr>");
            var trAnswer4 = $("<tr></tr>");

            var tdAnswer1Label = $("<p><p>Unesite prvi ponuđeni odgovor</p></p>");
            var tdAnswer2Label = $("<td><p>Unesite drugi ponuđeni odgovor</p></td>");
            var tdAnswer3Label = $("<td><p>Unesite treći ponuđeni odgovor</p></td>");
            var tdAnswer4Label = $("<td><p>Unesite četvrti ponuđeni odgovor</p></td>");

            var tdAnswer1 = $("<td><input style='width: 190px; float: left' type='text' id='answer1' name='answer1' placeholder='Upišite odgovor' /</td>");
            var tdAnswer2 = $("<td><input style='width: 190px; float: left' type='text' id='answer2' name='answer2' style='float: left' placeholder='Upišite drugi odgovor' /></td>");
            var tdAnswer3 = $("<td><input style='width: 190px; float: left' type='text' id='answer3' name='answer3' style='float: left' placeholder='Upišite treći odgovor' /></td>");
            var tdAnswer4 = $("<td><input style='width: 190px; float: left' type='text' id='answer4' name='answer4' style='float: left' placeholder='Upišite četvrti odgovor' /></td>");


            trAnswer1.append(tdAnswer1Label);
            trAnswer1.append(tdAnswer1);
            trAnswer2.append(tdAnswer2Label);
            trAnswer2.append(tdAnswer2);
            trAnswer3.append(tdAnswer3Label);
            trAnswer3.append(tdAnswer3);
            trAnswer4.append(tdAnswer4Label);
            trAnswer4.append(tdAnswer4);

            $("#questionAreaTable").append(trAnswer1);
            $("#questionAreaTable").append(trAnswer2);
            $("#questionAreaTable").append(trAnswer3);
            $("#questionAreaTable").append(trAnswer4);

        }
        else if($("#questionType option:selected").val() === "4"){

          var trAnswer1 = $("<tr></tr>");
          var trAnswer2 = $("<tr></tr>");

          var tdAnswer1Label = $("<p><p>Unesite prvi ponuđeni odgovor</p></p>");
          var tdAnswer2Label = $("<td><p>Unesite drugi ponuđeni odgovor</p></td>");

          var tdAnswer1 = $("<td><input style='width: 190px; float: left' type='text' id='option1' name='option1' placeholder='Upišite prvi odgovor' /</td>");
          var tdAnswer2 = $("<td><input style='width: 190px; float: left' type='text' id='option2' name='option2' style='float: left' placeholder='Upišite drugi odgovor' /></td>");

          trAnswer1.append(tdAnswer1Label);
          trAnswer1.append(tdAnswer1);
          trAnswer2.append(tdAnswer2Label);
          trAnswer2.append(tdAnswer2);
          $("#questionAreaTable").append(trAnswer1);
          $("#questionAreaTable").append(trAnswer2);
        }
  /*      else if ($("#questionType option:selected").val() === "3"){


        }*/
        var trSubmit = $("<tr></tr>");
        var tdSubmit = $("<td><input type='submit' id='submit' name='submit' value='Dodaj pitanje' /></td>");
        trSubmit.append($("<td></td>"));
        trSubmit.append(tdSubmit);
        $("#questionAreaTable").append(trSubmit);
    });
</script>
