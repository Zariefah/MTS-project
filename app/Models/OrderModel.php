<?php
namespace App\Models;
use CodeIgniter\Model;

class OrderModel extends Model {
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'customer_id', 'preferred_tailor_id', 'selected_tailor_id', 
        'garment_type', 'material', 'expected_date', 
        'appointment_date', 'completion_date', 'pickup_date', 'status',
        // Uploaded images uploaded by customer
        'design_images', 'material_images'
    ];

    public function getCustomerOrders($userId) {
        return $this->select('orders.*, u.username as tailor_name')
                    ->join('users u', 'u.id = orders.selected_tailor_id', 'left')
                    ->where('customer_id', $userId)
                    ->findAll();
    }
}