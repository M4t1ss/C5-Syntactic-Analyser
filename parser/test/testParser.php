<?php header('Content-type: text/html; charset=utf-8');
set_time_limit(2);
ini_set('memory_limit','2048M');
$rezs['SHIFT'] = $rezs['REDUCE'] = $rezs['LEFT ARC'] = $rezs['RIGHT ARC'] = 0;

	//lēmumu koks
	$file = fopen("training.json", "r");
	$json = fgets($file);
	$tree = json_decode($json, true);
	// var_dump($tree);
	
	//testa dati
	$fileTest = fopen("test.json", "r");
	$jsonTest = fgets($fileTest);
	$samples = json_decode($jsonTest, true);
	
	$correctMoveCount = $inCorrectMoveCount = 0;
	foreach($samples as $oneSample){
	
		$sample = array(
				'stack'=>$oneSample['stack'],
				'buffer'=>$oneSample['buffer'],
				'stackTopHasArc'=>$oneSample['stackTopHasArc'],
				'bufferNextHasArc'=>$oneSample['bufferNextHasArc']
			);
			
		$toDoMove = getMove($sample);
		
		if ($toDoMove == $oneSample['move']){
			$correctMoveCount++;
		}else{
			$inCorrectMoveCount++;
		}
	}
	$kopa = $correctMoveCount+$inCorrectMoveCount;
	echo "Pareizi - ".$correctMoveCount." (".round($correctMoveCount/$kopa*100)."%)</br>";
	echo "Nepareizi - ".$inCorrectMoveCount." (".round($inCorrectMoveCount/$kopa*100)."%)</br>";
	

//funkcija darbības uzminēšanai
function getMove($sample){
	global $tree;
	$root = $tree;
	while($root['type']!="result"){
		$attr = $root['name'];
		$sampleVal = $sample[$attr];
		foreach($root['vals'] as $val){
			if ($val['name']==$sampleVal){
				$childNode = $val;
				break;
			}
		}
		if(isset($childNode)) {
			$root = $childNode['child'];
		}else{//ja netiek pie konkrēta gājiena kokā, mēģina uzminēt
			$root['type']="result";
			global $rezs;
			traverseArray($root);
			//pārbauda, kurus gājienus vispār drīkst veikt
			if(//Vai drīkst shift
				$sample['buffer']==""
			) $disallowed[]="SHIFT";
			if(//Vai drīkst reduce
				$sample['stack']=="" || 
				$sample['stackTopHasArc']=='false'
			) $disallowed[]="REDUCE";
			if(//Vai drīkst left arc
				$sample['buffer']=="" || 
				$sample['stack']=="" || 
				$sample['stackTopHasArc']=='true'
			) $disallowed[]="LEFT ARC";
			if(//Vai drīkst right arc
				$sample['buffer']=="" || 
				$sample['stack']=="" || 
				$sample['bufferNextHasArc']=='true'
			) $disallowed[]="RIGHT ARC";
			//noņem no iespējamajiem minējumiem neatļautos
			//var_dump($sample);
			foreach($disallowed as $nomove) unset($rezs[$nomove]);
			$root['val'] = array_search(max($rezs), $rezs);
			$rezs['SHIFT'] = $rezs['REDUCE'] = $rezs['LEFT ARC'] = $rezs['RIGHT ARC'] = 0;
		}
		unset($childNode);
	}
	return $root['val'];
}


// Recursively traverses a multi-dimensional array.
function traverseArray($array)
{ 
	// Loops through each element. If element again is array, function is recalled. If not, result is echoed.
	foreach($array['vals'] as $value)
	{ 
		if(isset($value['child']['vals']))
		{ 
			traverseArray($value['child']); 
		}else{
			global $rezs;
			$rezs[$value['child']['name']]++;
		}
	}
}