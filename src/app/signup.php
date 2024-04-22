<?php
require 'connect-db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['pwd'];
    $email = $_POST['email'];
    $user_type = $_POST['user_type'];

    require 'controller.php';
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">   
  <meta http-equiv="X-UA-Compatible" content="IE=edge">  <!-- required to handle IE -->
  <meta name="viewport" content="width=device-width, initial-scale=1">  
  <title>CareerHub</title> 
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">  
  <link rel="stylesheet" href="/static/styling/maintenance-system.css" /> 
</head>
<body>  
  <div>  
    <h1>CareerHub</h1>
    <div>
        <?php if ($message != ''): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
    </div>
    <form action="login.php" method="post">
      Name: <input type="text" name="name" required /> <br />     
      Username: <input type="text" name="username" required /> <br/>
      Password: <input type="password" name="pwd" required /> <br/>
      Email: <input type="email" name="email" required /> <br />
      Role: <select id="user_type" name="user_type" required>
                <option value="" disabled selected>-- Select an Option --</option>
                <option value="job_seeker">Job Seeker</option>
                <option value="employer">Employer</option>
            </select> <br />
      <input type="submit" value="Submit" class="btn" />
      <a href="login.php" class="btn">Back</a>
    </form>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    
</body>
</html>