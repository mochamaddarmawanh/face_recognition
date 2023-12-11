<?php

include('../global/databases.php');

if ($conn->connect_error) {
    echo "Connection to the database failed: " . $conn->connect_error . ".";
    exit();
}

$imageDataArray = json_decode($_POST['imageDataArray'], true);
$className = json_decode($_POST['className'], true);
$descriptors = json_decode($_POST['descriptors'], true);

$duplicateName = "";
$errorMessage = "";
$allDataDifferent = true;
$hasError = false;

foreach ($className as $class => $name) {
    $n = str_replace(' ', '_', $name);
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM faces WHERE name = ?");
    $stmt->bind_param("s", $n);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];

    if ($count > 0) {
        $allDataDifferent = false;
        $duplicateName = $name;
        break;
    }
}

if ($allDataDifferent) {
    $insertedNames = array();
    $insertError = false;

    foreach ($imageDataArray as $class => $images) {
        $name = str_replace(' ', '_', $className[$class]);
        $folderPath = "../../assets/image/faces/" . $name;

        $stmt = $conn->prepare("INSERT INTO faces (name, compute, sum) VALUES (?, ?, ?)");
        $compute = json_encode($descriptors[$class][0]['descriptors']);
        $sum = count($images);
        $stmt->bind_param("ssi", $name, $compute, $sum);
        $stmt->execute();
        if ($stmt->error) {
            $insertError = true;
            break;
        }

        mkdir($folderPath, 0755);

        foreach ($images as $index => $image) {
            $number = str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            $imageId = $image['id'];
            $imageData = $image['val'];
            $imagePath = $folderPath . "/" . $name . "_" . $number . ".jpg";
            file_put_contents($imagePath, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData)));
        }

        $insertedNames[] = $name;
    }

    if ($insertError) {
        foreach ($insertedNames as $insertedName) {
            $stmt = $conn->prepare("DELETE FROM faces WHERE name = ?");
            $stmt->bind_param("s", $insertedName);
            $stmt->execute();

            $folderPath = "../../assets/image/faces/" . $insertedName;
            deleteDirectory($folderPath);
        }

        echo "Error in inserting data.";
        exit();
    }
} else {
    echo "Data duplicate detected for class name: " . $duplicateName . ".";
    exit();
}

function deleteDirectory($path)
{
    if (!is_dir($path)) {
        return;
    }

    $dir = opendir($path);
    while (($file = readdir($dir)) !== false) {
        if ($file != '.' && $file != '..') {
            $filePath = $path . '/' . $file;
            if (is_dir($filePath)) {
                deleteDirectory($filePath);
            } else {
                unlink($filePath);
            }
        }
    }
    closedir($dir);
    rmdir($path);
}

$conn->close();
