<?php
include_once '../../config/Database.php';
include_once '../../models/Quiz.php';
include_once '../../controllers/ErrorController.php';
include_once '../../models/Courses.php';
include_once '../../models/Sections.php';
include_once '../../models/Users.php';

$database = new Database();
$db = $database->connect();
$quiz = new Quiz($db);
$courseModel = new Courses($db);
$sectionModel = new Sections($db);
$userModel = new Users($db);
$errorCont = new ErrorController();

$firstlineSkipper = 0;
$dataCounter = 0;
$success = 0;

if (isset($_FILES['multiple']['tmp_name'])){
	
	if($errorCont->checkExtension($_FILES['multiple']['name'],'csv')){ // CHECK IF UPLOADED FILE IS CSV
		$handle = fopen($_FILES['multiple']['tmp_name'], 'r');
		if($errorCont->checkFormat($handle,6)){
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
				echo json_encode( array('error' => 'Failed Uploading of Files'));
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
		if($errorCont->checkFormat($handle,2)){
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
			}else{
				echo json_encode( array('error' => 'Failed Uploading of Files'));
			}
		}else{
			echo json_encode( array('error' => 'Your File is Unreadable try downloading our format.'));
			fclose($handle);
		}
	}else{
		echo json_encode( array('error' => 'Please Upload CSV Files Only.'));
	}

}elseif (isset($_FILES['GTW']['tmp_name'])) {
	if($errorCont->checkExtension($_FILES['GTW']['name'],'csv')){ // CHECK IF UPLOADED FILE IS CSV
		$handle = fopen($_FILES['GTW']['tmp_name'], 'r');
		if($errorCont->checkFormat($handle,2)){
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
			}else{
				echo json_encode( array('error' => 'Failed Uploading of Files'));
			}
		}else{
			echo json_encode( array('error' => 'Your File is Unreadable try downloading our format.'));
			fclose($handle);
		}
	}else{
		echo json_encode( array('error' => 'Please Upload CSV Files Only.'));
	}

}elseif (isset($_FILES['arrange']['tmp_name'])) {
	if($errorCont->checkExtension($_FILES['arrange']['name'],'csv')){ // CHECK IF UPLOADED FILE IS CSV
		$handle = fopen($_FILES['arrange']['tmp_name'], 'r');
		if($errorCont->checkFormat($handle,5)){
			$handle2 = fopen($_FILES['arrange']['tmp_name'], 'r');
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

		        	if($quiz->addQuestion()){
                        $success++;
                    }
                    $quiz->values = [];
                    $quiz->order = 'a';
				}
			}
			if($success == $dataCounter){
				echo json_encode( array('success' => 'CSV FILE SUCCESSFULLY ADDED!'));
			}else{
				echo json_encode( array('error' => 'Failed Uploading of Files'));
			}
		}else{
			echo json_encode( array('error' => 'Your File is Unreadable try downloading our format.'));
			fclose($handle);
		}
	}else{
		echo json_encode( array('error' => 'Please Upload CSV Files Only.'));
	}
}elseif (isset($_FILES['students']['tmp_name'])) {
	$rejects = array();
	$rejects['missingData'] = array();
	$rejects['wrongLen'] = array();
	$rejects['duplicate'] = array();
	$rejects['nonConvert'] = array();
	
	if($errorCont->checkExtension($_FILES['students']['name'],'csv')){ // CHECK IF UPLOADED FILE IS CSV
		$handle = fopen($_FILES['students']['tmp_name'], 'r');
		if($errorCont->checkFormat($handle,6)){
			$handle2 = fopen($_FILES['students']['tmp_name'], 'r');
				while ($datarow = fgetcsv($handle2)) {
					$dataCounter++;
					if($dataCounter>1){
						if($errorCont->checkCSVFormat($datarow,6)){ // check lang if kumpleto provided nyang data sa isang line
							if($userModel->checkStudId($datarow[0]) || $umoosya = true){ //check lang if may nag eexist nang student na ganto sa data base
								if($sectionModel->checkIfConvertable($datarow[2]) && $courseModel->checkIfConvertable($datarow[1])){ //check if convertible yung mga section hoho
									if($errorCont->checkLen() ){ //check nya kung tama yung data length ng lahat ng input
										$userModel->student_id = $datarow[0];
										$userModel->section_id = $sectionModel->foundSecId;
										$userModel->course_id = $courseModel->foundCourseId;
										$userModel->fname = $datarow[3];
										$userModel->mname = $datarow[4];
										$userModel->lname = $datarow[5];
										$userModel->registerStudent();
										$success++;
									}else{
										array_push($rejects['wrongLen'],$dataCounter);
									}
								}else{
									array_push($rejects['nonConvert'],$dataCounter);
								}
							}else{
								array_push($rejects['duplicate'],$dataCounter);
								//save mo sa array tapos ipapadala mo ngayon yan pabalik
								array(asdasjdhajshdashdjasjdjashdjasjdas);

							}
						}else{
							array_push($rejects['missingData'],$dataCounter);
						}
					}
				}
				fclose($handle2);
		}else{
			fclose($handle);
			echo json_encode( array('error' => 'Your File is unreadable try downloading our format.'));
		}
	}else{
		echo json_encode( array('error' => 'Please Upload CSV Files Only.'));
	}

	if($rejects != null){ // check kung may nag error na data
		echo json_encode($rejects ,JSON_PRETTY_PRINT);
	}

	if(($dataCounter-1) == $success){
		echo json_encode( array('success' => 'All Students Uploaded Successful'));
	}
}else if($o){

}


?>