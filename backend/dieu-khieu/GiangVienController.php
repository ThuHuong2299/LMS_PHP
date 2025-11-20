<?php
/**
 * File: GiangVienController.php
 * Mục đích: Controller xử lý các request liên quan đến giảng viên
 */

require_once __DIR__ . '/../co-so/BaseController.php';
require_once __DIR__ . '/../dich-vu/LopHocService.php';
require_once __DIR__ . '/../dich-vu/ThongKeService.php';
require_once __DIR__ . '/../dich-vu/HoatDongService.php';
require_once __DIR__ . '/../dich-vu/BaiGiangService.php';
require_once __DIR__ . '/../dich-vu/BaiTapService.php';
require_once __DIR__ . '/../dich-vu/BaiKiemTraService.php';
require_once __DIR__ . '/../dich-vu/ThongBaoService.php';
require_once __DIR__ . '/../dich-vu/SinhVienService.php';
require_once __DIR__ . '/../dich-vu/TaiLieuService.php';
require_once __DIR__ . '/../dich-vu/BinhLuanService.php';
require_once __DIR__ . '/../kho-du-lieu/BaiGiangRepository.php';

class GiangVienController extends BaseController {
    
    private $lopHocService;
    private $thongKeService;
    private $hoatDongService;
    private $baiGiangService;
    private $baiTapService;
    private $baiKiemTraService;
    private $thongBaoService;
    private $sinhVienService;
    private $taiLieuService;
    private $baiGiangRepo;
    private $binhLuanService;
    
    public function __construct() {
        $this->lopHocService = new LopHocService();
        $this->thongKeService = new ThongKeService();
        $this->hoatDongService = new HoatDongService();
        $this->baiGiangService = new BaiGiangService();
        $this->baiTapService = new BaiTapService();
        $this->baiKiemTraService = new BaiKiemTraService();
        $this->thongBaoService = new ThongBaoService();
        $this->sinhVienService = new SinhVienService();
        $this->taiLieuService = new TaiLieuService();
        $this->baiGiangRepo = new BaiGiangRepository();
        $this->binhLuanService = new BinhLuanService();
    }
    
    /**
     * API: Lấy danh sách lớp học
     * Method: GET
     * Endpoint: /api/giang-vien/danh-sach-lop-hoc
     */
    public function layDanhSachLopHoc() {
        try {
            // Kiểm tra quyền giảng viên
            $giangVienId = $this->kiemTraQuyenGiangVien();
            
            // Lấy dữ liệu
            $danhSach = $this->lopHocService->layDanhSachCuaGiangVien($giangVienId);
            
            // Trả về kết quả
            $this->traVeThanhCong($danhSach, 'Lấy danh sách lớp học thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi trong layDanhSachLopHoc: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy thống kê dashboard
     * Method: GET
     * Endpoint: /api/giang-vien/thong-ke-dashboard
     */
    public function layThongKeDashboard() {
        try {
            // Kiểm tra quyền giảng viên
            $giangVienId = $this->kiemTraQuyenGiangVien();
            
            // Lấy dữ liệu thống kê
            $thongKe = $this->thongKeService->layThongKeDashboard($giangVienId);
            
            // Trả về kết quả
            $this->traVeThanhCong($thongKe, 'Lấy thống kê thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi trong layThongKeDashboard: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy hoạt động gần đây
     * Method: GET
     * Endpoint: /api/giang-vien/hoat-dong-gan-day
     */
    public function layHoatDongGanDay() {
        try {
            // Kiểm tra quyền giảng viên
            $giangVienId = $this->kiemTraQuyenGiangVien();
            
            // Lấy tham số limit
            $limit = $this->layThamSoGetInt('limit', 10);
            
            // Lấy dữ liệu hoạt động
            $hoatDong = $this->hoatDongService->layHoatDongGanDay($giangVienId, $limit);
            
            // Trả về kết quả
            $this->traVeThanhCong($hoatDong, 'Lấy hoạt động gần đây thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi trong layHoatDongGanDay: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy thông tin lớp và bài giảng
     * Method: GET
     * Endpoint: /api/giang-vien/bai-giang-lop-hoc?id={lop_hoc_id}
     */
    public function layBaiGiangLopHoc() {
        try {
            $giangVienId = $this->kiemTraQuyenGiangVien();
            $lopHocId = $this->layThamSoGetInt('id');
            
            $duLieu = $this->baiGiangService->layBaiGiangTheoLop($lopHocId, $giangVienId);
            
            $this->traVeThanhCong($duLieu, 'Lấy danh sách bài giảng thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi trong layBaiGiangLopHoc: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy danh sách bài tập
     * Method: GET
     * Endpoint: /api/giang-vien/bai-tap-lop-hoc?id={lop_hoc_id}
     */
    public function layBaiTapLopHoc() {
        try {
            $giangVienId = $this->kiemTraQuyenGiangVien();
            $lopHocId = $this->layThamSoGetInt('id');
            
            $duLieu = $this->baiTapService->layBaiTapTheoLop($lopHocId, $giangVienId);
            
            $this->traVeThanhCong($duLieu, 'Lấy danh sách bài tập thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi trong layBaiTapLopHoc: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy chi tiết bài tập kèm thống kê và danh sách sinh viên
     * Method: GET
     * Endpoint: /api/giang-vien/chi-tiet-bai-tap?bai_tap_id={id}
     */
    public function layChiTietBaiTap() {
        try {
            $giangVienId = $this->kiemTraQuyenGiangVien();
            $baiTapId = $this->layThamSoGetInt('bai_tap_id');
            
            $duLieu = $this->baiTapService->layChiTietBaiTap($baiTapId, $giangVienId);
            
            $this->traVeThanhCong($duLieu, 'Lấy chi tiết bài tập thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi trong layChiTietBaiTap: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy chi tiết bài làm của sinh viên
     * Method: GET
     * Endpoint: /api/giang-vien/chi-tiet-bai-lam?bai_tap_id={id}&sinh_vien_id={id}
     */
    public function layChiTietBaiLamSinhVien() {
        try {
            $giangVienId = $this->kiemTraQuyenGiangVien();
            $baiTapId = $this->layThamSoGetInt('bai_tap_id');
            $sinhVienId = $this->layThamSoGetInt('sinh_vien_id');
            
            $duLieu = $this->baiTapService->layChiTietBaiLamSinhVien($baiTapId, $sinhVienId, $giangVienId);
            
            $this->traVeThanhCong($duLieu, 'Lấy chi tiết bài làm thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi trong layChiTietBaiLamSinhVien: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy danh sách bài kiểm tra
     * Method: GET
     * Endpoint: /api/giang-vien/bai-kiem-tra-lop-hoc?id={lop_hoc_id}
     */
    public function layBaiKiemTraLopHoc() {
        try {
            $giangVienId = $this->kiemTraQuyenGiangVien();
            $lopHocId = $this->layThamSoGetInt('id');
            
            $duLieu = $this->baiKiemTraService->layBaiKiemTraTheoLop($lopHocId, $giangVienId);
            
            $this->traVeThanhCong($duLieu, 'Lấy danh sách bài kiểm tra thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi trong layBaiKiemTraLopHoc: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy danh sách thông báo
     * Method: GET
     * Endpoint: /api/giang-vien/thong-bao-lop-hoc?id={lop_hoc_id}
     */
    public function layThongBaoLopHoc() {
        try {
            $giangVienId = $this->kiemTraQuyenGiangVien();
            $lopHocId = $this->layThamSoGetInt('id');
            
            $duLieu = $this->thongBaoService->layThongBaoTheoLop($lopHocId, $giangVienId);
            
            $this->traVeThanhCong($duLieu, 'Lấy danh sách thông báo thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi trong layThongBaoLopHoc: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy danh sách sinh viên với phân trang
     * Method: GET
     * Endpoint: /api/giang-vien/sinh-vien-lop-hoc?id={lop_hoc_id}&page=1&limit=5
     */
    public function laySinhVienLopHoc() {
        try {
            $giangVienId = $this->kiemTraQuyenGiangVien();
            $lopHocId = $this->layThamSoGetInt('id');
            $page = $this->layThamSoGetInt('page', 1);
            $limit = $this->layThamSoGetInt('limit', 5);
            
            $duLieu = $this->sinhVienService->laySinhVienTheoLop($lopHocId, $giangVienId, $page, $limit);
            
            $this->traVeThanhCong($duLieu, 'Lấy danh sách sinh viên thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi trong laySinhVienLopHoc: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy danh sách tài liệu
     * Method: GET
     * Endpoint: /api/giang-vien/tai-lieu-lop-hoc?id={lop_hoc_id}
     */
    public function layTaiLieuLopHoc() {
        try {
            $giangVienId = $this->kiemTraQuyenGiangVien();
            $lopHocId = $this->layThamSoGetInt('id');
            
            $duLieu = $this->taiLieuService->layDanhSachTaiLieu($lopHocId, $giangVienId);
            
            $this->traVeThanhCong($duLieu, 'Lấy danh sách tài liệu thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi trong layTaiLieuLopHoc: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Tạo thông báo mới
     * Method: POST
     * Endpoint: /api/giang-vien/tao-thong-bao
     * Body: {lop_hoc_id, tieu_de, noi_dung}
     */
    public function taoThongBao() {
        try {
            // Kiểm tra quyền giảng viên
            $giangVienId = $this->kiemTraQuyenGiangVien();
            
            // Lấy dữ liệu từ request
            $duLieu = $this->layDuLieuJson();
            
            $lopHocId = $duLieu['lop_hoc_id'] ?? null;
            $tieuDe = $duLieu['tieu_de'] ?? '';
            $noiDung = $duLieu['noi_dung'] ?? '';
            
            // Tạo thông báo
            $ketQua = $this->thongBaoService->taoThongBao($lopHocId, $giangVienId, $tieuDe, $noiDung);
            
            $this->traVeThanhCong(['id' => $ketQua['id']], $ketQua['thong_bao']);
            
        } catch (Exception $e) {
            error_log("Lỗi trong taoThongBao: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy danh sách chương theo lớp
     * Method: GET
     * Endpoint: /api/giang-vien/chuong-theo-lop?lop_hoc_id={id}
     */
    public function layChuongTheoLop() {
        try {
            $giangVienId = $this->kiemTraQuyenGiangVien();
            $lopHocId = $this->layThamSoGetInt('lop_hoc_id');
            
            $danhSach = $this->baiGiangService->layChuongTheoLopGiangVien($lopHocId, $giangVienId);
            
            $this->traVeThanhCong($danhSach, 'Lấy danh sách chương thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi trong layChuongTheoLop: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Tạo bài tập mới
     * Method: POST
     * Endpoint: /api/giang-vien/tao-bai-tap
     * Body: {lop_hoc_id, chuong_id, tieu_de, han_nop, cau_hoi: [{noi_dung, mo_ta, diem}]}
     */
    public function taoBaiTap() {
        try {
            $giangVienId = $this->kiemTraQuyenGiangVien();
            $duLieu = $this->layDuLieuJson();
            
            $lopHocId = $duLieu['lop_hoc_id'] ?? null;
            $chuongId = $duLieu['chuong_id'] ?? null;
            $tieuDe = $duLieu['tieu_de'] ?? '';
            $hanNop = $duLieu['han_nop'] ?? '';
            $cauHoiList = $duLieu['cau_hoi'] ?? [];
            
            $ketQua = $this->baiTapService->taoBaiTap(
                $lopHocId,
                $giangVienId,
                $chuongId,
                $tieuDe,
                $hanNop,
                $cauHoiList
            );
            
            $this->traVeThanhCong(['id' => $ketQua['id']], $ketQua['thong_bao']);
            
        } catch (Exception $e) {
            error_log("Lỗi trong taoBaiTap: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Tạo bài kiểm tra mới
     * Method: POST
     * Endpoint: /api/giang-vien/tao-bai-kiem-tra
     * Body: {lop_hoc_id, chuong_id, tieu_de, thoi_luong, thoi_gian_bat_dau, cau_hoi: [{noi_dung_cau_hoi, diem, cac_lua_chon}]}
     */
    public function taoBaiKiemTra() {
        try {
            $giangVienId = $this->kiemTraQuyenGiangVien();
            $duLieu = $this->layDuLieuJson();
            
            $ketQua = $this->baiKiemTraService->taoBaiKiemTra($duLieu, $giangVienId);
            
            $this->traVeThanhCong(
                ['id' => $ketQua['id']], 
                'Tạo bài kiểm tra thành công với tổng điểm: ' . $ketQua['tong_diem']
            );
            
        } catch (Exception $e) {
            error_log("Lỗi trong taoBaiKiemTra: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy chi tiết bài kiểm tra
     * Method: GET
     * Endpoint: /teacher/api/lay-chi-tiet-bai-kiem-tra.php
     */
    public function layChiTietBaiKiemTra() {
        try {
            $giangVienId = $this->kiemTraQuyenGiangVien();
            $baiKiemTraId = $this->layThamSoGetInt('bai_kiem_tra_id');
            
            $duLieu = $this->baiKiemTraService->layChiTietBaiKiemTra($baiKiemTraId, $giangVienId);
            
            $this->traVeThanhCong($duLieu, 'Lấy chi tiết bài kiểm tra thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi trong layChiTietBaiKiemTra: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Chấm điểm câu hỏi
     * Method: POST
     * Endpoint: /teacher/api/cham-diem-cau-hoi.php
     */
    public function chamDiemCauHoi($traLoiId, $diem) {
        try {
            $giangVienId = $this->kiemTraQuyenGiangVien();
            
            // Gọi service để chấm điểm
            $ketQua = $this->baiTapService->chamDiemCauHoi($traLoiId, $diem, $giangVienId);
            
            return [
                'thanh_cong' => true,
                'thong_bao' => 'Đã lưu điểm thành công',
                'du_lieu' => $ketQua
            ];
            
        } catch (Exception $e) {
            error_log("Lỗi trong chamDiemCauHoi: " . $e->getMessage());
            return [
                'thanh_cong' => false,
                'thong_bao' => $e->getMessage()
            ];
        }
    }
    
    /**
     * API: Lấy danh sách bình luận theo câu hỏi
     * Method: GET
     * Endpoint: /teacher/api/binh-luan-cau-hoi.php?bai_tap_id={id}&sinh_vien_id={id}&cau_hoi_id={id}
     */
    public function layBinhLuanCauHoi() {
        try {
            $giangVienId = $this->kiemTraQuyenGiangVien();
            $baiTapId = $this->layThamSoGetInt('bai_tap_id');
            $sinhVienId = $this->layThamSoGetInt('sinh_vien_id');
            $cauHoiId = $this->layThamSoGetInt('cau_hoi_id');
            
            $duLieu = $this->baiTapService->layBinhLuanCauHoiGiangVien($baiTapId, $sinhVienId, $cauHoiId, $giangVienId);
            
            $this->traVeThanhCong($duLieu, 'Lấy danh sách bình luận thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi trong layBinhLuanCauHoi: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Gửi bình luận mới
     * Method: POST
     * Endpoint: /teacher/api/binh-luan-cau-hoi.php
     */
    public function guiBinhLuanCauHoi() {
        try {
            $giangVienId = $this->kiemTraQuyenGiangVien();
            
            // Lấy dữ liệu từ request body
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['bai_tap_id']) || !isset($input['sinh_vien_id']) || 
                !isset($input['cau_hoi_id']) || !isset($input['noi_dung'])) {
                $this->traVeLoi('Thiếu thông tin bắt buộc');
                return;
            }
            
            $baiTapId = (int)$input['bai_tap_id'];
            $sinhVienId = (int)$input['sinh_vien_id'];
            $cauHoiId = (int)$input['cau_hoi_id'];
            $noiDung = trim($input['noi_dung']);
            
            $ketQua = $this->baiTapService->guiBinhLuanCauHoi(
                $baiTapId, 
                $sinhVienId, 
                $cauHoiId, 
                $noiDung, 
                $giangVienId
            );
            
            $this->traVeThanhCong($ketQua, 'Đã gửi bình luận thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi trong guiBinhLuanCauHoi: " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy chi tiết bài giảng (cho giảng viên xem)
     * Method: GET
     * Endpoint: /api/giang-vien/chi-tiet-bai-giang
     */
    public function layChiTietBaiGiang() {
        try {
            // Kiểm tra quyền giảng viên
            $giangVienId = $this->kiemTraQuyenGiangVien();
            
            $baiGiangId = $this->layThamSoGetInt('bai_giang_id');
            $lopHocId = $this->layThamSoGetInt('lop_hoc_id');
            
            if ($baiGiangId <= 0 || $lopHocId <= 0) {
                throw new Exception('Tham số không hợp lệ');
            }
            
            // Verify giảng viên dạy lớp này
            $lopHoc = $this->lopHocService->layThongTinLopHoc($lopHocId, $giangVienId);
            if (!$lopHoc) {
                throw new Exception('Bạn không có quyền truy cập lớp học này');
            }
            
            // Lấy thông tin bài giảng từ repository
            $baiGiang = $this->baiGiangRepo->layBaiGiangTheoId($baiGiangId);
            
            if (!$baiGiang) {
                throw new Exception('Không tìm thấy bài giảng');
            }
            
            // Lấy thông tin chương
            $chuong = $this->baiGiangRepo->layThongTinChuong($baiGiang['chuong_id']);
            
            // Format dữ liệu trả về (tương tự student nhưng không cần tiến độ)
            $duLieu = [
                'bai_giang_id' => $baiGiang['bai_giang_id'],
                'tieu_de' => $baiGiang['tieu_de'],
                'duong_dan_video' => $baiGiang['duong_dan_video'],
                'mo_ta' => $baiGiang['mo_ta'],
                'thu_tu' => $baiGiang['thu_tu'],
                'ten_chuong' => $chuong['ten_chuong'] ?? 'Chương',
                'chuong_id' => $baiGiang['chuong_id']
            ];
            
            $this->traVeThanhCong($duLieu, 'Lấy chi tiết bài giảng thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi layChiTietBaiGiang (teacher): " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Lấy bình luận bài giảng (cho giảng viên)
     * Method: GET
     * Endpoint: /api/giang-vien/binh-luan-bai-giang
     */
    public function layBinhLuanBaiGiang() {
        try {
            // Kiểm tra quyền giảng viên
            $giangVienId = $this->kiemTraQuyenGiangVien();
            
            $baiGiangId = $this->layThamSoGetInt('bai_giang_id');
            
            if ($baiGiangId <= 0) {
                throw new Exception('Tham số bai_giang_id không hợp lệ');
            }
            
            // Lấy dữ liệu từ service (không cần phân trang cho giảng viên)
            $duLieu = $this->binhLuanService->layDanhSachBinhLuan($baiGiangId, 1, 100);
            
            $this->traVeThanhCong($duLieu, 'Lấy bình luận thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi layBinhLuanBaiGiang (teacher): " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
    
    /**
     * API: Thêm bình luận bài giảng (cho giảng viên)
     * Method: POST
     * Body: { bai_giang_id, noi_dung, binh_luan_cha_id (optional) }
     */
    public function themBinhLuanBaiGiang() {
        try {
            // Kiểm tra quyền giảng viên
            $giangVienId = $this->kiemTraQuyenGiangVien();
            
            $duLieuNhap = $this->layDuLieuJSON();
            
            $baiGiangId = $duLieuNhap['bai_giang_id'] ?? 0;
            $noiDung = $duLieuNhap['noi_dung'] ?? '';
            $binhLuanChaId = $duLieuNhap['binh_luan_cha_id'] ?? null;
            
            if ($baiGiangId <= 0) {
                throw new Exception('Tham số bai_giang_id không hợp lệ');
            }
            
            if (empty(trim($noiDung))) {
                throw new Exception('Nội dung bình luận không được để trống');
            }
            
            // Gọi service để thêm bình luận
            // Giảng viên dùng ID âm hoặc check role ở service
            $binhLuanMoi = $this->binhLuanService->themBinhLuan(
                $baiGiangId, 
                $giangVienId, // Giảng viên ID
                $noiDung, 
                $binhLuanChaId,
                'giang_vien' // Thêm role để phân biệt
            );
            
            $this->traVeThanhCong($binhLuanMoi, 'Thêm bình luận thành công');
            
        } catch (Exception $e) {
            error_log("Lỗi themBinhLuanBaiGiang (teacher): " . $e->getMessage());
            $this->traVeLoi($e->getMessage());
        }
    }
}
