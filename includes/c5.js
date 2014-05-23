//C5 algoritms

var c5 = function(_s,target,features){
    var targets = _.unique(_s.pluck(target));
    if (targets.length == 1){
	return {type:"result", val: targets[0], name: targets[0],alias:targets[0]+randomTag() }; 
    }
    if(features.length == 0){
	var topTarget = mostCommon(targets);
	return {type:"result", val: topTarget, name: topTarget, alias: topTarget+randomTag()};
    }
    var bestFeature = maxGain(_s,target,features);
    var remainingFeatures = _.without(features,bestFeature);
    var possibleValues = _.unique(_s.pluck(bestFeature));
    var node = {name: bestFeature,alias: bestFeature+randomTag()};
    node.type = "feature";
    node.vals = _.map(possibleValues,function(v){
	var _newS = _(_s.filter(function(x) {return x[bestFeature] == v}));
	var child_node = {name:v,alias:v+randomTag(),type: "feature_value"};
	child_node.child =  c5(_newS,target,remainingFeatures);
	return child_node;
	
    });
    return node;
}

var predict = function(c5Model,sample) {
    var root = c5Model;
    while(root.type != "result"){
		var attr = root.name;
		var sampleVal = sample[attr];
		var childNode = _.detect(root.vals,function(x){return x.name == sampleVal});
		// console.log(sampleVal);
		// console.log(childNode);
		// console.log(childNode.child.type);
		root = childNode.child;
    }
	console.log(JSON.stringify(c5Model));
	// console.log(JSON.stringify(sample));
	// console.log(root.val);
    return root.val;
}



//visi aprēķini

var entropy = function(vals){
    var uniqueVals = _.unique(vals);
    var probs = uniqueVals.map(function(x){return prob(x,vals)});
    var logVals = probs.map(function(p){return -p*log2(p) });
    return logVals.reduce(function(a,b){return a+b},0);
}

var gain = function(_s,target,feature){
    var attrVals = _.unique(_s.pluck(feature));
    var setEntropy = entropy(_s.pluck(target));
    var setSize = _s.size();
    var entropies = attrVals.map(function(n){
	var subset = _s.filter(function(x){return x[feature] === n});
	return (subset.length/setSize)*entropy(_.pluck(subset,target));
    });
    var sumOfEntropies =  entropies.reduce(function(a,b){return a+b},0);
    return setEntropy - sumOfEntropies;
}

var maxGain = function(_s,target,features){
	//console.log(target+" - "+(_.max(features,function(e){return gain(_s,target,e)})));
    return _.max(features,function(e){return gain(_s,target,e)});
}

var prob = function(val,vals){
    var instances = _.filter(vals,function(x) {return x === val}).length;
    var total = vals.length;
    return instances/total;
}

var log2 = function(n){
    return Math.log(n)/Math.log(2);
}


var mostCommon = function(l){
   return  _.sortBy(l,function(a){
	return count(a,l);
    }).reverse()[0];
}

var count = function(a,l){
    return _.filter(l,function(b) { return b === a}).length
}

var randomTag = function(){
    return "_r"+Math.round(Math.random()*1000000).toString();
}

//Uzzīmējam...

var drawGraph = function(c5Model,divId){
    var g = new Array();
    g = addEdges(c5Model,g).reverse();
    window.g = g;
    var data = google.visualization.arrayToDataTable(g.concat(g));
    var chart = new google.visualization.OrgChart(document.getElementById(divId));
    google.visualization.events.addListener(chart, 'ready',function(){
    _.each($('.google-visualization-orgchart-node'),function(x){
	var oldVal = $(x).html();
	if(oldVal){
	    var cleanVal = oldVal.replace(/_r[0-9]+/,'');
	    $(x).html(cleanVal);
	}
}); 
    });
    chart.draw(data, {allowHtml: true});

}

var addEdges = function(node,g){
    if(node.type == 'feature'){
	_.each(node.vals,function(m){
	    g.push([m.alias,node.alias,'']);
	    g = addEdges(m,g);
	});
	return g;
    }
    if(node.type == 'feature_value'){

	g.push([node.child.alias,node.alias,'']);
	if(node.child.type != 'result'){
	    g = addEdges(node.child,g);
	}
	return g;
    }
    return g;
}


var renderSamples = function(samples,$el,model,target,features){
    _.each(samples,function(s){
	var features_for_sample = _.map(features,function(x){return s[x]});
	$el.append("<tr><td style='min-width:60px;'>&nbsp;"+features_for_sample.join('</td><td style="min-width:60px;">&nbsp;')+"</td><td><b>"+predict(model,s)+"</b></td></tr>");
    })
}

var renderTrainingData = function(_training,$el,target,features){
    _training.each(function(s){
	$el.append("<tr><td style='min-width:60px;'>&nbsp;"+_.map(features,function(x){return s[x]}).join('</td><td style="min-width:60px;">&nbsp;')+"</td><td>"+s[target]+"</td></tr>");
    })
}

var calcError = function(samples,model,target){
    var total = 0;
    var correct = 0;
    _.each(samples,function(s){
	total++;
	var pred = predict(model,s);
	var actual = s[target];
	if(pred == actual){
	    correct++;
	}
    });
    return correct/total;
}
