<?php

    Class Quiz {
        // Database Properties
        private $conn;
        private $tblname1 = "quiz";
        private $tblname2 = "hosted_quizzes";

        // Quiz Properties
        public $quizID;
        public $quizTitle;
        public $parts;
        public $hosted_id;
        public $admin_id;
        public $fname;
        public $date_created;
        public $kunware_session;
        public $description;
        public $filepath;
        public $totalQuiz;
        
        //Quiz Part Properties
        public $type_id;
        public $type_name;
        public $part_title;
        public $position;
        public $totalParts;
        public $duration;
        public $key;
        public $choices_keys = array();
        public $part_type;

        //Question Properties
        public $question;
        public $question_id;
        public $answer_id;
        public $values = array();
        public $correct;
        public $order = 'a';


        //Quiz Update Variables
        public $new_part_title;
        public $new_type_id;
        public $part_id;
        public $kind_part;
        
        private $section_id;


        // Constructor
        public function __construct($db) {
            $this->conn = $db;
        }
        //Read Questions
       public function readQuestions() {
           //Create query
       $query = " SELECT
                   a.question,
                   a.question_id,
                   b.quiz_id,
                   a.part_id,
                   (
                       SELECT value from answer_choices
                       WHERE a.answer = choice_id
                   )as rightAnswer,
                   (
                       SELECT
                       value
                       from answer_choices
                       WHERE
                       a.question_id = question_id
                       AND post = 'a' OR 'A'
                   )as choice1,
                    (
                       SELECT value
                       from answer_choices
                       WHERE
                       a.question_id = question_id
                       AND post = 'b' OR 'B'
                   )as choice2,
                    (
                       SELECT value
                       from answer_choices
                       WHERE
                       a.question_id = question_id
                       AND post = 'c' OR 'C'
                   )as choice3,
                    (
                       SELECT value
                       from answer_choices
                       WHERE
                       a.question_id = question_id
                       AND post = 'd' OR 'D'
                   )as choice4
                   FROM
                   questions a,
                   quizzes b,
                   admins c
                   WHERE
                   c.admin_id = b.admin_id
                   AND
                  b.quiz_id = a.quiz_id
                  AND
                  a.part_id = $this->partID";

           //Prepare Statement
           $stmt = $this->conn->prepare($query);

           //Execute Query
           $stmt->execute();

           return $stmt;
       }

       public function addQuiz() {
            $insertQuery = "INSERT INTO quizzes
                            SET
                              quiz_title = :quizTitle,
                              admin_id = :admin_id,
                              description = :description,
                              filepath = :filepath";

            $stmt = $this->conn->prepare($insertQuery);

            $this->quizTitle = htmlspecialchars(strip_tags($this->quizTitle));
            $this->description = htmlspecialchars(strip_tags($this->description));
            $this->admin_id = htmlspecialchars(strip_tags($this->admin_id));
            $this->filepath = htmlspecialchars(strip_tags($this->filepath));
            //$this->part_type = htmlspecialchars(strip_tags($this->part_type));

            // Bind parameters

            if($this->filepath == ''){
                $this->filepath = "../../../AdmInterfaceV2/uploads/quiz/default.jpeg";
            }
            $stmt->bindParam(':quizTitle', $this->quizTitle);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':admin_id', $this->admin_id);
            $stmt->bindParam(':filepath', $this->filepath);
            //$stmt->bindParam(':part_type', $this->part_type);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
        
        //Read Quiz
        public function readQUiz() {
            //Create query
            $query = "SELECT
            a.quiz_id,
            a.quiz_title,
            a.date_created,
            a.filepath,
            a.description,
            ( SELECT Count(quiz_id) FROM quizzes
                       Where admin_id = a.admin_id
                       ) as totalQuiz
            FROM
            quizzes a left join admins b
            on a.quiz_id = b.admin_id
            WHERE a.admin_id = $this->admin_id
                ORDER BY
                    a.quiz_id ASC";

            //Prepare Statement
            $stmt = $this->conn->prepare($query);

            //Execute Query
            $stmt->execute();

            return $stmt;
        }

         //Get Single Quiz
         public function singleQuiz() {
            //Create query
            $query = "SELECT * FROM `quizzes` WHERE `quiz_id` =  ?";

            //Prepate Statement
            $stmt = $this->conn->prepare($query);

            //Bind Student_ID
            $stmt->bindParam(1, $this->quizID);

            //Execute Query
            $stmt->execute();

            return $stmt;
        }

         //Update
       public function updateQuiz() {
            $insertQuery = 'UPDATE quizzes
                            SET
                              quiz_title = :quizTitle,
                              description = :description
                              WHERE
                              quiz_id = :quizID';

           // Prepare Insert Statement
           $stmt = $this->conn->prepare($insertQuery);

            // Clean inputted data
           $this->quizTitle = htmlspecialchars(strip_tags($this->quizTitle));
           $this->description = htmlspecialchars(strip_tags($this->description));
           $this->quizID = htmlspecialchars(strip_tags($this->quizID));

            // Bind parameters
            $stmt->bindParam(':quizTitle', $this->quizTitle);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':quizID', $this->quizID);

            // Execute
            if ($stmt->execute()) {
                return true;
            } else {
                printf("Error %s". \n, $stmt->err);
                return false;
            }
        }

        //UPDATE QUESTION
       public function updateTrueorFalse(){

           $updateQuery = " UPDATE questions
                            SET question= :new_question,
                                quiz_id = :quizID,
                                part_id = :partID
                            WHERE question_id = :question_id";

           $stmt = $this->conn->prepare($updateQuery);

           $stmt->bindParam(':new_question', $this->new_question);
           $stmt->bindParam(':quizID',  $this->quizID );
           $stmt->bindParam(':partID', $this->partID);
            $stmt->bindParam(':question_id', $this->question_id);

           //TESTING
           if($stmt->execute()){
               if($this->updateChoices()){
                   return true;
               }
           }else {
               return false;
           }
       }
         //UPDATE QUESTION
       public function updateChoices() {
       $updateQuery = "UPDATE answer_choices
                       SET
                           quiz_id = :quizID,
                           value = :value
                           WHERE choice_id =(SELECT answer FROM questions WHERE question_id = :question_id) ";

       $stmt = $this->conn->prepare($updateQuery);

           $stmt->bindParam(':quizID',  $this->quizID );
           $stmt->bindParam(':value', $this->correct);
           $stmt->bindParam(':question_id', $this->question_id);

           if ($stmt->execute()){
           return true;
       }else{
           return false;
       }
   }
          public function addQuestion() {
        $insertQuery = "INSERT INTO questions
                        SET
                            quiz_id = :quiz_id,
                            part_id = :part_id,
                            question = :question";

        $stmt = $this->conn->prepare($insertQuery);
        $this->quizID = htmlspecialchars(strip_tags($this->quizID));
        $this->totalParts = htmlspecialchars(strip_tags($this->part_id));
        $this->duration = htmlspecialchars(strip_tags($this->question));

        $stmt->bindParam(':quiz_id', $this->quizID);
        $stmt->bindParam(':part_id', $this->part_id);
        $stmt->bindParam(':question', $this->question);

        if($stmt->execute()){
            if($this->insertChoices()){
                return true;
            }
        }else{
            return false;
        }

    }
          public function GenericAddQuestion() {
        $insertQuery = "INSERT INTO questions
                        SET
                            quiz_id = :quiz_id,
                            part_id = :part_id,
                            question = :question";

        $stmt = $this->conn->prepare($insertQuery);
        $this->quizID = htmlspecialchars(strip_tags($this->quizID));
        $this->totalParts = htmlspecialchars(strip_tags($this->part_id));
        $this->duration = htmlspecialchars(strip_tags($this->question));

        $stmt->bindParam(':quiz_id', $this->quizID);
        $stmt->bindParam(':part_id', $this->part_id);
        $stmt->bindParam(':question', $this->question);

        if($stmt->execute()){
            if($this->GenericinsertToAnswerChoices()){
                return true;
            }
        }else{
            return false;
        }

    }

    public function insertChoices() {
        $counter = 0;
        $insertQuery = "INSERT INTO answer_choices
                        SET
                            question_id = (select max(question_id) from questions),
                            quiz_id = :quiz_id,
                            value = :value,
                            post = :order";

        $stmt = $this->conn->prepare($insertQuery);

        foreach ($this->values as $val){

            $this->question_id = htmlspecialchars(strip_tags($this->question_id));
            $this->quizID = htmlspecialchars(strip_tags($this->quizID));
            $this->order = htmlspecialchars(strip_tags($this->order));
            $val = htmlspecialchars(strip_tags($val));
            $stmt->bindParam(':quiz_id', $this->quizID);
            $stmt->bindParam(':value', $val);
            $stmt->bindParam(':order', $this->order);

            if ($stmt->execute()){
                $counter++;
                $this->order++;
            }
        }

        if($counter==4){
            return true;
        }else{
            return false;
        }


    }
        public function GenericinsertToAnswerChoices() {
        $insertQuery = "INSERT INTO answer_choices
                        SET
                            question_id = (select max(question_id) from questions),
                            quiz_id = :quiz_id,
                            value = :value";

        $stmt = $this->conn->prepare($insertQuery);

            $this->question_id = htmlspecialchars(strip_tags($this->question_id));
            $this->quizID = htmlspecialchars(strip_tags($this->quizID));
            $this->value = htmlspecialchars(strip_tags($this->correct));

            $stmt->bindParam(':quiz_id', $this->quizID);
            $stmt->bindParam(':value', $this->correct);

            if ($stmt->execute()){
            return true;
        }else{
            return false;
        }
    }
        public function searchQuiz() {
            //Select query
            $query =  "SELECT
                        a.quiz_id,
                        a.quiz_title,
                       (
                       SELECT Count(quiz_id) FROM quiz_parts
                       Where quiz_id = a.quiz_id
                       ) as partsperQuiz
                       FROM
                       quizzes a
                            WHERE
                                a.quiz_title LIKE '%".$_GET['quiz_title']."%'";

             //Prepare Statement
            $stmt = $this->conn->prepare($query);

            //Execute Query
            $stmt->execute();

            return $stmt;
        }

        public function getQuizID() {
            $query = "SELECT quiz_id FROM quizzes WHERE quiz_title = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->quizTitle);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->quiz_id = $row['quiz_id'];
        }

        public function getTypeID() {
            $query = "SELECT type_id FROM question_types WHERE type = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->type_name);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->type_id = $row['type_id'];
        }

        public function countParts() {
            $query = "SELECT MAX(q.position) FROM quiz_parts q WHERE q.quiz_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->quizID);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->totalParts = $row['MAX(q.position)'];
            return $this->totalParts;
        }

        public function addQuizPart() {
            $insertQuery = "INSERT INTO quiz_parts SET
                                type_id = :type_id,
                                quiz_id = :quiz_id,
                                part_title = :part_title,
                                duration = :duration,
                                position = :position";

            $stmt = $this->conn->prepare($insertQuery);

            // Bind parameters
            $stmt->bindParam(':type_id', $this->type_id);
            $stmt->bindParam(':quiz_id', $this->quizID);
            $stmt->bindParam(':part_title', $this->part_title);
            $stmt->bindParam(':position', $this->totalParts);
            $stmt->bindParam(':duration', $this->duration);
            
            // Execute
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }

        public function checkPartTitle($partTitle, $quizID) {
            $query = "SELECT * FROM quiz_parts WHERE part_title = :part_title AND quiz_id = :quiz_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':part_title', $partTitle);
            $stmt->bindParam(':quiz_id', $quizID);

            $stmt->execute();
            $result = $stmt->rowCount();
            return $result;
        }

        public function updateQuizPart(){

            $updateQuery = " UPDATE quiz_parts
                             SET part_title= :new_part_title,
                                 type_id= :new_type_id ,
                                 duration = :duration
                             WHERE part_id = :part_id";

            $stmt = $this->conn->prepare($updateQuery);

            $stmt->bindParam(':new_part_title', $this->new_part_title);
            $stmt->bindParam(':new_type_id',  $this->type_id );
            $stmt->bindParam(':part_id', $this->part_id);
            $stmt->bindParam(':duration', $this->duration);

            //TESTING
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }

        public function viewQuizList() {
            $query = 'SELECT * FROM quiz ORDER BY quizTitle ' . $_GET['order'];

            // Prepare Statement
            $stmt = $this->conn->prepare($query);

            // Execute Query
            $stmt->execute();

            return $stmt;
        }


        public function searchQuizPart() {
            //Select query
            $query =
            "SELECT
            a.part_title,
            a.duration,
            b.type
           FROM
            quiz_parts a left join question_types b
            on a.type_id = b.type_id
                WHERE
                  a.part_title LIKE '%".$_GET['part_title']."%'";

             //Prepare Statement
            $stmt = $this->conn->prepare($query);

            //Execute Query
            $stmt->execute();

            return $stmt;
        }

        public function blankGuessWord($word){

            $numOfLoops = floor((strlen($word) * .5));
            $array= array();
            while ($numOfLoops > 0) {
                $found = 0;
                $randomLoc = rand(0,strlen($word)-1);
                array_push($array, $randomLoc);
                for ($q=0; $q < count($array); $q++) {
                    if($randomLoc == $array[$q]){
                        $found += 1;
                    }
                }
                if($found == 1 && $word[$randomLoc] != " "){
                    $word[$randomLoc] = "_";
                }else{
                    $numOfLoops++;
                }
                $numOfLoops--;
            }
            echo $word;
        }


        public function insertAnswer(){

            $query = "SELECT max(choice_id) from answer_choices WHERE value = '$this->correct'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt;
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $this->answer_id = $row['max(choice_id)'];
            }

            $query = "SELECT max(question_id) FROM questions";
             $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt;
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $this->question_id = $row['max(question_id)'];
            }

            $updateQuery = "UPDATE questions set answer = '$this->answer_id' WHERE question_id = $this->question_id";
            $stmt = $this->conn->prepare($updateQuery);

            if($stmt->execute()){
                return true;
            }else{
                return false;
            }
    }

        public function GenericInsertQuestion(){
            $query = "SELECT max(choice_id) from answer_choices WHERE value = '$this->correct'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt;
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $this->answer_id = $row['max(choice_id)'];
            }
            $query = "SELECT max(question_id) FROM questions";
             $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt;
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $this->question_id = $row['max(question_id)'];
            }
            $updateQuery = "UPDATE questions set answer = '$this->answer_id' WHERE question_id = $this->question_id";
            $stmt = $this->conn->prepare($updateQuery);
            if($stmt->execute()){
                return true;
            }else{
                return false;
            }

    }

    public function viewQuizParts() {
        $query = "SELECT q.quiz_id, q.quiz_title, q.description, qt.type_id, pr.part_id, pr.part_title, (SELECT count(question) FROM questions WHERE part_id = pr.part_id) as 'totalQs' ,pr.duration, qt.type FROM quiz_parts pr
        INNER JOIN quizzes q ON q.quiz_id = pr.quiz_id
        INNER JOIN question_types qt ON qt.type_id = pr.type_id
        WHERE q.quiz_id = $this->quizID ORDER BY pr.position ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function singlePart(){
        $query = "SELECT pr.part_title, pr.duration, ty.type
        FROM quiz_parts pr INNER JOIN question_types ty ON pr.type_id = ty.type_id
        WHERE pr.part_id = $this->part_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }


    public function selectAllFromQuiz() {
        $query = "SELECT * FROM quizzes WHERE quiz_id = $this->quizID";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function updateQuestion(){
        $query = "UPDATE questions SET question = :question
                 WHERE question_id = :question_id";
        $stmt = $this->conn->prepare($query);
        $this->question =  htmlspecialchars(strip_tags($this->question));
        $this->question_id =  htmlspecialchars(strip_tags($this->question_id));
        $stmt->bindParam(':question', $this->question);
        $stmt->bindParam(':question_id', intval($this->question_id));
        
        if($stmt->execute()){
            return true;
        }else{
            return false;
        }
    }
      
    //get all choices by question_id
    public function fetchChoices(){
        $query = "SELECT * FROM answer_choices WHERE question_id = :question_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':question_id', $this->question_id);
        $stmt->execute();
        $result = $stmt;
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
              array_push($this->choices_keys, $row['choice_id']);
        }
    }
        
   public function updateAnswerChoices($fuckingKey, $fuckingValue){
       $query = "UPDATE answer_choices SET value = :value WHERE choice_id = :choice_id";
       $stmt = $this->conn->prepare($query);
       $stmt->bindParam(':value', $fuckingValue);
       $stmt->bindParam(':choice_id', $fuckingKey);
       $stmt->execute();
       return $stmt;
   }

   //select all with 2 where conditions
   //table name, column, columncompare, column2, column2compare
   public function selectAll($tblname, $col, $colCompare, $col2, $col2Compare) {
       $query = "SELECT * FROM $tblname WHERE $col = :$col and $col2 = :$col2";
       $stmt = $this->conn->prepare($query);
       $stmt->bindParam(":$col", $colCompare);
       $stmt->bindParam(":$col2", $col2Compare);
       $stmt->execute();
       return $stmt;
   }

   public function updateSomething($tblname, $col, $colCompare, $condition, $conditionValue){
       $updateQuery = "UPDATE $tblname SET $col = :$col WHERE $condition = :$condition";
       $stmt = $this->conn->prepare($updateQuery);
       $stmt->bindParam(":$col", $colCompare);
       $stmt->bindParam(":$condition", $conditionValue);
       if($stmt->execute()){
           return true;
       }else{
           return false;
       }
   }

}
