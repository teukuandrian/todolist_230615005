<?php
session_start();

// Inisialisasi array todo jika belum ada
if (!isset($_SESSION['todos'])) {
    $_SESSION['todos'] = [];
}

// Fungsi untuk menambah todo
if (isset($_POST['add'])) {
    $task = trim($_POST['task']);
    if (!empty($task)) {
        $newId = uniqid();
        $_SESSION['todos'][] = [
            'id' => $newId,
            'nama_tugas' => htmlspecialchars($task),
            'status' => false
        ];
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Fungsi untuk menghapus todo
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    foreach ($_SESSION['todos'] as $key => $todo) {
        if ($todo['id'] === $id) {
            unset($_SESSION['todos'][$key]);
            break;
        }
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Fungsi untuk mengupdate status todo
if (isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    foreach ($_SESSION['todos'] as &$todo) {
        if ($todo['id'] === $id) {
            $todo['status'] = !$todo['status'];
            break;
        }
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Fungsi untuk mengedit todo
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $newTask = trim($_POST['new_task']);
    if (!empty($newTask)) {
        foreach ($_SESSION['todos'] as &$todo) {
            if ($todo['id'] === $id) {
                $todo['nama_tugas'] = htmlspecialchars($newTask);
                break;
            }
        }
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List Web-Based App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-md">
        <h1 class="text-3xl font-bold text-center text-green-600 mb-8">Todo List Web-Based App</h1>
        
        <!-- Form Tambah Todo -->
        <form method="POST" class="mb-6 bg-white p-4 rounded-lg shadow">
            <div class="flex">
                <input type="text" name="task" placeholder="Tambahkan tugas baru..." 
                       class="flex-grow px-4 py-2 border rounded-l-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                <button type="submit" name="add" class="bg-purple-500 text-white px-4 py-2 rounded-r-lg hover:bg-purple-600 transition">
                    Tambah
                </button>
            </div>
        </form>
        
        <!-- Daftar Todo -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <?php if (empty($_SESSION['todos'])): ?>
                <p class="p-4 text-gray-500">Tidak ada tugas saat ini.</p>
            <?php else: ?>
                <?php foreach ($_SESSION['todos'] as $todo): ?>
                    <div class="border-b border-gray-200 last:border-b-0">
                        <div class="p-4 flex items-center justify-between">
                            <div class="flex items-center">
                                <!-- Checkbox Status -->
                                <a href="?toggle=<?= $todo['id'] ?>" class="mr-3">
                                    <input type="checkbox" <?= $todo['status'] ? 'checked' : '' ?> 
                                           class="h-5 w-5 text-green-500 rounded focus:ring-green-400 cursor-pointer">
                                </a>
                                
                                <!-- Nama Tugas -->
                                <span class="<?= $todo['status'] ? 'line-through text-gray-400' : 'text-gray-800' ?>">
                                    <?= $todo['nama_tugas'] ?>
                                </span>
                            </div>
                            
                            <!-- Tombol Aksi -->
                            <div class="flex space-x-2">
                                <!-- Form Edit -->
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = true" class="text-green-500 hover:text-green-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </button>
                                    
                                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-64 bg-white rounded-md shadow-lg z-10 p-4">
                                        <form method="POST">
                                            <input type="hidden" name="id" value="<?= $todo['id'] ?>">
                                            <input type="text" name="new_task" value="<?= $todo['nama_tugas'] ?>" 
                                                   class="w-full px-3 py-2 border rounded mb-2">
                                            <div class="flex justify-end space-x-2">
                                                <button type="button" @click="open = false" class="px-3 py-1 text-gray-600 rounded hover:bg-gray-100">
                                                    Batal
                                                </button>
                                                <button type="submit" name="edit" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                                    Simpan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Tombol Hapus -->
                                <a href="?delete=<?= $todo['id'] ?>" class="text-red-500 hover:text-red-600" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus tugas ini?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Alpine JS untuk dropdown edit -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
</body>
</html>