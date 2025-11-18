<?php
/**
 * File: ThongBao.php
 * Mục đích: Model đại diện cho thông báo
 */

require_once __DIR__ . '/../co-so/BaseModel.php';

class ThongBao extends BaseModel {
    
    private $id;
    private $tieuDe;
    private $noiDung;
    private $thoiGianGui;
    private $lopHocId;
    private $nguoiGuiId;
    
    // Thông tin người gửi
    private $hoTenNguoiGui;
    private $anhDaiDienNguoiGui;
    
    // Getters
    public function getId() { return $this->id; }
    public function getTieuDe() { return $this->tieuDe; }
    public function getNoiDung() { return $this->noiDung; }
    public function getThoiGianGui() { return $this->thoiGianGui; }
    public function getHoTenNguoiGui() { return $this->hoTenNguoiGui; }
    public function getAnhDaiDienNguoiGui() { return $this->anhDaiDienNguoiGui; }
    
    // Setters
    public function setId($id) { $this->id = $this->chuyenSangInt($id); }
    public function setTieuDe($tieuDe) { $this->tieuDe = $this->lamSachChuoi($tieuDe); }
    public function setNoiDung($noiDung) { $this->noiDung = $this->lamSachChuoi($noiDung); }
    public function setThoiGianGui($thoiGianGui) { $this->thoiGianGui = $thoiGianGui; }
    public function setLopHocId($lopHocId) { $this->lopHocId = $this->chuyenSangInt($lopHocId); }
    public function setNguoiGuiId($nguoiGuiId) { $this->nguoiGuiId = $this->chuyenSangInt($nguoiGuiId); }
    public function setHoTenNguoiGui($hoTen) { $this->hoTenNguoiGui = $this->lamSachChuoi($hoTen); }
    public function setAnhDaiDienNguoiGui($anh) { $this->anhDaiDienNguoiGui = $this->lamSachChuoi($anh); }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'tieu_de' => $this->tieuDe,
            'noi_dung' => $this->noiDung,
            'thoi_gian_gui' => $this->thoiGianGui,
            'nguoi_gui' => [
                'ho_ten' => $this->hoTenNguoiGui,
                'anh_dai_dien' => $this->anhDaiDienNguoiGui
            ]
        ];
    }
}
