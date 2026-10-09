<?php

$conn = mysqli_connect("localhost", "root", "12345", "school");
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - Check Result</title>
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
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="custom-card p-4 p-md-5 mb-4 text-center">
                    <div class="d-inline-flex p-3 rounded-circle bg-primary-subtle text-primary mb-3">
                        <i class="bi bi-mortarboard fs-2"></i>
                    </div>
                    <h3 class="fw-bold mb-1">Check Exam Result</h3>
                    <p class="text-muted small mb-4">Enter your full or partial name to view your official grade report.</p>

                    <form method="POST" action="student.php">
                        <div class="input-group mb-3">
                            <span class="input-group-text input-group-text-custom">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" 
                                   name="search_name" 
                                   class="form-control form-control-custom form-control-lg border-start-0" 
                                   placeholder="e.g. Ahmed or Mohamed" 
                                   value="<?= isset($_POST['search_name']) ? htmlspecialchars($_POST['search_name']) : '' ?>" 
                                   required 
                                   autofocus>
                        </div>
                        <button type="submit" class="btn btn-primary-gradient w-100 py-2 fs-6">
                            <i class="bi bi-search me-1"></i> Retrieve Result
                        </button>
                    </form>
                </div>

                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_name'])) {
                    $search_name = trim($_POST['search_name']);
                    $escaped_name = mysqli_real_escape_string($conn, $search_name);
                    $search_sql = "SELECT * FROM students WHERE name LIKE '%$escaped_name%' ORDER BY id DESC";
                    $result = mysqli_query($conn, $search_sql);

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($student = mysqli_fetch_assoc($result)) {
                            $score = (float)$student['score'];
                            $isPass = $score >= 50;
                            $statusText = $isPass ? "Passed" : "Needs Retake";
                            $cardStatusClass = $isPass ? "passed" : "failed";
                            $statusBadgeClass = $isPass ? "badge-pass" : "badge-fail";
                            $motivationalMsg = $score >= 85 ? "Excellent work! Keep reaching for the stars." : 
                                               ($score >= 50 ? "Good effort! You have successfully passed." : 
                                               "Do not be discouraged. Please consult your academic advisor for assistance.");
                ?>
                            <div class="result-card <?= $cardStatusClass ?> shadow-sm p-4 p-md-5 text-center mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-body-secondary text-muted border px-3 py-1 rounded-pill small">
                                        ID #<?= $student['id'] ?>
                                    </span>
                                    <span class="badge-custom <?= $statusBadgeClass ?>">
                                        <i class="bi <?= $isPass ? 'bi-patch-check-fill' : 'bi-exclamation-triangle-fill' ?>"></i>
                                        <?= $statusText ?>
                                    </span>
                                </div>

                                <h2 class="fw-bold mb-1"><?= htmlspecialchars($student['name']) ?></h2>
                                <p class="text-muted small mb-4">Official Academic Standing</p>

                                <div class="score-circle <?= $cardStatusClass ?> mb-4">
                                    <span class="number"><?= $score ?></span>
                                    <span class="label">out of 100</span>
                                </div>

                                <div class="p-3 rounded-3 <?= $isPass ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' ?> small fw-semibold mb-4">
                                    <?= $motivationalMsg ?>
                                </div>

                                <div class="d-flex gap-2 justify-content-center">
                                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill" onclick="window.print()">
                                        <i class="bi bi-printer me-1"></i> Print Slip
                                    </button>
                                </div>
                            </div>
                <?php
                        }
                    } else {
                ?>
                        <div class="custom-card p-4 text-center">
                            <div class="text-warning mb-2 fs-1">
                                <i class="bi bi-search"></i>
                            </div>
                            <h5 class="fw-bold">No Student Record Found</h5>
                            <p class="text-muted small mb-0">
                                We couldn't find any student matching 
                                <strong class="text-primary">"<?= htmlspecialchars($search_name) ?>"</strong>. 
                                Please check spelling and try again.
                            </p>
                        </div>
                <?php
                    }
                }
                ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>