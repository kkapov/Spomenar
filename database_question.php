<?php
    require_once ('model/Question.php');
    require_once ('model/Answer.php');

function addQuestion($question, $answers)
{ //dodavanje pitanja u bazu , dobijemo question object, te asnwers polje ako je question type == 2
// i correct answer nam govori na kojoj je poziciji u polju tocan odgovor

    global $connection;

    try {
        $statement = $connection->prepare("INSERT INTO questions
                                                (question, questionType, imageForQuestion) VALUES
                                                (:question, :questionType, :imageForQuestion)");

        $statement->bindParam(':question', $question->question, PDO::PARAM_STR);
        $statement->bindParam(':questionType', $question->questionType, PDO::PARAM_INT);
        $statement->bindParam(':imageForQuestion', $question->imageForQuestion, PDO::PARAM_STR);

        $statement->execute();
        $questionId = $connection->lastInsertId();//spremimo question u bazu i dobavimo id od pitanja kojeg je mysql sam generirao

        if (sizeof($answers)>0)
        {
          for($i=0;$i < sizeof($answers); $i++){  //ako je question type == 2, postoje ponudjeni odgovori i spremi ih u bazu
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
function getQuestions() { //dobavi pitanja

  global $connection;
  try {

     $statement = $connection->prepare("SELECT * FROM questions");
     $statement->execute();
     $resultArray = Array();

     while($item = $statement->fetchObject()){ //idi kroz redove dobivene iz baze, kreiraj novi question object
          // popuni ga podacima, ako je type 2 onda dobavi ponudjene odgovore sa questionId od questiona, idi
          // kroz redove i popuni object answer, te ga onda dodaj u array answers koji se nalazi u objektu question i onda taj question dodaj u array

          //array_push($resultArray, $item);
          $questionType = $item->questionType;
          $questionId=$item->questionId;


          if($questionType == 2 || $questionType == 4) {
              $statement2 = $connection->prepare("SELECT * FROM options_for_questions WHERE questionId = :questionId");
              $statement2->bindParam(':questionId', $questionId, PDO::PARAM_INT);
              $statement2->execute();
              $item->answers = array();
              while ($item2 = $statement2->fetchObject()) {
                  $value = $item2->value;
                  array_push($item->answers, $value);
              }
          }
          array_push($resultArray, $item);
      }
      return $resultArray;
    }
    catch(PDOException $e) {
        echo $e;
    }
}

/*function getQuestions() { //dobavi pitanja
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
} */


function getAnswersForQuestions($questionId){
    global $connection;

    try {
        $statement = $connection->prepare("SELECT answers.answer as answer, users.username as username, questions.type as type
                                        FROM answers,questions,users WHERE answers.questionId = question Id AND answers.Id = users.id AND questions.id = :questionId ");
        $statement->bindParam(':questionId', $questionId, PDO::PARAM_INT);
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
