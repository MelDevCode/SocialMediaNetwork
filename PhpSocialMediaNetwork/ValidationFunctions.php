<?php
function ValidateId($userId) {
    if (empty($userId)) {
        return "Please provide your user ID.";
    }
    return "";
}

function ValidateName($name) {
    if (empty($name)) {
        return "Please provide your name.";
    }
    return "";
}

function ValidatePhone($phone) {
    $pattern = '/^[2-9]\d{2}-[2-9]\d{2}-\d{4}$/';
    if (empty($phone) || !preg_match($pattern, $phone)) {
        return "Please enter a valid phone number (e.g., 123-456-7890).";
    }
    return "";
}

function ValidatePassword($password) {
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{6,}$/';
    if (empty($password) || !preg_match($pattern, $password)) {
        return "Please enter a valid password.";
    }
    return "";
}

function ValidatePasswordReenter($password, $passwordReenter) {
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{6,}$/';
    
    // Check if re-entered password is empty or doesn't meet pattern requirements
    if (empty($passwordReenter) || !preg_match($pattern, $passwordReenter)) {
        return "Please enter a valid password.";
    }
    
    // Check if passwords match
    if ($password !== $passwordReenter) {
        return "Passwords do not match.";
    }
    
    return "";
}

function createImageResource($filePath, $imageType) {
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            return imagecreatefromjpeg($filePath);
        case IMAGETYPE_PNG:
            return imagecreatefrompng($filePath);
        case IMAGETYPE_GIF:
            return imagecreatefromgif($filePath);
        default:
            return false;
    }
}

function resampleImage($srcImage, $maxWidth, $maxHeight) {
    $srcWidth = imagesx($srcImage);
    $srcHeight = imagesy($srcImage);

    // Calculate new dimensions
    $aspectRatio = $srcWidth / $srcHeight;
    if ($maxWidth / $maxHeight > $aspectRatio) {
        $newWidth = $maxHeight * $aspectRatio;
        $newHeight = $maxHeight;
    } else {
        $newWidth = $maxWidth;
        $newHeight = $maxWidth / $aspectRatio;
    }

    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($newImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);

    return $newImage;
}
?>
