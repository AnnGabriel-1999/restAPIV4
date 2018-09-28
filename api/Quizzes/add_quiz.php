<?php
    // Headers
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Methods, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../../config/Database.php';
    include_once '../../models/Quiz.php';
    include_once '../../controllers/ErrorController.php';

    // Instantiate Classes
    $database = new Database();
    $db = $database->connect();
    $quiz = new Quiz($db);
    $errorCont = new ErrorController();
    

    // Get Raw Data
    $data = json_decode(file_get_contents('php://input'));

     if($errorCont->checkField($_POST['quizTitle'], 'Quiz Title', 5, 250)){
        if($errorCont->checkField($_POST['description'], 'Quiz Description', 10, 250)){
            if(isset($_FILES['file']['tmp_name'])) {
                $filepath = '../../../AdmInterfaceV2/uploads/quiz/'.$_FILES['file']['name'];
                $quiz->filepath = $filepath;
            }
                $quiz->quizTitle = $_POST['quizTitle'];
                $quiz->description =  $_POST['description'];
                $quiz->admin_id = $_POST['admin_id'];
                
                if ($quiz->addQuiz()) {
                    echo json_encode(array('success' => 'Quiz created successfully!'));
                    if(isset($_FILES['file']['tmp_name'])) {
                        move_uploaded_file($_FILES['file']['tmp_name'], $filepath);
                    }
                } else {
                    echo json_encode(array('error' => 'Failed to create quiz!'));
                }
        }   
    }


    if($errorCont->errors != null) {
        echo json_encode (
            $errorCont->errors  
        );
    }