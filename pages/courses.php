<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../includes/Course.php';
$course = new Course();

// Handle delete request
if (isset($_POST['delete']) && isset($_POST['id'])) {
    if ($course->deleteCourse($_POST['id'])) {
        header('Location: courses.php');
        exit;
    } else {
        $error = "Cannot delete course. It may be assigned to students.";
    }
}

// Handle edit/add requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete'])) {
    $course_code = $_POST['course_code'];
    $course_name = $_POST['course_name'];
    $description = $_POST['description'];

    if (isset($_POST['edit']) && isset($_POST['id'])) {
        // Handle edit
        if ($course->updateCourse($_POST['id'], $course_code, $course_name, $description)) {
            header('Location: courses.php');
            exit;
        } else {
            $error = "Failed to update course.";
        }
    } else {
        // Handle add
        if ($course->addCourse($course_code, $course_name, $description)) {
            header('Location: courses.php');
            exit;
        } else {
            $error = "Failed to add course.";
        }
    }
}

// Fetch course for editing
$editCourse = null;
if (isset($_GET['edit']) && isset($_GET['id'])) {
    $editCourse = $course->getCourseById($_GET['id']);
}

$courses = $course->getAllCourses();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('../templates/nav.php'); ?>
    <div class="container mt-4">
        <h1><?= isset($editCourse) ? 'Edit Course' : 'Manage Courses' ?></h1>
        <form method="POST" class="mb-4">
            <?php if (isset($editCourse)): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($editCourse['id']) ?>">
                <input type="hidden" name="edit" value="1">
            <?php endif; ?>
            <div class="row g-3">
                <div class="col-md-2">
                    <input type="text" name="course_code" class="form-control" placeholder="Course Code" required
                           value="<?= htmlspecialchars($editCourse['course_code'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <input type="text" name="course_name" class="form-control" placeholder="Course Name" required
                           value="<?= htmlspecialchars($editCourse['course_name'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <input type="text" name="description" class="form-control" placeholder="Description" required
                           value="<?= htmlspecialchars($editCourse['course_description'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-<?= isset($editCourse) ? 'success' : 'primary' ?> w-100">
                        <?= isset($editCourse) ? 'Update' : 'Add' ?>
                    </button>
                </div>
            </div>
            <?php if (isset($error)): ?>
                <p class="text-danger mt-2"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                <tr>
                    <td><?= htmlspecialchars($course['course_code']) ?></td>
                    <td><?= htmlspecialchars($course['course_name']) ?></td>
                    <td><?= htmlspecialchars($course['course_description']) ?></td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="?edit=1&id=<?= urlencode($course['id']) ?>" 
                               class="btn btn-warning btn-sm">Edit</a>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($course['id']) ?>">
                                <button type="submit" name="delete" class="btn btn-danger btn-sm" 
                                        onclick="return confirm('Are you sure you want to delete this course?')">
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