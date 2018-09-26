<?php
include_once '../../config/Database.php';
include_once '../../models/Quiz.php';
include_once '../../controllers/ErrorController.php';
include_once '../../models/Courses.php';
include_once '../../models/Sections.php';

$database = new Database();
$db = $database->connect();
$quiz = new Quiz($db);
$courseModel = new Courses($db);
$sectionModel = new Sections($db);
$errorCont = new ErrorController();

$firstlineSkipper = 0;
$dataCounter = 0;
$success = 0;

if(isset($_FILES['multiple']['tmp_name'])){
	
	if($errorCont->checkExtension($_FILES['multiple']['name'],'csv')){ // CHECK IF UPLOADED FILE IS CSV
		$handle = fopen($_FILES['multiple']['tmp_name'], 'r');
		if($errorCont->checkCSVFormat($handle,6)){
			$handle2 = fopen($_FILES['multiple']['tmp_name'], 'r');
			while ($data = fgetcsv($handle2)) {
				$firstlineSkipper++;
				if($firstlineSkipper > 1){
					$dataCounter++;
					//EXECUTE SUCCESS
					$quiz->quizID = $_POST['quiz_id'];
                    $quiz->part_id = $_POST['part_id'];
                    $quiz->question = $data[0];
                    
                    array_push($quiz->values, $data[1]);
                    array_push($quiz->values, $data[2]);
                    array_push($quiz->values, $data[3]);
                    array_push($quiz->values, $data[4]);

                    $quiz->correct = in_array($data[5], $quiz->values) ? $data[5] : $data[1];

		        	if($quiz->addQuestion()){
                        if($quiz->insertAnswer()){
                        	$success++;
                        }else{
                        	$quiz->deleteFrequentQ();
                        }
                    }
                    $quiz->values = [];
                    $quiz->order = 'a';
				}
			}
			if($success == $dataCounter){
				echo json_encode( array('success' => 'CSV FILE SUCCESSFULLY ADDED!'));
			}else{
				echo json_encode( array('error' => 'Some of the questions failed to upload'));
			}
		}else{
			echo json_encode( array('error' => 'Your File is Unreadable try downloading our format.'));
			fclose($handle);
		}
	}else{
		echo json_encode( array('error' => 'Please Upload CSV Files Only.'));
	}

}elseif (isset($_FILES['TorF']['tmp_name'])) {
	
	if($errorCont->checkExtension($_FILES['TorF']['name'],'csv')){ // CHECK IF UPLOADED FILE IS CSV
		$handle = fopen($_FILES['TorF']['tmp_name'], 'r');
		if($errorCont->checkCSVFormat($handle,2)){
			$handle2 = fopen($_FILES['TorF']['tmp_name'], 'r');
			while ($data = fgetcsv($handle2)) {
				$firstlineSkipper++;
				if($firstlineSkipper > 1){
					$dataCounter++;
					//EXECUTE SUCCESS
					$quiz->quizID = $_POST['quiz_id'];
		        	$quiz->part_id = $_POST['part_id'];
		        	$quiz->question = $data[0];

		        	if($data[1] != 'TRUE' || $data[1] != 'FALSE'){
		        		$quiz->correct = 'TRUE';
		        	}else{
		        		$quiz->correct = $data[1];
		        	}


		        	if($quiz->GenericAddQuestion()){
                        if($quiz->GenericInsertQuestion()){
                        	$success++;
                        }
                    }
				}
			}

			if($success == $dataCounter){
				echo json_encode( array('success' => 'CSV FILE SUCCESSFULLY ADDED!'));
			}
		}else{
			echo json_encode( array('error' => 'Your File is Unreadable try downloading our format.'));
			fclose($handle);
		}
	}else{
		echo json_encode( array('error' => 'Please Upload CSV Files Only.'));
	}

}elseif(isset($_FILES['GTW']['tmp_name'])) {
	if($errorCont->checkExtension($_FILES['GTW']['name'],'csv')){ // CHECK IF UPLOADED FILE IS CSV
		$handle = fopen($_FILES['GTW']['tmp_name'], 'r');
		if($errorCont->checkCSVFormat($handle,2)){
			$handle2 = fopen($_FILES['GTW']['tmp_name'], 'r');
			while ($data = fgetcsv($handle2)) {
				$firstlineSkipper++;
				if($firstlineSkipper > 1){
					$dataCounter++;
					//EXECUTE SUCCESS
					$quiz->quizID = $_POST['quiz_id'];
		        	$quiz->part_id = $_POST['part_id'];
		        	$quiz->question = $data[0];
		        	$quiz->correct = $data[1];

		        	if($quiz->GenericAddQuestion()){
                        if($quiz->GenericInsertQuestion()){
                        	$success++;
                        }
                    }
				}
			}

			if($success == $dataCounter){
				echo json_encode( array('success' => 'CSV FILE SUCCESSFULLY ADDED!'));
			}
		}else{
			echo json_encode( array('error' => 'Your File is Unreadable try downloading our format.'));
			fclose($handle);
		}
	}else{
		echo json_encode( array('error' => 'Please Upload CSV Files Only.'));
	}
}
?>