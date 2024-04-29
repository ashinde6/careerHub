<?php 
include('header.php');

require 'connect-db.php'; 


$role = "";
$user = "";

$search_input = isset($_GET['search']) ? $_GET['search'] : '';

if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];

    $stmt = $db->prepare("SELECT * FROM User WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $role = $user["role"];
        $name = $user["name"];
    }
}

$jobs = [];
$stmt = $db->prepare("
    SELECT j.job_name, j.work_type, c.name, l.address_city, l.address_state, i.industry_name, s.min_salary, s.max_salary
    FROM Job j
    JOIN Company c ON j.company_id = c.company_id
    JOIN Location l ON c.location_id = l.location_id
    JOIN Industry i ON j.industry_id = i.industry_id
    JOIN Salary s ON j.salary_id = s.salary_id
    WHERE 
        j.job_name LIKE :search OR
        c.name LIKE :search OR
        i.industry_name LIKE :search OR
        l.address_city LIKE :search OR
        l.address_state LIKE :search
");

$search = '%' . $search_input . '%';
$stmt->execute(['search' => $search]);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$employer_jobs = [];
$stmt2 = $db->prepare("
    SELECT j.job_name, j.work_type, c.name, l.address_city, l.address_state, i.industry_name, s.min_salary, s.max_salary
    FROM Job j
    JOIN Company c ON j.company_id = c.company_id
    JOIN Location l ON c.location_id = l.location_id
    JOIN Industry i ON j.industry_id = i.industry_id
    JOIN Salary s ON j.salary_id = s.salary_id
    WHERE c.company_id = :company_id");

$stmt2->bindParam(':company_id', $_SESSION["company_id"], PDO::PARAM_INT);

$stmt2->execute();
$employer_jobs = $stmt2->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['logout'])) {
  // Destroy the session
  session_destroy();
  
  // Redirect to login page
  header("Location: login.php");
  exit();
}
?>

<html>
<head>
  <meta charset="utf-8">   
  <meta http-equiv="X-UA-Compatible" content="IE=edge">  <!-- required to handle IE -->
  <meta name="viewport" content="width=device-width, initial-scale=1">  
  <title>CareerHub</title> 
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">  
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnwNcsZrvWX6N3ceRF2cT4Hf0pLCrvU7ywIs4yvWHZZLw4FzlmFu9eT" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

  <link rel="stylesheet" href="/static/styling/maintenance-system.css" /> 
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        var role = '<?php echo $role; ?>';

        var jobseekers = document.getElementById('jobseekers');
        var employerFields = document.getElementById('employerFields');

        if (role === 'job_seeker') {
            jobseekers.style.display = 'block';
            employerFields.style.display = 'none';
        } else {
            employerFields.style.display = 'block';
            jobseekers.style.display = 'none';
        }
    });
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Encode+Sans+Expanded:wght@100;200;300;400;500;600;700;800;900&family=Inter:wght@100..900&family=Libre+Franklin:ital,wght@0,100..900;1,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Overlock+SC&family=Quicksand:wght@300..700&family=Wix+Madefor+Text:ital,wght@0,400..800;1,400..800&display=swap" rel="stylesheet">
</head>
<body>  
  <div style="width: 100%">  
    <h1 class="text-align: left; font-family: 'Libre Franklin', sans-serif; font-size: 1.5em; margin: 20px">Welcome, <?php echo htmlspecialchars($name);?>!</h1>
    <div id="jobseekers" style="display: none">
        <form method="GET" style="display: flex; align-items: center;">
            <input type="search" class="search-input" name="search" placeholder="Search for a job..." value="<?php echo htmlspecialchars($search_input); ?>">
            <button type="submit" style="background: none; border: none; padding: 0; margin: 0; cursor: pointer;">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
              </svg>
            </button>
        </form>
        <table class="table table-hover">
            <thead>
            <tr style="clickable-row">
                <th scope="col">Job Title</th>
                <th scope="col">Company</th>
                <th scope="col">Industry</th>
                <th scope="col">Location</th>
                <th scope="col">Department</th>
                <th scope="col">Employment Type</th>
            </tr>
            </thead>
            <tbody>
              <!-- PHP code to create table rows dynamically -->
              <?php 
                for ($i = 1; $i < count($jobs); $i++) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($jobs[$i]['job_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($jobs[$i]['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($jobs[$i]['industry_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($jobs[$i]['address_city']) . ", " . htmlspecialchars($jobs[$i]['address_state']) . "</td>";
                    echo "<td>" . htmlspecialchars($jobs[$i]['min_salary']) . "-" . htmlspecialchars($jobs[$i]['max_salary']) . "</td>";
                    echo "<td>" . htmlspecialchars($jobs[$i]['work_type']) . "</td>";
                    echo "</tr>";
                }
              ?>
            </tbody>
        </table>
    </div>
    <div id="employerFields" style="display: none">
        <table class="table table-hover">
            <thead>
            <tr style="clickable-row">
                <th scope="col">Job Title</th>
                <th scope="col">Company</th>
                <th scope="col">Industry</th>
                <th scope="col">Workplace</th>
                <th scope="col">Location</th>
            </tr>
            </thead>
            <tbody>
              <?php 
                for ($i = 1; $i < count($employer_jobs); $i++) {
                    echo "<tr>";
                    echo "<td><a href=\"submit_job.php?id=" . urlencode($jobs[$i]['job_id']) . "\">" . htmlspecialchars($jobs[$i]['job_name']) . "</a></td>";
                    echo "<td>" . htmlspecialchars($employer_jobs[$i]['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($employer_jobs[$i]['industry_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($employer_jobs[$i]['work_type']) . "</td>";
                    echo "<td>" . htmlspecialchars($employer_jobs[$i]['address_city']) . ", " . htmlspecialchars($jobs[$i]['address_state']) . "</td>";
                    echo "</tr>";
                }
              ?>
            </tbody>
        </table>
        <form action="submit_job.php" method="GET">
            <input type="submit" value="Add Job Listing">
        </form>
    </div>
    <a href="javascript:void(0);" onclick="logout()">Logout</a>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    
</body>
