<?php
// client/core/Client.php

class Client {
    private $baseUrl;

    public function __construct() {
        // Ambil URL dasar dari file konfigurasi
        // __DIR__ adalah folder 'core', jadi kita perlu '../'
        require_once __DIR__ . '/../config/config.php';
        $this->baseUrl = API_BASE_URL;
    }

    /**
     * Metode internal untuk eksekusi cURL
     * @param string $url URL lengkap ke API endpoint
     * @param string $method Metode HTTP (GET, POST, PUT, DELETE)
     * @param array|null $data Data yang akan dikirim (untuk POST/PUT)
     * @return array Hasil data yang sudah di-decode dari JSON
     */
    private function execute($url, $method = 'GET', $data = null) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($data) {
            $jsonData = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData)
            ]);
        }

        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return ["status" => "error", "message" => "Koneksi Gagal: " . $error];
        }
        
        curl_close($ch);
        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
             return ["status" => "error", "message" => "Gagal decode JSON", "server_response" => $response];
        }

        return $decodedResponse;
    }

    // --- Metode Publik CRUD ---

    public function get($table, $id = null) {
        $url = $this->baseUrl . '/' . $table;
        if ($id) {
            $url .= '/' . $id;
        }
        return $this->execute($url, 'GET');
    }

    public function post($table, $data) {
        $url = $this->baseUrl . '/' . $table;
        return $this->execute($url, 'POST', $data);
    }

    public function put($table, $id, $data) {
        $url = $this->baseUrl . '/' . $table . '/' . $id;
        return $this->execute($url, 'PUT', $data);
    }

    public function delete($table, $id) {
        $url = $this->baseUrl . '/' . $table . '/' . $id;
        return $this->execute($url, 'DELETE');
    }
}
?>