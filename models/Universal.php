<?php

    class Universal {
         
        private $conn;
        private $tblname = "courses";
        private $course_id;
        
        //Constructor   
        public function __construct($db){
            $this->conn = $db;
        }

    //select all with 2 where conditions
    public function selectAll($tblname, $col, $colCompare, $col2, $col2Compare) {
        $query = "SELECT * FROM $tblname WHERE $col = :$col and $col2 = :$col2";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":$col", $colCompare);
        $stmt->bindParam(":$col2", $col2Compare);
        $stmt->execute();
        return $stmt;
    }

    //select all with 1 where condition
    public function selectAll2($tblname, $col, $colCompare) {
        $query = "SELECT * FROM $tblname WHERE $col = :$col";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":$col", $colCompare);
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

    public function insert5($tblname, $col, $col1, $col2, $col3, $col4, $colV, $col1V, $col2V, $col3V, $col4V){
        $insertQuery = "INSERT INTO $tblname 
                        SET $col = :$col,
                        $col1 = :$col1,
                        $col2 = :$col2,
                        $col3 = :$col3,
                        $col4 = :$col4";
        $stmt = $this->conn->prepare($insertQuery);
        $stmt->bindParam(":$col", $colV);
        $stmt->bindParam(":$col1", $col1V);
        $stmt->bindParam(":$col2", $col2V);
        $stmt->bindParam(":$col3", $col3V);
        $stmt->bindParam(":$col4", $col4V);
        if($stmt->execute()){
            return true;
        }else{
            return false;
        }
    }

}

