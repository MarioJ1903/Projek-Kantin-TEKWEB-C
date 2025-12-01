<?php
// File: api/menu_api.php
header("Content-Type: application/json");
include_once '../config/Database.php';
include_once '../classes/Menu.php';

$db = new Database();
$menu = new Menu($db->conn);
$action = $_GET['action'] ?? '';

if ($action == 'read') {
    $result = $menu->read();
    $data = [];
    while ($row = $result->fetch_assoc()) $data[] = $row;
    echo json_encode($data);
} 
elseif ($action == 'create' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Logika Upload Gambar
    $imageName = "default.jpg"; // Default jika user tidak upload gambar
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "../assets/img/";
        
        // Pastikan folder ada
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Rename file agar unik (pake timestamp)
        $fileExtension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $newFileName = time() . "_" . uniqid() . "." . $fileExtension;
        $targetFile = $targetDir . $newFileName;

        // Pindahkan file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imageName = $newFileName;
        }
    }

    // 2. Simpan ke Database
    if($menu->create($_POST['name'], $_POST['price'], $_POST['stock'], $imageName)){
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menyimpan ke database"]);
    }
} 
elseif ($action == 'delete') {
    if($menu->delete($_POST['id'])) echo json_encode(["status" => "success"]);
}
?>