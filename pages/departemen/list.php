if (isset($_POST['aksi']) && $_POST['aksi'] == 'ubah') {

$id_user = $_POST['id_user']; // Ambil ID dari form

// Siapkan data TANPA password dulu
$data = [
"username" => $_POST['username'], // Asumsi Anda akan menambahkan field ini
"nama_lengkap" => $_POST['nama_lengkap'],
"email" => $_POST['email'],
"role" => $_POST['role'],
"departemen" => $_POST['departemen']
];

// (INI YANG BARU) Cek apakah password diisi
if (!empty($_POST['password'])) {
// Jika diisi, baru tambahkan password ke data yang akan dikirim
$data['password'] = $_POST['password'];
}
// Jika $_POST['password'] kosong, kita tidak mengirimkannya ke API
// sehingga server tidak akan meng-update-nya.

$client = new Client();
$response = $client->put('users', $id_user, $data); // Panggil metode PUT

if (isset($response['status']) && $response['status'] == 'success') {
Helper::setFlashMessage('success', 'Data user berhasil diperbarui.');
} else {
Helper::setFlashMessage('error', 'Gagal memperbarui data: ' . ($response['message'] ?? 'Unknown error'));
}
Helper::redirect('pages/users/list.php');
}