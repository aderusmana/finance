<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Approver;
use Illuminate\Http\Request;

class ApproverController extends Controller
{
    public function index()
    {
        return Approver::with('user')->paginate(50);
    }

    public function show(Approver $approver)
    {
        return $approver;
    }

    public function store(Request $request)
    {
        $a = Approver::create($request->all());
        return response()->json($a, 201);
    }

    public function update(Request $request, Approver $approver)
    {
        $approver->update($request->all());
        return response()->json($approver);
    }

    public function destroy(Approver $approver)
    {
        $approver->delete();
        return response()->json(null, 204);
    }
}
