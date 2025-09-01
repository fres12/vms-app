<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dept extends Model
{
    protected $table = 'depts';
    protected $primaryKey = 'deptID';
    
    protected $fillable = [
        'nameDept'
    ];

    // Relationship with visitors
    public function visitors()
    {
        return $this->hasMany(Visitor::class, 'deptpurpose', 'deptID');
    }

    // Relationship with accounts
    public function accounts()
    {
        return $this->hasMany(Account::class, 'deptID', 'deptID');
    }
} 