<?php

namespace App\Http\Controllers;

use App\User;
use App\UangKeluar;
use App\KeteranganRumah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UangKeluarController extends Controller
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
        $uang = DB::table('uang_keluars as a')
            ->join('wargas as b', 'a.nik', '=', 'b.nik')
            ->select('a.id', 'b.nik', 'a.jenis_pengeluaran', 'a.total', 'a.keterangan', 'a.tanggal_pengeluaran', 'a.created_at', 'a.updated_at')
            ->get();

        $totalUangKeluar = DB::table('uang_keluars')->select(DB::raw("SUM(total) as total"))
            ->get();

        $responseData = [
            'code' => 00,
            'message' => 'Ok',
            'data' => $uang,
            'totalUang' => $totalUangKeluar
        ];

        return response()->json($responseData);
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
        if ($request->nik == null || $request->jenis_pengeluaran == null || $request->total == null || $request->keterangan == null || $request->tanggal_pengeluaran == null) {
            $responseData = [
                'code' => "01",
                'message' => 'Harap isi seluruh kolom!',
            ];
            return response()->json($responseData, 409);
        } else {

            $uang = User::where('nik', $request->nik)->first();

            if ($uang != null) {
                $uang = UangKeluar::create($request->all());
                return response()->json([
                    'code' => '00',
                    'message' => 'Success Create',
                ]);
            } else {
                return response()->json([
                    'code' => '01',
                    'message' => 'NIK Tidak Terdaftar'
                ]);
            }
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
        $uang = UangKeluar::where('id', $id)->first();

        if ($uang) {
            return [
                'message' => 'Data ditemukan',
                'data' => $uang
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
        if ($request->jenis_pengeluaran == null || $request->total == null || $request->keterangan == null || $request->tanggal_pengeluaran == null) {
            $responseData = [
                'code' => "01",
                'message' => 'Harap isi seluruh kolom!',
            ];
            return response()->json($responseData, 409);
        } else {

            $uang = UangKeluar::where('id', $id)->first();

            $updateData = [];
            if ($uang != null) {

                if ($request->jenis_pengeluaran != null) {
                    $updateData['jenis_pengeluaran'] = $request->jenis_pengeluaran;
                }
                if ($request->total != null) {
                    $updateData['total'] = $request->total;
                }
                if ($request->keterangan != null) {
                    $updateData['keterangan'] = $request->keterangan;
                }

                if ($request->tanggal_pengeluaran != null) {
                    $updateData['tanggal_pengeluaran'] = $request->tanggal_pengeluaran;
                }
            }
            $update = UangKeluar::where('id', $id)->update($updateData);

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
        $uang = UangKeluar::where('id', $id)->delete();

        if ($uang) {
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
