<?php
header('Content-Type: text/html; charset=utf8');
set_time_limit(100);
// sākuma mainīgie

// Open the file 
$file = fopen("sample-data.conll", "r"); 
$correctResult = array(); 
$currentResult = array(); 
while (!feof($file)) { 
	$tempword = fgets($file);
	$tempword = explode("	",$tempword);
	if($tempword[6]!="0"){
		$buffer[] = $tempword[1];
		$correctResult[]=array($tempword[6],$tempword[0]);
	}
} 
fclose($file); 

$ixixix = 1;
foreach ($buffer as &$value) {// sanumurē visus vārdus
	$value=$ixixix."_".$value;
	$ixixix++;
}
$stack = new SplDoublyLinkedList();
$arcs = array();
$moves = array();
$finalResult = array();
$toC5 = array();
$allMoves = array();
$currentMoves = array();

nivre($stack, $buffer, $arcs, $moves);
// var_dump($allMoves);

function nivre($stack, $buffer, $arcs, $moves){
global $words, $rules;
	if(count($buffer)==0&&$stack->isEmpty()){
		printVars("END", $stack, $buffer, $arcs, $moves);
		return;
	}
	if(count($buffer)!=0){ // vai drīkst SHIFT?
		$stackCopy = copyStack($stack);
		$bufferCopy = $buffer;
		$movesCopy = $moves;
		
		$movesCopy[] = array("SHIFT", $stackCopy, $bufferCopy, $arcs);
		$stackCopy->push(array_shift($bufferCopy));
		printVars("SHIFT", $stackCopy, $bufferCopy, $arcs, $movesCopy);
		nivre($stackCopy, $bufferCopy, $arcs, $movesCopy);
	}
	$canBeReduced = 0;
	if(!$stack->isEmpty()){
		foreach($arcs as $arc){
			if(strcmp($arc[1], $stack->top())==0)$canBeReduced = 1;
		}
	}
	if($canBeReduced == 1){ // vai drīkst REDUCE?
		$stackCopy = copyStack($stack);
		$movesCopy = $moves;
		
		$movesCopy[] = array("REDUCE", $stackCopy, $buffer, $arcs);
		$stackCopy->pop();
		printVars("REDUCE",$stackCopy, $buffer, $arcs, $movesCopy);
		nivre($stackCopy, $buffer, $arcs, $movesCopy);
	}
	
	$canmakeLeftArc = 1;
	if(count($buffer)!=0&&!$stack->isEmpty()){
		foreach($arcs as $arc){
			if(strcmp($arc[1], $stack->top())==0) $canmakeLeftArc = 0;
		}
	}else{
		$canmakeLeftArc = 0;
	}
	if($canmakeLeftArc == 1){ // vai drīkst LEFTARC?
		$stackCopy = copyStack($stack);		
		$bufferCopy = $buffer;
		$arcsCopy = $arcs;
		$movesCopy = $moves;
		
		//if(filterArc(array($bufferCopy[0],$stackCopy->top(),"<-L-"), $words, $rules)){
			$movesCopy[] = array("LEFT ARC", $stackCopy, $bufferCopy, $arcs);
			$arcsCopy[] = array($bufferCopy[0],$stackCopy->pop(),"<-L-");
			printVars("LEFT ARC", $stackCopy, $bufferCopy, $arcsCopy, $movesCopy);
			nivre($stackCopy, $bufferCopy, $arcsCopy, $movesCopy);
		//}
	}
		
	$canmakeRightArc = 1;
	if(count($buffer)!=0&&!$stack->isEmpty()){
		foreach($arcs as $arc){
			if(strcmp($arc[1], $buffer[0])==0) $canmakeRightArc = 0;
		}
	}else{
		$canmakeRightArc = 0;
	}
	if($canmakeRightArc == 1){ // vai drīkst RIGHTARC?
		$stackCopy = copyStack($stack);
		$bufferCopy = $buffer;
		$arcsCopy = $arcs;
		$movesCopy = $moves;
		
		//if(filterArc(array($stackCopy->top(),$bufferCopy[0],"<-R-"), $words, $rules)){
			$movesCopy[] = array("RIGHT ARC", $stackCopy, $bufferCopy, $arcs);
			$arcsCopy[] = array($stackCopy->top(),$bufferCopy[0],"<-R-");
			$stackCopy->push(array_shift($bufferCopy));
			printVars("RIGHT ARC", $stackCopy, $bufferCopy, $arcsCopy, $movesCopy);
			nivre($stackCopy, $bufferCopy, $arcsCopy, $movesCopy);
		//}
	}
}

//funkcija steka kopesanai
function copyStack($stack){
	$stackCopy = new SplDoublyLinkedList();
	$stack->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
	$stack->rewind();
	while( $stack->valid() )
	{
		$stackCopy->push($stack->current());
		$stack->next();
	}
	$stack->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO);
	
	return $stackCopy;
}

//funkcija mainīgo izdrukai
function printVars($action, $stack, $buffer, $arcs, $moves){
	if(count($buffer)==0&&$stack->count()==1){
		global $finalResult, $currentResult, $toC5, $currentMoves;
		if (!array_search($arcs, $finalResult)) {
			//jāpieliek teikuma galvenajam loceklim galā pieturzīme 
			$arcs[] = array("5_.",$stack->top(),"<-L-");//piecinieks būtu jāaizstāj un <-L- laikam arī.... nez kā tieši to visu :P
			$finalResult[] = $arcs;
			$rezRow = "";
			$tempRez = array();
			foreach ($arcs as $value) {
				$rezRow .= "(".$value[0].$value[2].$value[1]."), ";
				$tempRez[] = array(explode("_",$value[0])[0], explode("_",$value[1])[0]);
				//echo "(".$value[0].$value[2].$value[1]."), ";
			}
			$toC5[] = $rezRow;
			$currentResult[] = $tempRez;
			$currentMoves[] = $moves;
		}
	}
	//visas darbības
	global $allMoves;
	if (!in_array(array($action, $stack, $buffer, $arcs), $allMoves))$allMoves[] = array($action, $stack, $buffer, $arcs);
}

//funkcija šķautnes filtresanai
function filterArc($arc, $words, $rules){
	$expStr=explode("_",$arc[1]);
	$Wi=$expStr[1];
	$expStr=explode("_",$arc[0]);
	$Wj=$expStr[1];
	// atrodam vārdšķiru
	foreach($words as $wordArray){
		$wordInfo = array_search($Wi, $wordArray);
		$wordInfoBuff = array_search($Wj, $wordArray);
		if($wordInfo===0){$partOfSpeech = $words[array_search($wordArray, $words)][1];}
		if($wordInfoBuff===0){$partOfSpeechBuff = $words[array_search($wordArray, $words)][1];}
	}
	// atrodam likumu
	$foundRuleCount = 0;
	$foundRules = array();
	foreach($rules as $ruleArray){
		$ruleInfo1 = array_search($partOfSpeech, $ruleArray);
		$ruleInfo2 = array_search($partOfSpeechBuff, $ruleArray);
		if($ruleInfo2===0&&$ruleInfo1===1){
			$wordFunction = $rules[array_search($ruleArray, $rules)][2];
			$arcDirection = $rules[array_search($ruleArray, $rules)][3];
			$foundRules[$foundRuleCount][0]=$wordFunction;
			$foundRules[$foundRuleCount][1]=$arcDirection;
			$foundRuleCount++;
		}
	}
	if($foundRuleCount == 0){
		return 0;
	}else{
		return 1;
	}
}