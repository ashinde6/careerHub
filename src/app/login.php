<?php
require 'connect-db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['name'];
    $password = $_POST['pwd'];

    // Retrieve user from database
    $stmt = $db->prepare("SELECT * FROM User WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password correct, redirect to home.php
            header("Location: home.php");
            exit();
        } else {
            // Incorrect password
            $error_message = "Incorrect password or username";
        }
    } else {
        // User not found
        $error_message = "Incorrect password or username";
    }
}
?>

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
    <form action="login.php" method="post">     
      Username: <input type="text" name="name" required /> <br/>
      Password: <input type="password" name="pwd" required /> <br/>
      <input type="submit" value="Submit" class="btn" /> 
      <a href="signup.php" class="btn">Sign Up</a>
    </form>
    <?php if (isset($error_message)) : ?>
      <p><?php echo $error_message; ?></p>
    <?php endif; ?>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    
</body>
</html>
