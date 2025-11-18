<?php
/**
 * File: SinhVien.php
 * Mục đích: Model đại diện cho sinh viên
 */

require_once __DIR__ . '/../co-so/BaseModel.php';

class SinhVien extends BaseModel {
    
    private $id;
    private $maNguoiDung;
    private $hoTen;
    private $anhDaiDien;
    private $email;
    private $tienDo;
    private $lastUpdated;
    
    // Getters
    public function getId() { return $this->id; }
    public function getMaNguoiDung() { return $this->maNguoiDung; }
    public function getHoTen() { return $this->hoTen; }
    public function getAnhDaiDien() { return $this->anhDaiDien; }
    public function getEmail() { return $this->email; }
    public function getTienDo() { return $this->tienDo; }
    public function getLastUpdated() { return $this->lastUpdated; }
    
    // Setters
    public function setId($id) { $this->id = $this->chuyenSangInt($id); }
    public function setMaNguoiDung($maNguoiDung) { $this->maNguoiDung = $this->lamSachChuoi($maNguoiDung); }
    public function setHoTen($hoTen) { $this->hoTen = $this->lamSachChuoi($hoTen); }
    public function setAnhDaiDien($anhDaiDien) { $this->anhDaiDien = $this->lamSachChuoi($anhDaiDien); }
    public function setEmail($email) { $this->email = $this->lamSachChuoi($email); }
    public function setTienDo($tienDo) { $this->tienDo = $this->chuyenSangFloat($tienDo); }
    public function setLastUpdated($lastUpdated) { $this->lastUpdated = $lastUpdated; }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'ma_nguoi_dung' => $this->maNguoiDung,
            'ho_ten' => $this->hoTen,
            'anh_dai_dien' => $this->anhDaiDien,
            'email' => $this->email,
            'tien_do' => $this->tienDo,
            'last_updated' => $this->lastUpdated
        ];
    }
}
