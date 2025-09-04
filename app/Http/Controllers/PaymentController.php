<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->get('page') ?? 1;
        $perPage = $request->get('per_page') ?? 5;
        $status = $request->get('status') ?? '';

        $payments = Payment::when($status, function ($query) use ($status) {
            $query->where('status', $status);
        })->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'message' => 'Payments fetched successfully',
            'data' => $payments,
        ]);
    }

    public function show(int $id)
    {
        $payment = Payment::find($id);

        return response()->json([
            'message' => 'Payment fetched successfully',
            'data' => $payment,
        ]);
    }

    public function update(int $id, Request $request)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:255',
            'status' => 'required|string|max:255',
        ]);

        $payment = Payment::find($id);

        $payment->update($request->all());

        return response()->json([
            'message' => 'Payment updated successfully',
            'data' => $payment,
        ]);
    }
}
