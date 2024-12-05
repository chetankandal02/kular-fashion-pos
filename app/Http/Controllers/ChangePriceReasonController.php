<?php

namespace App\Http\Controllers;

use App\Models\ChangePriceReason;
use Illuminate\Http\Request;

class ChangePriceReasonController extends Controller
{
    public function index(){
        return ChangePriceReason::select('id', 'name')->whereNull('deleted_at')->get();
    }
}
