<!DOCTYPE html>
<html>
<head>
<title>CSV uploading</title>
<link rel="stylesheet" type="text/css" href="css/style.css"/>
<script type="text/javascript" src="jquery-2.1.3.js"></script>
</head>
<body>
<ol class="navigation"><li><a href="index.php">Choose another project</a></li></ol>
<script type="text/javascript">
 
 function disable_f5(e)
{
  if (((e.which || e.keyCode) == 116) ||(e.ctrlKey && ((e.keycode||e.which) == 82)))
  {
      e.preventDefault();
  }
}

$(document).ready(function(){
    $(document).bind("keydown", disable_f5);    
});
 
</script>

<?php

$id = $_GET["id"];
//existuje vubec tento soubor
$username="root";
$password="fk29ix";
$servername="localhost";
$database="BP";

$tablename = "table".$id;

try 
{
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT file_name FROM files WHERE id=".$id);
    $stmt->execute();
    $result = $stmt->fetchAll();
    foreach ($result as $row) {
    	$filename = $row['file_name'];
    }


    $sql = "DELETE FROM files WHERE id=".$id."";
    $conn->exec($sql);
    

    //---------------------------------------------------
    $query = "SELECT file_name FROM `".$tablename."` WHERE file_created=true";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    

    $sql = "DROP TABLE `".$tablename."`";
    $conn->exec($sql);
    
    //kdyz je cela tabulka files prazdna-> vynulovat!!! aby jela id zase od 1
    $stmt = $conn->prepare("SELECT COUNT(*) FROM files");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_BOTH);

    if($result[0]==0){
    	$sql = "TRUNCATE TABLE files";
    	$conn->exec($sql);
    }
    


    echo "Record deleted successfully";
}
catch(PDOException $e)
{
	echo $sql . "<br>" . $e->getMessage();
}

foreach ($rows as $key=> $row){
     //echo $row['file_name'];
     unlink($row['file_name']);
}

unlink($filename);

$conn = null;

?>