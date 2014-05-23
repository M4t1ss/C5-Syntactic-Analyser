<?php header('Content-type: text/html; charset=utf-8');
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

	//viltīga gājienu padošana no SVM algoritma ar WEKA rīku
	$moveNumber = 0;
	$toDoMoves = array(
	0 => "SHIFT",
	1 => "LEFTARC",
	2 => "SHIFT",
	3 => "RIGHTARC",
	4 => "SHIFT",
	5 => "RIGHTARC",
	6 => "LEFTARC",
	7 => "LEFTARC",
	8 => "SHIFT",
	9 => "RIGHTARC",
	10 => "SHIFT",
	11 => "LEFTARC",
	12 => "SHIFT",
	13 => "RIGHTARC",
	14 => "LEFTARC",
	15 => "RIGHTARC",
	16 => "RIGHTARC",
	17 => "LEFTARC",
	18 => "LEFTARC",
	19 => "LEFTARC",
	20 => "LEFTARC",
	21 => "LEFTARC",
	22 => "REDUCE"
	);

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
		
	global $moveNumber, $toDoMoves;
	if(count($toDoMoves)!=$moveNumber){
		$toDoMove = $toDoMoves[$moveNumber++];
	}else{
		var_dump($sample);
		die();
	}
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
