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
 
/*$(document).ready(function(){
 $("#msgid").html("This is Hello World by JQuery");
});*/
/*$(document).on('click', '.btn', function() {
   var id = $(this).attr('data_id');
   var self = $(this);
    $.ajax({
       url: 'MyUrl?id=' . id;
       success:function(data){
           $(self).php(data);
       }
    })
}*/
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
<!--<div id="msgid">
</div>-->
<?php

$username="root";
$password="fk29ix";
$servername="localhost";
$database="BP";


//---------------------------------------------

$uploaddir = '/var/www/uploads/';


$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
$headerFlag = false;
$flag = false;
$uploadOk = 1;
echo "<p>";

$projectname = $_POST['projectname'];
echo "<h1>".$projectname."</h1>";


// Check file size
if ($_FILES["userfile"]["size"] > 50000) {
    $errmessage = "Your file is too large. ";
    $uploadOk = 0;
}

$fileType = pathinfo($uploadfile,PATHINFO_EXTENSION);
// Allow certain file formats
if($fileType != "csv") {
    $errmessage = $errmessage . "Only CSV files are allowed. ";
    $uploadOk = 0;
}

// Check if file already exists
//TO DO: PREJMENOVAVAM => MUSIM DETEKOVAT EXISTENCI SOUBORU JINYM ZPUSOBEM!!!
if (file_exists($uploadfile)) {
   $errmessage = $errmessage . "File already exists. ";
    $uploadOk = 0;
}

if ($uploadOk == 0) {
    echo "<div class=\"failed_message\"><p>Sorry, your file was not uploaded. ". $errmessage . "</p></div>";
// if everything is ok, try to upload file
} else {

    try
    {
        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected successfully <br>"; 

        $stmt = $conn->prepare("SELECT id FROM files WHERE id=(SELECT max(id) FROM files)"); 
        //id, project_name, user_file_name, file_name, has_header
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_BOTH);
        
        if($result[0] == null){
            $filecounter = 1;
        }else{
            $filecounter = $result[0]+1; 
        }
        //echo "result: ". $result[0];

    }
    catch(PDOException $e)
    {
        echo "Connection failed: " . $e->getMessage();
    }

    $newname = $uploaddir . 'data' . $filecounter . '.csv';

    //if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $newname)) {

    //CSV file has a header
    if (isset($_POST['header'])){
        $headerFlag = true;
    }



      $userfilename = basename($_FILES['userfile']['name']);
      //-------------------------------------------------------

    try
    {
        // prepare sql and bind parameters
        $stmt = $conn->prepare("INSERT INTO files (id, project_name, user_file_name, file_name, has_header) 
        VALUES (null, :projectname, :userfilename, :filename, :hasheader)");
        $stmt->bindParam(':projectname', $projectname);
        $stmt->bindParam(':userfilename', $userfilename);
        $stmt->bindParam(':filename', $newname);
        $stmt->bindParam(':hasheader', $headerFlag);
        $stmt->execute();

        $filecounter = $conn->lastInsertId();
        //echo "New record created successfully. Last inserted ID is: " . $last_id;
    }
    catch(PDOException $e)
    {
        echo "Connection failed: " . $e->getMessage();
    }
      
      echo "<div class=\"valid_message\"><p>File is valid, and was successfully uploaded.</p></div>";
    

//$myfile = fopen($uploadfile, "r") or die("Unable to open file!");
$myfile = fopen($newname, "r") or die("Unable to open file!");




if($headerFlag==true){
    //echo "mam header";
    $str = fgets($myfile);
    $tmphead = explode(",",$str);
    //$column = count($header);
}

$i = 1;
foreach ($tmphead as $value) {
    $head[$i] = $value;
    //echo $head[$i] . "<br>";
    $i++;
}

$tmprow = 1;
while (($tmpdata = fgetcsv($myfile,",")) !== FALSE) {
    $tmpcol = count($tmpdata);
    if($flag==false && $headerFlag == false){
        for($i=1; $i <= $tmpcol; $i++){
            $head[$i] = "var" . $i;
        }
        $flag = true;
    }
    
    for ($c=0; $c < $tmpcol; $c++) {
        $tmptable[$tmprow] = $tmpdata;
    }
    $tmprow++;
}
$tmprow--;

/**
*  class for a single column
*/
class Column 
{
    public $columnheader;
    public $isnumeric;
    public $arrvalues;

    function __construct($columnheader, $isnumeric, $arrvalues)
    {
        $this->columnheader = $columnheader;
        $this->isnumeric = $isnumeric;
        $this->arrvalues = $arrvalues;
    }
}

//echo "<table>";
for ($i=0; $i < $tmpcol; $i++) {
   
    //echo "<tr>"; 
    //echo "<td>" . $head[$i+1] . "</td>";
    $numericflag = true;
    for ($j=1; $j < $tmprow; $j++) {

        /*if(preg_match('^(\"|\')[-]{0,1}[0-9]*([\.][0-9]*){0,1}((e|E)[0-9]+){0,1}(\"|\')$', $tmptable[$j][$i], $matches)===true){
            echo $matches;
        }*/

        if ($tmptable[$j][$i]!="" && is_numeric($tmptable[$j][$i])==false){
            $numericflag = false;
        }
        $tmparr[$j] = $tmptable[$j][$i];
        //echo "<td>" . $tmptable[$j][$i] . "</td>";
    }

    //$tmpcolumn->setIsNumeric = $numericflag;
    $tmpcolumn = new Column($head[$i+1],$numericflag,$tmparr);
    $table[$i] = $tmpcolumn;

    //var_dump($tmpcolumn);
    //if($numericflag == false){
        //echo "<td> string </td>";
        //echo "<td> </td>";
    //}else{
        //echo "<td> number </td>";
        //echo "<td><a href=\"content.php\" target=\"info\"> info </a></td>";
    //}
    //echo "</tr>";
}
$rows = $tmpcol;
$columns = $tmprow;
//echo "</tr>";
//echo "</table>";


$tablename = "table". $filecounter;

try
{
    $sql = "CREATE TABLE `".$tablename."` (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
    variable VARCHAR(256) NOT NULL,
    type VARCHAR(256) NOT NULL,
    file_created BOOLEAN,
    file_name VARCHAR(256)
    )";
    //$stmt->bindParam(':tablename', $filecounter);
    $conn->exec($sql);

    echo "Table ".$filecounter." created successfully";
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
}

echo "<table id=\"vars\">";
echo "<tr><td>Variable</td><td>Type</td><td>Info</td></tr>";
for ($i=0; $i < $rows; $i++) { 
    //var_dump($table[$i]);
    echo "<tr>";
    echo "<td>". $table[$i]->columnheader ."</td>";

    
    

    if($table[$i]->isnumeric==true){
        //vytvorit soubor s nazvem data.$id._col.$idcolumn(i+1)
        $idcolumn = $i+1;
        $tmpfilename = "/var/www/uploads/data".$filecounter."_col".$idcolumn.".csv";
        $myfile = fopen($tmpfilename, "w")or die("Unable to open file!");
        //column header------------------------------------
        //$txt = $table[$i]->columnheader.",\n";
        //fwrite($myfile, $txt);
        //values
        for ($j=1; $j < $columns; $j++) { 
            //echo "<td>". $table[$i]->arrvalues[$j] ."</td>"; 
            $txt = $table[$i]->arrvalues[$j].",\n";
            fwrite($myfile, $txt);
        }
        fclose($myfile);
    }

    try {

        if($table[$i]->isnumeric==true){
            $sql = "INSERT INTO `".$tablename."` (id, variable, type, file_created, file_name)
            VALUES (null, '".$table[$i]->columnheader."', 'number', true, '".$tmpfilename."')";
        }else{
            $sql = "INSERT INTO `".$tablename."` (id, variable, type, file_created, file_name)
            VALUES (null, '".$table[$i]->columnheader."', 'string', false, null)";
        }    

        // use exec() because no results are returned
        $conn->exec($sql);
        //echo "New record created successfully";
        }
    catch(PDOException $e)
        {
        echo $sql . "<br>" . $e->getMessage();
        }
    

    if($table[$i]->isnumeric==true){
        echo "<td> number </td>";
    }else{
        echo "<td> string </td>";
    }

    //echo "<td><a href=\"\"> change </a></td>";
    //echo '<td><button type="button" class="btn" data_id="'.$i.'">Change</button></td>';

    if($table[$i]->isnumeric==true){
        //echo "<td><a href=\"content.php?dataId=$id&colId=$columnId\">Info</a></td>";
        echo "<td><a href=\"content.php?dataId=$filecounter&colId=$idcolumn\" > info </a></td>";
        ///*target=\"info\"*/
    }

    echo "</tr>";
}

echo "</table>";


//<a href="javascript:location.reload(true)">Refresh Page</a>

fclose($myfile);

//echo "<iframe name=\"info\" src=\"\" width=\"100%\"  
  //      height=\"50%\" frameBorder=\"0\"></iframe>"

} else {
      //$uploaded=false;
      echo "<div class=\"failed_message\"><p>Upload failed</p></div>";
    }
}

//mysql_close();

//----------------------------------------------


$conn = null;


?> 
</body>
</html>