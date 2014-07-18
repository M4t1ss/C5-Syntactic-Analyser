<?php 
header('Content-type: text/html; charset=utf-8');
$rezs['SHIFT'] = $rezs['REDUCE'] = $rezs['LEFT ARC'] = $rezs['RIGHT ARC'] = 0;

	//lēmumu koks
	// $file = fopen("training.json", "r");
	$file = fopen("newTree.json", "r");
	$json = fgets($file);
	$tree = json_decode($json, true);
	
	//treniņu dati
	// $fileTest = fopen("testTrainingData.json", "r");
	$fileTest = fopen("newTraining.json", "r");
	$jsonTest = fgets($fileTest);
	$samples = json_decode($jsonTest, true);
	
	$correctMoveCount = $inCorrectMoveCount = 0;
	$classifiedMoves = array(
		'SHIFT' => array(
			'SHIFT' => 0,
			'REDUCE' => 0,
			'LEFT ARC' => 0,
			'RIGHT ARC' => 0
		),
		'REDUCE' => array(
			'SHIFT' => 0,
			'REDUCE' => 0,
			'LEFT ARC' => 0,
			'RIGHT ARC' => 0
		),
		'LEFT ARC' => array(
			'SHIFT' => 0,
			'REDUCE' => 0,
			'LEFT ARC' => 0,
			'RIGHT ARC' => 0
		),
		'RIGHT ARC' => array(
			'SHIFT' => 0,
			'REDUCE' => 0,
			'LEFT ARC' => 0,
			'RIGHT ARC' => 0
		)
	);
	
	
	foreach($samples as $oneSample){
	
		$sample = array(
				's0'=>$oneSample['s0'],
				's1'=>$oneSample['s1'],
				's2'=>$oneSample['s2'],
				's3'=>$oneSample['s3'],
				's4'=>$oneSample['s4'],
				's5'=>$oneSample['s5'],
				's6'=>$oneSample['s6'],
				's7'=>$oneSample['s7'],
				's8'=>$oneSample['s8'],
				's9'=>$oneSample['s9'],
				's10'=>$oneSample['s10'],
				'b0'=>$oneSample['b0'],
				'b1'=>$oneSample['b1'],
				'b2'=>$oneSample['b2'],
				'b3'=>$oneSample['b3'],
				'b4'=>$oneSample['b4'],
				'b5'=>$oneSample['b5'],
				'b6'=>$oneSample['b6'],
				'b7'=>$oneSample['b7'],
				'b8'=>$oneSample['b8'],
				'b9'=>$oneSample['b9'],
				'b10'=>$oneSample['b10'],
				'stackTopHasArc'=>$oneSample['stackTopHasArc'],
			);
			
		$toDoMove = getMove($sample);
		
		//kurš gājiens kā klasificēts?
		$classifiedMoves[$oneSample['move']][$toDoMove]++;
		
		if ($toDoMove == $oneSample['move']){
			$correctMoveCount++;
		}else{
			$inCorrectMoveCount++;
		}
	}
	$kopa = $correctMoveCount+$inCorrectMoveCount;
	echo "Treniņu dati:<br/>";
	echo "Kopā klasificēti ".$kopa." piemēri</br>";
	echo "Pareizi - ".$correctMoveCount." (".round($correctMoveCount/$kopa*100, 2)."%)</br>";
	echo "Nepareizi - ".$inCorrectMoveCount." (".round($inCorrectMoveCount/$kopa*100, 2)."%)</br><br/>";
	var_dump($classifiedMoves);
	
	//testa dati
	// $fileTest = fopen("test.json", "r");
	$fileTest = fopen("newTest.json", "r");
	$jsonTest = fgets($fileTest);
	$samples = json_decode($jsonTest, true);
	
	$correctMoveCount = $inCorrectMoveCount = 0;
	$classifiedMoves = array(
		'SHIFT' => array(
			'SHIFT' => 0,
			'REDUCE' => 0,
			'LEFT ARC' => 0,
			'RIGHT ARC' => 0
		),
		'REDUCE' => array(
			'SHIFT' => 0,
			'REDUCE' => 0,
			'LEFT ARC' => 0,
			'RIGHT ARC' => 0
		),
		'LEFT ARC' => array(
			'SHIFT' => 0,
			'REDUCE' => 0,
			'LEFT ARC' => 0,
			'RIGHT ARC' => 0
		),
		'RIGHT ARC' => array(
			'SHIFT' => 0,
			'REDUCE' => 0,
			'LEFT ARC' => 0,
			'RIGHT ARC' => 0
		)
	);
	foreach($samples as $oneSample){
	
		$sample = array(
				's0'=>$oneSample['s0'],
				's1'=>$oneSample['s1'],
				's2'=>$oneSample['s2'],
				's3'=>$oneSample['s3'],
				's4'=>$oneSample['s4'],
				's5'=>$oneSample['s5'],
				's6'=>$oneSample['s6'],
				's7'=>$oneSample['s7'],
				's8'=>$oneSample['s8'],
				's9'=>$oneSample['s9'],
				's10'=>$oneSample['s10'],
				'b0'=>$oneSample['b0'],
				'b1'=>$oneSample['b1'],
				'b2'=>$oneSample['b2'],
				'b3'=>$oneSample['b3'],
				'b4'=>$oneSample['b4'],
				'b5'=>$oneSample['b5'],
				'b6'=>$oneSample['b6'],
				'b7'=>$oneSample['b7'],
				'b8'=>$oneSample['b8'],
				'b9'=>$oneSample['b9'],
				'b10'=>$oneSample['b10'],
				'stackTopHasArc'=>$oneSample['stackTopHasArc'],
			);
			
		$toDoMove = getMove($sample);
		
		//kurš gājiens kā klasificēts?
		$classifiedMoves[$oneSample['move']][$toDoMove]++;
		
		if ($toDoMove == $oneSample['move']){
			$correctMoveCount++;
		}else{
			$inCorrectMoveCount++;
		}
	}
	$kopa = $correctMoveCount+$inCorrectMoveCount;
	echo "Testa dati:<br/>";
	echo "Kopā klasificēti ".$kopa." piemēri</br>";
	echo "Pareizi - ".$correctMoveCount." (".round($correctMoveCount/$kopa*100, 2)."%)</br>";
	echo "Nepareizi - ".$inCorrectMoveCount." (".round($inCorrectMoveCount/$kopa*100, 2)."%)</br>";
	var_dump($classifiedMoves);
	

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
				$sample['b0']==""
			) $disallowed[]="SHIFT";
			if(//Vai drīkst reduce
				$sample['s0']=="" || 
				$sample['stackTopHasArc']=='false'
			) $disallowed[]="REDUCE";
			if(//Vai drīkst left arc
				$sample['b0']=="" || 
				$sample['s0']=="" || 
				$sample['stackTopHasArc']=='true'
			) $disallowed[]="LEFT ARC";
			if(//Vai drīkst right arc
				$sample['b0']=="" || 
				$sample['s0']==""
			) $disallowed[]="RIGHT ARC";
			//noņem no iespējamajiem minējumiem neatļautos
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