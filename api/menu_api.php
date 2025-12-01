<?php
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
    $imageName = "default.jpg"; 
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "../assets/img/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        $newFileName = time() . "_" . uniqid() . "." . pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetDir . $newFileName)) {
            $imageName = $newFileName;
        }
    }
    if($menu->create($_POST['name'], $_POST['price'], $_POST['stock'], $imageName)){
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
} 
// --- FITUR BARU: UPDATE ---
elseif ($action == 'update' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['menu_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    
    $imageName = null; // Default null artinya tidak ganti gambar

    // Cek jika ada upload gambar baru
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "../assets/img/";
        $newFileName = time() . "_" . uniqid() . "." . pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetDir . $newFileName)) {
            $imageName = $newFileName;
        }
    }

    if($menu->update($id, $name, $price, $stock, $imageName)){
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal update"]);
    }
}
elseif ($action == 'delete') {
    if($menu->delete($_POST['id'])) echo json_encode(["status" => "success"]);
}
?>