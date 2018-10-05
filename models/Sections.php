<?php


    class Sections {
         //Database Properties
        private $conn;

        //Constructor
        public function __construct($db){
            $this->conn = $db;
        }

        //Section properties
        private $section_id;
        private $course_id;
        private $admin_id;
        private $course;
        private $section;
        public $errors = array();
        public $foundSecId;



        public function getCourseID() {
            $query = "SELECT course_id FROM courses
                        WHERE course_id = ?";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->course);
            $stmt->execute();

            if($stmt->rowCount()>0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->course_id = $row['course_id'];
                return true;
            }else{
                $this->errors['field'] = "course";
                $this->errors['message'] = "Course entered is non existent.";
            }
        }

        public function validateSection() {
            if(!preg_match("/[a-z_\-0-9]/i", $this->section)){
                 $this->errors['field'] = "section";
                 $this->errors['message'] = "Letters, number and hypens are the only characters allowed.";
            }else{
                return true;
            }
        }


        public function getStudentsSections(){

            $query = "SELECT s.student_id, s.section_id, s.fname, s.mname, s.lname, s.status, sec.course_id
             FROM students s INNER JOIN sections sec ON s.section_id = sec.section_id
             WHERE s.section_id = ?";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->section_id);
            $stmt->execute();
            return $stmt;

        }

        public function singleSection(){
            $query = "SELECT s.section, c.course
            FROM sections s INNER JOIN courses c ON s.course_id = c.course_id
            WHERE s.section_id = $this->section_id";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }



        public function addSection(){
            $query = "INSERT INTO sections SET course_id = :course_id,
                     admin_id = :admin_id,
                     section = :section";

            $stmt = $this->conn->prepare($query);
            $this->course_id = htmlspecialchars(strip_tags($this->course_id));
            $this->section = htmlspecialchars(strip_tags($this->section));
            $this->admin_id = htmlspecialchars(strip_tags($this->admin_id));
            $stmt->bindParam(':course_id', $this->course_id);
            $stmt->bindParam(':section', $this->section);
            $stmt->bindParam(':admin_id', $this->admin_id);
            if($stmt->execute()){
                return true;
            }else{
                return false;
            }
        }

        public function updateSection(){
            $query = "UPDATE sections SET
            course_id = :course_id,
            admin_id = :admin_id,
            section = :section WHERE section_id = :section_id";

            $stmt = $this->conn->prepare($query);
            $this->course_id = htmlspecialchars(strip_tags($this->course_id));
            $this->section = htmlspecialchars(strip_tags($this->section));
            $this->admin_id = htmlspecialchars(strip_tags($this->admin_id));
            $this->section_id = htmlspecialchars(strip_tags($this->section_id));
            $stmt->bindParam(':course_id', $this->course_id);
            $stmt->bindParam(':section', $this->section);
            $stmt->bindParam(':admin_id', $this->admin_id);
            $stmt->bindParam(':section_id', $this->section_id);
            if($stmt->execute()){
                return true;
            }else{
                return false;
            }
        }

        public function setCourseID($course_id){
            $this->course_id = $course_id;
        }

        public function setAdminID($admin_id){
            $this->admin_id = $admin_id;
        }

        public function setSection($sectionName){
            $this->section = $sectionName;
        }

       public function setSectionID($sectionID){
            $this->section_id = $sectionID;
        }

        public function checkIfConvertable($courseString){

            $query = "SELECT a.section_id , a.section FROM sections a left join courses b on a.course_id = b.course_id WHERE a.section = ? ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $courseString);
            $stmt->execute();

            if($stmt->rowCount() > 0){
                $holder = $stmt->fetch(PDO::FETCH_ASSOC);
                extract($holder);
                $this->foundSecId = $section_id;
                return true;
            }else{
                return false;
            }

        }

    }
