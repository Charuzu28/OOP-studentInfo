<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Include necessary files
require_once '../includes/dbcon.php';
require_once '../includes/Student.php';
require_once '../includes/Course.php';

// Initialize database connection
$db = new Database();
$conn = $db->connect();

// Initialize objects
$studentObj = new Student($conn);
$courseObj = new Course($conn);

// Fetch all students and courses
$students = $studentObj->getAllStudents();
$courses = $courseObj->getCourses();

// Handle delete request
if (isset($_POST['delete']) && isset($_POST['student_id'])) {
    if ($studentObj->deleteStudent($_POST['student_id'])) {
        header('Location: students.php');
        exit;
    } else {
        $error = "Failed to delete student.";
    }
}

// Handle form submission for add/edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete'])) {
    $student_id = $_POST['student_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $course_id = $_POST['course_id'] ?? null;
    $year_level = $_POST['year_level'];

    if (isset($_POST['edit'])) {
        // Handle edit
        if ($studentObj->updateStudent($student_id, $first_name, $last_name, $course_id, $year_level)) {
            header('Location: students.php');
            exit;
        } else {
            $error = "Failed to update student.";
        }
    } else {
        // Handle add (existing code)
        if ($studentObj->addStudent($student_id, $first_name, $last_name, $course_id, $year_level)) {
            header('Location: students.php');
            exit;
        } else {
            $error = "Failed to add student.";
        }
    }
}

// Fetch student data for editing
$editStudent = null;
if (isset($_GET['edit']) && isset($_GET['student_id'])) {
    $editStudent = $studentObj->getStudentById($_GET['student_id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('../templates/nav.php'); ?>
    <div class="container mt-4">
        <h1><?= isset($editStudent) ? 'Edit Student' : 'Manage Students' ?></h1>
        <form method="POST" class="mb-4">
            <div class="row g-3">
                <div class="col-md-2">
                    <input type="text" name="student_id" class="form-control" placeholder="Student ID" required
                           value="<?= htmlspecialchars($editStudent['student_id'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <input type="text" name="first_name" class="form-control" placeholder="First Name" required
                           value="<?= htmlspecialchars($editStudent['first_name'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <input type="text" name="last_name" class="form-control" placeholder="Last Name" required
                           value="<?= htmlspecialchars($editStudent['last_name'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <select name="course_id" class="form-select">
                        <option value="">Select Course</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= $course['id'] ?>" 
                                <?= (isset($editStudent) && $editStudent['course_id'] == $course['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($course['course_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="year_level" class="form-control" placeholder="Year Level" required
                           value="<?= htmlspecialchars($editStudent['year_level'] ?? '') ?>">
                </div>
                <div class="col-md-1">
                    <?php if (isset($editStudent)): ?>
                        <input type="hidden" name="edit" value="1">
                        <button type="submit" class="btn btn-success w-100">Update</button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-primary w-100">Add</button>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (isset($error)): ?>
                <p class="text-danger mt-2"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Course</th>
                    <th>Year Level</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= htmlspecialchars($student['student_id']) ?></td>
                    <td><?= htmlspecialchars($student['first_name']) ?></td>
                    <td><?= htmlspecialchars($student['last_name']) ?></td>
                    <td><?= htmlspecialchars($student['course_name'] ?? 'None') ?></td>
                    <td><?= htmlspecialchars($student['year_level']) ?></td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="?edit=1&student_id=<?= urlencode($student['student_id']) ?>" 
                               class="btn btn-warning btn-sm">Edit</a>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="student_id" value="<?= htmlspecialchars($student['student_id']) ?>">
                                <button type="submit" name="delete" class="btn btn-danger btn-sm" 
                                        onclick="return confirm('Are you sure you want to delete this student?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>