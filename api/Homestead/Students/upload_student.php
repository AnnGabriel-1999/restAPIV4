<?php
    //Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Methods, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../../../config/Database.php';
    include_once '../../../models/Users.php';
    include_once '../../../models/Universal.php';
    include_once '../../../controllers/ErrorController.php';

    //Instantiate Classes
    $database = new Database();
    $db = $database->connect();
    $users = new Users($db); 
    $univ = new Universal($db); 
    $errorCont = new ErrorController();

    //Get Raw Data
    $data = json_decode(file_get_contents('php://input'));

   
        if($errorCont->checkField($data->fname,"First Name",1,101)){
            if($errorCont->checkField($data->fname,"Lastname Name",1,101)){
                
                $users->student_id = $data->student_id;
                $users->section_id = $data->section_id;
                $users->course_id = $data->course_id;
                $users->fname = $data->fname;
                $users->mname = $data->mname;
                $users->lname = $data->lname;

                $res = $univ->selectAll2('students', 'student_id', $data->student_id);
                    
                

                if ($res->rowCount() <= 0){
                    if(!$users->registerStudent()){
                        //$univ->insert2('students_sections', 'student_id', 'section_id', $users->student_id, $users->section_id );
                        //echo json_encode(array('success' => 'Student Insertion Succeed.'));
                        echo json_encode(array('error' => 'Student Insertion Failed.'));
                    }
                }

                if($univ->insert3('students_sections', 'student_id', 'section_id', 'schoolyear_id', $users->student_id, $users->section_id, $data->schoolyear_id )){
                    echo json_encode(array('success' => 'Student Insertion Succeed.'));
                }

                



                
            }
        }
    

    if($errorCont->errors != null){
        echo json_encode($errorCont->errors);
    }
    