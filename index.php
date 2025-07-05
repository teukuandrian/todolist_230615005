<?php
session_start();

// Inisialisasi array tugas jika belum ada
if (!isset($_SESSION['todos'])) {
    $_SESSION['todos'] = [];
}

// Fungsi untuk menambah tugas
if (isset($_POST['add'])) {
    $newTask = [
        'id' => uniqid(),
        'nama_tugas' => htmlspecialchars($_POST['nama_tugas']),
        'status' => 'belum'
    ];
    array_push($_SESSION['todos'], $newTask);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Fungsi untuk menghapus tugas
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $_SESSION['todos'] = array_filter($_SESSION['todos'], function($todo) use ($id) {
        return $todo['id'] !== $id;
    });
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Fungsi untuk mengupdate status tugas
if (isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    foreach ($_SESSION['todos'] as &$todo) {
        if ($todo['id'] === $id) {
            $todo['status'] = $todo['status'] === 'selesai' ? 'belum' : 'selesai';
            break;
        }
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Fungsi untuk mengedit tugas
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    foreach ($_SESSION['todos'] as &$todo) {
        if ($todo['id'] === $id) {
            $todo['nama_tugas'] = htmlspecialchars($_POST['nama_tugas']);
            break;
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
    <title>Aplikasi To-Do List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center mb-8 text-green-600">Aplikasi To-Do List</h1>
        
        <!-- Form Tambah Tugas -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Tambah Tugas Baru</h2>
            <form method="POST" class="flex gap-4">
                <input type="text" name="nama_tugas" placeholder="Masukkan tugas baru..." 
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                <button type="submit" name="add" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-md transition duration-200">
                    Tambah
                </button>
            </form>
        </div>
        
        <!-- Daftar Tugas -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Daftar Tugas</h2>
            
            <?php if (empty($_SESSION['todos'])): ?>
                <p class="text-gray-500 text-center py-4">Belum ada tugas. Yuk tambahkan tugas baru!</p>
            <?php else: ?>
                <ul class="divide-y divide-gray-200">
                    <?php foreach ($_SESSION['todos'] as $todo): ?>
                        <li class="py-4 flex items-center justify-between group">
                            <div class="flex items-center">
                                <!-- Checkbox Status -->
                                <a href="?toggle=<?= $todo['id'] ?>" class="mr-3">
                                    <div class="w-6 h-6 rounded-full border-2 border-blue-500 flex items-center justify-center <?= $todo['status'] === 'selesai' ? 'bg-blue-500' : '' ?>">
                                        <?php if ($todo['status'] === 'selesai'): ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        <?php endif; ?>
                                    </div>
                                </a>
                                
                                <!-- Nama Tugas -->
                                <span class="<?= $todo['status'] === 'selesai' ? 'line-through text-gray-400' : 'text-gray-800' ?>">
                                    <?= $todo['nama_tugas'] ?>
                                </span>
                            </div>
                            
                            <!-- Tombol Aksi -->
                            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <!-- Form Edit -->
                                <form method="POST" class="hidden" id="form-edit-<?= $todo['id'] ?>">
                                    <input type="hidden" name="id" value="<?= $todo['id'] ?>">
                                    <div class="flex gap-2">
                                        <input type="text" name="nama_tugas" value="<?= $todo['nama_tugas'] ?>" 
                                               class="px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <button type="submit" name="update" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md text-sm">
                                            Simpan
                                        </button>
                                    </div>
                                </form>
                                
                                <!-- Tombol Edit dan Hapus -->
                                <div class="flex gap-2" id="buttons-<?= $todo['id'] ?>">
                                    <button onclick="toggleEditForm('<?= $todo['id'] ?>')" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md text-sm">
                                        Edit
                                    </button>
                                    <a href="?delete=<?= $todo['id'] ?>" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-sm">
                                        Hapus
                                    </a>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleEditForm(id) {
            const form = document.getElementById(`form-edit-${id}`);
            const buttons = document.getElementById(`buttons-${id}`);
            
            if (form.classList.contains('hidden')) {
                form.classList.remove('hidden');
                buttons.classList.add('hidden');
                form.querySelector('input[name="nama_tugas"]').focus();
            } else {
                form.classList.add('hidden');
                buttons.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>
