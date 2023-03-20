<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Completed Courses</title>
</head>
<body>
<h1>Select Completed Courses</h1>

<form method="post" action="generate_schedule.php">
    <?php
    class Course {
        public $id;
        public $name;
        public $prerequisites;
        public $semesterAvailability;
        public $hours;

        public function __construct($id, $name, $prerequisites, $semesterAvailability, $hours) {
            $this->id = $id;
            $this->name = $name;
            $this->prerequisites = $prerequisites;
            $this->semesterAvailability = $semesterAvailability;
            $this->hours = $hours;
        }
    }

    $availableCourses = [
        new Course(1, 'Intro to Programming', [], [
            [1, 1],
            [1, 1],
        ], 3),
        new Course(2, 'Data Structures', [1], [
            [1, 1],
            [1, 1],
        ], 3),
        new Course(3, 'Algorithms', [2], [
            [1, 1],
            [1, 1],
        ], 3),
        new Course(4, 'Operating Systems', [2], [
            [0, 1],
            [1, 0],
        ], 4),
        new Course(5, 'Computer Networks', [2], [
            [0, 1],
            [1, 0],
        ], 3),
        new Course(6, 'Database Systems', [2], [
            [1, 0],
            [0, 1],
        ], 3),
    ];

    // Check if form was submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get completed courses from form submission
        $completedCourses = isset($_POST['completedCourses']) ? $_POST['completedCourses'] : [];
    } else {
        $completedCourses = [];
    }

    foreach ($availableCourses as $course) {
        $isChecked = in_array($course->id, $completedCourses);
        echo '<label>';
        echo "<input type='checkbox' name='completedCourses[]' value='{$course->id}'" . ($isChecked ? ' checked' : '') . ">";
        echo "{$course->name} ({$course->hours} hours)";
        echo '</label><br>';
    }
    ?>

    <br>
    <input type="submit" value="Generate Schedule">




</form>
</body>
</html>