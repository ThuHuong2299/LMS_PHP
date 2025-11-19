<?php
/**
 * File: TienDoHocTapService.php
 * Mục đích: Service xử lý logic tiến độ học tập
 */

require_once __DIR__ . '/../co-so/BaseService.php';
require_once __DIR__ . '/../kho-du-lieu/TienDoHocTapRepository.php';

class TienDoHocTapService extends BaseService {
    
    private $tienDoRepo;
    
    public function __construct() {
        $this->tienDoRepo = new TienDoHocTapRepository();
    }
    
    /**
     * Lấy phần trăm hoàn thành trung bình cho lớp học
     * @param int $sinhVienId
     * @param int $lopHocId
     * @return float
     */
    public function layPhanTramHoanThanhLopHoc($sinhVienId, $lopHocId) {
        // Validate
        if (!$this->kiemTraSoNguyen($sinhVienId)) {
            $this->nemLoi('ID sinh viên không hợp lệ');
        }
        if (!$this->kiemTraSoNguyen($lopHocId)) {
            $this->nemLoi('ID lớp học không hợp lệ');
        }
        
        $phanTram = $this->tienDoRepo->getPhanTramHoanThanhLopHoc($sinhVienId, $lopHocId);
        
        // Làm tròn đến 2 chữ số thập phân
        return round($phanTram, 2);
    }
    
    /**
     * Lấy chi tiết tiến độ
     * @param int $sinhVienId
     * @param int $lopHocId
     * @return array
     */
    public function layTienDoChiTiet($sinhVienId, $lopHocId) {
        // Validate
        if (!$this->kiemTraSoNguyen($sinhVienId)) {
            $this->nemLoi('ID sinh viên không hợp lệ');
        }
        if (!$this->kiemTraSoNguyen($lopHocId)) {
            $this->nemLoi('ID lớp học không hợp lệ');
        }
        
        $duLieu = $this->tienDoRepo->getTienDoChiTiet($sinhVienId, $lopHocId);
        
        return $duLieu ? $duLieu : [];
    }
    
    /**
     * Cập nhật tiến độ học tập
     * @param int $sinhVienId
     * @param int $lopHocId
     * @param string $loaiNoiDung
     * @param int $noiDungId
     * @param bool $daHoanThanh
     * @param float $phanTramHoanThanh
     * @return bool
     */
    public function capNhatTienDo($sinhVienId, $lopHocId, $loaiNoiDung, $noiDungId, $daHoanThanh = false, $phanTramHoanThanh = 0) {
        // Validate
        if (!$this->kiemTraSoNguyen($sinhVienId)) {
            $this->nemLoi('ID sinh viên không hợp lệ');
        }
        if (!$this->kiemTraSoNguyen($lopHocId)) {
            $this->nemLoi('ID lớp học không hợp lệ');
        }
        if (!$this->kiemTraChuoiKhongRong($loaiNoiDung)) {
            $this->nemLoi('Loại nội dung không hợp lệ');
        }
        if (!$this->kiemTraSoNguyen($noiDungId)) {
            $this->nemLoi('ID nội dung không hợp lệ');
        }
        
        // Validate phần trăm (0-100)
        $phanTramHoanThanh = (float)$phanTramHoanThanh;
        if ($phanTramHoanThanh < 0 || $phanTramHoanThanh > 100) {
            $this->nemLoi('Phần trăm hoàn thành phải từ 0 đến 100');
        }
        
        // Validate loại nội dung
        $loaiHopLe = ['bai_giang', 'bai_tap', 'bai_kiem_tra'];
        if (!in_array($loaiNoiDung, $loaiHopLe)) {
            $this->nemLoi('Loại nội dung không hợp lệ');
        }
        
        return $this->tienDoRepo->capNhatTienDo(
            $sinhVienId, 
            $lopHocId, 
            $loaiNoiDung, 
            $noiDungId, 
            $daHoanThanh, 
            $phanTramHoanThanh
        );
    }
}
