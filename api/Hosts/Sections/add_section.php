<?php
    //Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    include_once '../../../config/Database.php';
    include_once '../../../models/Sections.php';

    //Instantiate Database Class
    $database = new Database();
    $db = $database->connect();

    //Instantiate Users Class
    $secs = new Sections($db); 

    //Get Raw Data
    $data = json_decode(file_get_contents('php://input'));
    $secs->course = $data->course;
    $secs->section = $data->section;

    if($secs->getCourseID()){
        if($secs->validateSection()){
            if($secs->addSection()){
                echo json_encode(
                    array('success' => 'Section successfully added.')
                );
            }
        }else{
            echo json_encode(
                $secs->errors
            );
        }
    }else{
        echo json_encode(
            $secs->errors
        );
    }