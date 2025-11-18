<?php
/**
 * File: ThongKe.php
 * Mục đích: Model đại diện cho thống kê dashboard
 */

require_once __DIR__ . '/../co-so/BaseModel.php';

class ThongKe extends BaseModel {
    
    private $lopGiangDay;
    private $sinhVienTheoHoc;
    private $baiChoCham;
    private $thongBaoMoi;
    
    public function __construct() {
        $this->lopGiangDay = 0;
        $this->sinhVienTheoHoc = 0;
        $this->baiChoCham = 0;
        $this->thongBaoMoi = 0;
    }
    
    // Getters
    public function getLopGiangDay() {
        return $this->lopGiangDay;
    }
    
    public function getSinhVienTheoHoc() {
        return $this->sinhVienTheoHoc;
    }
    
    public function getBaiChoCham() {
        return $this->baiChoCham;
    }
    
    public function getThongBaoMoi() {
        return $this->thongBaoMoi;
    }
    
    // Setters
    public function setLopGiangDay($lopGiangDay) {
        $this->lopGiangDay = $this->chuyenSangInt($lopGiangDay);
    }
    
    public function setSinhVienTheoHoc($sinhVienTheoHoc) {
        $this->sinhVienTheoHoc = $this->chuyenSangInt($sinhVienTheoHoc);
    }
    
    public function setBaiChoCham($baiChoCham) {
        $this->baiChoCham = $this->chuyenSangInt($baiChoCham);
    }
    
    public function setThongBaoMoi($thongBaoMoi) {
        $this->thongBaoMoi = $this->chuyenSangInt($thongBaoMoi);
    }
    
    /**
     * Chuyển đổi sang array để trả về API
     */
    public function toArray() {
        return [
            'lop_giang_day' => $this->lopGiangDay,
            'sinh_vien_theo_hoc' => $this->sinhVienTheoHoc,
            'bai_cho_cham' => $this->baiChoCham,
            'thong_bao_moi' => $this->thongBaoMoi
        ];
    }
}
