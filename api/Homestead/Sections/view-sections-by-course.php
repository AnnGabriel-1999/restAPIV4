<?php
    //Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    include_once '../../../config/Database.php';
    include_once '../../../models/Universal.php';
    include_once '../../../models/Sections.php';
    include_once '../../../controllers/ErrorController.php';
    //Instantiate Database Class
    $database = new Database();
    $db = $database->connect();
    //Instantiate Users Class
    $univ = new Universal($db);
    $secs = new Sections($db);
    $sectionData = array();

    $courseID = $_GET['course_id'];
    $secs->setSchoolYear($_GET['syrid']);
    $secs->setCourseID($_GET['course_id']);
    $res = $secs->viewSectionsByCourse();
    
    if($res->rowCount() > 0) {
        while ($row = $res->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $sectionInfo = array(
                'section_id' => $section_id,
                'section' => $section,
                'year_level' => $year_level,
                'total' => $students
            );
            array_push($sectionData, $sectionInfo);
        }
    }

    echo json_encode ($sectionData); 