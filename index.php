<?php

if (isset($_POST['upload'])) {

    /* getting file name */
    $filename = $_FILES['image']['name'];

    $ext = @end(explode('.', $filename)); // explode the image name to get the extension
    $extension = strtolower($ext);

    $new_filename = time() . '.' . $extension;

    /* location */
    $location = "images/" . $new_filename;

    /* file extension */
    $file_extension = pathinfo($location, PATHINFO_EXTENSION);
    $file_extension = strtolower($file_extension);

    /* valid extension */
    $valid_ext = array('png', 'jpeg', 'jpg');

    /* check extension */
    if (in_array($file_extension, $valid_ext)) {

        /* check type */
        if ($_REQUEST['type'] == 'multipart') {

            $source = $_FILES['image']['tmp_name'];

            /* compress multipart image */
            compressedImage($source, $location, 60);
            unlink($_FILES['image']['tmp_name']);

        } else if ($_REQUEST['type'] == 'base64') {

            $imgContents = file_get_contents($_FILES['image']['tmp_name']);
            $source = base64_encode($imgContents);

            /* compress base64 image */
            compressedBase64Image($source, $location, 60);
            unlink($_FILES['image']['tmp_name']);

        } else {
            echo "select type.";
        }
    } else {
        echo "File format is not correct.";
    }
}

/* compress image */
function compressedImage($source, $path, $quality)
{

    $info = getimagesize($source);

    if ($info['mime'] == 'image/jpeg')
        $image = imagecreatefromjpeg($source);

    elseif ($info['mime'] == 'image/gif')
        $image = imagecreatefromgif($source);

    elseif ($info['mime'] == 'image/png')
        $image = imagecreatefrompng($source);

    imagejpeg($image, $path, $quality);
    echo "multipart file uploaded.";
}

/* compress base64 image */ 
function compressedBase64Image($source, $path, $quality)
{

    /* content type */
    // header('Content-Type: image/jpeg');

    // list($type,$source) = explode(';', $source);
    // list($type,$source) = explode(',', $source);
    $data = base64_decode($source);
    $image = imagecreatefromstring($data);
    $width = imagesx($image);
    $height = imagesy($image);
    $percent = 0.5;
    $newwidth = $width * $percent;
    $newheight = $height * $percent;

    $compressed_image = imagecreatetruecolor($newwidth, $newheight);

    /* resize */
    imagecopyresized($compressed_image, $image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    /* output */
    // imagejpeg($compressed_image);
    imagejpeg($compressed_image, $path, $quality);
    echo "base64 file uploaded.";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form method='post' action='' enctype='multipart/form-data'>
        <hr>
        <div>
            <label>
                <input type="radio" name="type" value="multipart" checked> multipart
            </label>
            <label>
                <input type="radio" name="type" value="base64"> base64
            </label>
        </div>
        <hr>
        <div>
            <input type='file' name='image'>
        </div>
        <hr>
        <div>
            <input type='submit' value='Upload Image' name='upload'>
        </div>
    </form>
</body>

</html>