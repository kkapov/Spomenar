<?php
    require_once ('model/Question.php');
    require_once ('model/Answer.php');

function addQuestion($question, $answers){ //dodavanje pitanja u bazu , dobijemo question object, te asnwers polje ako je question type == 2 ili 4

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

function getQuestions() { //dohvati pitanja

  global $connection;
  try {

     $statement = $connection->prepare("SELECT * FROM questions");
     $statement->execute();
     $resultArray = Array();

     while($item = $statement->fetchObject()){ //idi kroz redove dobivene iz baze te ako je tip pitanja 2 ili 4 dohvati ponudene odgovore
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

function updateAnswer($userId,$questionId, $answer) { //spremanje odgovora ili osvježivanje novog

    global $connection;

    try{
        $statement = $connection->prepare("SELECT * FROM answers WHERE id=:id AND questionId=:questionId");
        $statement->bindParam(':id', $userId, PDO::PARAM_INT);
        $statement->bindParam(':questionId', $questionId, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchObject();

        if($result){
            $answerId = $result->id;
            $questionId=$result->questionId;
            $statement = $connection->prepare("UPDATE answers SET textAnswer = :textAnswer
                                          WHERE id = :id AND questionId=:questionId");
            $statement->bindParam(':id', $answerId, PDO::PARAM_INT);
            $statement->bindParam(':textAnswer', $answer, PDO::PARAM_STR);
            $statement->bindParam(':questionId', $questionId, PDO::PARAM_INT);
            $statement->execute();

        }else{
            $statement = $connection->prepare("INSERT INTO answers (questionId, id, textAnswer)
                                                        VALUES (:questionId, :id, :textAnswer)");
            $statement->bindParam(':questionId', $questionId, PDO::PARAM_INT);
            $statement->bindParam(':id', $userId, PDO::PARAM_INT);
            $statement->bindParam(':textAnswer', $answer, PDO::PARAM_STR);
            $statement->execute();
        }
    }
     catch(PDOException $e) {
        echo $e;
    }

}

function getAnswersForQuestions($questionId){ //dohvacanje svih odgovora tog pitanja
    global $connection;

    try {
        $statement = $connection->prepare("SELECT answers.answer as answer, users.username as username, questions.type as type
                                          FROM answers,questions,users WHERE answers.questionId = :questionId AND answers.id = users.id AND questions.questionId = :questionId ");
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
