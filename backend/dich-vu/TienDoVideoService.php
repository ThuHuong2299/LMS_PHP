<?php
/**
 * File: TienDoVideoService.php
 * Mục đích: Xử lý logic nghiệp vụ cho tiến độ video
 */

require_once __DIR__ . '/../co-so/BaseService.php';
require_once __DIR__ . '/../kho-du-lieu/TienDoVideoRepository.php';

class TienDoVideoService extends BaseService {
    
    private $tienDoRepo;
    
    public function __construct() {
        $this->tienDoRepo = new TienDoVideoRepository();
    }
    
    /**
     * Lấy tiến độ video của sinh viên
     */
    public function layTienDoVideo($baiGiangId, $sinhVienId) {
        // Validate
        if (!$this->kiemTraSoNguyen($baiGiangId) || $baiGiangId <= 0) {
            $this->nemLoi('ID bài giảng không hợp lệ');
        }
        
        if (!$this->kiemTraSoNguyen($sinhVienId) || $sinhVienId <= 0) {
            $this->nemLoi('ID sinh viên không hợp lệ');
        }
        
        $tienDo = $this->tienDoRepo->layTienDoVideo($baiGiangId, $sinhVienId);
        
        // Nếu chưa có tiến độ, trả về trạng thái mặc định
        if (!$tienDo) {
            return [
                'trang_thai' => 'chua_xem',
                'thoi_gian_xem' => 0,
                'phan_tram_hoan_thanh' => 0,
                'lan_xem_cuoi' => null
            ];
        }
        
        // Format dữ liệu
        return [
            'id' => (int)$tienDo['id'],
            'trang_thai' => $tienDo['trang_thai'],
            'thoi_gian_xem' => (int)$tienDo['thoi_gian_xem'],
            'phan_tram_hoan_thanh' => (float)$tienDo['phan_tram_hoan_thanh'],
            'lan_xem_cuoi' => $tienDo['lan_xem_cuoi']
        ];
    }
    
    /**
     * Cập nhật tiến độ video
     */
    public function capNhatTienDoVideo($baiGiangId, $sinhVienId, $trangThai, $thoiGianXem = 0, $phanTramHoanThanh = 0) {
        // Validate
        if (!$this->kiemTraSoNguyen($baiGiangId) || $baiGiangId <= 0) {
            $this->nemLoi('ID bài giảng không hợp lệ');
        }
        
        if (!$this->kiemTraSoNguyen($sinhVienId) || $sinhVienId <= 0) {
            $this->nemLoi('ID sinh viên không hợp lệ');
        }
        
        // Validate trạng thái
        $trangThaiHopLe = ['chua_xem', 'dang_xem', 'xem_xong'];
        if (!in_array($trangThai, $trangThaiHopLe)) {
            $this->nemLoi('Trạng thái không hợp lệ');
        }
        
        // Validate thời gian xem
        if (!is_numeric($thoiGianXem) || $thoiGianXem < 0) {
            $thoiGianXem = 0;
        }
        
        // Validate phần trăm
        if (!is_numeric($phanTramHoanThanh) || $phanTramHoanThanh < 0 || $phanTramHoanThanh > 100) {
            $phanTramHoanThanh = 0;
        }
        
        // Tự động set 100% nếu xem xong
        if ($trangThai === 'xem_xong') {
            $phanTramHoanThanh = 100;
        }
        
        // Cập nhật
        $data = [
            'bai_giang_id' => $baiGiangId,
            'sinh_vien_id' => $sinhVienId,
            'trang_thai' => $trangThai,
            'thoi_gian_xem' => (int)$thoiGianXem,
            'phan_tram_hoan_thanh' => (float)$phanTramHoanThanh
        ];
        
        $result = $this->tienDoRepo->capNhatTienDoVideo($data);
        
        if (!$result) {
            $this->nemLoi('Không thể cập nhật tiến độ');
        }
        
        // Lấy tiến độ mới nhất
        return $this->layTienDoVideo($baiGiangId, $sinhVienId);
    }
    
    /**
     * Đánh dấu video đã xem xong
     */
    public function danhDauXemXong($baiGiangId, $sinhVienId) {
        // Validate
        if (!$this->kiemTraSoNguyen($baiGiangId) || $baiGiangId <= 0) {
            $this->nemLoi('ID bài giảng không hợp lệ');
        }
        
        if (!$this->kiemTraSoNguyen($sinhVienId) || $sinhVienId <= 0) {
            $this->nemLoi('ID sinh viên không hợp lệ');
        }
        
        $result = $this->tienDoRepo->danhDauXemXong($baiGiangId, $sinhVienId);
        
        if (!$result) {
            $this->nemLoi('Không thể đánh dấu video đã xem');
        }
        
        return $this->layTienDoVideo($baiGiangId, $sinhVienId);
    }
    
    /**
     * Lấy danh sách tiến độ trong lớp
     */
    public function layTienDoTheoLop($sinhVienId, $lopHocId) {
        // Validate
        if (!$this->kiemTraSoNguyen($sinhVienId) || $sinhVienId <= 0) {
            $this->nemLoi('ID sinh viên không hợp lệ');
        }
        
        if (!$this->kiemTraSoNguyen($lopHocId) || $lopHocId <= 0) {
            $this->nemLoi('ID lớp học không hợp lệ');
        }
        
        $danhSach = $this->tienDoRepo->layTienDoTheoLop($sinhVienId, $lopHocId);
        
        // Format dữ liệu
        foreach ($danhSach as &$item) {
            $item['id'] = (int)$item['id'];
            $item['bai_giang_id'] = (int)$item['bai_giang_id'];
            $item['thoi_gian_xem'] = (int)$item['thoi_gian_xem'];
            $item['phan_tram_hoan_thanh'] = (float)$item['phan_tram_hoan_thanh'];
        }
        
        return $danhSach;
    }
    
    /**
     * Tính phần trăm hoàn thành lớp học
     */
    public function tinhPhanTramLop($sinhVienId, $lopHocId) {
        // Validate
        if (!$this->kiemTraSoNguyen($sinhVienId) || $sinhVienId <= 0) {
            $this->nemLoi('ID sinh viên không hợp lệ');
        }
        
        if (!$this->kiemTraSoNguyen($lopHocId) || $lopHocId <= 0) {
            $this->nemLoi('ID lớp học không hợp lệ');
        }
        
        return $this->tienDoRepo->tinhPhanTramLop($sinhVienId, $lopHocId);
    }
}
