<?php
/**
 * File: BaiKiemTra.php
 * Mục đích: Model đại diện cho bài kiểm tra
 */

require_once __DIR__ . '/../co-so/BaseModel.php';

class BaiKiemTra extends BaseModel {
    
    private $id;
    private $tieuDe;
    private $moTa;
    private $thoiLuong;
    private $thoiGianBatDau;
    private $thoiGianKetThuc;
    private $diemToiDa;
    private $chuongId;
    private $lopHocId;
    private $ngayTao;
    
    // Thông tin bổ sung
    private $soThuTuChuong;
    private $tenChuong;
    private $soCauHoi;
    private $soSinhVienDaLam;
    private $tongSinhVien;
    
    // Getters
    public function getId() { return $this->id; }
    public function getTieuDe() { return $this->tieuDe; }
    public function getMoTa() { return $this->moTa; }
    public function getThoiLuong() { return $this->thoiLuong; }
    public function getThoiGianBatDau() { return $this->thoiGianBatDau; }
    public function getThoiGianKetThuc() { return $this->thoiGianKetThuc; }
    public function getSoCauHoi() { return $this->soCauHoi; }
    public function getSoSinhVienDaLam() { return $this->soSinhVienDaLam; }
    
    // Setters
    public function setId($id) { $this->id = $this->chuyenSangInt($id); }
    public function setTieuDe($tieuDe) { $this->tieuDe = $this->lamSachChuoi($tieuDe); }
    public function setMoTa($moTa) { $this->moTa = $this->lamSachChuoi($moTa); }
    public function setThoiLuong($val) { $this->thoiLuong = $this->chuyenSangInt($val); }
    public function setThoiGianBatDau($val) { $this->thoiGianBatDau = $val; }
    public function setThoiGianKetThuc($val) { $this->thoiGianKetThuc = $val; }
    public function setDiemToiDa($val) { $this->diemToiDa = $this->chuyenSangFloat($val); }
    public function setChuongId($val) { $this->chuongId = $this->chuyenSangInt($val); }
    public function setSoThuTuChuong($val) { $this->soThuTuChuong = $this->chuyenSangInt($val); }
    public function setTenChuong($val) { $this->tenChuong = $this->lamSachChuoi($val); }
    public function setSoCauHoi($val) { $this->soCauHoi = $this->chuyenSangInt($val); }
    public function setSoSinhVienDaLam($val) { $this->soSinhVienDaLam = $this->chuyenSangInt($val); }
    public function setTongSinhVien($val) { $this->tongSinhVien = $this->chuyenSangInt($val); }
    public function setNgayTao($val) { $this->ngayTao = $val; }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'tieu_de' => $this->tieuDe,
            'mo_ta' => $this->moTa,
            'thoi_luong' => $this->thoiLuong,
            'thoi_gian_bat_dau' => $this->thoiGianBatDau,
            'thoi_gian_ket_thuc' => $this->thoiGianKetThuc,
            'diem_toi_da' => $this->diemToiDa,
            'so_cau_hoi' => $this->soCauHoi,
            'so_sinh_vien_da_lam' => $this->soSinhVienDaLam,
            'tong_sinh_vien' => $this->tongSinhVien,
            'chuong' => $this->chuongId ? [
                'so_thu_tu' => $this->soThuTuChuong,
                'ten_chuong' => $this->tenChuong
            ] : null,
            'ngay_tao' => $this->ngayTao
        ];
    }
}
