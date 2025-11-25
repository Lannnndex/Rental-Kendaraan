<?php
$log = __DIR__ . '/../logs/transaksi_create_debug.log';
if (!file_exists($log)) {
    echo "Log tidak ditemukan: $log\n";
    exit(0);
}
echo "---- isi log terakhir ----\n";
$lines = file($log, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$last = array_slice($lines, -200); // show last 200 lines
foreach ($last as $l) echo $l . "\n";
?>
