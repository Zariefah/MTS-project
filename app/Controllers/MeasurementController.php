<?php

namespace App\Controllers;

use App\Models\MeasurementModel;
use App\Models\UserModel;
use Config\Database;

class MeasurementController extends BaseController {

    /**
     * Tailor may update a customer's measurements only when they are the selected tailor on an order
     * for that customer and the order status is Accepted or later (In Progress, Completed).
     */
    private function tailorCanEditCustomerMeasurements(int $tailorId, int $customerId): bool
    {
        $db = Database::connect();

        return $db->table('orders')
            ->where('customer_id', $customerId)
            ->where('selected_tailor_id', $tailorId)
            ->whereIn('status', ['Accepted', 'In Progress', 'Completed'])
            ->countAllResults() > 0;
    }

    public function index($userId = null) {
        $model = model(MeasurementModel::class);
        $userModel = model(UserModel::class);

        $targetId = in_roles('Tailor') ? $userId : user_id();

        if (empty($targetId)) {
            return redirect()->to('/')->with('error', 'User ID not found.');
        }

        $data = [
            'measurement' => $model->where('user_id', $targetId)->first(), // Search by FK
            'targetUser'  => $userModel->find($targetId),
            'tailorCanEditMeasurements' => in_roles('Tailor')
                && $this->tailorCanEditCustomerMeasurements((int) user_id(), (int) $targetId),
        ];

        return $this->_render('measurement', $data);
    }

    public function save() {
        $model = model(MeasurementModel::class);
        $postData = $this->request->getPost();

        if (in_roles('Customer')) {
            $targetUserId = (int) user_id();
        } elseif (in_roles('Tailor')) {
            $targetUserId = (int) ($postData['target_user_id'] ?? 0);
            if ($targetUserId < 1) {
                return redirect()->back()->with('error', 'Missing customer for measurement update.');
            }
            if (! $this->tailorCanEditCustomerMeasurements((int) user_id(), $targetUserId)) {
                return redirect()->back()->with(
                    'error',
                    'Measurements can only be updated after an order with this customer is Accepted (or In Progress / Completed).'
                );
            }
        } else {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        unset($postData['target_user_id']);

        $data = $postData;
        $existing = $model->where('user_id', $targetUserId)->first();

        $data['user_id'] = $targetUserId;

        if ($existing) {
            $data['meas_id'] = $existing['meas_id'];
        }

        if ($model->save($data)) {
            $msg = in_roles('Tailor')
                ? 'Customer body measurements updated successfully.'
                : 'Ukuran Jemari Concept berjaya disimpan.';

            return redirect()->back()->with('success', $msg);
        }

        return redirect()->back()->with('error', 'Gagal menyimpan ukuran.');
    }
}