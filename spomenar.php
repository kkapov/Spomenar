<?php
    require_once 'user_session.php';
    if(isUserLogined() === -1){
        header("Location: login.php"); //ako korisnik nije logiran preusmjeri na login.php, pocetak do maina je isti kao i kod index.php
    }
    require_once 'model/Question.php';

?>

<?php
    $title = "Spomenar";
    require_once "header.php";
?>

<div id="center">

    <?php
        require_once "user_info.php";
    ?>

    <div id="main">

        <input type="hidden" id="userId" value="<?php echo getSessionUser()->id; ?>"><!-- da u javiscript se moze uzeti id od ulogiranog korisnika-->

        <h3> Spomenar </h3>

        <div style="margin-bottom: 50px">
            <p style="float: right">trenutno pitanje <span id="currectQuestionSpan">0</span> od <span id="allQuestionSpan">0</span></p>
        </div>
        <br />
        <div id="dinamicContent"><!-- div za dinamicko kreiranje elemenata -->

        </div> <?php

                $answers = getAnswersForQuestions($question->questionId);

                foreach ($answers as $answer){
                    addAnswerCell($answer);
                } ?>
    </div>

</div>

<script>

    //globalne varijable koje ce se koristiti u Spomenaru

    var questions = new Array();   //sva pitanja iz odabrane kategorije
    var currentQuestionStep = 1;    //trenutno pitanje
    start();
    function start() { //dobavi pitanja za kategoriju
        $.ajax(
          { //ajax za dobavljanje pitanja
            url : "script_question.php",

            data :
                {
                    action : "get",
                },
            dataType : "json",
            success : function(data)
            {
               //console.log(data);
               max=data.length;
               questions = new Array();
                for(var i = 0; i < max; i++){
              //      console.log("RANDOM (0 i " + data.length + "): " + random);
                    questions.push(data[i]);
                  //  data.splice(random, 1);
                }

              /*  for(var i = 0; i < questions.length; i++){
                   console.log("QUESTION: " + questions[i]['question']);
                }*/
                showQuestion(data); //nako toga pokazi formu za prvo pitanje
            },
            error : function (xhr, status, errorThrown){
                alert("Greska");
            }
        });
    }
    function showQuestion(data){ //pokazi pitanje koje je na redu
        $("#dinamicContent").empty();
        var currentQuestion = questions[currentQuestionStep - 1];
        $("#currectQuestionSpan").html(currentQuestionStep);   //update trenutno pitanje i broj ukupnih pitanja
        $("#allQuestionSpan").html(questions.length);

        var selectedButtonForType2; // varijabla za kliknuti odgovor za type pitanja 2 i 4

        var questionParagraph = $("<p id='questionParagraph'></p>");  //dodaj paragraf sa pitanje u div
        questionParagraph.html(currentQuestion['question']);

        $("#dinamicContent").append(questionParagraph);

        if(currentQuestion['questionType'] == 3){ //ako je type 3, dodaj u sliku u div
          var trImage = $("<table><tr></tr><table>");
          var tdImageLabel = $("<td><p>Odaberite sliku (ne veću od 1MB)</p></td>");
          var tdImage = $("<td><input type='file' id='imageId' name='imageId' accept='image/*' /></td>");
          trImage.append(tdImageLabel);
          trImage.append(tdImage);

          $("#dinamicContent").append(trImage);

        }

        //show answer form
        if(currentQuestion['questionType'] == 1){  // ako su type 1 ili 3 dodaj input tipa text za upisat odgovor
            var answer = $("<input />");
            answer.attr("type", "text");
            answer.attr("id", "answer");
            answer.attr("placeholder", "Unesite odgovor");
            $("#dinamicContent").append("<br>");
            $("#dinamicContent").append(answer);
        }
        else if(currentQuestion['questionType'] == 2){// ako je type 2, onda dodaj 4 buttona sa tekstom iz ponudjenih odgovora

        }
        else if(currentQuestion['questionType'] == 4){// ako je type 2, onda dodaj 2 buttona
        }

        //answer question button
        var answerQuestion = $("<button></button>");  // dodaj odgovoi button u div
        answerQuestion.html("Odgovori");
        answerQuestion.attr("id", "answerButton");
        $("#dinamicContent").append("<br>");
        $("#dinamicContent").append(answerQuestion);
        var questionId=currentQuestion['questionId'];
        answerQuestion.on("click", function () {
           var odgovor=$("#answer").val();
           console.log(odgovor);
           setAnswer(odgovor, questionId);

           var nextQuestion = $("<button></button>");  //dodaj next button, a ako je zadnje pitanje onda button rezultat
           nextQuestion.attr("id", "nextQuestion");
           if((currentQuestionStep) === questions.length){
               nextQuestion.html("Gotovo");
           }else{
               nextQuestion.html("Sljedeće pitanje");
           }
           $("#dinamicContent").append(nextQuestion);

           nextQuestion.on("click", function () {  //click na button, ako je zadnje pitanje onda set result, a ako nije onda povecaj step i ponovo pozovi showQuestion

               if((currentQuestionStep) === questions.length){
                 var aHrefBack = $("<a>Gotovo</a>");
                 aHrefBack.attr("href", "index.php");
                 $("#dinamicContent").append(aHrefBack);

               }else{
                   currentQuestionStep++;
                   showQuestion();
               }

         });

       });

    }

    function setAnswer(answer, questionId) {
        console.log("userID: " +$("#userId").val() + questionId);
        $.ajax({
            url : "script_user.php",
            data :
                {
                    action: "answer",
                    userId: $("#userId").val(),
                    questionId: questionId,
                   answer: answer
                },
            type: "POST",
            dataType : "json"
        });
        $("#dinamicContent").empty();  //izbrisi sve
        $("#answerButton").remove();
        $("#answer").attr("disabled", true);
        $(".answer").attr("disabled", true);
        $(".buttonSelect").attr("disabled", true);
    }
</script>
