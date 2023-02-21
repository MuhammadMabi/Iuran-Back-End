<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\JenisIuran;

class JenisIuranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $iuran = JenisIuran::all();

        $totalIuran = DB::table('jenis_iurans')->select(DB::raw("SUM(biaya) as total"))->get();

        $responseData = [
            'code' => 00,
            'message' => 'Ok',
            'data' => $iuran,
            'total' => $totalIuran[0]->total
        ];

        return response()->json($responseData);
        // return view('kategori.index',compact('kategori'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->nama == null || $request->biaya == null) {
            $responseData = [
                'code' => "01",
                'message' => 'Harap isi seluruh kolom!',
            ];
            return response()->json($responseData, 409);
        } else {
            $this->validate($request, [
                'nama' => 'required',
                'biaya' => 'required'
            ]);

            $iuran = JenisIuran::create($request->all());

            // return redirect()->route('users.index');
            $responseData = [
                'code' => "00",
                'message' => 'Success Created',
                "data" => $iuran->id
            ];

            return response()->json($responseData, 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $iuran = JenisIuran::where('id', $id)->first();

        if ($iuran) {
            return [
                'message' => 'Data ditemukan',
                'iuran' => $iuran
            ];
        } else {
            return [
                'message' => 'Data tidak ditemukan'
            ];
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // JenisIuran::where('id_iuran', $id)->update([
        //     'nama' => $request->nama,
        //     'total_iuran' => $request->total_iuran
        // ]);
        if ($request->nama == null || $request->biaya == null) {
            $responseData = [
                'code' => "01",
                'message' => 'Harap isi seluruh kolom!',
            ];
            return response()->json($responseData, 409);
        } else {

            JenisIuran::find($id)->update($request->all());

            $responseData = [
                'code' => "00",
                'message' => 'Success Update'
            ];

            return response()->json($responseData);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $iuran = JenisIuran::where('id', $id)->delete();

        if ($iuran) {
            $responseData = [
                'code' => "00",
                'message' => 'Success Deleted'
            ];
        } else {
            $responseData = [
                'code' => "01",
                'message' => 'Data Not Found'
            ];
        }

        return response()->json($responseData);
    }
}
