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
	<ul id="sentences">
	</ul>
	<h2>Conditions:</h2>
	<ul id="conditions">
	</ul>

	<p class="" id="dump"></p>
	<p class="hide" id="dictionnary"></p>
	<p class="hide" id="conditions"></p>
</body>

<script>
	var sentencesList;
	var dictionnary;
	var conditionList;

	window.onload = function ()
	{
		//load dictionary
		readTextFile("conditions.txt"); 

		sentencesList = document.getElementById("sentenceList");
		dictionnary = document.getElementById("dictionnary").innerHTML;
		conditionList = document.getElementById("conditions");
		

		//parse dictionnary
		parse(dictionnary);

		dumpData();
		//vérifie les donnée du banc
		setInterval(dumpData, 5000);

	}



	// :::::::::::::::::::::::::::::: FUNTIONS
	function checkCondition(){

	}


	function parse(text){
		var condition = false,
		getVarName = false,
		getCondition = false,
		getConditionObject = false;

		var variableName, conditionMarquer, conditionObject;

		variableName = conditionMarquer = conditionObject = "";
		for (var i = 0; i < text.length; i++) 
		{			
			console.log(text[i])
			if(text[i]== "[")
			{
				condition = true;
				getVarName = true;
				i++;
			}
			
			if(condition)
			{
				if(getVarName && text[i] != " " && text[i] != "]")
				{	
					variableName += text[i];

					if(text[i] == " ")
					{
						getVarName = false;
						getCondition = true;
					}
				}
				else if(getCondition && text[i] != " " && text[i] != "]"){
					conditionMarquer += text[i];
					if(text[i] == " ")
					{
						getCondition = false;
						getConditionObject = true;
					}
				}
				else if(getConditionObject && text[i] != " " && text[i] != "]"){
						conditionObject += text[i];
				}

				if(text[i] == "]"){

					newConditions(variableName + " " + conditionMarquer + " " + conditionObject );

					variableName = conditionMarquer = conditionObject = "";
					condition = false;

				}
			}
		}
	}

	function newSentence(text)
	{
		var li = document.createElement("li");
		var t = document.createTextNode(text);
		li.appendChild(t);
		sentencesList.appendChild(li);
	}
	function newConditions(text)
	{
		var li = document.createElement("li");
		var t = document.createTextNode(text);
		li.appendChild(t);
		conditionList.appendChild(li);
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

				console.log(json)
				
				if (json.error == "ok") {
					console.log("ok")
					document.getElementById("dump").innerHTML = xmlHttp.responseText
				
				} else {
					console.log("bad")
					document.getElementById("dump").innerHTML = "Error"
				
				}
			}
		}
}
</script>
</html>