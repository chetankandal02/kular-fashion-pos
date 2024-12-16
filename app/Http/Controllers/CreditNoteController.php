<?php

namespace App\Http\Controllers;

use App\Models\CreditNote;
use Illuminate\Http\Request;

class CreditNoteController extends Controller
{
    public function create(Request $request){
        $creditNote = CreditNote::create([
            'amount' => abs($request->input('amount')),
            'generated_by' => $request->auth_user_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Credit note generated successfully!',
            'credit_note' => $creditNote
        ], 201);
    }
}
