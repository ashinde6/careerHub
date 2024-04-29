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
    SELECT j.job_name, j.work_type, c.name, l.address_city, l.address_state, i.industry_name, s.min_salary, s.max_salary, p.job_posting_url, d.description, ss.skill_name, b.type
    FROM Job j
    JOIN Company c ON j.company_id = c.company_id
    JOIN Location l ON c.location_id = l.location_id
    JOIN Industry i ON j.industry_id = i.industry_id
    JOIN Salary s ON j.salary_id = s.salary_id
    JOIN Posting p ON j.job_id = p.job_id
    JOIN Description d ON j.description_id = d.description_id
    JOIN Requires r ON j.job_id = r.job_id
    JOIN Skills ss ON r.skills_id = ss.skills_id
    JOIN Offers o ON j.job_id = o.job_id
    JOIN Benefits b ON o.benefits_id = b.benefits_id
    WHERE 
        j.job_name LIKE :search OR
        c.name LIKE :search OR
        i.industry_name LIKE :search OR
        l.address_city LIKE :search OR
        l.address_state LIKE :search
");

$search = '%' . strtolower($search_input) . '%';
$stmt->execute(['search' => $search]);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
$jobs_json = json_encode($jobs);
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
        const jobs = <?php echo $jobs_json; ?>;  
        console.log(jobs);
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

        const rows = document.querySelectorAll(".clickable-row");

        rows.forEach((row) => {
          row.addEventListener("click", function () {
            const firstCell = this.querySelector("td");
            const job_title = firstCell ? firstCell.textContent.trim() : "Job Title"; 

            const secondCell = this.querySelectorAll("td")[1];
            const company_name = secondCell ? secondCell.textContent.trim() : "Company";

            const thirdCell = this.querySelectorAll("td")[2];
            const industry_name = thirdCell ? thirdCell.textContent.trim() : "Industry";

            const sixthCell = this.querySelectorAll("td")[5];
            const work_type = sixthCell ? sixthCell.textContent.trim() : "Work Type";

            const posting = getPosting(jobs, job_title, company_name, industry_name, work_type);
            const description = getDescription(jobs, job_title, company_name, industry_name, work_type);
            const skills = getSkills(jobs, job_title, company_name, industry_name, work_type);
            // const benefits = getBenefits(job_title, company_name, industry_name, work_type);

            const modalTitle = document.getElementById("modalLabel");
            modalTitle.innerHTML = job_title;

            const modalDescription = document.getElementById("description-body");
            modalDescription.innerHTML = description;

            const modalPosting = document.getElementById("posting-body");
            modalPosting.innerHTML = posting;

            const modalSkills = document.getElementById("skills-body");
            modalSkills.innerHTML = skills;

            // const modalBenefits = document.getElementById("benefits-body");
            // modalBenefits.innerHTML = benefits;

            console.log(row);

            // Show the modal using Bootstrap's modal method
            const modal = new bootstrap.Modal(document.getElementById("jobModal"));
            modal.show();  // Open the modal
          });
        });

        function findJob(jobs, job_title, company_name, industry_name, work_type) {
          for (const job in jobs) {
            if (jobs[job].job_name === job_title &&
                jobs[job].name === company_name &&
                jobs[job].work_type === work_type) {
                      return job;
            }
          }
        }


        function getBenefits(jobs, job_title, company_name, industry_name, work_type) {
          const job = findJob(jobs, job_title, company_name, industry_name, work_type);
          console.log(jobs[job]);
          // const benefit = jobs[job]["type"];
          // return benefit;
        }

        function getDescription(jobs, job_title, company_name, industry_name, work_type) {
          const job = findJob(jobs, job_title, company_name, industry_name, work_type);
          const job_description = jobs[job]["description"];
          return job_description;
        }

        function getPosting(jobs, job_title, company_name, industry_name, work_type) {
          const job = findJob(jobs, job_title, company_name, industry_name, work_type);
          const job_posting = jobs[job]["job_posting_url"];
          return job_posting;
        }

        function getSkills(jobs, job_title, company_name, industry_name, work_type) {
          const job = findJob(jobs, job_title, company_name, industry_name, work_type);
          console.log(job);
          const skill = jobs[job]["skill_name"];
          return skill;
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
            <tr>
                <th scope="col">Job Title</th>
                <th scope="col">Company</th>
                <th scope="col">Industry</th>
                <th scope="col">Location</th>
                <th scope="col">Salary</th>
                <th scope="col">Employment Type</th>
            </tr>
            </thead>
            <tbody>
              <?php 
                for ($i = 1; $i < count($jobs); $i++) {
                    echo "<tr class='clickable-row'>";
                    echo "<td>" . htmlspecialchars($jobs[$i]['job_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($jobs[$i]['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($jobs[$i]['industry_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($jobs[$i]['address_city']) . ", " . htmlspecialchars($jobs[$i]['address_state']) . "</td>";
                    $min_salary = $jobs[$i]['min_salary'];
                    $max_salary = $jobs[$i]['max_salary'];
                    if ($min_salary > 0 && $max_salary > 0) {
                        echo "<td>" . htmlspecialchars($min_salary) . " - " . htmlspecialchars($max_salary) . "</td>";
                    } else {
                        echo "<td>N/A</td>"; // Default message
                    }
                    echo "<td>" . htmlspecialchars($jobs[$i]['work_type']) . "</td>";
                    echo "</tr>";
                }
              ?>
            </tbody>
        </table>
        <div class="modal fade bd-example-modal-lg" id="jobModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Job Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body" id="modal-job-content">
                <div class="posting" id="posting">Job Posting: </div>
                <div class="posting-body" id="posting-body"></div><br>
                <div class="description" id="description">Description: </div>
                <div class="description-body" id="description-body"></div><br>
                <div class="skills" id="skills">Skills: </div>
                <div class="skills-body" id="skills-body"></div><br>
                <div class="benefits" id="benefits">Benefits: </div>
                <div class="benefits-body" id="benefits-body"></div><br>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>

    </div>
    <div id="employerFields" style="display: none">
        <p>Employer</p>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    
</body>
