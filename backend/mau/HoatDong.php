<?php
/**
 * File: HoatDong.php
 * Mục đích: Model đại diện cho hoạt động gần đây
 */

require_once __DIR__ . '/../co-so/BaseModel.php';

class HoatDong extends BaseModel {
    
    private $id;
    private $loai;
    private $icon;
    private $hoTen;
    private $anhDaiDien;
    private $tieuDeBai;
    private $noiDung;
    private $thoiGian;
    private $thoiGianHienThi;
    
    // Getters
    public function getId() {
        return $this->id;
    }
    
    public function getLoai() {
        return $this->loai;
    }
    
    public function getIcon() {
        return $this->icon;
    }
    
    public function getHoTen() {
        return $this->hoTen;
    }
    
    public function getAnhDaiDien() {
        return $this->anhDaiDien;
    }
    
    public function getTieuDeBai() {
        return $this->tieuDeBai;
    }
    
    public function getNoiDung() {
        return $this->noiDung;
    }
    
    public function getThoiGian() {
        return $this->thoiGian;
    }
    
    public function getThoiGianHienThi() {
        return $this->thoiGianHienThi;
    }
    
    // Setters
    public function setId($id) {
        $this->id = $this->lamSachChuoi($id);
    }
    
    public function setLoai($loai) {
        $this->loai = $this->lamSachChuoi($loai);
    }
    
    public function setIcon($icon) {
        $this->icon = $this->lamSachChuoi($icon);
    }
    
    public function setHoTen($hoTen) {
        $this->hoTen = $this->lamSachChuoi($hoTen);
    }
    
    public function setAnhDaiDien($anhDaiDien) {
        $this->anhDaiDien = $this->lamSachChuoi($anhDaiDien);
    }
    
    public function setTieuDeBai($tieuDeBai) {
        $this->tieuDeBai = $this->lamSachChuoi($tieuDeBai);
    }
    
    public function setNoiDung($noiDung) {
        $this->noiDung = $this->lamSachChuoi($noiDung);
    }
    
    public function setThoiGian($thoiGian) {
        $this->thoiGian = $thoiGian;
    }
    
    public function setThoiGianHienThi($thoiGianHienThi) {
        $this->thoiGianHienThi = $this->lamSachChuoi($thoiGianHienThi);
    }
    
    /**
     * Chuyển đổi sang array để trả về API
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'loai' => $this->loai,
            'icon' => $this->icon,
            'ho_ten' => $this->hoTen,
            'anh_dai_dien' => $this->anhDaiDien,
            'tieu_de_bai' => $this->tieuDeBai,
            'noi_dung' => $this->noiDung,
            'thoi_gian' => $this->thoiGian,
            'thoi_gian_hien_thi' => $this->thoiGianHienThi
        ];
    }
}
