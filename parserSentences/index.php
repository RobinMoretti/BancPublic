<!DOCTYPE html>
<html>
<head>
	<title>Sentences parser </title>
	<style>
		.hide{
			display: none;
		}
	</style>
</head>
<body>

	<h2>Sentences:</h2>
	<ul id="sentenceList">
	</ul>
	<h2 class="hide">Conditions:</h2>
	<ul id="conditions" class="hide">
	</ul>

	<h2 class="hide">Sentences content dictionnary:</h2>
	<ul id="sentencesContent" class="hide">
	</ul>


	<p class="hide" id="dump"></p>
	<p class="hide" id="dictionnary"></p>
	<p class="hide" id="conditions"></p>
</body>

<script>
	var sentencesList;
	var dictionnary;
	var conditionList;
	var sentencesContent;
	var banc;


	window.onload = function ()
	{
		//load dictionary
		readTextFile("conditions.txt"); 

		sentencesList = document.getElementById("sentenceList");
		dictionnary = document.getElementById("dictionnary").innerHTML;
		conditionList = document.getElementById("conditions");
		sentencesContent = document.getElementById("sentencesContent");
		// console.log( " data 0" + bancData[0] )


		//parse dictionnary
		parse(dictionnary);

        generateSentence();
		//vérifie les donnée du banc
		setInterval(generateSentence, 2000);

	}



	// :::::::::::::::::::::::::::::: FUNTIONS
	function generateSentence()
	{
		dumpData();	
		var bancData;	

		if(document.getElementById("dump").innerHTML != "" || document.getElementById("dump").innerHTML != null)
		{
			try {
				 bancData = JSON.parse(document.getElementById("dump").innerHTML);
				 executeSentence(bancData.data[0]);
			} catch(e) {
				// statements
				console.log("Error: " + e);
			}
		}
	}


	function executeSentence(data)
	{	
		for (var i = 0; i < conditionList.children.length; i++) {

			var variableName = conditionList.children[i].children[0].children[0].textContent;
			var condition = conditionList.children[i].children[0].children[1].textContent;
			var limit = conditionList.children[i].children[0].children[2].textContent;

			var huidtystring = "humidity";
				// console.log("huidtystring  = " + huidtystring + " / " + huidtystring.length)
				// console.log("variableName  = " + variableName + " / " + variableName.length)
			//.log(data[toString(variableName)] + " variableName == " + variableName + " humidity == " + data[huidtystring])
			if(variableName == "humidity"){
				console.log(data[variableName])
			}

			if(data[variableName] != null){
				var variable = data[variableName];
				// console.log(eval("data[variableName]"))
				if(eval("data[variableName] " + condition + " " + limit))
				{	
					// crossSentenceForRandomWord(sentencesContent.children[i].textContent);
					newSentence(crossSentenceForRandomWord(sentencesContent.children[i].textContent));
				}
			}
			// if(window[variableName])
		}
		// for (var property in data) {
		// 	console.log(data[property])
		// }
	}

	function crossSentenceForRandomWord(text)
	{
		var recordword = false, indexToDelete, listWord = [], word = "";


		for (var i = 0; i < text.length; i++) 
		{

			var letter = text[i];
			if (letter == "{")
			{
				recordword = true;
				if(indexToDelete == null)
					indexToDelete = i;
			}
			else if (letter == "}") 
			{	
				recordword = false;
				word += letter;
				listWord.push(word);

				// console.log("word = " + word)
				// console.log("listWord = " + listWord)

				if(listWord.length > 1)
					text = replaceText(text, indexToDelete, i, listWord[Math.floor((Math.random() * listWord.length))]);
			}
			

			if(recordword)
			{
				if(letter == ","){
					listWord.push(word);
					word = "";
				}
				else
					word += letter;
				// renregistre chaque mot
			}
		}

		return text;

		console.log(text)
	}

	function replaceText(text, beginIndex, endIndex, textTarget)
	{
		var textToReplace = text.substring(beginIndex, endIndex+1);
		var character1 = /{/,
		    character2 = /}/;  // no quotes here

		if(character1.test(textTarget))
			textTarget = textTarget.replace("{", "");
		if(character2.test(textTarget))
			textTarget = textTarget.replace("}", "");
		
		//correction d'un bug
		text = text.replace("undefined", "");

		text = text.replace(textToReplace, textTarget)
		return text;
	}

	function parse(text){
		var condition = false,
		getVarName = false,
		getCondition = false,
		getConditionObject = false;

		var sentenceContent, getSentence = false;
		var variableName, conditionMarquer, conditionObject;

		var lastSentence = false; 

		variableName = conditionMarquer = conditionObject = "";

		for (var i = 0; i < text.length; i++) 
		{			
			if(text[i]== "[")
			{
				condition = true;
				getVarName = true;
				i++;
				getSentence = false;
				if(sentenceContent != "" && sentenceContent != null){
					newSentenceContentDictionnary(sentenceContent);
					sentenceContent = "";
				}
			}
			
			if(condition)
			{
				if(getVarName && text[i] != "]")
				{	
					variableName += text[i];

					if(text[i] == " ")
					{
						getVarName = false;
						getCondition = true;
					}
				}
				else if(getCondition && text[i] != "]"){
					conditionMarquer += text[i];
					if(text[i] == " ")
					{
						getCondition = false;
						getConditionObject = true;
					}
				}
				else if(getConditionObject && text[i] != "]"){
						conditionObject += text[i];
				}

				if(text[i] == "]"){
					newConditions(variableName.replace(/\s+/g, ''), conditionMarquer.replace(/\s+/g, ''), conditionObject.replace(/\s+/g, ''));

					variableName = conditionMarquer = conditionObject = "";
					condition = false;
					getSentence = true;
				}
			}
			else if(getSentence)
				sentenceContent += text[i];

			if(i == text.length-1)
					newSentenceContentDictionnary(sentenceContent);

		}
	}

	function newSentence(text)
	{
		var li = document.createElement("li");
		var t = document.createTextNode(text);
		li.appendChild(t);
		sentencesList.appendChild(li);
	}

	function newConditions(varName, condition, limit)
	{
		var liElem = document.createElement("li");
		var ulElem = document.createElement("ul");

		if(varName != null)
		{
			var li = document.createElement("li");
			var t = document.createTextNode(varName);
			li.classList.add("varName");
			li.appendChild(t);
			ulElem.appendChild(li);
		}

		if(condition != null)
		{
			var li = document.createElement("li");
			var t = document.createTextNode(condition);
			li.classList.add("condition");
			li.appendChild(t);
			ulElem.appendChild(li);
		}

		if(limit != null)
		{
			var li = document.createElement("li");
			var t = document.createTextNode(limit);
			li.classList.add("limit");
			li.appendChild(t);
			ulElem.appendChild(li);
		}

		liElem.appendChild(ulElem);

		conditionList.appendChild(liElem);

	}

	function newSentenceContentDictionnary(text)
	{
		var li = document.createElement("li");
		var t = document.createTextNode(text);
		li.appendChild(t);
		sentencesContent.appendChild(li);
	}

	function readTextFile(file)
	{
	    var rawFile = new XMLHttpRequest();
	    rawFile.open("GET", file, false);
	    rawFile.onreadystatechange = function ()
	    {
	        if(rawFile.readyState === 4)
	        {
	            if(rawFile.status === 200 || rawFile.status == 0)
	            {
	                var allText = rawFile.responseText;
	                document.getElementById("dictionnary").innerHTML = allText;
	            }
	        }
	    }
	    rawFile.send(null);
	}


	function dumpData() 
	{
		var xmlHttp = null

		xmlHttp = new XMLHttpRequest()
		//../guillaume/databench.php?dump=true' --> base de donnée
		xmlHttp.open("GET",'../guillaume/databench.php?dump=true', true)
		xmlHttp.setRequestHeader("Content-type", "application/json") // json header
		xmlHttp.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT") // IE Cache Hack
		xmlHttp.setRequestHeader("Cache-Control", "no-cache") // idem
		xmlHttp.send()

		xmlHttp.onreadystatechange=function() {
			if(xmlHttp.readyState == 4){
				var json = null

				try {
					json = JSON.parse(xmlHttp.responseText)
				} catch (err) {
					console.log("error json parse "+ err)
					console.log(xmlHttp.responseText)
					document.getElementById("dump").innerHTML = "JSON parse Error"
					
					return
				}

				//console.log(json)
				
				if (json.error == "ok") {
					document.getElementById("dump").innerHTML = xmlHttp.responseText;
				
				} else {
					document.getElementById("dump").innerHTML = "Error"
				
				}
			}
		}
}
</script>
</html>