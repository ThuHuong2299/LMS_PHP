<?php
/**
 * File: BaiGiang.php
 * Mục đích: Model đại diện cho bài giảng
 */

require_once __DIR__ . '/../co-so/BaseModel.php';

class BaiGiang extends BaseModel {
    
    private $id;
    private $soThuTuBai;
    private $tieuDe;
    private $moTa;
    private $duongDanVideo;
    private $chuongId;
    private $lopHocId;
    private $ngayTao;
    
    // Thông tin chương
    private $soThuTuChuong;
    private $tenChuong;
    
    // Getters
    public function getId() { return $this->id; }
    public function getSoThuTuBai() { return $this->soThuTuBai; }
    public function getTieuDe() { return $this->tieuDe; }
    public function getMoTa() { return $this->moTa; }
    public function getDuongDanVideo() { return $this->duongDanVideo; }
    public function getChuongId() { return $this->chuongId; }
    public function getLopHocId() { return $this->lopHocId; }
    public function getNgayTao() { return $this->ngayTao; }
    public function getSoThuTuChuong() { return $this->soThuTuChuong; }
    public function getTenChuong() { return $this->tenChuong; }
    
    // Setters
    public function setId($id) { $this->id = $this->chuyenSangInt($id); }
    public function setSoThuTuBai($soThuTuBai) { $this->soThuTuBai = $this->chuyenSangInt($soThuTuBai); }
    public function setTieuDe($tieuDe) { $this->tieuDe = $this->lamSachChuoi($tieuDe); }
    public function setMoTa($moTa) { $this->moTa = $this->lamSachChuoi($moTa); }
    public function setDuongDanVideo($duongDanVideo) { $this->duongDanVideo = $this->lamSachChuoi($duongDanVideo); }
    public function setChuongId($chuongId) { $this->chuongId = $this->chuyenSangInt($chuongId); }
    public function setLopHocId($lopHocId) { $this->lopHocId = $this->chuyenSangInt($lopHocId); }
    public function setNgayTao($ngayTao) { $this->ngayTao = $ngayTao; }
    public function setSoThuTuChuong($soThuTuChuong) { $this->soThuTuChuong = $this->chuyenSangInt($soThuTuChuong); }
    public function setTenChuong($tenChuong) { $this->tenChuong = $this->lamSachChuoi($tenChuong); }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'so_thu_tu_bai' => $this->soThuTuBai,
            'tieu_de' => $this->tieuDe,
            'mo_ta' => $this->moTa,
            'duong_dan_video' => $this->duongDanVideo,
            'chuong_id' => $this->chuongId,
            'so_thu_tu_chuong' => $this->soThuTuChuong,
            'ten_chuong' => $this->tenChuong,
            'ngay_tao' => $this->ngayTao
        ];
    }
}
