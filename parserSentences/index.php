<!DOCTYPE html>
<html>
<head>
	<title>Sentences parser </title>
</head>
<body>

</body>

<script>
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
	                alert(allText);
	            }
	        }
	    }
	    rawFile.send(null);
	}

	window.onload = function ()
	{
		readTextFile("file:///C:/your/path/to/file.txt");
	}
</script>
</html>