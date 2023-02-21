<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\KeteranganRumah;

class KeteranganRumahController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['store']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rumah = KeteranganRumah::all();

        $count_rumah = KeteranganRumah::count();

        $responseData = [
            'code' => 00,
            'message' => 'Ok',
            'data' => $rumah,
            'count' => $count_rumah,
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
        if ($request->no_kk == null || $request->nama_kepala_keluarga == null || $request->no_rumah == null || $request->alamat == null) {
            $responseData = [
                'code' => "01",
                'message' => 'Harap isi seluruh kolom!',
            ];
            return response()->json($responseData, 409);
        } else {

            $rumah = KeteranganRumah::where('no_kk', $request->no_kk)->first();
            $norumah = KeteranganRumah::where('no_rumah', $request->no_rumah)->first();
            if ($rumah != null) {
                return response()->json([
                    'code' => '00',
                    'message' => 'Nomor Kartu Keluarga Sudah Terdaftar!'
                ], 401);
            } else if ($norumah != null) {
                return response()->json([
                    'code' => '00',
                    'message' => 'Nomor Rumah Sudah Terdaftar!'
                ], 401);
            } else {

                $rumah = KeteranganRumah::create($request->all());

                // var_dump($kk);

                $responseData = [
                    'code' => "00",
                    'message' => 'Success Created',
                    "data" => $rumah->no_kk
                ];

                return response()->json($responseData, 201);
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
        $rumah = KeteranganRumah::where('no_kk', $id)->first();

        if ($rumah) {
            return [
                'message' => 'Data ditemukan',
                'data' => $rumah
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
        if ($request->no_kk == null || $request->nama_kepala_keluarga == null || $request->no_rumah == null || $request->alamat == null) {
            $responseData = [
                'code' => "01",
                'message' => 'Harap isi seluruh kolom!',
            ];
            return response()->json($responseData, 409);
        }

        KeteranganRumah::find($id)->update($request->all());

        $responseData = [
            'code' => "00",
            'message' => 'Success Update'
        ];

        return response()->json($responseData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = KeteranganRumah::where('no_kk', $id)->delete();

        if ($deleted) {
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

    public function getkk()
    {

        $kk = KeteranganRumah::where('no_kk', auth()->user()->no_kk)->get();
        return response()->json($kk);
    }
}
