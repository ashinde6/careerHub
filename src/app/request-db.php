<?php
function addRequests($reqDate, $roomNumber, $reqBy, $repairDesc, $reqPriority)
{
   global $db;   
   //$reqDate = date('Y-m-d');      // ensure proper data type before inserting it into a db
   
   // $query = "INSERT INTO requests (reqDate, roomNumber, reqBy, repairDesc, reqPriority) VALUES ('2024-03-18', 'CCC', 'Someone', 'fix light', 'medium')";
   // $query = "INSERT INTO requests (reqDate, roomNumber, reqBy, repairDesc, reqPriority) VALUES ('" . $reqDate . "', '" . $roomNumber . "', '" . $reqBy . "', '" . $repairDesc . "', '" . $reqPriority . "')";  
    
   $query = "INSERT INTO requests (reqDate, roomNumber, reqBy, repairDesc, reqPriority) VALUES (:reqDate, :roomNumber, :reqBy, :repairDesc, :reqPriority)";  
   
   try { 
      // $statement = $db->query($query);   // compile & exe

      // prepared statement
      // pre-compile
      $statement = $db->prepare($query);

      // fill in the value
      $statement->bindValue(':reqDate', $reqDate);
      $statement->bindValue(':roomNumber', $roomNumber);
      $statement->bindValue(':reqBy',$reqBy);
      $statement->bindValue(':repairDesc', $repairDesc);
      $statement->bindValue(':reqPriority', $reqPriority);

      // exe
      $statement->execute();
      $statement->closeCursor();
   } catch (PDOException $e)
   {
      $e->getMessage();   // consider a generic message
   } catch (Exception $e) 
   {
      $e->getMessage();   // consider a generic message
   }

}

function getAllRequests()
{
   global $db;
   $query = "select * from requests";    
   $statement = $db->prepare($query);    // compile
   $statement->execute();
   $result = $statement->fetchAll();     // fetch()
   $statement->closeCursor();

   return $result;
}

function getRequestByID($id)
{
    global $db;
    // Prepare the SQL query with a placeholder for the request ID
    $query = "SELECT * FROM requests WHERE reqId = :reqId";
    
    // Compile the query
    $statement = $db->prepare($query);
    
    // Bind the ID value to the placeholder
    $statement->bindValue(':reqId', $id);
    
    // Execute the query
    $statement->execute();
    
    // Fetch the result
    $result = $statement->fetch(); // Use fetch() instead of fetchAll() since you're retrieving a single record
    
    // Close the cursor to free connection to the server
    $statement->closeCursor();
    
    return $result;
}


function updateRequest($reqId, $reqDate, $roomNumber, $reqBy, $repairDesc, $reqPriority)
{
    global $db;
    $query = "update requests set reqDate=:reqDate, roomNumber=:roomNumber, reqBy=:reqBy, repairDesc=:repairDesc, reqPriority=:reqPriority where reqId=:reqId";
    
    // Compile the query
    $statement = $db->prepare($query);
    $statement->bindValue(':reqId', $reqId);
    $statement->bindValue(':reqDate', $reqDate);
    $statement->bindValue(':roomNumber', $roomNumber);
    $statement->bindValue(':reqBy',$reqBy);
    $statement->bindValue(':repairDesc', $repairDesc);
    $statement->bindValue(':reqPriority', $reqPriority);

    $statement->execute();
    $statement->closeCursor();

}

function deleteRequest($reqId)
{
   global $db;
   $query = "delete from requests where reqId=:reqId";
   
   $statement = $db->prepare($query);
   $statement->bindValue(':reqId', $reqId);
   $statement->execute();
   $statement->closeCursor();
    
}

?>