<?php

namespace App\Models;

use CodeIgniter\Model;

class MeasurementModel extends Model {
    protected $table      = 'measurements';
    protected $primaryKey = 'meas_id'; // Updated PK

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'user_id', 'leher', 'bahu', 'bahu_ke_dada', 
        'bahu_ke_pinggang', 'lilitan_dada', 'pinggang', 
        'punggung', 'bahu_ke_lutut', 'pinggang_ke_lutut', 
        'labuh_kain', 'labuh_tangan', 'lilitan_kekek', 
        'lubang_tangan'
    ];

    protected $useTimestamps = false;
}