<?php header('Content-type: text/html; charset=utf-8'); ?>
<html>
  <head>
	<title>C5 + Arc Eager Shift Reduce</title>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script type="text/javascript" src="includes/jquery-1.8.1.min.js"></script>
    <script type="text/javascript" src="includes/underscore-min.js"></script>
    <script type="text/javascript" src="includes/raphael-min.js"></script>
    <script type="text/javascript" src="includes/sigma.min.js"></script>
    <script type="text/javascript" src="includes/dracula_graffle.js"></script>
    <script type="text/javascript" src="includes/dracula_graph.js"></script>
    <script type="text/javascript" src="includes/dracula_algorithms.js"></script>
	<script type="text/javascript" src="includes/d3.min.js"></script>
    <script type="text/javascript" src="includes/c5.js"></script>
	<script type='text/javascript' src='includes/jsapi.js'></script>
    <script type="text/javascript" src="generator/data.js"></script>
	<script type="text/javascript">
	var model;
	function myFunction(){
		model = c5(examples,'move',features);
		drawGraph(model,'canvas');
		renderTrainingData(examples,$("#training"),'move',features);
	}
    </script>
  </head>
  <body onload="myFunction()">
    <div id="main">
		<div id="data-container">
			<div id='canvas'></div>
			<input type="text" id="stack" name="stack" placeholder="Top word in stack"/>
			<input type="text" id="buffer" name="buffer" placeholder="Next word in buffer"/>
			<input type="text" id="stackTopHasArc" name="stackTopHasArc" placeholder="Top word in stack has an arc"/>
			<input type="text" id="bufferNextHasArc" name="bufferNextHasArc" placeholder="Next word in buffer has an arc"/>
			<input type="button" name="submit" value="minēt" onclick="
			renderSamples(
				[{
					stack:$('#stack').val(), 
					buffer:$('#buffer').val(), 
					bufferNextHasArc:$('#bufferNextHasArc').val(),
					stackTopHasArc:$('#stackTopHasArc').val()
				}],
				$('#samples'),
				model,
				'move',
				features
			);
			"/>
		  <h3>Pāris minējumi</h3>
		  <table border="1" id='samples'>
			  <tr>
				  <th>Stack</th>
				  <th>Buffer</th>
				  <th>StackTopHasArc</th>
				  <th>BufferNextHasArc</th>
				  <th>Move</th>
			  </tr>
		  </table>
		  <h4>Treniņu dati</h4>
		  <table border="1" style="font-size:12px;" id='training'>
			  <tr>
				  <th>Stack</th>
				  <th>Buffer</th>
				  <th>StackTopHasArc</th>
				  <th>BufferNextHasArc</th>
				  <th>Move</th>
			  </tr>
		  </table>
      </div>
    </div>
  </body>
</html>
