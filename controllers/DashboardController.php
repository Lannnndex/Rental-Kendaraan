<?php
// File: controllers/DashboardController.php (SUDAH DIROMBAK)

require_once "controllers/BaseController.php";

class DashboardController extends BaseController {
    
    private $model;
    
    public function __construct($factory) {
        parent::__construct($factory); 
        $this->model = $this->factory->getModel('Dashboard');
    }

    public function index() {
        // HAK AKSES: (Sudah benar)
        $this->authorize(['admin', 'manajer']);
        
        try {
            // (Auto-delete Anda sudah benar)
            $this->factory->getModel('Kendaraan')->autoDeleteOld();
            $this->factory->getModel('Pelanggan')->autoDeleteOld();
            $this->factory->getModel('TransaksiSewa')->autoDeleteOld(); 
            $this->factory->getModel('Pembayaran')->autoDeleteOld();
            $this->factory->getModel('Pengembalian')->autoDeleteOld();
        } catch (Exception $e) {
            echo "Warning: Gagal menjalankan auto-delete. " . $e->getMessage();
        }

        // Tampilkan summary (LAMA - masih dipakai untuk Pelanggan, Transaksi, Pendapatan)
        $summary = $this->model->getSummary();
        
        // ======================================================
        // == INI ROMBAKANNYA (Panggil fungsi 'pintar' baru) ==
        // ======================================================
        
        // 1. Ambil model Kendaraan (kita sudah punya akses via factory)
        $kendaraanModel = $this->factory->getModel('Kendaraan');
        
        // 2. Panggil fungsi baru yang kita buat di Rombak 1
        $vehicleCounts = $kendaraanModel->getAvailableCountByType();
        // $vehicleCounts sekarang berisi array, cth: ['Mobil' => 7, 'Motor' => 3]
        
        // ======================================================

        // 3. Ambil KPI tambahan untuk manager view
        $kpi = [];
        $kpi['ongoing_rentals'] = $this->model->getOngoingRentalsCount();
        $kpi['available_vehicles'] = $this->model->getAvailableVehiclesCount();
        $kpi['monthly_revenue'] = $this->model->getMonthlyRevenueCurrent();
        $kpi['recent_transactions'] = $this->model->getRecentTransactions(6);

        // 4. Sertakan view. (View sekarang bisa mengakses $summary, $vehicleCounts, dan $kpi)
        include "views/dashboard/index.php";
    }
}
?>