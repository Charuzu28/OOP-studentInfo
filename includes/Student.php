<?php
require_once 'dbcon.php';

class Student {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getAllStudents() {
        $sql = "SELECT students.*, courses.course_name
                FROM students
                LEFT JOIN courses ON students.course_id = courses.id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentById($student_id) {
        $sql = "SELECT students.*, courses.course_name 
                FROM students 
                LEFT JOIN courses ON students.course_id = courses.id 
                WHERE students.student_id = :student_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':student_id' => $student_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addStudent($student_id, $first_name, $last_name, $course_id, $year_level) {
        $sql = "INSERT INTO students (student_id, first_name, last_name, course_id, year_level)
                VALUES (:student_id, :first_name, :last_name, :course_id, :year_level)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':student_id' => $student_id,
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':course_id' => $course_id,
            ':year_level' => $year_level
        ]);
    }

    public function updateStudent($student_id, $first_name, $last_name, $course_id, $year_level) {
        // Validate course_id
        if (!is_null($course_id)) {
            $courseCheck = $this->conn->prepare("SELECT id FROM courses WHERE id = :course_id");
            $courseCheck->execute([':course_id' => $course_id]);
            if ($courseCheck->rowCount() == 0) {
                $course_id = null; // Set to NULL if the course does not exist
            }
        }

        $sql = "UPDATE students 
                SET first_name = :first_name, 
                    last_name = :last_name, 
                    course_id = :course_id, 
                    year_level = :year_level 
                WHERE student_id = :student_id";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':student_id' => $student_id,
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':course_id' => $course_id,
            ':year_level' => $year_level
        ]);
    }

    public function deleteStudent($student_id) {
        $sql = "DELETE FROM students WHERE student_id = :student_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':student_id' => $student_id]);
    }
}
?>