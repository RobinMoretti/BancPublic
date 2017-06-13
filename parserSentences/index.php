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
	<div id="sendForm">
	  	<label for="humidity" >humidity</label>
	  	<input type="number" id="humidity" value=0>
	  	
	  	<label for="soundVolume" >soundVolume</label>
	  	<input type="number" id="soundVolume" value=0>
	  	
	  	<label for="peopleCount" >peopleCount</label>
	  	<input type="number" id="peopleCount" value=0>
	  	
	  	<label for="windSpeed" >windSpeed</label>
	  	<input type="number" id="windSpeed" value=0>
	  	
	  	<label for="temperature" >temperature</label>
	  	<input type="number" id="temperature" value=0>
	  	
	  	<label for="raining" >raining</label>
	  	<input type="number" id="raining" value=0>
	  	
	  	<label for="place" >place</label>
	  	<input type="number" id="place" value=0>
	  	
	  	<button name="send" type="button" onmousedown="sendData()">
	  		Send
	  	</button>
  	</div>


	<h2>Sentences:</h2>
	<ul id="sentenceList">
	</ul>
	<h2 class="hide">Conditions:</h2>
	<ul id="conditions" class="hide">
	</ul>

  	<button name="send" type="button" onmousedown="generateLastFiveSentence()">
  		Load Last Five sentence;
  	</button>

	<h2 class="hide">Sentences content dictionnary:</h2>
	<ul id="sentencesContent" class="hide">
	</ul>

	<!-- cache variable -->
	<p class="hide" id="dump"></p> 
	<p class="hide" id="dictionnary"></p>
</body>

<script>
	var sentencesList;
	var dictionnary;
	var conditionList;
	var sentencesContent;
	var banc;
	var bancData;	
	var indexHistorique = 0;
	var test = "ta raccce";

	window.onload = function ()
	{

		//load dictionary
		readTextFile("conditions.txt"); 

		sentencesList = document.getElementById("sentenceList");
		dictionnary = document.getElementById("dictionnary").textContent;
		conditionList = document.getElementById("conditions");
		sentencesContent = document.getElementById("sentencesContent");

		//parse dictionnary
		parse(dictionnary);


        generateSentence();
		//vérifie les donnée du banc
		setInterval(generateSentence, 2000);
	}



	// :::::::::::::::::::::::::::::: FUNTIONS
	function generateLastFiveSentence()
	{
		for (var i = 0; i < 5; i++) {
			generateSentenceByIndex(indexHistorique);
			indexHistorique ++;
		}
	}

	function generateSentence()
	{
		dumpData();	

		if(document.getElementById("dump").innerHTML != "" || document.getElementById("dump").innerHTML != null)
		{
			try {
				 bancData = JSON.parse(document.getElementById("dump").innerHTML);
				 executeSentence(bancData.data[bancData.data.length - 1], 0);
				 indexHistorique ++;
			} catch(e) {
				// statements
				console.log("Error: " + e);
			}
		}
	}

	function generateSentenceByIndex(index)
	{
		dumpData();	

		if(document.getElementById("dump").innerHTML != "" || document.getElementById("dump").innerHTML != null)
		{
			try {
				 bancData = JSON.parse(document.getElementById("dump").innerHTML);
				 if(index < bancData.data.length)
				 	executeSentence(bancData.data[bancData.data.length - index], 1);
			} catch(e) {
				// statements
				console.log("Error: " + e);
			}
		}
	}


	function executeSentence(data, index)
	{	
		for (var i = 0; i < conditionList.children.length; i++) {
			var variableName = conditionList.children[i].textContent;
			
			variableName = convertStringVarToData(variableName, data);

			if(variableName != false)
			{	
				if(eval(variableName)){
					// console.log(sentencesContent.children[i].textContent)
					newSentence(crossSentenceForRandomWord(sentencesContent.children[i].textContent), index);	
				}
			}
		}
	}

	function convertStringVarToData(text, data)
	{

		for (var i = 0; i < text.length; i++) {
			if(text[i] == "#"){
				// si variable vérifie si elle existe
				var x = i;
				var variable = "";

				while (text[x] != " ") 
				{
					x ++;
					variable += text[x];
				}

				// insert 'data[' + var + ']'

				var newCondition = 'data["' + text.slice(i, x) + '"]';
				newCondition = newCondition.replace("#", "");
				variable = variable.replace("#", "");
				variable = variable.replace(" ", "");


				if(data[variable] != null){
					text = text.replace("#" + variable, newCondition);
				}
				else 
					return false;

			}
		}

		return text;
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

				if(listWord.length > 1)
					text = replaceText(text, indexToDelete, i, listWord[Math.floor((Math.random() * listWord.length))]);
				else if(listWord.length == 1)
					text = replaceText(text, indexToDelete, i, listWord[0]);

				listWord = [];
			}
			

			if(recordword)
			{
				if(letter == ""){
					listWord.push(word);
					word = "";
				}
				else
					word += letter;
				// renregistre chaque mot
			}
		}

		return text;
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

		//si contien # ==> display le content de la var
		if(/#/.test(textTarget)){
			textTarget = textTarget.replace("#", "");

			try {
				textTarget = bancData.data[0][textTarget];

			} catch(e) {
				// statements
				textTarget = e +" // " + textTarget + " // ";
			}
		}
		
		//correction d'un bug
		text = text.replace("undefined", "");

		text = text.replace(textToReplace, textTarget)
		return text;
	}

	function parse(text)
	{
		var condition = false,
		getVarName = false,
		getCondition = false,
		getConditionObject = false;

		var sentenceContent, getSentence = false;
		var variableName;

		var lastSentence = false; 

		variableName = "";

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
				}

				if(text[i] == "]"){
					newConditions(variableName);
					variableName = "";
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

	function newSentence(text, index)
	{
		var li = document.createElement("li");
		var t = document.createTextNode(text);
		li.appendChild(t);
		if(!index)
			sentencesList.appendChild(li);
		else
			sentencesList.insertBefore(li, sentencesList.children[sentencesList.children.length]);
	}

	function newConditions(varName)
	{
		var liElem = document.createElement("li");
		var ulElem = document.createElement("ul");

		if(varName != null)
		{
			var li = document.createElement("li");
			var t = document.createTextNode(varName);
			li.classList.add("varName");
			li.appendChild(t);
			conditionList.appendChild(li);

		}
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
	                document.getElementById("dictionnary").textContent = allText;
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

	function sendData() {
		// recuperation des donnés de la page html
		var humidity = document.getElementById('humidity').value;
		var soundVolume = document.getElementById('soundVolume').value;
		var peopleCount = document.getElementById('peopleCount').value;
		var windSpeed= document.getElementById('windSpeed').value;
		var temperature = document.getElementById('temperature').value;
		var raining = document.getElementById('raining').value;
		var place = document.getElementById('place').value;

	 
		var JSONMarker = {
			humidity:humidity,
			soundVolume:soundVolume,
			peopleCount:peopleCount,
			windSpeed:windSpeed,
			temperature:temperature,
			raining:raining,
			place:place
		}

		// console.log("Sending path: " + JSONMarker.paths)
		// console.log("Sending group: " + JSONMarker.group)

		var JSONString = JSON.stringify(JSONMarker)
		console.log("Sending : " + JSONString)

		var xmlHttp = null

		xmlHttp = new XMLHttpRequest()
		xmlHttp.open("POST",'../guillaume/databench.php?feed=true', true)
		xmlHttp.setRequestHeader("Content-type", "application/json") // json header
		xmlHttp.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT") // IE Cache Hack
		xmlHttp.setRequestHeader("Cache-Control", "no-cache") // idem
		xmlHttp.send(JSONString)

		xmlHttp.onreadystatechange=function() {
			if(xmlHttp.readyState == 4){
				var json = null

				try {
					json = JSON.parse(xmlHttp.responseText)
				} catch (err) {
					console.log("error json parse "+ err)
					console.log(xmlHttp.responseText)
					return
				}

				console.log(json)
				
				if (json.error == "ok") {
					console.log("ok")
					
				} else {
					console.log("bad")
					
				}
			}
		}

	}
</script>
</html>