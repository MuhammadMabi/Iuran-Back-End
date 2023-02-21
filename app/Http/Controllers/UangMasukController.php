<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\UangMasuk;
use App\KeteranganRumah;

class UangMasukController extends Controller
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
        $uang =  DB::table('uang_masuks as a')
            ->join('keterangan_rumahs as b', 'a.no_kk', '=', 'b.no_kk')
            ->select('a.id', 'b.no_kk', 'b.no_rumah', 'a.jumblah_bayar', 'a.tanggal_setoran', 'a.bulan', 'a.tahun')
            ->get();

        $totalUangMasuk = DB::table('uang_masuks')->select(DB::raw("SUM(jumblah_bayar) as total"))
            ->first();

        $totalUangKeluar = DB::table('uang_keluars')->select(DB::raw("SUM(total) as total"))
            ->first();

        $total = $totalUangMasuk->total - $totalUangKeluar->total;

        $responseData = [
            'code' => 00,
            'message' => 'Ok',
            'data' => $uang,
            'totalUangMasuk' => $totalUangMasuk,
            'totalUangKeluar' => $totalUangKeluar,
            'totalKeseluruhan' => $total
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
        if ($request->no_kk == null || $request->jumblah_bayar == null || $request->tanggal_setoran == null || $request->bulan == null || $request->tahun == null) {
            $responseData = [
                'code' => "01",
                'message' => 'Harap isi seluruh kolom!',
            ];
            return response()->json($responseData, 409);
        } else {
            $cek = UangMasuk::where('bulan', $request->bulan)->where('tahun', $request->tahun)->where('no_kk', $request->no_kk)->first();
            if ($cek != null) {
                // var_dump($cek);
                $responseData = [
                    'code' => "01",
                    'message' => 'Data Sudah Tersedia',
                ];
                return response()->json($responseData, 409);
            } else {
                $rumah = KeteranganRumah::where('no_kk', $request->no_kk)->first();

                if ($rumah != null || $rumah != "") {

                    $uang = UangMasuk::create($request->all());
                    return response()->json([
                        'code' => '00',
                        'message' => 'Success Deposit',
                    ]);
                } else {
                    return response()->json([
                        'code' => '01',
                        'message' => 'No KK Tidak Terdaftar'
                    ]);
                }
            }
        }
    }

    public function payments()
    {
        $uang = UangMasuk::where('no_kk', auth()->user()->no_kk)->get();

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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $uang = UangMasuk::where('id', $id)->first();

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
        if ($request->no_kk == null || $request->jumblah_bayar == null || $request->tanggal_setoran == null || $request->bulan == null || $request->tahun == null) {
            $responseData = [
                'code' => "01",
                'message' => 'Harap isi seluruh kolom!',
            ];
            return response()->json($responseData, 409);
        } else
            $rumah = KeteranganRumah::where('no_kk', $request->no_kk)->first();
        if ($rumah == null) {
            return response()->json([
                'code' => '00',
                'message' => 'No Kartu Keluarga Tidak Terdaftar!'
            ], 401);
        }

        $uang = UangMasuk::where('id', $id)->first();

        if ($uang != null) {

            $updateData = [];

            if ($request->no_kk != null) {
                $updateData['no_kk'] = $request->no_kk;
            }

            if ($request->jumblah_bayar != null) {
                $updateData['jumblah_bayar'] = $request->jumblah_bayar;
            }

            if ($request->tanggal_setoran != null) {
                $updateData['tanggal_setoran'] = $request->tanggal_setoran;
            }

            if ($request->bulan != null) {
                $updateData['bulan'] = $request->bulan;
            }

            if ($request->tahun != null) {
                $updateData['tahun'] = $request->tahun;
            }
        }
        if ($request->no_kk != null) {
            $rumah = KeteranganRumah::where('no_kk', $request->no_kk)->first();

            if ($rumah != null) {
                $updateData['no_kk'] = $request->no_kk;
                KeteranganRumah::where('no_kk', $request->no_kk)->update(['no_kk' => $request->no_kk]);
            } else {
                $responseData = [
                    'code' => "01",
                    'message' => 'No KK Not Found'
                ];
                return $responseData;
            }

            $update = UangMasuk::where('id', $id)->update($updateData);

            $responseData = [
                'code' => "00",
                'message' => 'Success Update'
            ];
        } else {
            $responseData = [
                'code' => "01",
                'message' => 'Data Not Found'
            ];
        }

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
        $uang = UangMasuk::where('id', $id)->delete();

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

    public function dataQr($no_kk)
    {
        $totalIuran = DB::table('jenis_iurans')->select(DB::raw("SUM(biaya) as total"))
            ->get();

        $uang =  DB::table('uang_masuks as a')
            ->join('keterangan_rumahs as b', 'a.no_kk', '=', 'b.no_kk')
            ->select('a.id', 'b.no_kk', 'b.nama_kepala_keluarga', 'b.no_rumah', 'b.alamat', 'a.jumblah_bayar')
            ->get();

        $responseData = [
            'code' => 00,
            'message' => 'Ok',
            'data QR Code' => $uang,
            'totalIuran' => $totalIuran
        ];

        return response()->json($responseData);
    }

    public function ListTagihan(Request $request)
    {
        // time 
        if (isset($request->tahun) && $request->tahun != null || isset($request->no_kk) && $request->no_kk != null) {
            $year = $request->tahun;
            $no_kk = $request->no_kk;
        } else {
            $year = Date("Y");
            $no_kk = '';
        }

        $getList = DB::table('uang_masuks as a')
            ->join('keterangan_rumahs as b', 'a.no_kk', '=', 'b.no_kk')
            ->select('a.id', 'a.bulan', 'a.tahun', 'a.tanggal_setoran', 'b.no_kk', 'b.nama_kepala_keluarga', 'b.no_rumah', 'b.alamat')
            ->where('a.tahun', '=', $year)
            ->where('a.no_kk', '=', $no_kk)
            ->get();

        $totalUangMasuk = DB::table('uang_masuks')->select(DB::raw("SUM(jumblah_bayar) as total"))
            ->where('tahun', '=', $year)
            ->where('no_kk', '=', $no_kk)
            ->get();



        $result = [];
        $result['Januari']['status'] = "Belum Lunas";
        $result['Februari']['status'] = "Belum Lunas";
        $result['Maret']['status'] = "Belum Lunas";
        $result['April']['status'] = "Belum Lunas";
        $result['Mei']['status'] = "Belum Lunas";
        $result['Juni']['status'] = "Belum Lunas";
        $result['Juli']['status'] = "Belum Lunas";
        $result['Agustus']['status'] = "Belum Lunas";
        $result['September']['status'] = "Belum Lunas";
        $result['Oktober']['status'] = "Belum Lunas";
        $result['November']['status'] = "Belum Lunas";
        $result['Desember']['status'] = "Belum Lunas";

        foreach ($getList as $data => $getData) {

            if ($getData->bulan == 1) {
                $result['Januari']['status'] = "Lunas";
                $result['Januari']['tanggal_setoran'] = $getData->tanggal_setoran;

            }

            if ($getData->bulan == 2) {
                $result['Februari']['status'] = "Lunas";
                $result['Februari']['tanggal_setoran'] = $getData->tanggal_setoran;
            }

            if ($getData->bulan == 3) {
                $result['Maret']['status'] = "Lunas";
                $result['Maret']['tanggal_setoran'] = $getData->tanggal_setoran;
            }

            if ($getData->bulan == 4) {
                $result['April']['status'] = "Lunas";
                $result['April']['tanggal_setoran'] = $getData->tanggal_setoran;
            }

            if ($getData->bulan == 5) {
                $result['Mei']['status'] = "Lunas";
                $result['Mei']['tanggal_setoran'] = $getData->tanggal_setoran;
            }

            if ($getData->bulan == 6) {
                $result['Juni']['status'] = "Lunas";
                $result['Juni']['tanggal_setoran'] = $getData->tanggal_setoran;
            }

            if ($getData->bulan == 7) {
                $result['Juli']['status'] = "Lunas";
                $result['Juli']['tanggal_setoran'] = $getData->tanggal_setoran;
            }

            if ($getData->bulan == 8) {
                $result['Agustus']['status'] = "Lunas";
                $result['Agustus']['tanggal_setoran'] = $getData->tanggal_setoran;
            }

            if ($getData->bulan == 9) {
                $result['September']['status'] = "Lunas";
                $result['September']['tanggal_setoran'] = $getData->tanggal_setoran;
            }

            if ($getData->bulan == 10) {
                $result['Oktober']['status'] = "Lunas";
                $result['Oktober']['tanggal_setoran'] = $getData->tanggal_setoran;
            }

            if ($getData->bulan == 11) {
                $result['November']['status'] = "Lunas";
                $result['November']['tanggal_setoran'] = $getData->tanggal_setoran;
            }

            if ($getData->bulan == 12) {
                $result['Desember']['status'] = "Lunas";
                $result['Desember']['tanggal_setoran'] = $getData->tanggal_setoran;
            }
        }

        if (isset($request->bulan) && !empty($request->bulan)) {

            $get1Month = DB::table('uang_masuks as a')
                ->join('keterangan_rumahs as b', 'a.no_kk', '=', 'b.no_kk')
                ->select('a.id', 'a.bulan', 'a.tahun', 'a.tanggal_setoran', 'b.no_kk', 'b.nama_kepala_keluarga', 'b.no_rumah', 'b.alamat')
                ->where('a.tahun', '=', $year)
                ->where('a.no_kk', '=', $no_kk)
                ->where('a.bulan', '=', $request->bulan)
                ->first();

            // if ($get1Month != null) {

            // var_dump($get1Month);
            // die;
            if ($request->bulan == 1 && $get1Month != null) {
                $result['Januari']['status'] = "Lunas";
                $result['Januari']['tanggal_setoran'] = $get1Month->tanggal_setoran;
                $result = $result['Januari'];
            } else if ($request->bulan == 1 && $get1Month == null) {
                $result['Januari']['status'] = "Belum Lunas";
                $result = $result['Januari'];
            }

            if ($request->bulan == 2 && $get1Month != null) {
                $result['Februari']['status'] = "Lunas";
                $result['Februari']['tanggal_setoran'] = $get1Month->tanggal_setoran;
                $result = $result['Februari'];
            } else if ($request->bulan == 2 && $get1Month == null) {
                $result['Februari']['status'] = "Belum Lunas";
                $result = $result['Februari'];
            }
            if ($request->bulan == 3 && $get1Month != null) {
                $result['Maret']['status'] = "Lunas";
                $result['Maret']['tanggal_setoran'] = $get1Month->tanggal_setoran;
                $result = $result['Maret'];
            } else if ($request->bulan == 3 && $get1Month == null) {
                $result['Maret']['status'] = "Belum Lunas";
                $result = $result['Maret'];
            }
            if ($request->bulan == 4 && $get1Month != null) {
                $result['April']['status'] = "Lunas";
                $result['April']['tanggal_setoran'] = $get1Month->tanggal_setoran;
                $result = $result['April'];
            } else if ($request->bulan == 4 && $get1Month == null) {
                $result['April']['status'] = "Belum Lunas";
                $result = $result['April'];
            }
            if ($request->bulan == 5 && $get1Month != null) {
                $result['Mei']['status'] = "Lunas";
                $result['Mei']['tanggal_setoran'] = $get1Month->tanggal_setoran;
                $result = $result['Mei'];
            } else if ($request->bulan == 5 && $get1Month == null) {
                $result['Mei']['status'] = "Belum Lunas";
                $result = $result['Mei'];
            }
            if ($request->bulan == 6 && $get1Month != null) {
                $result['Juni']['status'] = "Lunas";
                $result['Juni']['tanggal_setoran'] = $get1Month->tanggal_setoran;
                $result = $result['Juni'];
            } else if ($request->bulan == 6 && $get1Month == null) {
                $result['Juni']['status'] = "Belum Lunas";
                $result = $result['Juni'];
            }
            if ($request->bulan == 7 && $get1Month != null) {
                $result['Juli']['status'] = "Lunas";
                $result['Juli']['tanggal_setoran'] = $get1Month->tanggal_setoran;
                $result = $result['Juli'];
            } else if ($request->bulan == 7 && $get1Month == null) {
                $result['Juli']['status'] = "Belum Lunas";
                $result = $result['Juli'];
            }
            if ($request->bulan == 8 && $get1Month != null) {
                $result['Agustus']['status'] = "Lunas";
                $result['Agustus']['tanggal_setoran'] = $get1Month->tanggal_setoran;
                $result = $result['Agustus'];
            } else if ($request->bulan == 8 && $get1Month == null) {
                $result['Agustus']['status'] = "Belum Lunas";
                $result = $result['Agustus'];
            }
            if ($request->bulan == 9 && $get1Month != null) {
                $result['September']['status'] = "Lunas";
                $result['September']['tanggal_setoran'] = $get1Month->tanggal_setoran;
                $result = $result['September'];
            } else if ($request->bulan == 9 && $get1Month == null) {
                $result['September']['status'] = "Belum Lunas";
                $result = $result['September'];
            }
            if ($request->bulan == 10 && $get1Month != null) {
                $result['Oktober']['status'] = "Lunas";
                $result['Oktober']['tanggal_setoran'] = $get1Month->tanggal_setoran;
                $result = $result['Oktober'];
            } else if ($request->bulan == 10 && $get1Month == null) {
                $result['Oktober']['status'] = "Belum Lunas";
                $result = $result['Oktober'];
            }
            if ($request->bulan == 11 && $get1Month != null) {
                $result['November']['status'] = "Lunas";
                $result['November']['tanggal_setoran'] = $get1Month->tanggal_setoran;
                $result = $result['November'];
            } else if ($request->bulan == 11 && $get1Month == null) {
                $result['November']['status'] = "Belum Lunas";
                $result = $result['November'];
            }
            if ($request->bulan == 12 && $get1Month != null) {
                $result['Desember']['status'] = "Lunas";
                $result['Desember']['tanggal_setoran'] = $get1Month->tanggal_setoran;
                $result = $result['Desember'];
            } else if ($request->bulan == 12 && $get1Month == null) {
                $result['Desember']['status'] = "Belum Lunas";
                $result = $result['Desember'];
            }
        }

        $nokk = KeteranganRumah::where('no_kk', $request->no_kk)->first();

        if ($nokk) {
            $responseData = [
                'code' => '00',
                'message' => 'Ok',
                'data' => $result,
                'total' => $totalUangMasuk
            ];
        } else {
            $responseData = [
                'code' => '01',
                'message' => 'No KK Tidak Ditemukan'
            ];
        }

        return response()->json($responseData);
    }
}
