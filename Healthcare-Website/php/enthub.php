<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$server = "sci-mysql";
$username = "coa123edb";
$password = "E4XujVcLcNPhwfBjx-";
$database = "coa123edb";
$error = "";

// Establish database connection
$connection = mysqli_connect($server, $username, $password, $database);
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle user input safely
$specialty = isset($_POST['specialty']) ? mysqli_real_escape_string($connection, $_POST['specialty']) : '';
$date = isset($_POST['date']) ? mysqli_real_escape_string($connection, $_POST['date']) : '';
$location = isset($_POST['location']) ? mysqli_real_escape_string($connection, $_POST['location']) : '';

// Validate date format (YYYY-MM-DD)
if (!empty($date) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    die("Invalid date format. Please use YYYY-MM-DD.");
}

// Check if a date is provided
$dateCondition = !empty($date) ? "AND b.booking_date = '$date'" : "";
$locationCondition = ($location !== 'all') ? "AND cl.name = '$location'" : '';

// SQL query to fetch consultants based on specialty and availability
$query = "SELECT c.id, c.name AS consultant_name, cl.name AS clinic_name, s.speciality AS specialty,
            (SELECT AVG(r.score) FROM reviews r WHERE r.consultant_id = c.id) AS rating
          FROM consultants c
          LEFT JOIN specialities s ON c.speciality_id = s.id
          LEFT JOIN clinics cl ON c.clinic_id = cl.id
          WHERE ('$specialty' = 'all' OR s.speciality = '$specialty')
          AND ('$location' = 'all' OR cl.name = '$location')
          AND NOT EXISTS (
              SELECT 1 FROM bookings b
              WHERE b.consultant_id = c.id
              $dateCondition
          )
          GROUP BY c.id, c.name, cl.name, s.speciality";

$result = mysqli_query($connection, $query);

if (!empty($date) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    $error = "Invalid date format. Please use YYYY-MM-DD.";
}

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ENT Healthcare Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="barchart.js"></script>
</head>
<body>
    <header class="py-3 bg-dark border-bottom">
        <div class="container">
            <h1 class="display-5 fw-bold text-white bg-dark">ENT Care Hub</h1>
            <nav class="navbar navbar-dark bg-dark">
                <div class="container-fluid">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a href="#about" class="nav-link">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a href="#contact" class="nav-link">Contact Us</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <!--Home Section-->
        <section id="home" class="py-5 bg-dark text-light">
            <div class="container text-center">
                <h2>Welcome to ENT Care Hub</h2>
                <p>Your one-stop solution for finding the best ENT consultants!</p>
                <a class="btn btn-primary" href="#consultantCarousel">Find a Consultant</a>
            </div>
        </section>


        <!-- Main Section -->
        <section class="carousel-section bg-dark text-white">
            <div id="consultantCarousel" class="carousel slide" data-bs-ride="carousel">
                <!-- Carousel Indicators -->
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#consultantCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#consultantCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#consultantCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>

                <!-- Carousel Images -->
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="group.jpg" class="d-block img-fluid w-100 rounded" style="height: 500px;" alt="Responsive image">
                    </div>
                    <div class="carousel-item">
                        <img src="doctor.jpg" class="d-block img-fluid w-100 rounded" style="height: 500px;" alt="Responsive image">
                    </div>
                    <div class="carousel-item">
                        <img src="Surgery.jpg" class="d-block img-fluid w-100 rounded" style="height: 500px;" alt="Responsive image">
                    </div>
                </div>

                <!-- Carousel Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#consultantCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#consultantCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>

            <!-- Search -->
            <div class="carousel-overlay">
                <div class="container text-center">
                    <h2 class="text-white">Search for Consultants</h2>
                    <?php if (!empty($error)) : ?>
                        <div class="alert alert-danger" role="alert">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>
                    <form action="" method="POST" class="row gy-3 justify-content-center">
                        <div class="col-md-4">
                            <label for="specialty" class="form-label text-white">Specialty:</label>
                            <select name="specialty" id="specialty" class="form-select">
                                <option value="all">All</option>
                                <option value="Otology">Otology</option>
                                <option value="Allergy">Allergy</option>
                                <option value="Rhinology">Rhinology</option>
                                <option value="Paediatric ENT">Paediatric ENT</option>
                                <option value="Head and Neck Surgery">Head and Neck Surgery</option>
                                <option value="Laryngology">Laryngology</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="clinic" class="form-label text-white">Clinic:</label>
                            <select name="location" id="clinic" class="form-select">
                                <option value="all">All Clinics</option>
                                <option value="Riverside ENT Clinic">Riverside ENT Clinic</option>
                                <option value="Oakwood ENT Centre">Oakwood ENT Centre</option>
                                <option value="Elmwood Medical Hub">Elmwood Medical Hub</option>
                                <option value="Haven ENT Clinic">Haven ENT Clinic</option>
                                <option value="Meadowlands Health Point">Meadowlands Health Point</option>
                                <option value="Hillside ENT Centre">Hillside ENT Centre</option>
                                <option value="Valley View Clinic">Valley View Clinic</option>
                                <option value="Lakeside ENT Clinic">Lakeside ENT Clinic</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="date" class="form-label text-white">Select a Date:</label>
                            <input type="date" id="date" name="date" class="form-control">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Results Section -->
        <section class="py-5 bg-dark text-white">
            <div class="container">
                <h2>Search Results</h2>
                <div class="row">
                    <?php
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Fetch reviews for the current consultant
                            $consultantId = $row['id'];
                            $reviewsQuery = "SELECT score, feedback AS comment FROM reviews WHERE consultant_id = $consultantId LIMIT 3";
                            $reviewsResult = mysqli_query($connection, $reviewsQuery);

                            echo "<div class='col-md-6'>
                                    <div class='card mb-4 shadow-sm'>
                                        <div class='card-body'>
                                            <h5 class='card-title'>" . $row['consultant_name'] . "</h5>
                                            <p class='card-text'>Specialty: " . $row['specialty'] . "</p>
                                            <p class='card-text'>Clinic: " . $row['clinic_name'] . "</p>
                                            <p class='card-text'>Rating: " . number_format($row['rating'], 1) . "/5</p>
                                        </div>
                                    </div>
                                  </div>";

                            // Display reviews next to the consultant's box
                            echo "<div class='col-md-6'>
                                    <div class='reviews'>
                                        <h6>Reviews:</h6>";
                            if ($reviewsResult && mysqli_num_rows($reviewsResult) > 0) {
                                while ($review = mysqli_fetch_assoc($reviewsResult)) {
                                    echo "<div class='review'>
                                            <p><strong>Feedback:</strong> " . $review['comment'] . "</p>
                                            <p>Rating: " . $review['score'] . "/5</p>
                                          </div>";
                                }
                            } else {
                                echo "<p>No reviews available.</p>";
                            }
                            echo "</div>
                                  </div>";
                        }
                    } else {
                        echo "<p class='text-center'>No consultants found for the selected criteria.</p>";
                    }
                    ?>
                </div>
            </div>
        </section>

        <!-- Chart Section -->
        <section id="chart-section" class="py-5 bg-dark">
            <div class="container text-center bg-dark">
                <button id="toggleChartButton" class="btn btn-primary mb-3">Consultant Data</button>
                <canvas id="myChart" width="400" height="200" style="display: none;"></canvas>
            </div>
        </section>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const toggleButton = document.getElementById("toggleChartButton");
                const chartCanvas = document.getElementById("myChart");

                toggleButton.addEventListener("click", function () {
                    if (chartCanvas.style.display === "none") {
                        chartCanvas.style.display = "block";
                    } else {
                        chartCanvas.style.display = "none";
                    }
                });
            });
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const ctx = document.getElementById("myChart");
                if (ctx) {
                    new Chart(ctx.getContext("2d"), {
                        type: "bar",
                        data: {
                            labels: ["Dr. Aisha Khan", "Dr. Wei Zhang", "Dr. Carlos Gonzales", "Dr. John Doe", "Dr. Emily Davis", "Dr. Suresh Patel", 
                                    "Dr. Fatima Al-Amin", "Dr. Laura White", "Dr. Rohan Mehta", "Dr. Nguyen Tran", "Dr. Mia Scott", "Dr. Benjamin Harris",
                                    "Dr. Akira Yamamoto", "Dr. James Clark", "Dr. Fiona Baker", "Dr. Maria Santos", "Dr. Amara Okafor", "Dr. Daniel Walker",
                                    "Dr. Grace Hall", "Dr. Ethan Taylor", "Dr. William Allen", "Dr. Priya Sharma", "Dr. Abdullah Youssef", "Dr. Isabella King",
                                    "Dr. Jack Wright"],
                            datasets: [{
                                label: 'Consultant Reviews',
                                backgroundColor: 'rgba(173, 216, 230, 0.8)',
                                borderColor: 'rgba(173, 216, 230, 1)',
                                borderWidth: 1,
                                data: [2.6, 4.8, 4.0, 2.5, 2.5, 5.0, 4.0, 3.5, 3.0, 2.3, 2.0, 3.3, 4.0, 2.9, 3.9, 2.8, 3.1, 3.2, 2.1, 3.3, 4.4, 3.3, 2.2,
                                4.0, 1.8, 3.3, 3.1, 2.8, 4.2, 2.7, 2.7, 4.4]
                            }]
                        },
                        options: {
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    }
                                },
                                y: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>

        <!--About Section-->
        <section id="about" class="py-5 bg-dark text-light">
            <div class="container">
                <h2>About Us</h2>
                <p>
                    ENT Care Hub is a healthcare organisation with the sole task in ensuring patients
                    recive the the best care possible, easily and efficiently. Formed in 1987 by Wesley Da Silva, This organisation
                    made it our sole mission that patients no longer have to struggle when finding the best doctors
                    that will best suit their needs. We provide a seemless experience for finding consultants, booking appointments,
                    and accessing healthcare resources. If you're looking for Otology, Rhinology, Allergy, Pediatric ENT care, Head and Neck Surgery,
                    Laryngology, we've got you covered.
                </p>
            </div>
        </section>

        <section id="contact" class="py-5 bg-secondary text-white">
            <div class="container text-center">
                <h2>Contact Us:</h2>
                <p>Enthub@Healthcare.co.uk</p>
            </div>
        </section>

    </main>

    <footer class="py-3 bg-secondary text-white">
        <div class="container text-center">
            <p>&copy; 2025 ENT Care Hub</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>