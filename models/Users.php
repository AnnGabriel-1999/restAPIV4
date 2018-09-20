<?php

    class Users {
        //Database Properties
        private $conn;
        private $tblname = "students";

        //Student Properties
        public $new_id;
        public $stud_id;
        public $section_id;
        public $section_name;
        public $name;
        public $fname;
        public $mname;
        public $lname;
        public $order;
        public $keyword;

        //Constructor
        public function __construct($db){
            $this->conn = $db;
        }

        //Get All Students
        public function getStudents() {
            //Create query
            $query = "SELECT s.student_id, s.fname, s.mname, s.lname, c.section from Students s left join sections c on s.section_id = c.section_id
                ORDER BY
                    s.lname $this->order";

            //Prepate Statement
            $stmt = $this->conn->prepare($query);


            //Execute Query
            $stmt->execute();

            return $stmt;
        }

        //Get Single Student
         public function singleStudent() {
            //Create query
            $query = "SELECT s.student_id, s.section_id, s.fname, s.mname, s.lname, s.status, sec.course_id
             FROM students s INNER JOIN sections sec ON s.section_id = sec.section_id
             WHERE s.student_id = ?";

            //Prepate Statement
            $stmt = $this->conn->prepare($query);

            //Bind Student_ID
            $stmt->bindParam(1, $this->stud_id);

            //Execute Query
            $stmt->execute();
            return $stmt;

        }

        public function getStudentSection() {
              //Create Query
            $query = "SELECT section_id FROM sections
                        WHERE
                            section = ?";

            //Prepare Statement
            $stmt = $this->conn->prepare($query);

            //Bind Section Name and Execute
            $stmt->bindParam(1, $this->section_name);
            $stmt->execute();

            //Get Section ID
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->section_id = $row['section_id'];

        }

        //Create
        public function registerStudent() {
            $insertQuery = "INSERT INTO students
                            SET
                              student_id = :student_id,
                              section_id = :section_id,
                              fname = :fname,
                              mname = :mname,
                              lname = :lname
                              ";

            //Prepare Insert Statement
            $stmt = $this->conn->prepare($insertQuery);

            //Clean inputted data
            $this->section_id = htmlspecialchars(strip_tags($this->section_id));
            $this->section_name = htmlspecialchars(strip_tags($this->section_id));
            $this->student_id = htmlspecialchars(strip_tags($this->student_id));
            $this->fname = htmlspecialchars(strip_tags($this->fname));
            $this->mname = htmlspecialchars(strip_tags($this->mname));
            $this->lname = htmlspecialchars(strip_tags($this->lname));

            //Bind paramaters
            $stmt->bindParam(':student_id', $this->student_id);
            $stmt->bindParam(':section_id', $this->section_id);
            $stmt->bindParam(':fname', $this->fname);
            $stmt->bindParam(':mname', $this->mname);
            $stmt->bindParam(':lname', $this->lname);


            //Execute
            if($stmt->execute()){
                return true;
            }else{
                return false;
            }

        }

        public function verifyStudentID($stud_id) {
            $query = "SELECT student_id FROM students WHERE student_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $stud_id);
            $stmt->execute();
            if($stmt->rowCount()==0){
                return true;
            }else{
                return false;
            }

        }

        //Update
        public function updateStudent() {
            $insertQuery = "UPDATE students
                            SET
                              student_id = :new_id,
                              section_id = :section_id,
                              fname = :fname,
                              mname = :mname,
                              lname = :lname,
                              course_id = :course_id
                            WHERE student_id = :student_id";

            //Prepare Insert Statement
            $stmt = $this->conn->prepare($insertQuery);

            //Clean inputted data
            $this->new_id = htmlspecialchars(strip_tags($this->new_id));
            $this->section_id = htmlspecialchars(strip_tags($this->section_id));
            $this->fname = htmlspecialchars(strip_tags($this->fname));
            $this->mname = htmlspecialchars(strip_tags($this->mname));
            $this->lname = htmlspecialchars(strip_tags($this->lname));
            $this->course_id = htmlspecialchars(strip_tags($this->course_id));
            $this->student_id = htmlspecialchars(strip_tags($this->student_id));


            //Bind paramaters
            $stmt->bindParam(':new_id', $this->new_id);
            $stmt->bindParam(':section_id', $this->section_id);
            $stmt->bindParam(':fname', $this->fname);
            $stmt->bindParam(':mname', $this->mname);
            $stmt->bindParam(':lname', $this->lname);
            $stmt->bindParam(':course_id', $this->course_id);
            $stmt->bindParam(':student_id', $this->student_id);

            //Execute
            if($stmt->execute()){
                return true;
            }else{
                return false;
            }

        }
         public function searchStudent() {
            //Update query
            $query =
             "SELECT
              s.student_id,
              s.fname,
              s.mname,
              s.lname,
              concat(s.fname, ' ', s.mname, ' ', s.lname) as fullname,
              c.section
              FROM
              Students s left join sections c on s.section_id = c.section_id
                  WHERE
                    s.fname LIKE '%$this->keyword%' OR s.lname LIKE '%$this->keyword%'
                    or c.section LIKE '%$this->keyword%' or concat(s.fname, ' ', s.mname, ' ', s.lname) LIKE '%$this->keyword%'";

            $stmt = $this->conn->prepare($query);

            //Execute Query
            $stmt->execute();

            return $stmt;

        }

    }
