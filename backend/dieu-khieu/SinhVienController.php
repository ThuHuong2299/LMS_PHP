<?php
/**
 * File: SinhVienController.php
 * Mục đích: Controller xử lý các request liên quan đến sinh viên
 */

require_once __DIR__ . '/../co-so/BaseController.php';
require_once __DIR__ . '/../dich-vu/LopHocService.php';
require_once __DIR__ . '/../dich-vu/TienDoHocTapService.php';
require_once __DIR__ . '/../kho-du-lieu/SinhVienRepository.php';

class SinhVienController extends BaseController {
    
    private $lopHocService;
    private $tienDoService;
    private $sinhVienRepo;
    
    public function __construct() {
        $this->lopHocService = new LopHocService();
        $this->tienDoService = new TienDoHocTapService();
        $this->sinhVienRepo = new SinhVienRepository();
    }
    
    /**
     * API: Lấy dashboard trang chủ sinh viên
     * Method: GET
     * Endpoint: /api/sinh-vien/trang-chu-dashboard
     */
    public function layDashboardTrangChu() {
        try {
            // Kiểm tra quyền sinh viên
            $sinhVienId = $this->kiemTraQuyenSinhVien();
            
            // Lấy thông tin sinh viên
            $thongTinSinhVien = $this->sinhVienRepo->getThongTinSinhVien($sinhVienId);
            
            if (!$thongTinSinhVien) {
                $this->traVeLoi('Không tìm thấy thông tin sinh viên');
            }
            
            // Lấy danh sách lớp học
            $danhSachLopHoc = $this->sinhVienRepo->getDanhSachLopHoc($sinhVienId);
            
            // Bổ sung phần trăm hoàn thành cho mỗi lớp
            foreach ($danhSachLopHoc as &$lop) {
                $phanTram = $this->tienDoService->layPhanTramHoanThanhLopHoc(
                    $sinhVienId, 
                    $lop['lop_hoc_id']
                );
                $lop['phan_tram_hoan_thanh'] = $phanTram;
            }
            
            // Lấy nhắc nhở sắp hết hạn (top 5)
            $nhacNhoHetHan = $this->sinhVienRepo->getNhacNhoSapHetHan($sinhVienId, 5);
            
            // Lấy thêm thông báo mới nhất (để có thể kết hợp nếu cần)
            $thongBaoMoi = $this->sinhVienRepo->getThongBaoMoiNhat($sinhVienId, 3);
            
            // Kết hợp nhắc nhở: deadline sắp tới + thông báo mới (lấy top 3 từ tất cả)
            $allReminders = array_merge($nhacNhoHetHan, $thongBaoMoi);
            
            // Sắp xếp theo deadline (ưu tiên deadline gần nhất)
            usort($allReminders, function($a, $b) {
                $timeA = strtotime($a['deadline']);
                $timeB = strtotime($b['deadline']);
                return $timeA - $timeB;
            });
            
            // Lấy top 3
            $nhacNhoTop3 = array_slice($allReminders, 0, 3);
            
            // Chuẩn bị dữ liệu trả về
            $duLieu = [
                'thong_tin_sinh_vien' => [
                    'id' => $thongTinSinhVien['id'],
                    'ma_nguoi_dung' => $thongTinSinhVien['ma_nguoi_dung'],
                    'ho_ten' => $thongTinSinhVien['ho_ten'],
                    'email' => $thongTinSinhVien['email'],
                    'anh_dai_dien' => $thongTinSinhVien['anh_dai_dien']
                ],
                'danh_sach_lop_hoc' => $danhSachLopHoc,
                'nhac_nho_san_co' => $nhacNhoTop3
            ];
            
            // Trả về kết quả
            $this->traVeThanhCong($duLieu, 'Lấy dữ liệu trang chủ thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi trong layDashboardTrangChu: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
}
