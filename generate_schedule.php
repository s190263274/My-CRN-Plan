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
$maxHoursPerSemester = 12;
$numSemestersPerYear = 3;
$numYears = 8;

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

$semesters = scheduleCourses($completedCourses, $maxHoursPerSemester, $numSemestersPerYear, $numYears, $availableCourses);


foreach ($semesters as $semester) {
    var_dump($semester);
    // Output academic year and semester heading
    echo "<h2>{$semester['academicYear']} Semester {$semester['semester']}</h2>\n";

    // Output table header
    echo "<table>\n";
    echo "<thead><tr><th>Courses</th><th>Hours</th></tr></thead>\n";

    // Output table body
    echo "<tbody>";
    foreach ($semester['courses'] as $course) {
        if (is_array($course) && isset($course['name']) && isset($course['hours'])) {
            echo "<tr><td>{$course['name']}</td><td>{$course['hours']}</td></tr>\n";
        } elseif (is_object($course) && isset($course->name) && isset($course->hours)) {
            echo "<tr><td>{$course->name}</td><td>{$course->hours}</td></tr>\n";
        } else {
            echo "<tr><td colspan='2'>Invalid course format</td></tr>\n";
        }
    }
    echo "</tbody>";

    // Output table footer with total hours
    echo "<tfoot><tr><th>Total Hours</th><td>{$semester['hours']}</td></tr></tfoot>\n";
    echo "</table>\n";

    // Output line break
    echo "<br>\n";
}