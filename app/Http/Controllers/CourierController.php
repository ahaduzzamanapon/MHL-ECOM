<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class CourierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $couriers = Courier::all();
        return response()->json($couriers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $courier = Courier::create($request->all());
            return response()->json($courier);
        } catch (ValidationException $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $courier = Courier::findOrFail($id);
        return response()->json($courier);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $courier = Courier::findOrFail($id);
            $courier->fill($request->all())->save();
            return response()->json($courier);
        } catch (ValidationException $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $courier = Courier::findOrFail($id);
        $courier->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}

