<?php
function checkUserInput($data)
{
    $clean = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    $clean = strip_tags($clean);

    return $clean;
}

function checkLength($string)
{
    if (strlen($string) > 500)
        return string;
    else
        echo "<script>alert('your message was too long!')</script>";
}


function CheckConditions($gebruikersnaam, $wachtwoord)
{
    $errortabel = array();

    if (empty($gebruikersnaam) || (strlen($gebruikersnaam) < 4 && strlen($gebruikersnaam) > 20)) {
        $errortabel[] .= "Username must be longer than 4 characters and shorter than 20 characters!!";
    }
    if (empty($wachtwoord) || (strlen($wachtwoord) < 4 && strlen($wachtwoord) > 20)) {
        $errortabel[] .= "password must be longer than 4 characters and shorter than 20 characters!!";
    }

    return $errortabel;
}

function CalculateNewFileName($filename)
{
    $newfilename = sha1_file($filename);

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimetype = $finfo->file($filename);
    $extension = array_search($mimetype, Config::getConfigInstantie()->getMimetypes());

    $newfilename = $newfilename . "." . $extension;

    return $newfilename;
}


function CheckUploadedFile($uploadfile)
{
    $errortabel = array();
    switch ($uploadfile["error"]) {
        case UPLOAD_ERR_OK:
            if ($uploadfile["size"] == 0) {
                $errortabel["size"] = "Uploaded file is empty";
            }

            $mimetypes = Config::getConfigInstantie()->getMimetypes();
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $uploadfileMimetype = $finfo->file($uploadfile["tmp_name"]);

            if (!in_array($uploadfileMimetype, $mimetypes)) {
                $errortabel["mime"] = "Mime type is not correct, only gif, jpeg en png are allowed";
            }
            break;
        case UPLOAD_ERR_INI_SIZE:
            $errortabel["error"] = "file too big!";
            break;
        case UPLOAD_ERR_FORM_SIZE:
            $errortabel["error"] = "file too big according to the form!";
            break;
        case UPLOAD_ERR_PARTIAL:
            $errortabel["error"] = "Uploaded file was only partially uploaded";
            break;
        case UPLOAD_ERR_NO_FILE:
            $errortabel["error"] = "No file was found?!";
            break;
        case UPLOAD_ERR_NO_TMP_DIR: //6
            $errortabel["error"] = "temporary directory not found!";
            break;
        case UPLOAD_ERR_CANT_WRITE: //7
            $errortabel["error"] = "Error writing to server";
            break;
        case UPLOAD_ERR_EXTENSION: //8
            $errortabel["error"] = "File upload cancelled by php extension";
            break;
        default:
            $errortabel["error"] = "unknown upload error";
            break;

            return $errortabel;
    }
}


function CheckReCaptcha($captchaResponse)
{
    $privateKey = "6LckHxETAAAAAAeCk-PxpCOPCEtEhDtLiVVT2JHD";
    $response = null;
    $reCaptcha = new ReCaptcha($privateKey);

    if ($captchaResponse) {
        $response = $reCaptcha->verifyResponse(
            $_SERVER["REMOTE_ADDR"],
            $_POST["g-recaptcha-response"]
        );
    }

    if ($response != null && $response->success)
        return true;
    else
        return false;

}

function showErrors($errortabel)
{
    $resultaatstring = "Errors: ";
    foreach ($errortabel as $error) {
        $resultaatstring .= $error . "; ";
    }
    $resultaatstring .= "<hr />";
    echo $resultaatstring;
}


?>