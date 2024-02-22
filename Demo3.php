<?php
include "conn.php";

$sql = "SELECT * FROM patient";

$result=mysqli_query($conn,$sql) or die (mysqli_error($conn));

$rows = array();
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

$json_data = json_encode($rows);


?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Matching</title>
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
    <div class="row">
       
        <h3><b style="color:white">Demonstration of Fingerprint Matching</b></h3>
        <div class="col-md-10">
            
            <table width="1012" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td class="auto-style2" align="right" valign="top">
               	 
		             Match Score<input type='text' id=quality size=10 value="100"> <br><br>
                </td>
                <td class="style3" align="left">
                    <span class="download_href"> 
                    <center>
		                <img border="2" id="FPImage1" alt="Fingerpint Image" height=300 width=210 src=".\Images\PlaceFinger.bmp" > 
		                <img border="2" id="FPImage2" alt="Fingerpint Image" height=300 width=210 src=".\Images\PlaceFinger2.bmp" > <br>
		                <input type="button" value="Click to Scan" onclick="CallSGIFPGetData(SuccessFunc1, ErrorFunc)"> 
		                <input type="button" value="Click to Scan" onclick="CallSGIFPGetData(SuccessFunc2, ErrorFunc)"> <br><br>
		                <input type="button" value="Click to Match" onclick="matchScore(succMatch, failureFunc)"> <br><br>
		                <div style=" color:black; padding:20px;">
		                    <p id="nni"> </p>
		                    <p id="nomcomplet"> </p>
		                </div>
		            </center>
                    </span>
                </td>
                <td>&nbsp;</td>
            </tr>
            </table>
        </div>
    </div>
</body>
<script type="text/javascript">
    var template_1 = "";
    var template_2 = "";

    var patientData = <?php echo $json_data; ?>;


    function SuccessFunc1(result) {
        if (result.ErrorCode == 0) {
            /* 	Display BMP data in image tag
                BMP data is in base 64 format 
            */
            if (result != null && result.BMPBase64.length > 0) {
                document.getElementById('FPImage1').src = "data:image/bmp;base64," + result.BMPBase64;
            }
            template_1 = result.TemplateBase64;
            console.log(template_1);
        }
        else {
            alert("Fingerprint Capture Error Code:  " + result.ErrorCode + ".\nDescription:  " + ErrorCodeToString(result.ErrorCode) + ".");
        }
    }

    function SuccessFunc2(result) {
        if (result.ErrorCode == 0) {
            /* 	Display BMP data in image tag
                BMP data is in base 64 format 
            */
            if (result != null && result.BMPBase64.length > 0) {
                document.getElementById('FPImage2').src = "data:image/bmp;base64," + result.BMPBase64;
            }
            template_2 = result.TemplateBase64;
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
        alert("Check if SGIBIOSRV is running; status = " + status + ":");
    }

    function CallSGIFPGetData(successCall, failCall) {
        var uri = "https://localhost:8443/SGIFPCapture";
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
        xmlhttp.onerror = function () {
            failCall(xmlhttp.status);
        }
        var params = "Timeout=" + "10000";
        params += "&Quality=" + "50";
        params += "&licstr=" + encodeURIComponent(secugen_lic);
        params += "&templateFormat=" + "ISO";
        xmlhttp.open("POST", uri, true);
        xmlhttp.send(params);

      
    }

    function matchScore(succFunction, failFunction) {
        if ( template_2 == "") {
            alert("Please scan two fingers to verify!!");
            return;
        }
        var uri = "https://localhost:8443/SGIMatchScore";

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                fpobject = JSON.parse(xmlhttp.responseText);
                succFunction(fpobject);
            }
            else if (xmlhttp.status == 404) {
                failFunction(xmlhttp.status)
            }
        }

        xmlhttp.onerror = function () {
            failFunction(xmlhttp.status);
        }

        // patientData.forEach(element => {
        //     var params = "template1=" + encodeURIComponent(element.empreinte);
        //     params += "&template2=" + encodeURIComponent(template_2);
        //     params += "&licstr=" + encodeURIComponent(secugen_lic);
        //     params += "&templateFormat=" + "ISO";
        //     xmlhttp.open("POST", uri, false);
        //     xmlhttp.send(params);
        // });
        
        patientData.forEach(element => {
            var params = "template1=" + encodeURIComponent(element.empreinte);
            params += "&template2=" + encodeURIComponent(template_2);
            params += "&licstr=" + encodeURIComponent(secugen_lic);
            params += "&templateFormat=" + "ISO";
            xmlhttp.open("POST", uri, false);
            xmlhttp.send(params);
            if (succFunction) {
                $("#nni").text(element.nni);
                $("#nomcomplet").text(element.nom_complet);
                return true; // this will break the loop

            }
            else{
                alert("NOT MATCHED !");
                return false
            }
        });
        
        

        
    }

    function succMatch(result) {
        var idQuality = document.getElementById("quality").value;
        if (result.ErrorCode == 0) {
            if (result.MatchingScore >= idQuality){
                alert("MATCHED ! (" + result.MatchingScore + ")");
                return true;
            }
            
                
        }
        else {
            alert("Error Scanning Fingerprint ErrorCode = " + result.ErrorCode);
            return false;
        }
    }

    function failureFunc(error) {
        alert ("On Match Process, failure has been called");
    }

</script>


<script type="text/javascript">
    // nice global area, so that only 1 location, contains this information
    // var secugen_lic = "hE/78I5oOUJnm5fa5zDDRrEJb5tdqU71AVe+/Jc2RK0=";   // webapi.secugen.com
    var secugen_lic = "";

    function ErrorCodeToString(ErrorCode) {
        var Description;
        switch (ErrorCode) {
            // 0 - 999 - Comes from SgFplib.h
            // 1,000 - 9,999 - SGIBioSrv errors 
            // 10,000 - 99,999 license errors
            case 51:
                Description = "System file load failure";
                break;
            case 52:
                Description = "Sensor chip initialization failed";
                break;
            case 53:
                Description = "Device not found";
                break;
            case 54:
                Description = "Fingerprint image capture timeout";
                break;
            case 55:
                Description = "No device available";
                break;
            case 56:
                Description = "Driver load failed";
                break;
            case 57:
                Description = "Wrong Image";
                break;
            case 58:
                Description = "Lack of bandwidth";
                break;
            case 59:
                Description = "Device Busy";
                break;
            case 60:
                Description = "Cannot get serial number of the device";
                break;
            case 61:
                Description = "Unsupported device";
                break;
            case 63:
                Description = "SgiBioSrv didn't start; Try image capture again";
                break;
            default:
                Description = "Unknown error code or Update code to reflect latest result";
                break;
        }
        return Description;
    }

</script>
</body>
</html>


