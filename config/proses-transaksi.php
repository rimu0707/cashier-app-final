<?php
include 'koneksi.php';
include 'navbar-admin.php';


if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

if (isset($_POST['barcode'])) {
    $barcode = $_POST['barcode'];

    $query = "SELECT * FROM Produk WHERE ProdukID = '$barcode'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);

        if (isset($_SESSION['keranjang'][$barcode])) {
            $_SESSION['keranjang'][$barcode]['Jumlah']++;
        } else {
            $_SESSION['keranjang'][$barcode] = [
                'ProdukID' => $product['ProdukID'],
                'NamaProduk' => $product['NamaProduk'],
                'Harga' => $product['Harga'],
                'Jumlah' => 1
            ];
        }
    } else {
        header('Location: penjualan.php?pesan=gagal');
        exit;
    }
    header('Location: penjualan.php');
    exit;
}

if (isset($_POST['hapus'])) {
    unset($_SESSION['keranjang']);
    header('Location: ../pages/penjualan.php?pesan=hapus');
    exit;
}

if (isset($_POST['simpan'])) {
    $totalHarga = 0;
    $tanggalPenjualan = date('Y-m-d');

    foreach ($_SESSION['keranjang'] as $item) {
        $totalHarga += $item['Harga'] * $item['Jumlah'];
    }

    $queryPenjualan = "INSERT INTO Penjualan (TanggalPenjualan, TotalHarga) VALUES ('$tanggalPenjualan', '$totalHarga')";
    mysqli_query($conn, $queryPenjualan);
    $penjualanId = mysqli_insert_id($conn);

    foreach ($_SESSION['keranjang'] as $item) {
        $produkID = $item['ProdukID'];
        $namaProduk = $item['NamaProduk'];
        $jumlah = $item['Jumlah'];
        $subtotal = $item['Harga'] * $item['Jumlah'];

        $queryDetail = "INSERT INTO DetailPenjualan (PenjualanID, TanggalPenjualan, NamaProduk, Jumlah, TotalHarga) VALUES ('$penjualanId', '$tanggalPenjualan', '$namaProduk', $jumlah,'$subtotal')";
        mysqli_query($conn, $queryDetail);

        $queryUpdateStok = "UPDATE Produk SET Stok = Stok - {$item['Jumlah']} WHERE ProdukID = '$produkID'";
        mysqli_query($conn, $queryUpdateStok);
    }

    unset($_SESSION['keranjang']);
    echo "<script>
    window.open('../pages/struk.php?penjualan_id=$penjualanId', 'Struk', 'width=400,height=600');
    setTimeout(function() {
        window.location.href = '../pages/penjualan.php';
    }, 2000);
</script>";
exit;
}
?>
