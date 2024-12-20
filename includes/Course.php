<?php
require_once 'dbcon.php';

class Course {
    private $conn;
    
    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getAllCourses() {
        $sql = "SELECT * FROM courses";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCourseById($id) {
        $sql = "SELECT * FROM courses WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addCourse($courseCode, $courseName, $courseDescription) {
        $sql = "INSERT INTO courses (course_code, course_name, course_description) 
                VALUES (:course_code, :course_name, :course_description)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':course_code' => $courseCode,
            ':course_name' => $courseName,
            ':course_description' => $courseDescription
        ]);
    }

    public function deleteCourse($id) {
        // First check if course is being used by any students
        $checkSql = "SELECT COUNT(*) FROM students WHERE course_id = :id";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->execute([':id' => $id]);
        if ($checkStmt->fetchColumn() > 0) {
            return false; // Course is being used by students
        }

        $sql = "DELETE FROM courses WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function updateCourse($id, $courseCode, $courseName, $courseDescription) {
        $sql = "UPDATE courses 
                SET course_code = :course_code, 
                    course_name = :course_name, 
                    course_description = :course_description 
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':course_code' => $courseCode,
            ':course_name' => $courseName,
            ':course_description' => $courseDescription
        ]);
    }
    public function getCourses() {
        $sql = "SELECT * FROM courses";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>