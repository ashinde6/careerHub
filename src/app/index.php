<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
switch (@parse_url($_SERVER['REQUEST_URI'])['path']) {
   case '/':                   // URL (without file name) to a default screen
      require 'signup.php';
      break; 
   // case '/simpleform.php':     // if you plan to also allow a URL with the file name 
   //    require 'simpleform.php';
   //    break;              
   // case '/contact.php':
   //    require 'contact.php';
   //    break;
   default:
      http_response_code(404);
      exit('Not Found');
}  
?>