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

<<<<<<< HEAD:api/Homestead/Students/upload_student.php
   
=======
    if($errorCont->checkField($data->student_id,"Student Number",10,11)){
>>>>>>> cbac1f60c0f825d17fdee53cb11885c875ad9fd0:api/Homestead/Students/upload_student.php
        if($errorCont->checkField($data->fname,"First Name",1,101)){
            if($errorCont->checkField($data->fname,"Lastname Name",1,101)){
                $users->setStudentID($data->student_id);
                //$users->student_id = $data->student_id;
                $users->section_id = $data->section_id;
                $users->course_id = $data->course_id;
                $users->fname = $data->fname;
                $users->mname = $data->mname;
                $users->lname = $data->lname;

                $res = $univ->selectAll2('students', 'student_id', $data->student_id);
                    
                

                if ($res->rowCount() <= 0){
                    if(!$users->registerStudent()){
<<<<<<< HEAD:api/Homestead/Students/upload_student.php
                        //$univ->insert2('students_sections', 'student_id', 'section_id', $users->student_id, $users->section_id );
                        //echo json_encode(array('success' => 'Student Insertion Succeed.'));
=======
>>>>>>> cbac1f60c0f825d17fdee53cb11885c875ad9fd0:api/Homestead/Students/upload_student.php
                        echo json_encode(array('error' => 'Student Insertion Failed.'));
                    }
                }

<<<<<<< HEAD:api/Homestead/Students/upload_student.php
                if($univ->insert3('students_sections', 'student_id', 'section_id', 'schoolyear_id', $users->student_id, $users->section_id, $data->schoolyear_id )){
=======
                if($univ->insert3('students_sections', 'student_id', 'section_id', 'schoolyear_id', $users->getStudentID(), $users->section_id, $data->schoolyear_id )){
>>>>>>> cbac1f60c0f825d17fdee53cb11885c875ad9fd0:api/Homestead/Students/upload_student.php
                    echo json_encode(array('success' => 'Student Insertion Succeed.'));
                }

                



                
            }
        }
<<<<<<< HEAD:api/Homestead/Students/upload_student.php
=======
    }
>>>>>>> cbac1f60c0f825d17fdee53cb11885c875ad9fd0:api/Homestead/Students/upload_student.php
    

    if($errorCont->errors != null){
        echo json_encode($errorCont->errors);
    }
    