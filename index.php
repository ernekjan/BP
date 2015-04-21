<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/style.css"/>
	<script type="text/javascript" src="jquery-2.1.3.js"></script>
</head>
<body>
<script>
function myFunction(val) {
    //alert("The input value has changed. The new value is: " + val);
    //zavolat PHP fci, ktera se podiva do databaze, zda zadany nazev projektu jiz existuje
    //pokud existuje -> zablokovat submit button -> uzivatel musi zmenit nazev projektu, aby byl jedinecny
    //pokud neexistuje -> pokud je zablokovany submit, tak odblokovat, jinak nic 
}

$('#upload').change(function() {
    alert('changed!');
});  


</script>
<form enctype="multipart/form-data" action="upload.php" method="POST">
    Project name: <br>
    <input type="text" name="projectname" onchange="myFunction(this.value)" required><br>
    Choose file to upload: <input name="userfile" onchange="myFunction(this.value);" type="file" id="upload" required/>
    <input type="checkbox" name="header" value="header">CSV has a header line<br>
    <input type="submit" value="Upload File" />
</form>           
<?php


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
    $stmt = $conn->prepare("SELECT * FROM files"); 
    //id, project_name, user_file_name, file_name, has_header
    $stmt->execute();
    $result = $stmt->fetchAll();

    if ($result!=null) {
    	
    
	    echo "<table style='border: solid 1px black;'>";
		//echo "<tr><th>Id</th><th>Project Name</th><th>User File Name</th><th>File Name</th><th>Has header</th><th>Delete project</th></tr>";
		echo "<tr><th>Project Name</th><th>User File Name</th><th></th></tr>";
	    
	    foreach ($result as $row) {
	    	$id = $row['id'];
	    	//<td style='border: solid 1px black;'><a href=\"project.php?id=$id\">". $id ."</a></td>
	    	//<td style='border: solid 1px black;'>". $row['file_name'] . "</td>
			//<td style='border: solid 1px black;'>". $row['has_header'] . "</td>
	    	echo "<tr>
			<td style='border: solid 1px black;'><a href=\"project.php?id=$id\">". $row['project_name'] ."</a></td>
			<td style='border: solid 1px black;'>". $row['user_file_name'] . "</td>
			<td style='border: solid 1px black;'><a href=\"deleteproject.php?id=$id\">Delete project</a></td>
			</tr>";
	    }

	    echo "</table>";
	}

}
catch(PDOException $e)
{
	echo "Connection failed: " . $e->getMessage();
}


$conn = null;

?>
</body>
</html>  
       