<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
switch (@parse_url($_SERVER['REQUEST_URI'])['path']) {
   case '/':                   // URL (without file name) to a default screen
      require 'login.php';
      break; 
   case '/signup.php':     // if you plan to also allow a URL with the file name 
       require 'signup.php';
       break;              
    case '/login.php':
       require 'login.php';
       break;
   default:
      http_response_code(404);
      exit('Not Found');
}  
?>