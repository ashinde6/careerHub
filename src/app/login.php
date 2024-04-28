<?php
session_start();
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
            $_SESSION["username"] = $username;
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

<header>  
  <nav class="navbar navbar-expand-md navbar-dark bg-light">
    <div class="container-fluid">            
      <a class="navbar-brand" href="#">
        <span style="color: #000000; font-size: 1.5em; margin: 0;">Career</span>
        <span style="color: #52B4EE; font-size: 1.5em; margin: 0;">Hub</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar" aria-controls="collapsibleNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
  </nav>
</header>    

<body>  
  <div>  
    <div class="bluebox">
      <h3>Welcome to CareerHub!</h3>
      <form action="login.php" method="post">     
        Username: <input type="text" name="name" required /> <br/>
        Password: <input type="password" name="pwd" required /> <br/>
        <input type="submit" value="Log In" class="btn" /> 
        <a href="signup.php" class="btn">Sign Up</a>
      </form>
      <?php if (isset($error_message)) : ?>
        <p><?php echo $error_message; ?></p>
      <?php endif; ?>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    
</body>
</html>
