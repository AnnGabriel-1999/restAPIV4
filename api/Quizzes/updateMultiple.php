<?php
    //Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: PUT');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Methods, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../../config/Database.php';
    include_once '../../models/Quiz.php';
    include_once '../../controllers/ErrorController.php';

    $database = new Database();
    $db = $database->connect();
    $quiz = new Quiz($db); 
    $errorCont = new ErrorController();

    //Get Raw Data
    $data = json_decode(file_get_contents('php://input'));

     if($errorCont->checkField($data->question, "Question",1,1001)){
         if($errorCont->checkField($data->a, 'Value of A', 0, 201)){
             if($errorCont->checkField($data->b, 'Value of B', 0, 201)){
                 if($errorCont->checkField($data->c, 'Value of C', 0, 201)){
                     if($errorCont->checkField($data->d, 'Value of D', 0, 201)){
                         if($errorCont->checkField($data->correct, 'Answer', 0, 201)){
                            $quiz->question = $data->question;
                            $quiz->question_id = $data->question_id;
                            array_push($quiz->values, $data->a);
                            array_push($quiz->values, $data->b);
                            array_push($quiz->values, $data->c);
                            array_push($quiz->values, $data->d);
                            $quiz->correct = $data->correct;
                             if($quiz->updateQuestion()){
                                 $quiz->fetchChoices();
                                 $ctr = 0;
                                 for ($x=0; $x<4; $x++){
                                     if($quiz->updateAnswerChoices($quiz->choices_keys[$x], $quiz->values[$x])){
                                         $ctr++;
                                     }
                                 }
                                 
                                 if($ctr==4){
                                     $result = $quiz->selectAll('answer_choices', 'value', $quiz->correct, 'question_id', $quiz->question_id);
                                     while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                         extract($row);
                                       //  $answer_id = $row['answer_id'];
                                         if($quiz->updateSomething('questions', 'answer', $row['choice_id'], 'question_id', $quiz->question_id)){
                                            echo json_encode( array("success' => 'Question updated."));
                                         }else{
                                            echo json_encode( array('error' => 'Question not updated.'));
                                         }
                                     }
                                  
                                 }
                               
                             }else{
                                 echo json_encode( array('error' => 'Theres an error modifying the question.') );
                             }
                         }
                     }
                 }
             }
         }
      
     }
