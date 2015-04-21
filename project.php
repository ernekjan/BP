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
 
 
</script>

<?php


$id = $_GET["id"];

$tablename = "table".$id;

$username="root";
$password="fk29ix";
$servername="localhost";
$database="BP";

try
    {
        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo "Connected successfully <br>"; 


        $stmt = $conn->prepare("SELECT * FROM files WHERE id=".$id); 
        //id, project_name, user_file_name, file_name, has_header
        $stmt->execute();
        $result = $stmt->fetchAll();

        //$sql = 'SELECT * FROM files WHERE id='.$id;
 
        //$result = $conn->query($sql);
        //$result->setFetchMode(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            //echo "<p>".$result['id']." " . $file_name ."</p>";
            $projectname = $row['project_name'];
            //echo "Project name:". $projectname . "<br>";
            $userfilename=$row['user_file_name'];
            //echo "User file name:". $userfilename. "<br>";
            $filename=$row['file_name'];
            //echo "File name:". $filename. "<br>";
            $hasheader=$row['has_header'];
            //echo "Has header:". $hasheader. "<br>";



        $query = "SELECT * FROM `".$tablename."`";
        $stmt = $conn->prepare($query); 
        //id, project_name, user_file_name, file_name, has_header
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        }
    }
    catch(PDOException $e)
    {
        echo "Connection failed: " . $e->getMessage();
    }

if($rows != null){
    echo "<table><tr><td>Variable</td><td>Type</td><td>Info</td></tr>";
    foreach ($rows as $key=> $row){
        echo "<tr><td>".$row['variable']."</td><td>".$row['type']."</td>";
        if ($row['type']==number){
            $columnId = $row['id'];
            echo "<td><a href=\"content.php?dataId=$id&colId=$columnId\">Info</a></td>";
        }

        echo "</tr>";
    }
}

$conn = null;




?> 
</body>
</html>