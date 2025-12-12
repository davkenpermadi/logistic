<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Security;
use App\Models\Order;
use App\Models\User;

class DashboardController extends Controller {
    private $orderModel;
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        Security::requireAuth();
        $this->orderModel = new Order();
        $this->userModel = new User();
    }
    
    public function index() {
        // Get statistics
        $orderStats = $this->orderModel->countByStatus();
        $totalUsers = $this->userModel->count();
        $totalOrders = array_sum(array_column($orderStats, 'count'));
        
        // Get recent orders
        $recentOrders = $this->orderModel->getAll(['limit' => 10]);
        
        // Get monthly statistics for chart
        $monthlyStats = $this->orderModel->getMonthlyStats(date('Y'));
        
        // Prepare chart data
        $chartData = $this->prepareChartData($monthlyStats);
        
        $data = [
            'title' => 'Dashboard',
            'orderStats' => $orderStats,
            'totalUsers' => $totalUsers,
            'totalOrders' => $totalOrders,
            'recentOrders' => $recentOrders,
            'chartData' => $chartData
        ];
        
        $this->view('dashboard/index', $data);
    }
    
    private function prepareChartData($monthlyStats) {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $data = [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Total Orders',
                    'data' => array_fill(0, 12, 0),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 2
                ],
                [
                    'label' => 'Delivered',
                    'data' => array_fill(0, 12, 0),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 2
                ]
            ]
        ];
        
        foreach ($monthlyStats as $stat) {
            $monthIndex = $stat['month'] - 1;
            $data['datasets'][0]['data'][$monthIndex] = $stat['total_orders'];
            $data['datasets'][1]['data'][$monthIndex] = $stat['delivered'];
        }
        
        return $data;
    }
}
?>