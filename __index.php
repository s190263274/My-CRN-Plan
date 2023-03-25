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

function scheduleCourses($completedCourses, $maxHoursPerSemester, $numSemestersPerYear, $numYears, $availableCourses) {
    $semesters = [];

    $currentYear = date('Y');
    $currentMonth = date('n');
    $currentSemester = ($currentMonth >= 9 || $currentMonth < 2) ? 1 : 2;

    for ($year = 0; $year < $numYears; $year++) {
        for ($semesterNumber = 1; $semesterNumber <= $numSemestersPerYear; $semesterNumber++) {
            $semesterCourses = [];
            $semesterHours = 0;

            foreach ($availableCourses as $key => $course) {
                if (in_array($course->id, $completedCourses)) {
                    unset($availableCourses[$key]);
                    continue;
                }

                $prerequisitesMet = true;
                foreach ($course->prerequisites as $prerequisite) {
                    if (!in_array($prerequisite, $completedCourses)) {
                        $prerequisitesMet = false;
                        break;
                    }
                }

                if ($prerequisitesMet && $course->semesterAvailability[$year][$semesterNumber] && $semesterHours + $course->hours <= $maxHoursPerSemester) {
                    $completedCourses[] = $course->id;
                    $semesterCourses[] = $course->name;
                    $semesterHours += $course->hours;
                    unset($availableCourses[$key]);
                }
            }

            if ($semesterHours > 0) {
                $semesterYear = $currentYear + $year;

                $semesters[] = [
                    'academicYear' => "{$semesterYear}-" . ($semesterYear + 1),
                    'semester' => $semesterNumber,
                    'courses' => $semesterCourses,
                    'hours' => $semesterHours,
                ];
            }
        }
    }

    return $semesters;
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get completed courses from form submission
    $completedCourses = isset($_POST['completedCourses']) ? $_POST['completedCourses'] : [];
} else {
    $completedCourses = [ ]; // Default completed courses
}
$maxHoursPerSemester = 18; // Maximum number of hours per semester
$numSemestersPerYear = 2; // Number of semesters per year
$numYears = 4; // Number of years in program

// Define available courses
$availableCourses = [
    new Course('MATH 101', 'Calculus I', [], [[1, 2], [1, 2], [1, 2], [1, 2]], 3),
    new Course('MATH 102', 'Calculus II', ['MATH 101'], [[1, 2], [1, 2], [1, 2], [1, 2]], 3),
    new Course('MATH 201', 'Calculus III', ['MATH 102'], [[0, 0], [1, 2], [1, 2], [1, 2]], 3),
    new Course('MATH 301', 'Linear Algebra', ['MATH 102'], [[0, 0], [0, 0], [1, 2], [1, 2]], 3),
    new Course('MATH 401', 'Differential Equations', ['MATH 201'], [[0, 0], [0, 0], [1, 2], [1, 2]], 3),
    new Course('CS 101', 'Introduction to Computer Science', [], [[1, 2], [1, 2], [1, 2], [1, 2]], 3),
    new Course('CS 201', 'Data Structures and Algorithms', ['CS 101'], [[0, 0], [1, 2], [1, 2], [1, 2]], 3),
    new Course('CS 301', 'Computer Architecture', ['CS 201'], [[0, 0], [0, 0], [1, 2], [1, 2]], 3),
    new Course('CS 401', 'Operating Systems', ['CS 301'], [[0, 0], [0, 0], [1, 2], [1, 2]], 3),
    new Course('CS 501', 'Database Systems', ['CS 201'], [[0, 0], [0, 0], [1, 2], [1, 2]], 3),
    new Course('CS 601', 'Artificial Intelligence', ['CS 201'], [[0, 0], [0, 0], [1, 2], [1, 2]], 3),
    new Course('STAT 101', 'Introduction to Statistics', [], [[1, 2], [1, 2], [1, 2], [1, 2]], 3),
    new Course('STAT 201', 'Regression Analysis', ['STAT 101'], [[0, 0], [1, 2], [1, 2], [1, 2]], 3),
    new Course('ENG 101', 'English Composition', [], [[1, 2], [1, 2], [1, 2], [1, 2]], 3),
    new Course('ENG 201', 'Advanced Composition', ['ENG 101'], [[0, 0], [1, 2], [1, 2], [1, 2]], 3),
    new Course('PHIL 101', 'Introduction to Philosophy', [], [[1, 2], [1, 2], [1, 2], [1, 2]], 3),
    new Course('PHIL 201', 'Ethics', ['PHIL 101'], [[0, 0], [1, 2], [1, 2], [1, 2]], 3),
];

// Schedule courses
$semesters = scheduleCourses($completedCourses, $maxHoursPerSemester, $numSemestersPerYear, $numYears, $availableCourses);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Course Schedule</title>
</head>
<body>
<h1>Course Schedule</h1>
<form method="post">
    <label for="completedCourses">Completed Courses:</label>
    <input type="text" name="completedCourses" id="completedCourses" value="<?= implode(',', $completedCourses) ?>">
    <button type="submit">Submit</button>
</form>
<table>
    <thead>
    <tr>
        <th>Academic Year</th>
        <th>Semester</th>
        <th>Courses</th>
    </tr>
    </thead>
        <?php foreach ($semesters as $semester): ?>
            <tbody>
    <tr>
        <td><?= $semester['academicYear'] ?></td>
        <td><?= $semester['semester'] ?></td>
        <td>
            <ul>
                <?php foreach ($semester['courses'] as $course): ?>
                <li><?= $course ?></li>
                <?php endforeach; ?>
            </ul>
        </td>
    </tr>
    </tbody>
    <?php endforeach; ?>
</table>
</body>
</html>