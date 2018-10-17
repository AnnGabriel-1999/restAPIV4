<?php
    //Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Methods, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../../config/Database.php';
    include_once '../../models/Universal.php';
    include_once '../../models/Users.php';
    include_once '../../controllers/ErrorController.php';
    //Instantiate Database Class
    $database = new Database();
    $db = $database->connect();
    //Instantiate Users Class
    $univ = new Universal($db);
    $users = new Users($db);
    //Instatiate Error Controller
    $errorCont = new ErrorController();
    //Get Raw Data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user_ids = 0;
    $student_id = 0;
    $Fname = "";
    $Mname = "";
    $Lname = "";
    $un = "";
    $SyrID = "";
    $secID = "";
    $sec = "";
    $courseID = "";
    $Course = ""; 

    if($errorCont->checkField($username, 'Username', 8,20)){
        if($errorCont->checkField($password, 'Password' , 8, 31)){
            $res = $users->loginStudent($username, $password);
            if ($res->rowCount() > 0){
                if($row = $res->fetch(PDO::FETCH_ASSOC)){
                    extract($row);
                    $user_ids = $user_id;
                    $Fname = $fname;
                    $Mname = $mname;
                    $Lname = $lname;
                    $un = $username;
                    $SyrID = $schoolyear_id;
                    $secID = $section_id;
                    $sec = $section;
                    $courseID = $course_id;
                    $Course = $course;
                    
                }
                echo json_encode(
                    array('message1' => 'Login successful',
                          'user_id' => $user_ids,
                          'username' => $un,
                          'fname' => $Fname,
                          'mname' => $Mname,
                          'lname' => $Lname,
                          'schoolyear_id' => $SyrID,
                          'section_id' => $secID,
                          'section' => $sec,
                          'course_id' => $course_id,
                          'course' => $course
                        ));
            }else{
                echo json_encode(array('message2' => 'Login failed'));
            }
             
            
        }
    }
        
    

    if ($errorCont->errors != null) {
        echo json_encode($errorCont->errors);
    }