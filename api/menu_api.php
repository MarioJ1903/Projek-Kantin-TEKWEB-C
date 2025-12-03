<?php
// File: api/menu_api.php
// Matikan display error agar tidak merusak format JSON jika ada warning kecil
ini_set('display_errors', 0);
error_reporting(E_ALL);

header("Content-Type: application/json");
include_once '../config/Database.php';
include_once '../classes/Menu.php';

$db = new Database();
$menu = new Menu($db->conn);
$action = $_GET['action'] ?? '';

try {
    if ($action == 'read') {
        $result = $menu->read();
        $data = [];
        while ($row = $result->fetch_assoc()) $data[] = $row;
        echo json_encode($data);
    } 
    elseif ($action == 'create' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $imageName = "default.jpg"; 
        
        // Cek Upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $targetDir = "../assets/img/";
            if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
            
            $ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $newFileName = time() . "_" . uniqid() . "." . $ext;
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetDir . $newFileName)) {
                $imageName = $newFileName;
            }
        }

        if($menu->create($_POST['name'], (int)$_POST['price'], (int)$_POST['stock'], $imageName)){
            echo json_encode(["status" => "success"]);
        } else {
            throw new Exception("Gagal insert database");
        }
    } 
    // --- BAGIAN UPDATE YANG DIPERBAIKI ---
    elseif ($action == 'update' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        // 1. Ambil & Validasi Data
        $id = (int)$_POST['menu_id'];
        $name = $_POST['name'];
        $price = (int)$_POST['price'];
        $stock = (int)$_POST['stock'];
        
        $imageName = null; 

        // 2. Cek apakah ada file gambar baru yang diupload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $targetDir = "../assets/img/";
            
            // Buat folder jika belum ada
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $newFileName = time() . "_" . uniqid() . "." . $ext; // Nama unik

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetDir . $newFileName)) {
                $imageName = $newFileName;
            } else {
                throw new Exception("Gagal upload gambar. Cek permission folder assets/img.");
            }
        }

        // 3. Panggil Class Update
        if($menu->update($id, $name, $price, $stock, $imageName)){
            echo json_encode(["status" => "success"]);
        } else {
            throw new Exception("Gagal update database.");
        }
    }
    elseif ($action == 'delete') {
        if($menu->delete($_POST['id'])) echo json_encode(["status" => "success"]);
    }
} catch (Exception $e) {
    // Tangkap error dan kirim sebagai JSON
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>