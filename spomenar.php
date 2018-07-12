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
            <p style="float: right">Pitanje:  <span id="currectQuestionSpan">0</span> / <span id="allQuestionSpan">0</span></p>
        </div>
        <br />
        <div id="dinamicContent"><!-- div za dinamicko kreiranje elemenata -->

        </div>
    </div>

</div>

<script>

    //globalne varijable koje ce se koristiti u Spomenaru

    var questions = new Array();
    var niz=new Array();   //sva pitanja iz odabrane kategorije
    var currentQuestionStep = 1;    //trenutno pitanje
    start();
    function start() { //dobavi pitanja za kategoriju
        $.ajax(
          { //ajax za dobavljanje pitanja
            url : "script_question.php",
            data :
                {
                    action : "get_questions",
                },
            dataType : "json",
            success : function(data)
            {
               console.log(data);
               max=data.length;
               questions = new Array();
                for(var i = 0; i < max; i++){
                    questions.push(data[i]);
                }
                showQuestion(data); //nako toga pokazi formu za prvo pitanje
            },
            error : function (xhr, status, errorThrown){
                alert("Greska!");
            }
        });
    }
    function showQuestion(data){ //pokazi pitanje koje je na redu
        $("#dinamicContent").empty();
        var currentQuestion = questions[currentQuestionStep - 1];

        $("#currectQuestionSpan").html(currentQuestionStep);   //update trenutno pitanje i broj ukupnih pitanja
        $("#allQuestionSpan").html(questions.length);

        var selectedButtonForType4; // varijabla za kliknuti odgovor za type pitanja  4

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

        //prikazi answer form
        if(currentQuestion['questionType'] == 1){  // ako su type 1 ili 3 dodaj input tipa text za upisat odgovor
            var answer = $("<input />");
            answer.attr("type", "text");
            answer.attr("id", "answer");
            answer.attr("placeholder", "Unesite odgovor");
            $("#dinamicContent").append("<br>");
            $("#dinamicContent").append(answer);
        }
        else if(currentQuestion['questionType'] == 2){// ako je type 2, onda dodaj 4 checkboxa sa tekstom iz ponudjenih odgovora
            var answers = currentQuestion['answers'];

            console.log(answers);
            for(var i = 0; i < answers.length; i++){
                var checkbox = $("<input type='checkbox' name='answer' value='"+answers[i]+"'/>");
                checkbox.html(answers[i]);
                checkbox.attr("class", "buttonSelect");
                $("#dinamicContent").append(checkbox);


                checkbox.on("click", function () {  //dodaj click butonima
                    selectedButtonForType2 = $(this);
                    console.log(selectedButtonForType2.val());
                    niz.push(selectedButtonForType2.val());
                });
            }
        }
        else if(currentQuestion['questionType'] == 4){ // ako je type 4, onda dodaj 4 checked box
          var answers = currentQuestion['answers'];
          for(var i = 0; i < answers.length; i++){
             var button = $("<input type='radio' name='answer' value='"+ answers[i]+"'>");
             button.html(answers[i]);
              if(i % 2 == 0){
                  button.css("margin-left", "0px");
              }else{
                  button.css("margin-right", "0px");
              }
              button.attr("class", "buttonSelect");
              $("#dinamicContent").append(button);

              button.on("click", function () {  //na click spremi vrijednost
                  selectedButtonForType4 = $(this);
                  console.log(selectedButtonForType4.val());
              });
          }
        }

        //answer question button
        var answerQuestion = $("<button></button>");  // dodaj odgovoi button u div
        answerQuestion.html("Odgovori");
        answerQuestion.attr("id", "answerButton");
        $("#dinamicContent").append("<br>");
        $("#dinamicContent").append(answerQuestion);
        var questionId=currentQuestion['questionId'];
        answerQuestion.on("click", function () {

          if(currentQuestion['questionType'] == 1)
          {
            var odgovor=$("#answer").val();
            console.log(odgovor);
            setAnswer(odgovor, questionId);

          }
          else if(currentQuestion['questionType'] == 2)
          {
            for (var i=0; i < niz.length; i++)
            {
              var odgovor=niz[i];
              setAnswer(odgovor, questionId);
            }
          }
          else if(currentQuestion['questionType'] == 4)
          {
            var odgovor=selectedButtonForType4.val();
            console.log(odgovor);
            setAnswer(odgovor, questionId);
          }

           var nextQuestion = $("<button></button>");  //dodaj next button, a ako je zadnje pitanje onda button rezultat
           nextQuestion.attr("id", "nextQuestion");
           var previousQuestion= $("<button></button>");
           previousQuestion.attr("id", "previousQuestion")
           if((currentQuestionStep) === questions.length){
             var aHrefBack = $("<a>Gotovo</a>");
             aHrefBack.attr("href", "index.php");
             $("#dinamicContent").append(aHrefBack);
           }
           else{
               nextQuestion.html("Sljedeće pitanje");
               $("#dinamicContent").append(nextQuestion);

               previousQuestion.html("Prethodno pitanje");
               $("#dinamicContent").append(previousQuestion);
               nextQuestion.on("click", function () {
               currentQuestionStep++;
               showQuestion();
              });
              previousQuestion.on("click", function () {
              currentQuestionStep--;
              showQuestion();
             });
           }
       });
    }

    function setAnswer(answer, questionId) {  //prikazi u rezultat korisnika i dodaj link za vracanje na index.php
        console.log("userID: " +$("#userId").val());
        console.log("questionId" + questionId);
        $.ajax({
            url : "script_question.php",
            data :
                {
                    action: "answer",
                    userId: $("#userId").val(),
                    questionId: questionId,
                    answer: answer
                },
            success: function(data)
            {
              console.log(data);
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
