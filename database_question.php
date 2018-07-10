<?php
    require_once ('model/Question.php');
    require_once ('model/Answer.php');

function addQuestion($question, $answers)  //dodavanje pitanja u bazu , dobijemo question object, te asnwers polje ako je question type == 2 ili 4
{
    global $connection;

    try {
        $statement = $connection->prepare("INSERT INTO questions
                                                (question, questionType, imageForQuestion) VALUES
                                                (:question, :questionType, :imageForQuestion)");

        $statement->bindParam(':question', $question->question, PDO::PARAM_STR);
        $statement->bindParam(':questionType', $question->questionType, PDO::PARAM_INT);
        $statement->bindParam(':imageForQuestion', $question->imageForQuestion, PDO::PARAM_STR);

        $statement->execute();
        $questionId = $connection->lastInsertId();   //spremimo question u bazu i dobavimo id od pitanja kojeg je mysql sam generirao

        if (sizeof($answers)>0)
        {
          for($i=0;$i < sizeof($answers); $i++){  //ako je question type == 2 i 4, postoje ponudjeni odgovori i spremi ih u bazu
           $statement = $connection->prepare("INSERT INTO options_for_questions
                                                   (questionId, value) VALUES
                                                   (:questionId, :value)");
           $statement->bindParam(':questionId', $questionId, PDO::PARAM_INT);
           $statement->bindParam(':value', $answers[$i], PDO::PARAM_STR);
           $statement->execute();
         }
        }

        return "";
    }
    catch(PDOException $e) {
        echo $e;
    }
}



function getQuestions() { //dohvati pitanja
  global $connection;
  try {
    $statement = $connection->prepare("SELECT * FROM questions");
     $statement->execute();
     $resultArray = Array();
     while($item = $statement->fetchObject()){
       array_push($resultArray, $item);
     }
     return $resultArray;

    }
    catch(PDOException $e) {
        echo $e;
    }
}
