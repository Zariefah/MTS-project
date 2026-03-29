<?php
namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\RoleModel;
use App\Models\UserModel;
use App\Models\UserOptionModel;
use App\Models\MeasurementModel;
use App\Libraries\Gravatar;

class OrderController extends BaseController {

    // --- SHARED/CUSTOMER VIEWS ---

    public function index() {
        $model = model(OrderModel::class);
        // Get orders where I am the customer
        $data['orders'] = $model->getCustomerOrders(user_id()); 
        return $this->_render('orders/list', $data);
    }

    public function searchTailor() {
        $userModel = model(UserModel::class);
        $optModel = model(UserOptionModel::class);
        
        // Users in Tailor role (same role checked by in_roles('Tailor'))
        $tailorRole = model(RoleModel::class)->where('name', 'Tailor')->first();
        $tailors = $tailorRole
            ? $userModel->select('users.*')
                ->join('roles_users', 'roles_users.user_id = users.id')
                ->where('roles_users.role_id', $tailorRole->id)
                ->findAll()
            : [];

        foreach($tailors as &$t) {
            $t->options = $optModel->getOptionsForUser($t->id);
        }

        return $this->_render('orders/search_tailor', ['tailors' => $tailors]);
    }

    /**
     * Public read-only tailor profile (same fields as tailor edit profile), for customers browsing tailors.
     */
    public function tailorDetails($tailorId) {
        $tailorRole = model(RoleModel::class)->where('name', 'Tailor')->first();
        if (!$tailorRole) {
            return redirect()->to('/orders/search_tailor')->with('error', 'No tailors available.');
        }

        $user = model(UserModel::class)
            ->select('users.*')
            ->join('roles_users', 'roles_users.user_id = users.id')
            ->where('roles_users.role_id', $tailorRole->id)
            ->where('users.id', (int) $tailorId)
            ->first();

        if (!$user) {
            return redirect()->to('/orders/search_tailor')->with('error', 'Tailor not found.');
        }

        $profile = model(UserOptionModel::class)->getOptionsForUser((int) $tailorId);

        $gravatar = new Gravatar();
        $gravatarUrl = $gravatar->get($user->email);
        $avaUrl = base_url('upload/avatars/');
        $avatarOpt = $profile['avatar'] ?? 'default_male.png';
        if (($profile['avatar'] ?? '') === 'gravatar') {
            $displayAvatar = $gravatarUrl;
        } else {
            $displayAvatar = $avaUrl . $avatarOpt;
        }

        return $this->_render('orders/tailor_details', [
            'user' => $user,
            'profile' => $profile,
            'displayAvatar' => $displayAvatar,
        ]);
    }

    public function create($tailorId = null) {
        $options = $tailorId ? model(UserOptionModel::class)->getOptionsForUser($tailorId) : null;
        [$garmentOptions, $materialOptions] = $this->specialtyChoicesForOrder($options);

        $data = [
            'preferred_id' => $tailorId,
            'tailor' => $tailorId ? model(UserModel::class)->find($tailorId) : null,
            'options' => $options,
            'garment_options' => $garmentOptions,
            'material_options' => $materialOptions,
        ];

        return $this->_render('orders/create', $data);
    }

    /**
     * Dropdown values match the tailor profile Specialties tab (users_options: tailor_type, tailor_material).
     * If a preferred tailor has saved specialties, only those are offered; otherwise the full default lists apply.
     */
    private function specialtyChoicesForOrder(?array $options): array
    {
        $defaultTypes = ['Baju Kurung', 'Baju Melayu', 'Kemeja', 'Seluar', 'Skirt', 'Blouse', 'Jubah', 'Kebaya'];
        $defaultMaterials = ['Cotton', 'Silk', 'Lycra', 'Chiffon', 'Satin', 'Batik', 'Linen', 'Songket'];

        if ($options === null) {
            return [$defaultTypes, $defaultMaterials];
        }

        $savedTypes = array_filter(array_map('trim', explode(',', $options['tailor_type'] ?? '')));
        $savedMats = array_filter(array_map('trim', explode(',', $options['tailor_material'] ?? '')));

        $garmentOptions = $savedTypes !== [] ? array_values($savedTypes) : $defaultTypes;
        $materialOptions = $savedMats !== [] ? array_values($savedMats) : $defaultMaterials;

        return [$garmentOptions, $materialOptions];
    }

    public function store() {
        $model = model(OrderModel::class);
        $db = \Config\Database::connect();

        $orderData = [
            'customer_id' => user_id(),
            'preferred_tailor_id' => $this->request->getPost('preferred_tailor_id') ?: null,
            'garment_type' => $this->request->getPost('type'),
            'material' => $this->request->getPost('material'),
            'expected_date' => $this->request->getPost('expected_date'),
            'status' => 'Pending'
        ];

        $model->save($orderData);
        $orderId = $model->getInsertID();

        $offeredDates = $this->request->getPost('offered_dates');
        if (is_array($offeredDates)) {
            foreach (array_filter($offeredDates) as $date) {
                $db->table('order_offered_dates')->insert(['order_id' => $orderId, 'offered_date' => $date]);
            }
        }

        return redirect()->to('/orders')->with('success', 'Order created!');
    }

    // --- TAILOR VIEWS ---

    public function bidList() {
        $db = \Config\Database::connect();
        $tailorId = (int) user_id();
        $data['orders'] = $db->table('orders')
            ->select('orders.*, users.username as customer_name, users.region as customer_region')
            ->join('users', 'users.id = orders.customer_id')
            ->groupStart()
                ->where('preferred_tailor_id', user_id())
                ->orWhere('preferred_tailor_id', null)
            ->groupEnd()
            ->whereIn('orders.status', ['Pending', 'Need Action'])
            ->where(
                "NOT EXISTS (SELECT 1 FROM order_bids ob WHERE ob.order_id = orders.id AND ob.tailor_id = {$tailorId})",
                null,
                false
            )
            ->get()->getResultArray();

        return $this->_render('orders/bid_list', $data);
    }

    public function customerDetails($orderId) {
        $db = \Config\Database::connect();
        $data['order'] = model(OrderModel::class)->find($orderId);
        $data['customer'] = model(UserModel::class)->find($data['order']['customer_id']);
        $data['measurements'] = model(MeasurementModel::class)->where('user_id', $data['order']['customer_id'])->first();
        $data['offered_dates'] = $db->table('order_offered_dates')->where('order_id', $orderId)->get()->getResultArray();
        $data['already_bid'] = $db->table('order_bids')
            ->where('order_id', $orderId)
            ->where('tailor_id', user_id())
            ->countAllResults() > 0;

        return $this->_render('orders/customer_details', $data);
    }

    public function customerDetails2($orderId) {
        $data['order'] = model(OrderModel::class)->find($orderId);
        $data['customer'] = model(UserModel::class)->find($data['order']['customer_id']);
        $data['measurements'] = model(MeasurementModel::class)->where('user_id', $data['order']['customer_id'])->first();
        $data['offered_dates'] = \Config\Database::connect()->table('order_offered_dates')->where('order_id', $orderId)->get()->getResultArray();
        
        return $this->_render('orders/customer_details2', $data);
    }

    public function submitBid() {
        $db = \Config\Database::connect();
        $orderId = $this->request->getPost('order_id');
        $dateId = $this->request->getPost('date_id');
        $time = $this->request->getPost('time');

        $duplicate = $db->table('order_bids')
            ->where('order_id', $orderId)
            ->where('tailor_id', user_id())
            ->countAllResults() > 0;
        if ($duplicate) {
            return redirect()->back()->with('error', 'You have already submitted a bid for this order.');
        }

        $db->table('order_bids')->insert([
            'order_id' => $orderId,
            'tailor_id' => user_id(),
            'offered_date_id' => $dateId,
            'offered_time' => $time
        ]);

        $order = model(OrderModel::class)->find($orderId);
        if ($order['preferred_tailor_id'] == user_id()) {
            $date = $db->table('order_offered_dates')->where('id', $dateId)->get()->getRowArray();
            if ($date === null) {
                return redirect()->back()->with('error', 'Invalid appointment date.');
            }
            model(OrderModel::class)->update($orderId, [
                'selected_tailor_id' => user_id(),
                'appointment_date' => $date['offered_date'] . ' ' . $time,
                'status' => 'Accepted'
            ]);
        } else {
            model(OrderModel::class)->update($orderId, ['status' => 'Need Action']);
        }

        return redirect()->to('/orders/my_orders')->with('success', 'Action completed!');
    }

    public function viewBids($orderId) {
        $db = \Config\Database::connect();
        $data['order'] = model(OrderModel::class)->find($orderId);
        
        $data['bids'] = $db->table('order_bids')
            ->select('order_bids.*, users.username as company_name, order_offered_dates.offered_date')
            ->join('users', 'users.id = order_bids.tailor_id')
            ->join('order_offered_dates', 'order_offered_dates.id = order_bids.offered_date_id')
            ->where('order_bids.order_id', $orderId)
            ->get()->getResultArray();

        return $this->_render('orders/view_bids', $data);
    }

    public function acceptBid() {
        $db = \Config\Database::connect();
        $orderId = $this->request->getPost('order_id');
        $bidId = $this->request->getPost('bid_id');

        $bid = $db->table('order_bids')
            ->select('order_bids.*, order_offered_dates.offered_date')
            ->join('order_offered_dates', 'order_offered_dates.id = order_bids.offered_date_id')
            ->where('order_bids.id', $bidId)
            ->get()->getRow();

        model(OrderModel::class)->update($orderId, [
            'selected_tailor_id' => $bid->tailor_id,
            'appointment_date'   => $bid->offered_date . ' ' . $bid->offered_time,
            'status'             => 'Accepted'
        ]);

        return redirect()->to('/orders')->with('success', 'Tailor Accepted! Appointment set.');
    }

    public function updateStatus() {
        $model = model(OrderModel::class);
        $orderId = $this->request->getPost('order_id');
        $newStatus = $this->request->getPost('status');
        $order = $model->find($orderId);

        // Only tailor can update status of their own assigned order.
        if (!in_roles('Tailor') || empty($order) || (int) $order['selected_tailor_id'] !== (int) user_id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Once pickup is marked, order can no longer be altered by status updates.
        if (!empty($order['pickup_date'])) {
            return redirect()->back()->with('error', 'Order is locked after pickup is done.');
        }

        $updateData = ['status' => $newStatus];

        // If the tailor marks it as completed, automatically set the date
        if ($newStatus === 'Completed') {
            $updateData['completion_date'] = date('Y-m-d');
        }

        if ($model->update($orderId, $updateData)) {
            return redirect()->back()->with('success', 'Status updated to ' . $newStatus);
        }

        return redirect()->back()->with('error', 'Failed to update status.');
    }

    // For TAILOR: mark garment as picked up by customer (one-time action)
    public function markPickupDone() {
        $model = model(OrderModel::class);
        $orderId = (int) $this->request->getPost('order_id');
        $order = $model->find($orderId);

        if (!in_roles('Tailor') || empty($order) || (int) $order['selected_tailor_id'] !== (int) user_id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // pickup_date is immutable once set.
        if (!empty($order['pickup_date'])) {
            return redirect()->back()->with('error', 'Pickup date is already set and cannot be changed.');
        }

        if (($order['status'] ?? '') !== 'Completed') {
            return redirect()->back()->with('error', 'Pickup can only be marked after order is Completed.');
        }

        if ($model->update($orderId, ['pickup_date' => date('Y-m-d')])) {
            return redirect()->back()->with('success', 'Pickup marked as done.');
        }

        return redirect()->back()->with('error', 'Failed to mark pickup.');
    }

    // For TAILOR: Assigned jobs plus orders where this tailor bid and customer has not chosen another tailor yet
    public function myOrders() {
        $model = model(OrderModel::class);
        $tailorId = (int) user_id();

        $data['orders'] = $model->select('orders.*, users.username as customer_name')
            ->join('users', 'users.id = orders.customer_id')
            ->groupStart()
                ->where('orders.selected_tailor_id', $tailorId)
                ->orGroupStart()
                    ->where('orders.selected_tailor_id', null)
                    ->where(
                        "EXISTS (SELECT 1 FROM order_bids ob WHERE ob.order_id = orders.id AND ob.tailor_id = {$tailorId})",
                        null,
                        false
                    )
                ->groupEnd()
            ->groupEnd()
            ->orderBy('orders.id', 'DESC')
            ->findAll();

        foreach ($data['orders'] as &$o) {
            $o['display_status'] = empty($o['selected_tailor_id']) ? 'Pending' : $o['status'];
        }
        unset($o);

        return $this->_render('orders/tailor_my_orders', $data);
    }

    // For CUSTOMER: See their assigned tailors per order
    public function myTailorsList() {
        $data['orders'] = model(OrderModel::class)
            ->select('orders.id, orders.status, orders.completion_date, orders.pickup_date, users.username as tailor_name')
            ->join('users', 'users.id = orders.selected_tailor_id', 'left')
            ->where('orders.customer_id', user_id())
            ->findAll();

        return $this->_render('orders/my_tailors_list', $data);
    }

    // For TAILOR: See their customers per assigned order
    public function myCustomersList() {
        $data['orders'] = model(OrderModel::class)
            ->select('orders.id, orders.status, orders.completion_date, orders.pickup_date, users.username as customer_name')
            ->join('users', 'users.id = orders.customer_id')
            ->where('orders.selected_tailor_id', user_id())
            ->findAll();

        return $this->_render('orders/my_customer_list', $data);
    }
}