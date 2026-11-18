<?php
session_start();
$_SESSION['tes'] = "Halo, session berfungsi!";
echo "Session ID: " . session_id() . "<br>";
echo "Isi Session: " . $_SESSION['tes'] . "<br>";
echo "Lokasi simpan session: " . session_save_path();