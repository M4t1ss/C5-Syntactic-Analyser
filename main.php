<?php
error_reporting(0);
include_once('nivre.php');
//////////////////////////////////////////////////////////
//						Dati							//
//////////////////////////////////////////////////////////
$sentance_instances = array();
$sentances = array();
$rezCounter = 0;
foreach ($currentResult as $result){
	sort($correctResult);
    sort($result);
	if($result == $correctResult){
		$correctResultNumber = $rezCounter;
	}
	$rezCounter++;
}
$rezCounter = 0;
	
$stacks = array();
$buffers = array();
$arcs = array();
	foreach ($currentMoves[$correctResultNumber] as $correctMove){
	//saliek steku masīvā
	$c = array();
	foreach($correctMove[1] as $k => $v) { $c[$k] = $v; }
	
	//saliek masīvus simbolu virknēs
	$stackString = "";
	$bufferString = "";
	$arcsString = "";
	foreach($c as $val) { $stackString .= $val.";"; }
	foreach($correctMove[2] as $val) { $bufferString .= $val.";"; }
	foreach($correctMove[3] as $val) { $arcsString .= $val[0].$val[2].$val[1].";"; }
	
		$move_instances [] = array("stack" => $stackString, "buffer" => $bufferString,  "arcs" => $arcsString, "category" => $correctMove[0]);
		$stacks[] = $stackString;
		$buffers[] = $bufferString;
		$arcs[] = $arcsString;
	}
	$move_values = array(
		"stack" => $stacks,
		"buffer" => $buffers,
		"arcs" => $arcs
	);
	$move_AttrList = array('stack','buffer','arcs');
	$move_class = array("SHIFT","REDUCE","LEFT ARC","RIGHT ARC");

?>
<html>
  <head>
	<title>C5 + Arc Eager Shift Reduce</title>
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.1.min.js"></script>
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.3.3/underscore-min.js"></script>
    <script type="text/javascript" src="https://raw.githubusercontent.com/DmitryBaranovskiy/raphael/master/raphael-min.js"></script>
    <script type="text/javascript" src="http://sigmajs.org/assets/js/sigma.min.js"></script>
    <script type="text/javascript" src="https://raw.githubusercontent.com/strathausen/dracula/master/lib/dracula_graffle.js"></script>
    <script type="text/javascript" src="https://raw.githubusercontent.com/strathausen/dracula/master/lib/dracula_graph.js"></script>
    <script type="text/javascript" src="https://raw.githubusercontent.com/strathausen/dracula/master/lib/dracula_algorithms.js"></script>
	<script type="text/javascript" src="https://raw.githubusercontent.com/mbostock/d3/master/d3.min.js"></script>
    <script type="text/javascript" src="js/c5.js"></script>
	<script type='text/javascript' src='https://www.google.com/jsapi?autoload={"modules":[{"name":"visualization","version":"1","packages":["orgchart"]}]}'></script>
    <script type="text/javascript">
	var examples = [
	<?php
		$n = 1;
		$c = count($move_instances);
		foreach($move_instances as $move){
			echo "{move:'M".$n."',stack:'".$move['stack']."', buffer:'".$move['buffer']."', arcs:'".$move['arcs']."', move:'".$move['category']."'}";
			if($n<$c){$n++;echo ",";}
		}
	?>
	];

	examples = _(examples);
	var features = ['stack','buffer','arcs'];
	var samples = [
		{stack:'1_Zēns;', buffer:'1_Zēns;2_gāja;3_uz;4_skolu;', arcs:''}
	]
	</script>

    <script type="text/javascript">
	 $(document).ready(function(){
	  
		console.log('all systems go');

		$("#fire_tennis").click(function(e){
			e.preventDefault();
			var testModel = c5(examples,'move',features);
			drawGraph(testModel,'canvas');
			renderSamples(samples,$("#samples"),testModel,'move',features);//palaiž minēšanu
			/*
			vienam minējumam:
			renderSamples([{stack:'1_Zēns;', buffer:'1_Zēns;2_gāja;3_uz;4_skolu;', arcs:''}],$("#samples"),testModel,'play',features);
			*/
			renderTrainingData(examples,$("#training"),'move',features);
			console.log("error");
			console.log(calcError(samples,testModel,'move'));//aprēķina kļūdu
		  });
	});
    </script>
  </head>
  <body>
    <div id="main">
      <h1>C5 + Arc Eager Shift Reduce</h1>
		<a id="fire_tennis" href="#">Darbināt</a>
		<div id="data-container">
		<div  id='canvas'></div>
		<div >
		  <h3>Pāris minējumi</h3>
		  <table id='samples'>
		  </table>
		  <h3>Treniņu dati</h3>
		  <table id='training'>
		  </table>
		</div>
      </div>
    </div>
  </body>
</html>
