<!DOCTYPE html>
<html>
<head>
<title>CSV uploading</title>
<script type="text/javascript" src="jquery-1.11.2.js"></script>
<!--<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>-->

<!-- For Sage -->
<script src="https://sagecell.sagemath.org/static/jquery.min.js"></script>
<script src="https://sagecell.sagemath.org/static/embedded_sagecell.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">



<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/style.css"/>



<script>
function goBack() {
    window.history.back();
}

$(document).ready(function(){
        activaTab('sagemath');
    });

    function activaTab(tab){
        $('.nav-tabs a[href="#' + tab + '"]').tab('show');
    };

//for sage
$(function () {
    // Make *any* div with class 'compute' a Sage cell
    sagecell.makeSagecell({inputLocation: 'div.compute',
                          template: sagecell.templates.minimal,
                          autoeval: true,
                      	  hide: ["evalButton",]});
    });
//autoeval - automaticke vyhodnoceni kodu bunky pri nacteni stranky
//hide: ["element_1"] - skryje první buňku na stránce (i s vyhodnocením) 
//hide: evalButton
</script>
<style type="text/css">

span.ui-button-text{
	display: none;
}

button [type=button]{
	display: none;
}

</style>
</head>
<body>
<?php
$dataId = $_GET["dataId"];
$colId = $_GET["colId"];

$username="root";
$password="fk29ix";
$servername="localhost";
$database="BP";

$filepath = "/uploads/data".$dataId."_col".$colId.".csv";
$webdevfilepath = "http://webdev.fit.cvut.cz/~ernekjan/data".$dataId."_col".$colId.".csv";



echo "<ol class=\"navigation\"><li><a href=\"index.php\">Choose another project</a></li><li><a href=\"project.php?id=$dataId\">Choose another column</a></li></ol>"

?>

<ul class="nav nav-tabs">
	<li><a href="#sagemath" data-toggle="tab">Sage</a></li>
    <li><a href="#rproject" data-toggle="tab">R</a></li>
</ul>
<div class="tab-content" id="tabs">
    
    
	
	<div class="tab-pane" id="sagemath">
	<!--	<div class="compute">
			<script type="text/x-sage">
@interact
def f(a=1, b=1, op=['+', '-', '*']):
  if (op == '+'):
    print a+b
  if (op == '-'):
    print a-b
  if (op == '*'):
    print a*b
			</script>
		</div>-->

	
		<div class="compute">

<script type="text/x-sage">
import urllib
colId = "<?php Print($colId); ?>"
dataId = "<?php Print($dataId); ?>"
#print 'column Id is {}.'.format(colId)
#print dataId
#import os
#__dir__ = '/var/www/'
#curpath = os.path.abspath(os.curdir)
#print "Current path is: %s" % (curpath)
#print "Trying to open: %s" % (os.path.join(curpath, "<?php Print($filepath); ?>"))
#f = open('<?php Print($filepath); ?>', 'r')
#print f
url = '<?php Print($webdevfilepath); ?>'
values = urllib.urlopen(url).read().strip().strip('"').split(',')
numberOfElements = len(values)
#print values
i = 0
sum = 0
while i<numberOfElements:
	#print values[i]
	if values[i]!='':
		sum+=int(values[i])
	i+=1
#print "i is: %d, sum is: %d" % (i, sum)
arithMean = 0
if i!=0:
	arithMean = float(sum/i)
print "Arithmetic mean of the given data is: %.2f" % (arithMean)
</script>
		</div>

	</div>

	<div class="tab-pane" id="rproject">
	    <div> 
		    <object type="text/html" data="https://ernekjan.ocpu.io/appStat/www/index.html" width="800px" height="600px" style="overflow:auto;"></object>
	    </div>
    </div>

<?php $conn = null; ?>

</body>
</html>