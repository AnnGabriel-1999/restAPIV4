<?php
    //Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Methods, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../../../config/Database.php';
    include_once '../../../models/Users.php';
    include_once '../../../controllers/ErrorController.php';

    //Instantiate Classes
    $database = new Database();
    $db = $database->connect();
    $users = new Users($db); 
    $errorCont = new ErrorController();

    //Get Raw Data
    $data = json_decode(file_get_contents('php://input'));

    if($users->verifyStudentID($data->student_id)){  
        if($errorCont->checkField($data->fname,"First Name",1,101)){
            if($errorCont->checkField($data->fname,"Lastname Name",1,101)){
                
                $users->student_id = $data->student_id;
                $users->section_id = $data->section_id;
                $users->course_id = $data->course_id;
                $users->fname = $data->fname;
                $users->mname = $data->mname;
                $users->lname = $data->lname;

                if($users->registerStudent()){
                    echo json_encode(array('success' => 'Student Insertion Succeed.'));
                }else{
                    echo json_encode(array('error' => 'Student Insertion Failed.'));
                }
            }
        }
    }else{
        echo json_encode(array('error' => 'Student ID is already in use.'));
    }

    if($errorCont->errors != null){
        echo json_encode($errorCont->errors);
    }
    