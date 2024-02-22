<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Demo1</title>
	<link rel="icon" href="/echiva/EchivaPenitentier/public/themes/AMNIRECHIVA/img/bigicon.png">
	<link href="css/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css">
	<link href="css/bootstrap-theme.css" media="screen" rel="stylesheet" type="text/css">
<script src="bootstrap.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<style>
body {
    overflow-x: hidden;
    background-image: radial-gradient( circle at top left , #21529E, #14315B );
    background-size: cover;
    font-family: 'Lato', sans-serif;
    padding: 0px
 !important;
    margin: 0px
 !important;
    font-size: 14px !important;
}
</style>
<body>
    <center>

    <img id="FPImage1" alt="Fingerpint Image" height=250 width=170 align="center" src=".\Images\PlaceFinger.bmp"> <br>
    <button class="btn" onclick="captureFP()" class="text-white" style="color:white">
			<i class="fa fa-plus "></i> Cliquez pour Scanner
    </button>
        <form method="post" action="save.php" class="w-50 container">

        <input type="number" name="nni" class="form-control" placeholder="votre numero d'indentification" required/><br>
        <input type="text" name="nom_complet" class="form-control" placeholder="nom complet" required/><br>
        <input type="hidden" name="empreinte" value="" required id="Empreinte">
        <input type="hidden" name="base64" value="" required id="base64">

        <input type="submit" name="submit" class="btn btn-success" value="Enregistrer"/>

		
        </form>
    </center>
	
	<script type="text/javascript">
window.onresize = function() 
{
    window.resizeTo(190,355);
}
window.onclick = function() 
{
    window.resizeTo(190,355);
}
</script>
<script type="text/javascript">

    var template_1 = "";
    function captureFP() {
    CallSGIFPGetData(SuccessFunc, ErrorFunc);
}

/* 
    This functions is called if the service sucessfully returns some data in JSON object
 */
function SuccessFunc(result) {
	console.log(result);
    if (result.ErrorCode == 0) {
        /* 	Display BMP data in image tag
            BMP data is in base 64 format 
        */
        if (result != null && result.BMPBase64.length > 0) {
            document.getElementById("FPImage1").src = "data:image/bmp;base64," + result.BMPBase64;
        }
		$.ajax({
                type: 'POST',
                url: 'base64tobmp.php',
                dataType: 'text',
                data: {img: "data:image/bmp;base64,"+ result.BMPBase64},
                success: function (data) {
                    window.close();
                },
                error: function(data) {
					alert('Error '+data);
				}
            });
        template_1 = result.TemplateBase64;
        $("#Empreinte").val(template_1);
        $("#base64").val(result.BMPBase64);
        

    }
    else {
        alert("Fingerprint Capture Error Code:  " + result.ErrorCode + ".\nDescription:  " + ErrorCodeToString(result.ErrorCode) + ".");
    }
}

function ErrorFunc(status) {

    /* 	
        If you reach here, user is probabaly not running the 
        service. Redirect the user to a page where he can download the
        executable and install it. 
    */
    alert("Check if SGIBIOSRV is running; Status = " + status + ":");

}


function CallSGIFPGetData(successCall, failCall) {
    // 8.16.2017 - At this time, only SSL client will be supported.
    var uri = "https://localhost:8443/SGIFPCapture";
	  //var secugen_lic ="3tGZLzR+hRMuZByK9ZIcici89OQDxXrLzX4+jNn9Y18=";

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            fpobject = JSON.parse(xmlhttp.responseText);
            successCall(fpobject);
        }
        else if (xmlhttp.status == 404) {
            failCall(xmlhttp.status)
        }
    }
    var params = "Timeout=" + "10000";
    params += "&Quality=" + "50";
   // params += "&licstr=" + encodeURIComponent(secugen_lic);
    params += "&templateFormat=" + "ISO";
    console.log
    xmlhttp.open("POST", uri, true);
    xmlhttp.send(params);

    xmlhttp.onerror = function () {
        failCall(xmlhttp.statusText);
    }
}


</script>
</body>
</html>