<?php

    class Hosts {
        //Database Properties
        private $conn;
        private $tblname = "admins";

        //Host Properties
        public $new_id;
        public $host_id;
        public $fname;
        public $mname;
        public $lname;
        public $username;
        public $password;
        public $confirm_password;
        public $admin_id_blah;
        public $mirror_id;
        //Error Code Properties
        /*
            0. Passwords do not match
            2. All Fields are required
            3. Password must be atleast 8 characters
        */

        public $error_code;

        //Constructor
        public function __construct($db){
            $this->conn = $db;
        }

        public function validateHostDetails() {
            /*check if inputs are left blank
            if passwords match and atleast 8 characters
            username must be unique*/

        }

        public function registerHost(){
            $insertQuery = "INSERT INTO admins
                            SET
                              mirror_id = :mirror_id,
                              username = :username,
                              password = :password
                              ";

            //Prepare Insert Statement
            $stmt = $this->conn->prepare($insertQuery);

            //Clean inputted data
            $this->fnammirror_ide = htmlspecialchars(strip_tags($this->mirror_id));
            $this->username = htmlspecialchars(strip_tags($this->username));
            $this->password = htmlspecialchars(strip_tags($this->password));

            //Bind paramaters
            $stmt->bindParam(':mirror_id', $this->mirror_id);
            $stmt->bindParam(':username', $this->username);
            $stmt->bindParam(':password', $this->password);


            //Execute
            if($stmt->execute()){
                return true;
            }else{
                return false;
            }


    }

    function logInHost(){
        //QUERY
        $query = "SELECT * FROM admins WHERE username = :username && password = :password";
        //PREAPARE STATMENT
        $stmt = $this->conn->prepare($query);
        //BIND PARAMS AND EXECURE QUERY
        $stmt->execute([ 'username' => $this->username , 'password' => $this->password]);

        //IF MAY RESULT
        if( $row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $this->admin_id_blah = $admin_id;
            return true;
        }else{
            return false;
        }

    }

    public function getHosts() {
            //Create query
            $query = "SELECT * FROM admins";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt;
    }

    public function addGuessQuestion($quiz_id, $part_id, $question , $answer){
        // query to be executed
        $query = "INSERT INTO questions
                            SET
                              quiz_id = :quiz_id,
                              part_id = :part_id,
                              question = :question,
                              answer = :answer
                              ";
         //prepares the query
         $stmt = $this->conn->prepare($query);

         //binding of the query
         $stmt->bindParam(':quiz_id', $quiz_id);
         $stmt->bindParam(':part_id', $part_id);
         $stmt->bindParam(':question', $question);
         $stmt->bindParam(':answer', $answer);

         if($stmt->execute()){
            return true;
         }else{
            return false;
         }
    }

    public function listCourses(){
      $query = "SELECT * FROM `courses`";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt;
    }

    public function listSections(){
      $query = "SELECT s.section_id, s.course_id, s.admin_id, c.course_prefix, s.section, c.course FROM sections s INNER JOIN courses c ON s.course_id = c.course_id";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt;
    }

    public function listNamesA(){
      $query = "SELECT * FROM `admins`";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt;
    }

    public function listNamesS(){
      $query = "SELECT s.student_id, s.fname, s.mname, s.lname, sec.section,c.course FROM
                students s inner join sections sec on s.section_id = sec.section_id
                inner join courses c on sec.course_id = c.course_id";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt;
    }
}
