<?php
$conn = mysqli_connect("localhost", "root", "12345", "school");
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_name'], $_POST['student_score'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['student_name']));
    $score = $_POST['student_score'];
    if (!empty($name) && is_numeric($score)) {
        $score = max(0, min(100, (float)$score));
        mysqli_query($conn, "INSERT INTO students (name, score) VALUES ('$name', $score)");
        header("Location: index.php?status=created");
        exit();
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM students WHERE id = $delete_id");
    header("Location: index.php?status=deleted");
    exit();
}

$stats_query = mysqli_query($conn, "SELECT 
    COUNT(*) as total, 
    AVG(score) as avg_score,
    SUM(CASE WHEN score >= 50 THEN 1 ELSE 0 END) as passed,
    SUM(CASE WHEN score < 50 THEN 1 ELSE 0 END) as failed
FROM students");
$stats = mysqli_fetch_assoc($stats_query) ?: ['total' => 0, 'avg_score' => 0, 'passed' => 0, 'failed' => 0];
$totalStudents = (int)$stats['total'];
$avgScore = round((float)$stats['avg_score'], 1);
$passedCount = (int)$stats['passed'];
$failedCount = (int)$stats['failed'];
$passRate = $totalStudents > 0 ? round(($passedCount / $totalStudents) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - WE Portal</title>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <?php include 'nav.php'; ?>

    <main class="container pb-5">
        <!-- Toast / Alerts -->
        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] === 'created'): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                    <div>Student record has been successfully added!</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($_GET['status'] === 'deleted'): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-trash3-fill fs-5 me-2"></i>
                    <div>Student record has been deleted.</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h2 class="fw-bold mb-1">Admin Dashboard</h2>
                <p class="text-muted mb-0">Overview of student performance and grade management.</p>
            </div>
            <a href="student.php" class="btn btn-outline-primary rounded-pill px-4">
                <i class="bi bi-eye me-1"></i> Student View
            </a>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Total Students</div>
                        <div class="fs-4 fw-bold"><?= $totalStudents ?></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Passed (Rate)</div>
                        <div class="fs-4 fw-bold text-success"><?= $passedCount ?> <span class="fs-6 text-muted fw-normal">(<?= $passRate ?>%)</span></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon bg-danger-subtle text-danger">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Failed Students</div>
                        <div class="fs-4 fw-bold text-danger"><?= $failedCount ?></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon bg-info-subtle text-info">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Average Score</div>
                        <div class="fs-4 fw-bold"><?= $avgScore ?> <span class="fs-6 text-muted fw-normal">/ 100</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="custom-card p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="p-2 rounded-3 bg-success-subtle text-success">
                            <i class="bi bi-person-plus-fill fs-5"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Add New Student</h5>
                    </div>
                    <p class="text-muted small mb-4">Enter the student's name and score out of 100.</p>

                    <form method="POST" action="index.php">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Student Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text input-group-text-custom"><i class="bi bi-person"></i></span>
                                <input type="text" name="student_name" class="form-control form-control-custom border-start-0" placeholder="e.g. Sarah Connor" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary small">Exam Score (0 - 100)</label>
                            <div class="input-group">
                                <span class="input-group-text input-group-text-custom"><i class="bi bi-award"></i></span>
                                <input type="number" name="student_score" min="0" max="100" class="form-control form-control-custom border-start-0" placeholder="e.g. 85" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success-gradient w-100 py-2">
                            <i class="bi bi-plus-lg me-1"></i> Save to Database
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="custom-card p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-2 rounded-3 bg-primary-subtle text-primary">
                                <i class="bi bi-card-list fs-5"></i>
                            </div>
                            <h5 class="fw-bold mb-0">Student Directory</h5>
                        </div>
                        <span class="badge bg-body-secondary text-muted border px-3 py-2 rounded-pill">
                            <?= $totalStudents ?> Total Records
                        </span>
                    </div>

                    <div class="table-responsive rounded-3">
                        <table class="table custom-table align-middle">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 70px;">ID</th>
                                    <th scope="col">Student</th>
                                    <th scope="col">Score</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = mysqli_query($conn, "SELECT * FROM students ORDER BY id DESC");
                                if ($result && mysqli_num_rows($result) > 0):
                                    while ($row = mysqli_fetch_assoc($result)):
                                        $isPass = $row['score'] >= 50;
                                        $initial = mb_substr(trim($row['name']), 0, 1, 'UTF-8');
                                ?>
                                        <tr>
                                            <td class="text-muted fw-bold">#<?= $row['id'] ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="student-avatar"><?= htmlspecialchars(strtoupper($initial)) ?></span>
                                                    <span class="fw-bold"><?= htmlspecialchars($row['name']) ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold mb-1"><?= $row['score'] ?> / 100</div>
                                                <div class="progress" style="height: 6px; width: 90px;">
                                                    <div class="progress-bar <?= $isPass ? 'bg-success' : 'bg-danger' ?>" 
                                                         role="progressbar" 
                                                         style="width: <?= min(100, max(0, $row['score'])) ?>%"></div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge-custom <?= $isPass ? 'badge-pass' : 'badge-fail' ?>">
                                                    <i class="bi <?= $isPass ? 'bi-check-circle-fill' : 'bi-x-circle-fill' ?>"></i>
                                                    <?= $isPass ? 'Pass' : 'Fail' ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="index.php?delete_id=<?= $row['id'] ?>" 
                                                   class="btn btn-sm btn-outline-danger border-0 rounded-circle p-2"
                                                   title="Delete record"
                                                   onclick="return confirm('Are you sure you want to delete <?= htmlspecialchars(addslashes($row['name'])) ?>?');">
                                                    <i class="bi bi-trash3"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile;
                                else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="bi bi-folder2-open display-4 d-block mb-3 text-secondary"></i>
                                                <h6>No student records found</h6>
                                                <p class="small text-muted mb-0">Use the form on the left to add your first student.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>