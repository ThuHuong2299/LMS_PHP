<?php
/**
 * File: BaiTap.php
 * Mục đích: Model đại diện cho bài tập
 */

require_once __DIR__ . '/../co-so/BaseModel.php';

class BaiTap extends BaseModel {
    
    private $id;
    private $tieuDe;
    private $moTa;
    private $hanNop;
    private $diemToiDa;
    private $chuongId;
    private $baiGiangId;
    private $lopHocId;
    private $ngayTao;
    
    // Thông tin bổ sung
    private $soThuTuChuong;
    private $tenChuong;
    private $soThuTuBai;
    private $tenBaiGiang;
    private $soCauHoi;
    private $soSinhVienDaNop;
    private $tongSinhVien;
    
    // Getters
    public function getId() { return $this->id; }
    public function getTieuDe() { return $this->tieuDe; }
    public function getMoTa() { return $this->moTa; }
    public function getHanNop() { return $this->hanNop; }
    public function getDiemToiDa() { return $this->diemToiDa; }
    public function getSoCauHoi() { return $this->soCauHoi; }
    public function getSoSinhVienDaNop() { return $this->soSinhVienDaNop; }
    public function getTongSinhVien() { return $this->tongSinhVien; }
    
    // Setters
    public function setId($id) { $this->id = $this->chuyenSangInt($id); }
    public function setTieuDe($tieuDe) { $this->tieuDe = $this->lamSachChuoi($tieuDe); }
    public function setMoTa($moTa) { $this->moTa = $this->lamSachChuoi($moTa); }
    public function setHanNop($hanNop) { $this->hanNop = $hanNop; }
    public function setDiemToiDa($diemToiDa) { $this->diemToiDa = $this->chuyenSangFloat($diemToiDa); }
    public function setChuongId($chuongId) { $this->chuongId = $this->chuyenSangInt($chuongId); }
    public function setBaiGiangId($baiGiangId) { $this->baiGiangId = $this->chuyenSangInt($baiGiangId); }
    public function setSoThuTuChuong($val) { $this->soThuTuChuong = $this->chuyenSangInt($val); }
    public function setTenChuong($val) { $this->tenChuong = $this->lamSachChuoi($val); }
    public function setSoThuTuBai($val) { $this->soThuTuBai = $this->chuyenSangInt($val); }
    public function setTenBaiGiang($val) { $this->tenBaiGiang = $this->lamSachChuoi($val); }
    public function setSoCauHoi($val) { $this->soCauHoi = $this->chuyenSangInt($val); }
    public function setSoSinhVienDaNop($val) { $this->soSinhVienDaNop = $this->chuyenSangInt($val); }
    public function setTongSinhVien($val) { $this->tongSinhVien = $this->chuyenSangInt($val); }
    public function setNgayTao($val) { $this->ngayTao = $val; }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'tieu_de' => $this->tieuDe,
            'mo_ta' => $this->moTa,
            'han_nop' => $this->hanNop,
            'diem_toi_da' => $this->diemToiDa,
            'so_cau_hoi' => $this->soCauHoi,
            'so_sinh_vien_da_nop' => $this->soSinhVienDaNop,
            'tong_sinh_vien' => $this->tongSinhVien,
            'chuong' => [
                'so_thu_tu' => $this->soThuTuChuong,
                'ten_chuong' => $this->tenChuong
            ],
            'bai_giang' => $this->baiGiangId ? [
                'so_thu_tu_bai' => $this->soThuTuBai,
                'tieu_de' => $this->tenBaiGiang
            ] : null,
            'ngay_tao' => $this->ngayTao
        ];
    }
}
