<?php
        //Headers
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
<<<<<<< HEAD
=======
        
>>>>>>> cbac1f60c0f825d17fdee53cb11885c875ad9fd0

        include_once '../../../config/Database.php';
        include_once '../../../models/Universal.php';

        //Instantiate Database Class
        $database = new Database();
        $db = $database->connect();

        //Instantiate Quiz Class
        $univ = new Universal($db);

        $res = $univ->selectAll2('admin_requests', 'status', $_GET['status']);

        if($res->rowCount() > 0) {
            $employeeData = array();
            while ($row = $res->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $empInfo = array(
<<<<<<< HEAD
=======
                    'request_id' => $req_id,
>>>>>>> cbac1f60c0f825d17fdee53cb11885c875ad9fd0
                    'employee_id' => $employee_id,
                    'fname' => $fname,
                    'mname' => $mname,
                    'lname' => $lname,
                    'message' => $message,
<<<<<<< HEAD
                    'date_requested' => $date_requested
=======
                    'date_requested' => $date_requested,
                    'date_processed' => $date_processed
>>>>>>> cbac1f60c0f825d17fdee53cb11885c875ad9fd0
                );
                array_push($employeeData, $empInfo);
            }
            echo json_encode($employeeData);
        }else{
            echo json_encode(array('message' => 'No requests'));
        }
<<<<<<< HEAD
=======

?>
>>>>>>> cbac1f60c0f825d17fdee53cb11885c875ad9fd0
