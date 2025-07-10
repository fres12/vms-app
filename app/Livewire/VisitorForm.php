<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class VisitorForm extends Component
{
    use WithFileUploads;

    public $nik;
    public $id_card_photo;
    public $full_name;
    public $company;
    public $phone;
    public $department_purpose;
    public $section_purpose;
    public $self_photo;
    public $visit_date;
    public $visit_time;

    public function submit()
    {
        // Validasi dan simpan data di sini
    }

    public function render()
    {
        return view('livewire.visitor-form');
    }
}
