<?php
/**
 * File: SinhVienController.php
 * Mục đích: Controller xử lý các request liên quan đến sinh viên
 */

require_once __DIR__ . '/../co-so/BaseController.php';
require_once __DIR__ . '/../dich-vu/LopHocService.php';
require_once __DIR__ . '/../dich-vu/BaiGiangService.php';
require_once __DIR__ . '/../dich-vu/TienDoHocTapService.php';
require_once __DIR__ . '/../dich-vu/BaiTapService.php';
require_once __DIR__ . '/../dich-vu/BinhLuanService.php';
require_once __DIR__ . '/../dich-vu/TienDoVideoService.php';
require_once __DIR__ . '/../dich-vu/DiemService.php';
require_once __DIR__ . '/../dich-vu/TaiLieuService.php';
require_once __DIR__ . '/../dich-vu/BaiKiemTraService.php';
require_once __DIR__ . '/../kho-du-lieu/SinhVienRepository.php';
require_once __DIR__ . '/../kho-du-lieu/BaiGiangRepository.php';

class SinhVienController extends BaseController {
    
    private $lopHocService;
    private $baiGiangService;
    private $tienDoService;
    private $baiTapService;
    private $sinhVienRepo;
    private $binhLuanService;
    private $tienDoVideoService;
    private $baiGiangRepo;
    private $diemService;
    private $taiLieuService;
    private $baiKiemTraService;
    
    public function __construct() {
        $this->lopHocService = new LopHocService();
        $this->baiGiangService = new BaiGiangService();
        $this->tienDoService = new TienDoHocTapService();
        $this->baiTapService = new BaiTapService();
        $this->sinhVienRepo = new SinhVienRepository();
        $this->binhLuanService = new BinhLuanService();
        $this->tienDoVideoService = new TienDoVideoService();
        $this->baiGiangRepo = new BaiGiangRepository();
        $this->diemService = new DiemService();
        $this->taiLieuService = new TaiLieuService();
        $this->baiKiemTraService = new BaiKiemTraService();
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
    
    /**
     * API: Lấy danh sách tất cả chương + tiến độ
     * Method: GET
     * Params: lop_hoc_id
     */
    public function layDanhSachChuong() {
        try {
            $sinhVienId = $this->kiemTraQuyenSinhVien();
            
            $lopHocId = $this->layThamSoGetInt('lop_hoc_id');
            
            if ($lopHocId <= 0) {
                throw new Exception('Tham số không hợp lệ');
            }
            
            // Verify sinh viên đăng ký lớp này
            if (!$this->sinhVienRepo->kiemTraSinhVienTrongLop($sinhVienId, $lopHocId)) {
                throw new Exception('Bạn không có quyền truy cập lớp học này');
            }
            
            // Lấy dữ liệu từ service
            $duLieu = $this->baiGiangService->layDanhSachChuong($lopHocId, $sinhVienId);
            
            $this->traVeThanhCong($duLieu, 'Lấy danh sách chương thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi layDanhSachChuong: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy danh sách bài học trong chương
     * Method: GET
     * Params: chuong_id, lop_hoc_id
     */
    public function layDanhSachBaiHoc() {
        try {
            $sinhVienId = $this->kiemTraQuyenSinhVien();
            
            $chuongId = $this->layThamSoGetInt('chuong_id');
            $lopHocId = $this->layThamSoGetInt('lop_hoc_id');
            
            if ($chuongId <= 0 || $lopHocId <= 0) {
                throw new Exception('Tham số không hợp lệ');
            }
            
            // Verify sinh viên
            if (!$this->sinhVienRepo->kiemTraSinhVienTrongLop($sinhVienId, $lopHocId)) {
                throw new Exception('Bạn không có quyền truy cập');
            }
            
            $duLieu = $this->baiGiangService->layDanhSachBaiHoc(
                $chuongId, 
                $lopHocId, 
                $sinhVienId
            );
            
            $this->traVeThanhCong($duLieu, 'Lấy danh sách bài học thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi layDanhSachBaiHoc: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy chi tiết bài tập + câu hỏi + bài làm đã lưu
     * Method: GET
     * Params: bai_tap_id
     */
    public function layChiTietBaiTap() {
        try {
            $sinhVienId = $this->kiemTraQuyenSinhVien();
            
            $baiTapId = $this->layThamSoGetInt('bai_tap_id');
            
            if ($baiTapId <= 0) {
                throw new Exception('Tham số bai_tap_id không hợp lệ');
            }
            
            // Lấy dữ liệu từ service (đã kiểm tra quyền truy cập bên trong)
            $duLieu = $this->baiTapService->layChiTietBaiTapChoSinhVien($baiTapId, $sinhVienId);
            
            $this->traVeThanhCong($duLieu, 'Lấy chi tiết bài tập thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi layChiTietBaiTap: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lưu trả lời bài tập (auto-save)
     * Method: POST
     * Body: { bai_tap_id, tra_loi: [{cau_hoi_id, noi_dung_tra_loi}] }
     */
    public function luuTraLoiBaiTap() {
        try {
            $sinhVienId = $this->kiemTraQuyenSinhVien();
            
            // Đọc dữ liệu từ request body
            $duLieuNhap = json_decode(file_get_contents('php://input'), true);
            
            if (!$duLieuNhap) {
                throw new Exception('Dữ liệu không hợp lệ');
            }
            
            $baiTapId = $duLieuNhap['bai_tap_id'] ?? 0;
            $traLoiList = $duLieuNhap['tra_loi'] ?? [];
            
            if ($baiTapId <= 0) {
                throw new Exception('Tham số bai_tap_id không hợp lệ');
            }
            
            if (!is_array($traLoiList)) {
                throw new Exception('Dữ liệu tra_loi phải là mảng');
            }
            
            // Gọi service để lưu
            $ketQua = $this->baiTapService->luuTraLoiBaiTap($baiTapId, $sinhVienId, $traLoiList);
            
            $this->traVeThanhCong($ketQua, 'Lưu trả lời thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi luuTraLoiBaiTap: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Nộp bài tập (final submit)
     * Method: POST
     * Body: { bai_tap_id }
     */
    public function nopBaiTap() {
        try {
            $sinhVienId = $this->kiemTraQuyenSinhVien();
            
            // Đọc dữ liệu từ request body
            $duLieuNhap = json_decode(file_get_contents('php://input'), true);
            
            if (!$duLieuNhap) {
                throw new Exception('Dữ liệu không hợp lệ');
            }
            
            $baiTapId = $duLieuNhap['bai_tap_id'] ?? 0;
            
            if ($baiTapId <= 0) {
                throw new Exception('Tham số bai_tap_id không hợp lệ');
            }
            
            // Gọi service để nộp bài
            $ketQua = $this->baiTapService->nopBaiTap($baiTapId, $sinhVienId);
            
            $this->traVeThanhCong($ketQua, 'Nộp bài tập thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi nopBaiTap: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy chi tiết bài giảng + thông tin chương
     * Method: GET
     * Params: bai_giang_id, lop_hoc_id
     */
    public function layChiTietBaiGiang() {
        try {
            $sinhVienId = $this->kiemTraQuyenSinhVien();
            
            $baiGiangId = $this->layThamSoGetInt('bai_giang_id');
            $lopHocId = $this->layThamSoGetInt('lop_hoc_id');
            
            if ($baiGiangId <= 0 || $lopHocId <= 0) {
                throw new Exception('Tham số không hợp lệ');
            }
            
            // Verify sinh viên trong lớp
            if (!$this->sinhVienRepo->kiemTraSinhVienTrongLop($sinhVienId, $lopHocId)) {
                throw new Exception('Bạn không có quyền truy cập');
            }
            
            // Lấy thông tin bài giảng
            $baiGiang = $this->baiGiangRepo->layBaiGiangTheoId($baiGiangId);
            
            if (!$baiGiang) {
                throw new Exception('Không tìm thấy bài giảng');
            }
            
            // Lấy thông tin chương
            $chuong = $this->baiGiangRepo->layThongTinChuong($baiGiang['chuong_id']);
            
            // Lấy tiến độ video
            $tienDoVideo = $this->tienDoVideoService->layTienDoVideo($baiGiangId, $sinhVienId);
            
            $duLieu = [
                'bai_giang' => $baiGiang,
                'thong_tin_chuong' => $chuong,
                'tien_do' => $tienDoVideo
            ];
            
            $this->traVeThanhCong($duLieu, 'Lấy chi tiết bài giảng thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi layChiTietBaiGiang: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy danh sách bình luận bài giảng
     * Method: GET
     * Params: bai_giang_id, page (optional), limit (optional)
     */
    public function layBinhLuanBaiGiang() {
        try {
            $sinhVienId = $this->kiemTraQuyenSinhVien();
            
            $baiGiangId = $this->layThamSoGetInt('bai_giang_id');
            $page = $this->layThamSoGetInt('page', 1);
            $limit = $this->layThamSoGetInt('limit', 20);
            
            if ($baiGiangId <= 0) {
                throw new Exception('Tham số bai_giang_id không hợp lệ');
            }
            
            // Lấy dữ liệu từ service
            $duLieu = $this->binhLuanService->layDanhSachBinhLuan($baiGiangId, $page, $limit);
            
            $this->traVeThanhCong($duLieu, 'Lấy bình luận thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi layBinhLuanBaiGiang: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Thêm bình luận bài giảng
     * Method: POST
     * Body: { bai_giang_id, noi_dung, binh_luan_cha_id (optional) }
     */
    public function themBinhLuanBaiGiang() {
        try {
            $sinhVienId = $this->kiemTraQuyenSinhVien();
            
            $duLieuNhap = $this->layDuLieuJSON();
            
            $baiGiangId = $duLieuNhap['bai_giang_id'] ?? 0;
            $noiDung = $duLieuNhap['noi_dung'] ?? '';
            $binhLuanChaId = $duLieuNhap['binh_luan_cha_id'] ?? null;
            
            if ($baiGiangId <= 0) {
                throw new Exception('Tham số bai_giang_id không hợp lệ');
            }
            
            // Gọi service để thêm bình luận
            $binhLuanMoi = $this->binhLuanService->themBinhLuan(
                $baiGiangId, 
                $sinhVienId, 
                $noiDung, 
                $binhLuanChaId
            );
            
            $this->traVeThanhCong($binhLuanMoi, 'Thêm bình luận thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi themBinhLuanBaiGiang: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Cập nhật tiến độ video
     * Method: POST
     * Body: { bai_giang_id, trang_thai, thoi_gian_xem, phan_tram_hoan_thanh }
     */
    public function capNhatTienDoVideo() {
        try {
            $sinhVienId = $this->kiemTraQuyenSinhVien();
            
            $duLieuNhap = $this->layDuLieuJSON();
            
            $baiGiangId = $duLieuNhap['bai_giang_id'] ?? 0;
            $trangThai = $duLieuNhap['trang_thai'] ?? 'chua_xem';
            $thoiGianXem = $duLieuNhap['thoi_gian_xem'] ?? 0;
            $phanTramHoanThanh = $duLieuNhap['phan_tram_hoan_thanh'] ?? 0;
            
            if ($baiGiangId <= 0) {
                throw new Exception('Tham số bai_giang_id không hợp lệ');
            }
            
            // Gọi service để cập nhật
            $tienDoMoi = $this->tienDoVideoService->capNhatTienDoVideo(
                $baiGiangId, 
                $sinhVienId, 
                $trangThai, 
                $thoiGianXem, 
                $phanTramHoanThanh
            );
            
            $this->traVeThanhCong($tienDoMoi, 'Cập nhật tiến độ thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi capNhatTienDoVideo: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy điểm theo chương
     * Method: GET
     * Params: lop_hoc_id (required), chuong_id (optional, default: all)
     */
    public function layDiemTheoChuong() {
        try {
            // Kiểm tra quyền sinh viên
            $sinhVienId = $this->kiemTraQuyenSinhVien();
            
            // Lấy parameters
            $lopHocId = $_GET['lop_hoc_id'] ?? null;
            $chuongId = $_GET['chuong_id'] ?? 'all';
            
            if (!$lopHocId) {
                $this->traVeLoi('Thiếu lop_hoc_id');
                return;
            }
            
            // Lấy dữ liệu điểm
            $ketQua = $this->diemService->layDiemTheoChuong(
                $lopHocId,
                $sinhVienId,
                $chuongId === 'all' ? null : $chuongId
            );
            
            $this->traVeThanhCong($ketQua, 'Lấy điểm thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi layDiemTheoChuong: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy danh sách tài liệu
     * Method: GET
     * Params: lop_hoc_id (required)
     */
    public function layDanhSachTaiLieu() {
        try {
            // Kiểm tra quyền sinh viên
            $sinhVienId = $this->kiemTraQuyenSinhVien();
            
            // Lấy parameters
            $lopHocId = $_GET['lop_hoc_id'] ?? null;
            
            if (!$lopHocId) {
                $this->traVeLoi('Thiếu lop_hoc_id');
                return;
            }
            
            // Lấy danh sách tài liệu
            $danhSachTaiLieu = $this->taiLieuService->layDanhSachTaiLieu($lopHocId);
            
            $this->traVeThanhCong($danhSachTaiLieu, 'Lấy danh sách tài liệu thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi layDanhSachTaiLieu: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy thông báo trong (hoạt động)
     * Method: GET
     */
    public function layThongBaoTrong() {
        try {
            // Kiểm tra quyền sinh viên
            $sinhVienId = $this->kiemTraQuyenSinhVien();
            
            // Lấy danh sách thông báo trong (hoạt động)
            $thongBaoList = $this->sinhVienRepo->getThongBaoTrong($sinhVienId);
            
            $this->traVeThanhCong([
                'thong_bao_list' => $thongBaoList
            ], 'Lấy thông báo thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi layThongBaoTrong: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy danh sách bài kiểm tra của lớp học
     * Method: GET
     * Params: lop_hoc_id
     */
    public function layDanhSachBaiKiemTra() {
        try {
            $sinhVienId = $this->kiemTraQuyenSinhVien();
            $lopHocId = $this->layThamSoGetInt('lop_hoc_id');
            
            $duLieu = $this->baiKiemTraService->layDanhSachChoSinhVien($lopHocId, $sinhVienId);
            
            $this->traVeThanhCong($duLieu, 'Lấy danh sách bài kiểm tra thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi layDanhSachBaiKiemTra: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy chi tiết bài kiểm tra
     * Method: GET
     * Params: bai_kiem_tra_id
     */
    public function layChiTietBaiKiemTra() {
        try {
            $sinhVienId = $this->kiemTraQuyenSinhVien();
            $baiKiemTraId = $this->layThamSoGetInt('bai_kiem_tra_id');
            
            $duLieu = $this->baiKiemTraService->layChiTietChoSinhVien($baiKiemTraId, $sinhVienId);
            
            $this->traVeThanhCong($duLieu, 'Lấy chi tiết bài kiểm tra thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi layChiTietBaiKiemTra: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Bắt đầu làm bài kiểm tra
     * Method: POST
     * Body: bai_kiem_tra_id
     */
    public function batDauBaiKiemTra() {
        try {
            $sinhVienId = $this->kiemTraQuyenSinhVien();
            $baiKiemTraId = $this->layThamSoJsonInt('bai_kiem_tra_id');
            
            $duLieu = $this->baiKiemTraService->batDauLamBai($baiKiemTraId, $sinhVienId);
            
            $this->traVeThanhCong($duLieu, $duLieu['thong_bao']);
            
        } catch (Exception $e) {
            error_log("Lỗi batDauBaiKiemTra: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lưu câu trả lời
     * Method: POST
     * Body: bai_lam_id, cau_hoi_id, lua_chon_id
     */
    public function luuTraLoiKiemTra() {
        try {
            $sinhVienId = $this->kiemTraQuyenSinhVien();
            
            $baiLamId = $this->layThamSoJsonInt('bai_lam_id');
            $cauHoiId = $this->layThamSoJsonInt('cau_hoi_id');
            $luaChonId = $this->layThamSoJsonInt('lua_chon_id');
            
            $duLieu = $this->baiKiemTraService->luuTraLoi($baiLamId, $cauHoiId, $luaChonId, $sinhVienId);
            
            $this->traVeThanhCong($duLieu, 'Lưu câu trả lời thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi luuTraLoiKiemTra: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Nộp bài kiểm tra
     * Method: POST
     * Body: bai_lam_id
     */
    public function nopBaiKiemTra() {
        try {
            $sinhVienId = $this->kiemTraQuyenSinhVien();
            $baiLamId = $this->layThamSoJsonInt('bai_lam_id');
            
            $duLieu = $this->baiKiemTraService->nopBai($baiLamId, $sinhVienId);
            
            $this->traVeThanhCong($duLieu, 'Nộp bài kiểm tra thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi nopBaiKiemTra: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Xem kết quả bài kiểm tra
     * Method: GET
     * Params: bai_lam_id
     */
    public function xemKetQuaBaiKiemTra() {
        try {
            $sinhVienId = $this->kiemTraQuyenSinhVien();
            $baiLamId = $this->layThamSoGetInt('bai_lam_id');
            
            $duLieu = $this->baiKiemTraService->xemKetQua($baiLamId, $sinhVienId);
            
            $this->traVeThanhCong($duLieu, 'Lấy kết quả bài kiểm tra thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi xemKetQuaBaiKiemTra: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy bình luận của câu hỏi trong bài tập
     * Method: GET
     * Params: bai_tap_id, cau_hoi_id
     */
    public function layBinhLuanCauHoi() {
        try {
            $sinhVienId = $this->kiemTraQuyenSinhVien();
            
            $baiTapId = $this->layThamSoGetInt('bai_tap_id');
            $cauHoiId = $this->layThamSoGetInt('cau_hoi_id');
            
            if ($baiTapId <= 0 || $cauHoiId <= 0) {
                throw new Exception('Tham số không hợp lệ');
            }
            
            // Lấy bình luận
            $binhLuan = $this->baiTapService->layBinhLuanCauHoi($baiTapId, $cauHoiId, $sinhVienId);
            
            $this->traVeThanhCong($binhLuan, 'Lấy bình luận thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi layBinhLuanCauHoi: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Thêm bình luận cho câu hỏi
     * Method: POST
     * Body: { bai_tap_id, cau_hoi_id, noi_dung }
     */
    public function themBinhLuanCauHoi() {
        try {
            $sinhVienId = $this->kiemTraQuyenSinhVien();
            
            // Đọc dữ liệu từ request body
            $duLieuNhap = json_decode(file_get_contents('php://input'), true);
            
            if (!$duLieuNhap) {
                throw new Exception('Dữ liệu không hợp lệ');
            }
            
            $baiTapId = $duLieuNhap['bai_tap_id'] ?? 0;
            $cauHoiId = $duLieuNhap['cau_hoi_id'] ?? 0;
            $noiDung = trim($duLieuNhap['noi_dung'] ?? '');
            
            if ($baiTapId <= 0 || $cauHoiId <= 0) {
                throw new Exception('Tham số không hợp lệ');
            }
            
            if (empty($noiDung)) {
                throw new Exception('Nội dung bình luận không được để trống');
            }
            
            // Thêm bình luận
            $ketQua = $this->baiTapService->themBinhLuanCauHoi($baiTapId, $cauHoiId, $sinhVienId, $noiDung);
            
            $this->traVeThanhCong($ketQua, 'Thêm bình luận thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi themBinhLuanCauHoi: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
}

