<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Models\UserModel;
use App\Models\OrderModel;
use App\Models\LogModel;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $uid = user_id();
        if ($uid === null) {
            // Should be blocked by login filter, but keep safe.
            return $this->_render('home', ['page' => 'Home']);
        }

        if (in_roles('Administrator')) {
            return $this->admin();
        }
        if (in_roles('Tailor')) {
            return $this->tailor();
        }
        if (in_roles('Customer')) {
            return $this->customer();
        }

        // Fallback: show a minimal dashboard (for roles like Manager/User/etc.)
        return $this->generic();
    }

    protected function customer(): string
    {
        $uid = (int) user_id();
        $db = \Config\Database::connect();

        $orders = model(OrderModel::class);
        $recentOrders = $orders
            ->select('orders.*, u.username AS tailor_name')
            ->join('users u', 'u.id = orders.selected_tailor_id', 'left')
            ->where('orders.customer_id', $uid)
            ->orderBy('orders.id', 'DESC')
            ->limit(8)
            ->findAll();

        $statusCountsRows = $orders->builder()
            ->select('status, COUNT(*) AS c')
            ->where('customer_id', $uid)
            ->groupBy('status')
            ->get()
            ->getResultArray();

        $statusCounts = [];
        foreach ($statusCountsRows as $r) {
            $statusCounts[(string) $r['status']] = (int) $r['c'];
        }

        $totalOrders = array_sum($statusCounts);

        $upcomingAppointment = $orders->builder()
            ->select('orders.id, orders.garment_type, orders.material, orders.appointment_date, u.username AS tailor_name')
            ->join('users u', 'u.id = orders.selected_tailor_id', 'left')
            ->where('orders.customer_id', $uid)
            ->where('orders.appointment_date IS NOT NULL', null, false)
            ->where('orders.pickup_date IS NULL', null, false)
            ->orderBy('orders.appointment_date', 'ASC')
            ->get(1)
            ->getRowArray();

        // Orders per month (last 6 months)
        $perMonthRows = $db->table('orders')
            ->select("DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS c", false)
            ->where('customer_id', $uid)
            ->where("created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)", null, false)
            ->groupBy("DATE_FORMAT(created_at, '%Y-%m')", false)
            ->orderBy('ym', 'ASC')
            ->get()
            ->getResultArray();

        $perMonthLabels = array_map(fn ($r) => (string) $r['ym'], $perMonthRows);
        $perMonthCounts = array_map(fn ($r) => (int) $r['c'], $perMonthRows);

        $data = [
            'page' => 'Dashboard',
            'kpis' => [
                'totalOrders' => $totalOrders,
                'pending' => $statusCounts['Pending'] ?? 0,
                'needAction' => $statusCounts['Need Action'] ?? 0,
                'accepted' => $statusCounts['Accepted'] ?? 0,
                'completed' => $statusCounts['Completed'] ?? 0,
                'pickedUp' => $orders->builder()->where('customer_id', $uid)->where('pickup_date IS NOT NULL', null, false)->countAllResults(),
            ],
            'profileComplete' => user_profile_is_complete(),
            'measurementsComplete' => customer_measurements_are_complete(),
            'upcomingAppointment' => $upcomingAppointment ?: null,
            'recentOrders' => $recentOrders,
            'chart' => [
                'statusLabels' => array_keys($statusCounts),
                'statusData' => array_values($statusCounts),
                'perMonthLabels' => $perMonthLabels,
                'perMonthData' => $perMonthCounts,
            ],
        ];

        return $this->_render('dashboard/customer', $data);
    }

    protected function tailor(): string
    {
        $uid = (int) user_id();
        $db = \Config\Database::connect();
        $orders = model(OrderModel::class);

        $incomingRequests = $db->table('orders')
            ->whereIn('status', ['Pending', 'Need Action'])
            ->groupStart()
                ->where('preferred_tailor_id', $uid)
                ->orWhere('preferred_tailor_id', null)
            ->groupEnd()
            ->where(
                "NOT EXISTS (SELECT 1 FROM order_bids ob WHERE ob.order_id = orders.id AND ob.tailor_id = {$uid})",
                null,
                false
            )
            ->countAllResults();

        $activeJobs = $db->table('orders')
            ->where('selected_tailor_id', $uid)
            ->where('pickup_date IS NULL', null, false)
            ->countAllResults();

        $completedThisMonth = $db->table('orders')
            ->where('selected_tailor_id', $uid)
            ->where('status', 'Completed')
            ->where("completion_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')", null, false)
            ->countAllResults();

        $recentAssigned = $orders->builder()
            ->select('orders.*, u.username AS customer_name')
            ->join('users u', 'u.id = orders.customer_id', 'inner')
            ->where('orders.selected_tailor_id', $uid)
            ->orderBy('orders.id', 'DESC')
            ->limit(8)
            ->get()
            ->getResultArray();

        $statusRows = $db->table('orders')
            ->select('status, COUNT(*) AS c')
            ->where('selected_tailor_id', $uid)
            ->groupBy('status')
            ->get()
            ->getResultArray();

        $statusCounts = [];
        foreach ($statusRows as $r) {
            $statusCounts[(string) $r['status']] = (int) $r['c'];
        }

        $perMonthRows = $db->table('orders')
            ->select("DATE_FORMAT(completion_date, '%Y-%m') AS ym, COUNT(*) AS c", false)
            ->where('selected_tailor_id', $uid)
            ->where('status', 'Completed')
            ->where('completion_date IS NOT NULL', null, false)
            ->where("completion_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)", null, false)
            ->groupBy("DATE_FORMAT(completion_date, '%Y-%m')", false)
            ->orderBy('ym', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'page' => 'Dashboard',
            'kpis' => [
                'incomingRequests' => (int) $incomingRequests,
                'activeJobs' => (int) $activeJobs,
                'completedThisMonth' => (int) $completedThisMonth,
                'customersServed' => (int) $db->table('orders')->select('customer_id')->distinct()->where('selected_tailor_id', $uid)->countAllResults(),
            ],
            'recentAssigned' => $recentAssigned,
            'chart' => [
                'statusLabels' => array_keys($statusCounts),
                'statusData' => array_values($statusCounts),
                'perMonthLabels' => array_map(fn ($r) => (string) $r['ym'], $perMonthRows),
                'perMonthData' => array_map(fn ($r) => (int) $r['c'], $perMonthRows),
            ],
        ];

        return $this->_render('dashboard/tailor', $data);
    }

    protected function admin(): string
    {
        $db = \Config\Database::connect();
        $users = model(UserModel::class);
        $roles = model(RoleModel::class);

        $totalUsers = (int) $users->countAllResults();
        $activeUsers = (int) $users->where('active', 1)->countAllResults();

        $roleCounts = [];
        foreach (['Customer', 'Tailor', 'Administrator'] as $roleName) {
            $role = $roles->where('name', $roleName)->first();
            if (!$role) {
                $roleCounts[$roleName] = 0;
                continue;
            }
            $roleCounts[$roleName] = (int) $db->table('roles_users')->where('role_id', (int) $role->id)->countAllResults();
        }

        $ordersStatusRows = $db->table('orders')
            ->select('status, COUNT(*) AS c')
            ->groupBy('status')
            ->get()
            ->getResultArray();

        $ordersStatusCounts = [];
        foreach ($ordersStatusRows as $r) {
            $ordersStatusCounts[(string) $r['status']] = (int) $r['c'];
        }

        $recentUsers = $users
            ->select('id, username, email, created_at, active')
            ->orderBy('id', 'DESC')
            ->findAll(8);

        $recentLogs = model(LogModel::class)
            ->select('id, type, user, ip, event, created_at')
            ->orderBy('id', 'DESC')
            ->findAll(10);

        $perMonthRows = $db->table('orders')
            ->select("DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS c", false)
            ->where("created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)", null, false)
            ->groupBy("DATE_FORMAT(created_at, '%Y-%m')", false)
            ->orderBy('ym', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'page' => 'Admin Dashboard',
            'kpis' => [
                'totalUsers' => $totalUsers,
                'activeUsers' => $activeUsers,
                'totalOrders' => (int) $db->table('orders')->countAllResults(),
                'pendingOrders' => $ordersStatusCounts['Pending'] ?? 0,
            ],
            'roleCounts' => $roleCounts,
            'recentUsers' => $recentUsers,
            'recentLogs' => $recentLogs,
            'chart' => [
                'orderStatusLabels' => array_keys($ordersStatusCounts),
                'orderStatusData' => array_values($ordersStatusCounts),
                'perMonthLabels' => array_map(fn ($r) => (string) $r['ym'], $perMonthRows),
                'perMonthData' => array_map(fn ($r) => (int) $r['c'], $perMonthRows),
            ],
        ];

        return $this->_render('dashboard/admin', $data);
    }

    protected function generic(): string
    {
        $data = [
            'page' => 'Dashboard',
            'user' => user(),
        ];

        return $this->_render('dashboard/generic', $data);
    }
}

