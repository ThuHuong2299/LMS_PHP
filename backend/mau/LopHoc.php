<?php
/**
 * File: LopHoc.php
 * Mục đích: Model đại diện cho lớp học
 */

require_once __DIR__ . '/../co-so/BaseModel.php';

class LopHoc extends BaseModel {
    
    private $id;
    private $maLopHoc;
    private $tenLopHoc;
    private $tenMonHoc;
    private $trangThai;
    private $soSinhVien;
    private $soBaiTap;
    private $soBaiKiemTra;
    private $ngayTao;
    
    // Getters
    public function getId() {
        return $this->id;
    }
    
    public function getMaLopHoc() {
        return $this->maLopHoc;
    }
    
    public function getTenLopHoc() {
        return $this->tenLopHoc;
    }
    
    public function getTenMonHoc() {
        return $this->tenMonHoc;
    }
    
    public function getTrangThai() {
        return $this->trangThai;
    }
    
    public function getSoSinhVien() {
        return $this->soSinhVien;
    }
    
    public function getSoBaiTap() {
        return $this->soBaiTap;
    }
    
    public function getSoBaiKiemTra() {
        return $this->soBaiKiemTra;
    }
    
    public function getNgayTao() {
        return $this->ngayTao;
    }
    
    // Setters
    public function setId($id) {
        $this->id = $this->chuyenSangInt($id);
    }
    
    public function setMaLopHoc($maLopHoc) {
        $this->maLopHoc = $this->lamSachChuoi($maLopHoc);
    }
    
    public function setTenLopHoc($tenLopHoc) {
        $this->tenLopHoc = $this->lamSachChuoi($tenLopHoc);
    }
    
    public function setTenMonHoc($tenMonHoc) {
        $this->tenMonHoc = $this->lamSachChuoi($tenMonHoc);
    }
    
    public function setTrangThai($trangThai) {
        $this->trangThai = $this->lamSachChuoi($trangThai);
    }
    
    public function setSoSinhVien($soSinhVien) {
        $this->soSinhVien = $this->chuyenSangInt($soSinhVien);
    }
    
    public function setSoBaiTap($soBaiTap) {
        $this->soBaiTap = $this->chuyenSangInt($soBaiTap);
    }
    
    public function setSoBaiKiemTra($soBaiKiemTra) {
        $this->soBaiKiemTra = $this->chuyenSangInt($soBaiKiemTra);
    }
    
    public function setNgayTao($ngayTao) {
        $this->ngayTao = $ngayTao;
    }
    
    /**
     * Chuyển đổi sang array để trả về API
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'ma_lop_hoc' => $this->maLopHoc,
            'ten_lop_hoc' => $this->tenLopHoc,
            'ten_mon_hoc' => $this->tenMonHoc,
            'trang_thai' => $this->trangThai,
            'so_sinh_vien' => $this->soSinhVien,
            'so_bai_tap' => $this->soBaiTap,
            'so_bai_kiem_tra' => $this->soBaiKiemTra,
            'ngay_tao' => $this->ngayTao
        ];
    }
}
