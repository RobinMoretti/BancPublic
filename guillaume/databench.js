
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
	xmlHttp.open("POST",'databench.php?feed=true', true)
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

function dumpData() {
	var xmlHttp = null

	xmlHttp = new XMLHttpRequest()
	xmlHttp.open("GET",'databench.php?dump=true', true)
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