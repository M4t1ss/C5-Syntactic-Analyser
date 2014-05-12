<?php header('Content-type: text/html; charset=utf-8');
set_time_limit(2);
ini_set('memory_limit','2048M');
			$rezs['SHIFT'] = $rezs['REDUCE'] = $rezs['LEFT ARC'] = $rezs['RIGHT ARC'] = 0;

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
	$file = fopen("training.json", "r");
	$json = fgets($file);
	$tree = json_decode($json, true);
	// var_dump($tree);

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
	//var_dump($finalMoves);

}

//oriģinālais arc-eager shift-reduce variants, kas izdara visas darbības...
//Nivres arc-eager shift-reduce funkcija
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
	// var_dump($arcsString);
		
	// var_dump($sample);
		
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
	
	if($toDoMove=="LEFT ARC"&&count($buffer)!=0&&!$stackCopy->isEmpty()&&!$stackTopHasArc){ // vai drīkst LEFTARC?
		$arcsCopy = $arcs;
		$movesCopy = unserialize($moves);
		
		$movesCopy[] = array("LEFT ARC", $stackCopy, $buffer, $arcs);
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
	
	if($toDoMove=="RIGHT ARC"&&count($buffer)!=0&&!$stackCopy->isEmpty()&&!$bufferNextHasArc){// vai drīkst RIGHTARC?
		$bufferCopy = $buffer;
		$arcsCopy = $arcs;
		$movesCopy = unserialize($moves);
		
		$movesCopy[] = array("RIGHT ARC", $stackCopy, $bufferCopy, $arcs);
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