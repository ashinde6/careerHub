<?php 
include('header.php');

require 'connect-db.php'; 

$role = "";
$user = "";

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
$stmt = $db->prepare("SELECT job_name, work_type FROM job");
$stmt->execute();
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<html>
<head>
  <meta charset="utf-8">   
  <meta http-equiv="X-UA-Compatible" content="IE=edge">  <!-- required to handle IE -->
  <meta name="viewport" content="width=device-width, initial-scale=1">  
  <title>CareerHub</title> 
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">  
  <link rel="stylesheet" href="/static/styling/maintenance-system.css" /> 
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        var role = '<?php echo $role; ?>';

        var jobseekers = document.getElementById('jobseekers');
        var employerFields = document.getElementById('employerFields');

        if (role === 'job_seeker') {
            jobseekers.style.display = 'block';
            employerFields.style.none = 'none';
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
  <div>  
    <h1 class="text-align: left; font-family: 'Libre Franklin', sans-serif; font-size: 2em; margin: 20px">Welcome, <?php echo htmlspecialchars($name);?>!</h1>
    <div id="jobseekers" style="display: none">
        <input type="search">
        <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col">Job Title</th>
                <th scope="col">Category</th>
                <th scope="col">Workplace</th>
                <th scope="col">Location</th>
                <th scope="col">Department</th>
                <th scope="col">Employment Type</th>
            </tr>
            </thead>
        </table>
    </div>
    <div id="employerFields" style="display: none">
        <p>Employer</p>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    
</body>
