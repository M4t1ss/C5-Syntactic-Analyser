<?php
include_once('nivre.php');

	$move_values = array(
		"stack" => $stacks,
		"buffer" => $buffers,
		"arcs" => $arcsz
	);
	$move_AttrList = array('stack','buffer','arcs');
	$move_class = array("SHIFT","REDUCE","LEFT ARC","RIGHT ARC");

	//priekš C5 sintaktiskā analizatora, koka lēmumu ģenerēšanai
	$toFile = "var examples = [
";

		$n = 1;
		$c = count($move_instances);
		foreach($move_instances as $move){
			//sadalam steku pa simbolam
			if($move['stack']!=''){
				switch($move['stack'][0]){
					case 'v':
						$s0=$move['stack'][0];
						$s1=$move['stack'][1];
						$s2=$move['stack'][2];
						$s3=$move['stack'][3];
						$s4=$move['stack'][4];
						$s5=$move['stack'][5];
						$s6=$move['stack'][6];
						$s7=$move['stack'][7];
						$s8=$move['stack'][8];
						$s9=$move['stack'][9];
						$s10=$move['stack'][10];
						break;
					case 'a':
					case 'p':
					case 'm':
						$s0=$move['stack'][0];
						$s1=$move['stack'][1];
						$s2=$move['stack'][2];
						$s3=$move['stack'][3];
						$s4=$move['stack'][4];
						$s5=$move['stack'][5];
						$s6=$move['stack'][6];
						$s7='NULL';
						$s8='NULL';
						$s9='NULL';
						$s10='NULL';
						break;
					case 'n':
						$s0=$move['stack'][0];
						$s1=$move['stack'][1];
						$s2=$move['stack'][2];
						$s3=$move['stack'][3];
						$s4=$move['stack'][4];
						$s5=$move['stack'][5];
						$s6='NULL';
						$s7='NULL';
						$s8='NULL';
						$s9='NULL';
						$s10='NULL';
						break;
					case 'c':
					case 'r':
						$s0=$move['stack'][0];
						$s1=$move['stack'][1];
						$s2=$move['stack'][2];
						$s3='NULL';
						$s4='NULL';
						$s5='NULL';
						$s6='NULL';
						$s7='NULL';
						$s8='NULL';
						$s9='NULL';
						$s10='NULL';
						break;
					case 'q':
					case 'z':
						$s0=$move['stack'][0];
						$s1=$move['stack'][1];
						$s2='NULL';
						$s3='NULL';
						$s4='NULL';
						$s5='NULL';
						$s6='NULL';
						$s7='NULL';
						$s8='NULL';
						$s9='NULL';
						$s10='NULL';
						break;
				}				
			}else{
				$s0='NULL';
				$s1='NULL';
				$s2='NULL';
				$s3='NULL';
				$s4='NULL';
				$s5='NULL';
				$s6='NULL';
				$s7='NULL';
				$s8='NULL';
				$s9='NULL';
				$s10='NULL';
			}
			//sadalam rindu pa simbolam
			if($move['buffer']==''){
				switch($move['buffer'][0]){
					case 'v':
						$b0=$move['buffer'][0];
						$b1=$move['buffer'][1];
						$b2=$move['buffer'][2];
						$b3=$move['buffer'][3];
						$b4=$move['buffer'][4];
						$b5=$move['buffer'][5];
						$b6=$move['buffer'][6];
						$b7=$move['buffer'][7];
						$b8=$move['buffer'][8];
						$b9=$move['buffer'][9];
						$b10=$move['buffer'][10];
						break;
					case 'a':
					case 'p':
					case 'm':
						$b0=$move['buffer'][0];
						$b1=$move['buffer'][1];
						$b2=$move['buffer'][2];
						$b3=$move['buffer'][3];
						$b4=$move['buffer'][4];
						$b5=$move['buffer'][5];
						$b6=$move['buffer'][6];
						$b7='NULL';
						$b8='NULL';
						$b9='NULL';
						$b10='NULL';
						break;
					case 'n':
						$b0=$move['buffer'][0];
						$b1=$move['buffer'][1];
						$b2=$move['buffer'][2];
						$b3=$move['buffer'][3];
						$b4=$move['buffer'][4];
						$b5=$move['buffer'][5];
						$b6='NULL';
						$b7='NULL';
						$b8='NULL';
						$b9='NULL';
						$b10='NULL';
						break;
					case 'c':
					case 'r':
						$b0=$move['buffer'][0];
						$b1=$move['buffer'][1];
						$b2=$move['buffer'][2];
						$b3='NULL';
						$b4='NULL';
						$b5='NULL';
						$b6='NULL';
						$b7='NULL';
						$b8='NULL';
						$b9='NULL';
						$b10='NULL';
						break;
					case 'q':
					case 'z':
						$b0=$move['buffer'][0];
						$b1=$move['buffer'][1];
						$b2='NULL';
						$b3='NULL';
						$b4='NULL';
						$b5='NULL';
						$b6='NULL';
						$b7='NULL';
						$b8='NULL';
						$b9='NULL';
						$b10='NULL';
						break;
				}				
			}else{
				$b0='NULL';
				$b1='NULL';
				$b2='NULL';
				$b3='NULL';
				$b4='NULL';
				$b5='NULL';
				$b6='NULL';
				$b7='NULL';
				$b8='NULL';
				$b9='NULL';
				$b10='NULL';
			}
			
			$toFile .= "{s0:'".$s0."', s1:'".$s1."', s2:'".$s2."', s3:'".$s3."', s4:'".$s4."', s5:'".$s5."', s6:'".$s6."', s7:'".$s7."', s8:'".$s8."', s9:'".$s9."', s10:'".$s10."', b0:'".$b0."', b1:'".$b1."', b2:'".$b2."', b3:'".$b3."', b4:'".$b4."', b5:'".$b5."', b6:'".$b6."', b7:'".$b7."', b8:'".$b8."', b9:'".$b9."', b10:'".$b10."', stackTopHasArc:'".($move['stackTopHasArc'] ? 'true' : 'false')."', move:'".$move['category']."'}";
			if($n<$c){$n++;$toFile .= ",\n";}
		}
		$toFile .= "
];\n";
		$toFile .= "examples = _(examples);\n";
		$toFile .= "var features = ['s0','s1','s2','s3','s4','s5','s6','s7','s8','s9','s10','b0','b1','b2','b3','b4','b5','b6','b7','b8','b9','b10','stackTopHasArc'];\n";
		
		$file = 'results/data.js';
		file_put_contents($file, $toFile);
	
	//priekš oriģinālās C5 programmas
	$toFile = "";
		$n = 1;
		$c = count($move_instances);
		foreach($move_instances as $move){
			//sadalam steku pa simbolam
			if($move['stack']!=''){
				switch($move['stack'][0]){
					case 'v':
						$s0=$move['stack'][0];
						$s1=$move['stack'][1];
						$s2=$move['stack'][2];
						$s3=$move['stack'][3];
						$s4=$move['stack'][4];
						$s5=$move['stack'][5];
						$s6=$move['stack'][6];
						$s7=$move['stack'][7];
						$s8=$move['stack'][8];
						$s9=$move['stack'][9];
						$s10=$move['stack'][10];
						break;
					case 'a':
					case 'p':
					case 'm':
						$s0=$move['stack'][0];
						$s1=$move['stack'][1];
						$s2=$move['stack'][2];
						$s3=$move['stack'][3];
						$s4=$move['stack'][4];
						$s5=$move['stack'][5];
						$s6=$move['stack'][6];
						$s7='NULL';
						$s8='NULL';
						$s9='NULL';
						$s10='NULL';
						break;
					case 'n':
						$s0=$move['stack'][0];
						$s1=$move['stack'][1];
						$s2=$move['stack'][2];
						$s3=$move['stack'][3];
						$s4=$move['stack'][4];
						$s5=$move['stack'][5];
						$s6='NULL';
						$s7='NULL';
						$s8='NULL';
						$s9='NULL';
						$s10='NULL';
						break;
					case 'c':
					case 'r':
						$s0=$move['stack'][0];
						$s1=$move['stack'][1];
						$s2=$move['stack'][2];
						$s3='NULL';
						$s4='NULL';
						$s5='NULL';
						$s6='NULL';
						$s7='NULL';
						$s8='NULL';
						$s9='NULL';
						$s10='NULL';
						break;
					case 'q':
					case 'z':
						$s0=$move['stack'][0];
						$s1=$move['stack'][1];
						$s2='NULL';
						$s3='NULL';
						$s4='NULL';
						$s5='NULL';
						$s6='NULL';
						$s7='NULL';
						$s8='NULL';
						$s9='NULL';
						$s10='NULL';
						break;
				}				
			}else{
				$s0='NULL';
				$s1='NULL';
				$s2='NULL';
				$s3='NULL';
				$s4='NULL';
				$s5='NULL';
				$s6='NULL';
				$s7='NULL';
				$s8='NULL';
				$s9='NULL';
				$s10='NULL';
			}
			//sadalam rindu pa simbolam
			if($move['buffer']==''){
				switch($move['buffer'][0]){
					case 'v':
						$b0=$move['buffer'][0];
						$b1=$move['buffer'][1];
						$b2=$move['buffer'][2];
						$b3=$move['buffer'][3];
						$b4=$move['buffer'][4];
						$b5=$move['buffer'][5];
						$b6=$move['buffer'][6];
						$b7=$move['buffer'][7];
						$b8=$move['buffer'][8];
						$b9=$move['buffer'][9];
						$b10=$move['buffer'][10];
						break;
					case 'a':
					case 'p':
					case 'm':
						$b0=$move['buffer'][0];
						$b1=$move['buffer'][1];
						$b2=$move['buffer'][2];
						$b3=$move['buffer'][3];
						$b4=$move['buffer'][4];
						$b5=$move['buffer'][5];
						$b6=$move['buffer'][6];
						$b7='NULL';
						$b8='NULL';
						$b9='NULL';
						$b10='NULL';
						break;
					case 'n':
						$b0=$move['buffer'][0];
						$b1=$move['buffer'][1];
						$b2=$move['buffer'][2];
						$b3=$move['buffer'][3];
						$b4=$move['buffer'][4];
						$b5=$move['buffer'][5];
						$b6='NULL';
						$b7='NULL';
						$b8='NULL';
						$b9='NULL';
						$b10='NULL';
						break;
					case 'c':
					case 'r':
						$b0=$move['buffer'][0];
						$b1=$move['buffer'][1];
						$b2=$move['buffer'][2];
						$b3='NULL';
						$b4='NULL';
						$b5='NULL';
						$b6='NULL';
						$b7='NULL';
						$b8='NULL';
						$b9='NULL';
						$b10='NULL';
						break;
					case 'q':
					case 'z':
						$b0=$move['buffer'][0];
						$b1=$move['buffer'][1];
						$b2='NULL';
						$b3='NULL';
						$b4='NULL';
						$b5='NULL';
						$b6='NULL';
						$b7='NULL';
						$b8='NULL';
						$b9='NULL';
						$b10='NULL';
						break;
				}				
			}else{
				$b0='NULL';
				$b1='NULL';
				$b2='NULL';
				$b3='NULL';
				$b4='NULL';
				$b5='NULL';
				$b6='NULL';
				$b7='NULL';
				$b8='NULL';
				$b9='NULL';
				$b10='NULL';
			}
			$toFile .= $s0.", ".$s1.", ".$s2.", ".$s3.", ".$s4.", ".$s5.", ".$s6.", ".$s7.", ".$s8.", ".$s9.", ".$s10.", ".$b0.", ".$b1.", ".$b2.", ".$b3.", ".$b4.", ".$b5.", ".$b6.", ".$b7.", ".$b8.", ".$b9.", ".$b10.", ".($move['stackTopHasArc'] ? 'true' : 'false').", ".$move['category'];
			if($n<$c){$n++;$toFile .= "\n";}
		}
		
		$file = 'results/data.data';
		file_put_contents($file, $toFile);
		
		
		$namesFile = "SHIFT, REDUCE, LEFT ARC, RIGHT ARC.\n\n";
		$namesFile .= "s0: discrete 1000000.\n";
		$namesFile .= "s1: discrete 1000000.\n";
		$namesFile .= "s2: discrete 1000000.\n";
		$namesFile .= "s3: discrete 1000000.\n";
		$namesFile .= "s4: discrete 1000000.\n";
		$namesFile .= "s5: discrete 1000000.\n";
		$namesFile .= "s6: discrete 1000000.\n";
		$namesFile .= "s7: discrete 1000000.\n";
		$namesFile .= "s8: discrete 1000000.\n";
		$namesFile .= "s9: discrete 1000000.\n";
		$namesFile .= "s10: discrete 1000000.\n";
		$namesFile .= "b0: discrete 1000000.\n";
		$namesFile .= "b1: discrete 1000000.\n";
		$namesFile .= "b2: discrete 1000000.\n";
		$namesFile .= "b3: discrete 1000000.\n";
		$namesFile .= "b4: discrete 1000000.\n";
		$namesFile .= "b5: discrete 1000000.\n";
		$namesFile .= "b6: discrete 1000000.\n";
		$namesFile .= "b7: discrete 1000000.\n";
		$namesFile .= "b8: discrete 1000000.\n";
		$namesFile .= "b9: discrete 1000000.\n";
		$namesFile .= "b10: discrete 1000000.\n";
		$namesFile .= "stackTopHasArc: true, false.\n";


		$namesFilefile = 'results/data.names';
		file_put_contents($namesFilefile, $namesFile);
	