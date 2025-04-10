<?php
include '../config/koneksi.php';

// Pastikan ada parameter 'penjualan_id' di URL
if (!isset($_GET['PenjualanID'])) {
    echo "ID transaksi tidak ditemukan.";
    exit;
}

$penjualanId = $_GET['PenjualanID'];


$queryDetail = "
    SELECT dp.NamaProduk, dp.Jumlah, p.Harga, (dp.Jumlah * p.Harga) AS TotalHarga
    FROM DetailPenjualan dp
    JOIN Produk p ON dp.NamaProduk = p.NamaProduk
    WHERE dp.PenjualanID = '$penjualanId'
";

$resultDetail = mysqli_query($conn, $queryDetail);

// Ambil total harga transaksi dari tabel Penjualan
$queryTotal = "SELECT TotalHarga FROM Penjualan WHERE PenjualanID = '$penjualanId'";
$resultTotal = mysqli_query($conn, $queryTotal);
$totalData = mysqli_fetch_assoc($resultTotal);
$totalHarga = $totalData['TotalHarga'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/struk.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembelian</title>
</head>
<body>
    <div class="struk">
        <div class="header">
            <p>Lapo Rutma</p>
            <p>Jl. Kalimalang Pinggir Kali</p>
            <p>Bekasi, Jawa Barat</p>
            <hr>
            <p><?php echo date('d-m-Y H:i'); ?></p>
        </div>
        <table>
            <tr>
                <th>Nama</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
            <?php
            while ($row = mysqli_fetch_assoc($resultDetail)) {
                echo "<tr>";
                echo "<td>{$row['NamaProduk']}</td>";
                echo "<td>{$row['Jumlah']}</td>";
                echo "<td>Rp " . number_format($row['Harga'], 0, ',', '.') . "</td>";
                echo "<td>Rp " . number_format($row['TotalHarga'], 0, ',', '.') . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
        <hr>
        <p class="total"><strong>Total: Rp <?php echo number_format($totalHarga, 0, ',', '.'); ?></strong></p>
        <p class="footer">
            Terima kasih, Selamat Belanja Kembali!<br>
            Layanan Konsumen<br>
            SMS/CALL: 0866-6666-6666
        </p>
    </div>
</body>
</html>
