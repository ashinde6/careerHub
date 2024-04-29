<?php 
session_start(); 
?>

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
      <?php if (isset($_SESSION["username"])) : ?>
        <form action="logout.php" method="post">
          <button type="submit" class="btn btn-danger">Logout</button>
        </form>
        <form action="delete.php" method="post">
          <button type="submit" class="btn btn-danger">Delete User</button>
        </form>
      <?php endif; ?>
  </nav>
</header>    