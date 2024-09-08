
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Validate the file type (allow only image types)
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedTypes)) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        exit;
    }

    // Move the uploaded file to the uploads directory
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        // Add white border
        $img = null;
        if ($imageFileType == 'jpg' || $imageFileType == 'jpeg') {
            $img = imagecreatefromjpeg($targetFile);
        } elseif ($imageFileType == 'png') {
            $img = imagecreatefrompng($targetFile);
        } elseif ($imageFileType == 'gif') {
            $img = imagecreatefromgif($targetFile);
        }

        if ($img !== null) {
            // Create a new image with added white borders
            $borderSize = $_POST['bordersize']; // Size of the white border
            $width = imagesx($img);
            $height = imagesy($img);
            $newWidth = $width + 2 * $borderSize;
            $newHeight = $height + 2 * $borderSize;

            $newImg = imagecreatetruecolor($newWidth, $newHeight);

            // Fill with white color
            $white = imagecolorallocate($newImg, 255, 255, 255);
            imagefill($newImg, 0, 0, $white);

            // Copy original image into the new one with borders
            imagecopy($newImg, $img, $borderSize, $borderSize, 0, 0, $width, $height);

            // Save the new image
            $newFileName = $targetDir . 'bordered_' . basename($targetFile);
            if ($imageFileType == 'jpg' || $imageFileType == 'jpeg') {
                imagejpeg($newImg, $newFileName);
            } elseif ($imageFileType == 'png') {
                imagepng($newImg, $newFileName);
            } elseif ($imageFileType == 'gif') {
                imagegif($newImg, $newFileName);
            }

            // Free memory
            imagedestroy($img);
            imagedestroy($newImg);

            // Provide download link
            echo "<h2>Image with White Border:</h2>";
            //echo "<img src='$newFileName' alt='Image with border'><br>";
            echo "<a href='$newFileName' download>Download Image</a>";
        } else {
            echo "Failed to process the image.";
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
} else {
    echo "No file uploaded.";
}
?>