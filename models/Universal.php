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
        $query = "SELECT * FROM $tblname WHERE $col = :$col AND $col2 = :$col2";
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



    public function select3WithSubquery($tblname, $col, $col1, $col2, $subquery, $subqueryColName){
        $query = "SELECT $col, $col1, $col2, $subquery as $subqueryColName from $tblname";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function select3WithSubquery2($tblname, $col, $col2, $subquery, $subqueryColName, $cond, $cond1, $condV, $cond1V){
        $query = "SELECT $col,  $col2, $subquery as '$subqueryColName' from $tblname WHERE $cond = :$condV AND $cond1 =:$cond1V";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":$cond", $condV);
        $stmt->bindValue(":$cond1", $cond1V);
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

    public function update2($tblname, $col, $colV, $col1, $col1V, $colC, $colCV){
        $updateQuery = "UPDATE $tblname SET $col = :$col, $col1 = :$col1 WHERE $colC = :$colC";
        $stmt = $this->conn->prepare($updateQuery);
        $stmt->bindParam(":$col", $colV);
        $stmt->bindParam(":$col1", $col1V);
        $stmt->bindParam("$colC", $colCV);
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

    public function insert2($tblname, $col, $col1, $colV, $col1V){
        $insertQuery = "INSERT INTO $tblname
                        SET $col = :$col,
                        $col1 = :$col1";
        $stmt = $this->conn->prepare($insertQuery);
        $stmt->bindParam(":$col", $colV);
        $stmt->bindParam(":$col1", $col1V);
        if($stmt->execute()){
            return true;
        }else{
            return false;
        }
    }

    public function insert3($tblname, $col, $col1, $col2, $colV, $col1V, $col2V){
        $insertQuery = "INSERT INTO $tblname
                        SET $col = :$col,
                        $col1 = :$col1,
                        $col2 = :$col2";
        $stmt = $this->conn->prepare($insertQuery);
        $stmt->bindParam(":$col", $colV);
        $stmt->bindParam(":$col1", $col1V);
        $stmt->bindParam(":$col2", $col2V);
        if($stmt->execute()){
            return true;
        }else{
            return false;
        }
    }

    public function insert2WithSubquery($tblname, $col, $col1, $colv, $subquery){
        $result = $this->conn->exec("INSERT INTO $tblname ($col, $col1) VALUES ($colv, ($subquery))");
        return $result;
    }

    public function updateWithKey($tblname, $col, $colV, $col1, $col1V, $colC, $colCV){
        $result = $this->conn->exec("UPDATE $tblname SET $col = $colV, $col1 = $col1V WHERE $colC = $colCV");
        return $result;
    }

    /*custom*/
    public function getAllSY(){
        $query = "select s.schoolyear_id, s.schoolYear, s.semester, (select COUNT(DISTINCT section_id)
                  FROM sections_handled WHERE schoolyear_id = s.schoolyear_id) 
                  AS total FROM school_years s order by s.status desc";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}

