<?php

include('../global/databases.php');

if (!$conn) {
  die('Koneksi database gagal: ' . mysqli_connect_error());
}

$stmt = $conn->prepare("SELECT * FROM faces");
$stmt->execute();

$result = $stmt->get_result();

$data = array();

while ($row = $result->fetch_assoc()) {
  $temp = array(
    'name' => $row['name'],
    'descriptions' => json_decode($row['compute'], true),
    'sum' => $row['sum']
  );
  $data[] = $temp;
}

$conn->close();

echo json_encode(['data' => $data]);
