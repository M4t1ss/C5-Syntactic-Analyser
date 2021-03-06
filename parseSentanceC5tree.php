﻿<?php header('Content-type: text/html; charset=utf-8');
$rezs['SHIFT'] = $rezs['REDUCE'] = $rezs['LEFTARC'] = $rezs['RIGHTARC'] = 0;

//teikums
if(!isset($_GET['sentence'])){
	echo "<form action='?' method='get'>
	Teikums: <input type='text' name='sentence'><br/>
	<input type='submit' value='Aiziet!'>
	</form>";
}else{
	echo "<form action='?' method='get'>
	Teikums: <input type='text' name='sentence'><br/>
	<input type='submit' value='Aiziet!'>
	</form>";
	$sentence = $_GET['sentence'];

	//lēmumu koks
	$file = fopen("c5treeToJSON.json", "r");
	$json = fgets($file);
	$tree = json_decode($json, true);

	$buffer = $arcz = $moves = $currentMoves = array();
	$stack = new SplDoublyLinkedList();

	$buffer = explode(" ",$sentence);

	array_walk($buffer, function(&$item) { static $i=1;	$item = $i++."_".$item; });
		
	nivre(serialize($stack), $buffer, $arcz, serialize($moves));
	
	//rezultāti
	$finalMoves = unserialize($currentMoves);
	$finalArcs = $finalMoves[count($finalMoves)-1][3];
	if($finalMoves[count($finalMoves)-1][1]->count()==2)$finalMoves[count($finalMoves)-1][1]->pop();
	//teikuma galvenais elements
	if(!$finalMoves[count($finalMoves)-1][1]->isEmpty()) $lastWord = $finalMoves[count($finalMoves)-1][1]->top();
	if(count($finalMoves[count($finalMoves)-1][2])>0) $lastWord = $finalMoves[count($finalMoves)-1][2][0];
	//rezultējošās bultas
	foreach($finalArcs as $finalArc){
		$wordNumbers = explode("→",$finalArc);
		$numberOne = explode("_",$wordNumbers[0])[0];
		$numberTwo = explode("_",$wordNumbers[1])[0];
		$finalRez[] = $numberTwo."-".$numberOne;
	}
	$finalRez[] = explode("_",$lastWord)[0]."-0";
	sort($finalRez);
	var_dump($finalRez);
}

//oriģinālaisARC-eager shift-reduce variants, kas izdara visas darbības...
//NivresARC-eager shift-reduce funkcija
function nivre($stack, $buffer, $arcs, $moves){
	$stackCopy = unserialize($stack);
	if((count($buffer)==0)&&($stackCopy->isEmpty())){
		return;
	}
	
	//kādu gājienu veikt?
	$arcsString = "";
	//saliek masīvus simbolu virknēs
	foreach($arcs as $val) { $arcsString .= ltrim(explode("→", $val)[1], '0123456789_').";"; }
	$stackTopHasArc = ($stackCopy->isEmpty() ? false : (strrpos($arcsString, $stackCopy->top())!== false));
	$bufferNextHasArc = (count($buffer)!=0 ? (strrpos($arcsString, $buffer[0])!== false) : false);
	
	$sample = array(
			'stack'=>($stackCopy->isEmpty() ? "NULL" : ltrim($stackCopy->top(), '0123456789_')),
			'buffer'=>(count($buffer)==0 ? "NULL" : ltrim($buffer[0], '0123456789_')),
			'stackTopHasArc'=>($stackTopHasArc ? 'true' : 'false'),
			'bufferNextHasArc'=>($bufferNextHasArc ? 'true' : 'false')
		);
		
	$toDoMove = getMove($sample);
	$stackCopy = unserialize($stack);
	
	var_dump($toDoMove);
	
	if($toDoMove=="SHIFT"&&count($buffer)!=0){ // vai drīkst SHIFT?
	
		$bufferCopy = $buffer;
		$movesCopy = unserialize($moves);
		
		$movesCopy[] = array("SHIFT", $stackCopy, $bufferCopy, $arcs);
		$movesCopy = serialize($movesCopy);
		$stackCopy->push(array_shift($bufferCopy));
		if($stackCopy->count()==1&&count($bufferCopy)==0){
			global $currentMoves;
			$currentMoves = $movesCopy;
			return;
		}
		nivre(serialize($stackCopy), $bufferCopy, $arcs, $movesCopy);
	}
	
	$stackCopy = unserialize($stack);
	
	foreach($arcs as $val) { $arcsString .= ltrim(explode("→", $val)[1], '0123456789_').";"; }
	$stackTopHasArc = ($stackCopy->isEmpty() ? false : (strrpos($arcsString, $stackCopy->top())!== false));
	$bufferNextHasArc = (count($buffer)!=0 ? (strrpos($arcsString, $buffer[0])!== false) : false);
	
	if($toDoMove=="REDUCE"&&(!$stackCopy->isEmpty())&&($stackTopHasArc)){ // vai drīkst REDUCE?
		$movesCopy = unserialize($moves);
		
		$movesCopy[] = array("REDUCE", $stackCopy, $buffer, $arcs);
		$movesCopy = serialize($movesCopy);
		$stackCopy->pop();
		if($stackCopy->count()==1&&count($buffer)==0){
			global $currentMoves;
			$currentMoves = $movesCopy;
			return;
		}
		nivre(serialize($stackCopy), $buffer, $arcs, $movesCopy);
	}
	
	$stackCopy = unserialize($stack);
	
	foreach($arcs as $val) { $arcsString .= ltrim(explode("→", $val)[1], '0123456789_').";"; }
	$stackTopHasArc = ($stackCopy->isEmpty() ? false : (strrpos($arcsString, $stackCopy->top())!== false));
	$bufferNextHasArc = (count($buffer)!=0 ? (strrpos($arcsString, $buffer[0])!== false) : false);
	
	if($toDoMove=="LEFTARC"&&count($buffer)!=0&&!$stackCopy->isEmpty()&&!$stackTopHasArc){ // vai drīkst LEFTARC?
		$arcsCopy = $arcs;
		$movesCopy = unserialize($moves);
		
		$movesCopy[] = array("LEFTARC", $stackCopy, $buffer, $arcs);
		$movesCopy = serialize($movesCopy);
		$arcsCopy[] = $buffer[0]."→".$stackCopy->pop();
		if(count($buffer)==0&&$stackCopy->count()==1) {
			global $currentMoves;;
			$currentMoves = $movesCopy;
			return;
		}
		nivre(serialize($stackCopy), $buffer, $arcsCopy, $movesCopy);
	}

	$stackCopy = unserialize($stack);
	
	foreach($arcs as $val) { $arcsString .= ltrim(explode("→", $val)[1], '0123456789_').";"; }
	$stackTopHasArc = ($stackCopy->isEmpty() ? false : (strrpos($arcsString, $stackCopy->top())!== false));
	$bufferNextHasArc = (count($buffer)!=0 ? (strrpos($arcsString, $buffer[0])!== false) : false);
	
	if($toDoMove=="RIGHTARC"&&count($buffer)!=0&&!$stackCopy->isEmpty()&&!$bufferNextHasArc){// vai drīkst RIGHTARC?
		$bufferCopy = $buffer;
		$arcsCopy = $arcs;
		$movesCopy = unserialize($moves);
		
		$movesCopy[] = array("RIGHTARC", $stackCopy, $bufferCopy, $arcs);
		$movesCopy = serialize($movesCopy);
		$arcsCopy[] = $stackCopy->top()."→".$bufferCopy[0];
		$stackCopy->push(array_shift($bufferCopy));
		if(count($bufferCopy)==0&&$stackCopy->count()==1) {
			global $currentMoves;
			$currentMoves = $movesCopy;
			return;
		}
		nivre(serialize($stackCopy), $bufferCopy, $arcsCopy, $movesCopy);
	}
	global $currentMoves;
	if(is_array($currentMoves)){$currentMoves=$moves;}
}

//funkcija darbības uzminēšanai
function getMove($sample){
	global $tree;
	$root = $tree;
	while($root['type']!="result"){
		$attr = $root['name'];
		$sampleVal = $sample[$attr];
		foreach($root['vals'] as $val){
			$vals = explode(",",$val['name']);
			if (in_array($sampleVal,$vals)){
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
			if(//Vai drīkst leftARC
				$sample['buffer']=="" || 
				$sample['stack']=="" || 
				$sample['stackTopHasArc']=='true'
			) $disallowed[]="LEFTARC";
			if(//Vai drīkst rightARC
				$sample['buffer']=="" || 
				$sample['stack']=="" || 
				$sample['bufferNextHasArc']=='true'
			) $disallowed[]="RIGHTARC";
			//noņem no iespējamajiem minējumiem neatļautos
			foreach($disallowed as $nomove) unset($rezs[$nomove]);
			$root['val'] = array_search(max($rezs), $rezs);
			$rezs['SHIFT'] = $rezs['REDUCE'] = $rezs['LEFTARC'] = $rezs['RIGHTARC'] = 0;
		}
		unset($childNode);
	}
	return $root['val'];
}


//rekursīvi apstaigā masīvu
function traverseArray($array)
{ 
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