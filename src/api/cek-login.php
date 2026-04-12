<?php
session_start();

// Kalau sudah login → langsung ke halaman booking
if (isset($_SESSION['user_id'])) {
    header('Location: /COST-APP/src/pages/booking.php');
    exit;
}

// Belum login → ke halaman login dengan pesan
header('Location: /COST-APP/src/pages/login.php?redirect=booking');
exit;