<?php
    //Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Methods, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../../config/Database.php';
    include_once '../../models/Quiz.php';
    include_once '../../controllers/ErrorController.php';

    //Instantiate Database Class
    $database = new Database();
    $db = $database->connect();
    //Instantiate Quiz Class
    $quiz = new Quiz($db);
     //Instatiate Error Controller
    $errorCont = new ErrorController();

    //Get Raw Data
    $data = json_decode(file_get_contents('php://input'));

 	// VALIDATION

    if ($errorCont->checkField($data->new_question, "New Question" , 0, 200)) {
                $quiz->new_question = $data->new_question ;
                $quiz->quizID = $data->quizID;
                $quiz->partID = $data->partID ;
                $quiz->question_id = $data->question_id;
                $quiz->correct = $data->correct;

                if( $quiz->updateTrueorFalse() ){
                    echo json_encode(array('success' => 'T/F Updated successfully.') );
                }else{
                    echo json_encode(array('message' => 'T/F Update failed.') );
                }
            }


    if($errorCont->errors != null){
        echo json_encode(
            $errorCont->errors
        );
    }


?>
