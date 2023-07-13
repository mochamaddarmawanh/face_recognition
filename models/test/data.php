<?php

include('../global/databases.php');

if (!$conn) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}

$stmt = $conn->prepare("SELECT * FROM data LIMIT 1");
$stmt->execute();

$result = $stmt->get_result();

$data = array();

while ($row = $result->fetch_assoc()) {
    $temp = array(
        'nama' => $row['name'],
        'sum' => $row['sum']
    );
    $data[] = $temp;
}

// Tambahkan satu data lagi
$data[] = array(
    'nama' => 'Mochamad_Darmawan_Hardjakusumah',
    'sum' => 6
);

$conn->close();

echo json_encode(['data' => $data]);
