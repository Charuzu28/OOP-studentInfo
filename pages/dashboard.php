<?php
session_start();


if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit; // Stop further script execution
}

require_once '../includes/Student.php';
require_once '../includes/Course.php';

$student = new Student();
$course = new Course();
$students = $student->getAllStudents();
$courses = $course->getAllCourses();

// Calculate summary statistics
$totalStudents = count($students);
$totalCourses = count($courses);
$coursesWithStudents = 0;
$courseStudentCounts = array();

foreach ($courses as $course) {
    $studentCount = 0;
    foreach ($students as $student) {
        if (isset($student['course_name']) && $student['course_name'] == $course['course_name']) {
            $studentCount++;
        }
    }
    if ($studentCount > 0) $coursesWithStudents++;
    $courseStudentCounts[$course['course_name']] = $studentCount;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-bg: #f8f9fa;
            --sidebar-bg: #2c3e50;
            --sidebar-hover: #34495e;
            --card-border: #e9ecef;
        }
        
        body {
            background-color: var(--primary-bg);
        }
        
        .sidebar {
            background-color: var(--sidebar-bg) !important;
            min-height: 100vh;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: #fff;
            padding: 1rem 1.5rem;
            border-radius: 0.25rem;
            margin: 0.2rem 0;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover {
            background-color: var(--sidebar-hover);
            padding-left: 2rem;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .table thead {
            background-color: #f8f9fa;
        }
        
        .stats-icon {
            font-size: 2rem;
            opacity: 0.8;
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <?php include('../templates/nav.php'); ?>
    <div class="container-fluid">
        <div class="row">
            <?php include('../templates/sidebar.php'); ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <h2 class="mb-4">Dashboard Overview</h2>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="dashboard-card p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted">Total Students</h6>
                                    <h3 class="mb-0"><?= $totalStudents ?></h3>
                                </div>
                                <div class="stats-icon text-primary">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dashboard-card p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted">Total Courses</h6>
                                    <h3 class="mb-0"><?= $totalCourses ?></h3>
                                </div>
                                <div class="stats-icon text-success">
                                    <i class="fas fa-book"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dashboard-card p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted">Active Courses</h6>
                                    <h3 class="mb-0"><?= $coursesWithStudents ?></h3>
                                </div>
                                <div class="stats-icon text-warning">
                                    <i class="fas fa-book-reader"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dashboard-card p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted">Avg Students/Course</h6>
                                    <h3 class="mb-0"><?= $totalCourses > 0 ? round($totalStudents / $totalCourses, 1) : 0 ?></h3>
                                </div>
                                <div class="stats-icon text-info">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Students Table -->
                <div class="table-responsive mb-4">
                    <h4 class="mb-3">Recent Students</h4>
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Course</th>
                                <th>Year</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $recentStudents = array_slice($students, 0, 5); // Show only 5 recent students
                            foreach ($recentStudents as $index => $student): 
                            ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($student['first_name']) ?></td>
                                <td><?= htmlspecialchars($student['last_name']) ?></td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?= htmlspecialchars($student['course_name'] ?? 'Not Assigned') ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($student['year_level']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Courses Table -->
                <div class="table-responsive">
                    <h4 class="mb-3">Active Courses</h4>
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Description</th>
                                <th>Students</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $recentCourses = array_slice($courses, 0, 5); // Show only 5 recent courses
                            foreach ($recentCourses as $index => $course): 
                            ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><strong><?= htmlspecialchars($course['course_code']) ?></strong></td>
                                <td><?= htmlspecialchars($course['course_name']) ?></td>
                                <td><?= htmlspecialchars($course['course_description']) ?></td>
                                <td>
                                    <span class="badge bg-success">
                                        <?= $courseStudentCounts[$course['course_name']] ?? 0 ?> students
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
</body>
</html>