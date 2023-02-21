<?php

namespace App\Http\Controllers;

use App\User;
use App\KeteranganRumah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Warga;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }


    public function register(Request $request)
    {
        if ($request->nik == null || $request->no_kk == null || $request->nama == null || $request->tanggal_lahir == null || $request->jenis_kelamin == null || $request->password == null || $request->role == null) {
            $responseData = [
                'code' => "01",
                'message' => 'Harap isi seluruh kolom!',
            ];
            return response()->json($responseData, 401);
        } else {

            $warga = User::where('nik', $request->nik)->first();
            if ($warga != null) {
                return response()->json([
                    'code' => '00',
                    'message' => 'NIK Sudah Terdaftar!'
                ], 401);
            }

            $kk = KeteranganRumah::where('no_kk', $request->no_kk)->first();

            if ($kk != null || $kk != "") {
                // $warga = Warga::create($request->all());
                $nik = $request->nik;
                $no_kk = $request->no_kk;
                $nama = $request->nama;
                $tanggal_lahir = $request->tanggal_lahir;
                $jenis_kelamin = $request->jenis_kelamin;
                $password = bcrypt($request->password);
                $role = $request->role;

                $insert = User::create([
                    'nik' => $nik,
                    'no_kk' => $no_kk,
                    'nama' => $nama,
                    'tanggal_lahir' => $tanggal_lahir,
                    'jenis_kelamin' => $jenis_kelamin,
                    'password' => $password,
                    'role' => $role
                ], 200);

                return $insert;

            } else {
                return response()->json([
                    'code' => '01',
                    'message' => 'Nomor Kartu Keluarga Tidak Terdaftar!'
                ], 400);
            }
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'nik' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validator Fails',
                'error' => 'Harap isi seluruh kolom!'
            ], 422);
        }

        $credentials = request(['nik', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'NIK atau Password salah'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $warga = Warga::where('role', auth()->user()->role)->get();
        $role = Warga::where('role', auth()->user()->role)->get();

        // return response()->json(auth()->user());
        // $warga = Warga::where('nik')
        // ->select('role')
        // ->get();
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'role' => auth()->user()->role,
            'nik' => auth()->user()->nik,
            'no_kk' => auth()->user()->no_kk,
            'nama' => auth()->user()->nama,
            'expires_in' => auth()->factory()->getTTL() * 60

            // 'access_token' => $token,
            // 'token_type' => 'bearer',
            // 'role' => $warga[0]->role,
            // 'nik' => $warga[0]->nik,
            // 'no_kk' => $warga[0]->no_kk,
            // 'nama' => $warga[0]->nama,
            // 'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function index()
    {
        $warga =  DB::table('wargas as a')
            ->join('keterangan_rumahs as b', 'a.no_kk', '=', 'b.no_kk')
            ->select('a.nik', 'b.no_kk', 'b.nama_kepala_keluarga', 'a.nama', 'a.tanggal_lahir', 'jenis_kelamin', 'b.no_rumah', 'b.alamat', 'a.password', 'a.role', 'a.created_at', 'a.updated_at')
            ->get();
        // $warga = DB::table('wargas')
        // ->join('kepala_keluargas', 'wargas.no_kk', '=', 'kepala_keluargas.no_kk')
        // ->get();

        $count = User::count();

        $responseData = [
            'code' => 00,
            'message' => 'Ok',
            'data' => $warga,
            'count' => $count,
        ];

        return response()->json($responseData);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required',
            'confirm_password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Harap isi seluruh kolom!'
            ], 422);
        } else {
            $validator = Validator::make($request->all(), [
                'password' => 'required',
                'confirm_password' => 'required|same:password'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Password baru dan confirm password baru tidak sama!'
                ], 422);
            }
        }

        $warga = $request->User();

        if (Hash::check($request->old_password, $warga->password)) {
            $warga->update([
                'password' => Hash::make($request->password)
            ]);
            return response()->json([
                'message' => 'Password Berhasi Di Update',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Password Lama Salah',
            ], 400);
        }
    }

    public function show($id)
    {
        $warga = User::where('nik', $id)->first();

        if ($warga) {
            User::where('nik', $id)->first();
            return response([
                'status' => 'Done',
                'message' => 'Data Ditemukan',
                'data' => $warga
            ]);
        } else {
            return response([
                'status' => 'Not Found',
                'message' => 'Data Tidak Ditemukan'
            ]);
        }
    }

    public function showPetugas()
    {
        $warga = User::where('role', '=', 'Petugas')->get();

        return response()->json($warga);
    }

    public function updatePassword(Request $request, $nik)
    {

        if ($request->password == null) {
            $responseData = [
                'code' => "01",
                'message' => 'Harap isi seluruh kolom!',
            ];
            return response()->json($responseData, 401);
        }

        $warga = User::where('nik', $nik)->first();

        if ($warga != null) {
            // Warga::where('nik',$nik)->update($request->all());

            $updateData = [];

            if ($request->password != null) {
                $updateData['password'] = bcrypt($request->password);
            }

            $update = User::where('nik', $nik)->update($updateData);

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

    public function update(Request $request, $nik)
    {
        if ($request->nik == null || $request->no_kk == null || $request->nama == null || $request->tanggal_lahir == null || $request->jenis_kelamin == null || $request->role == null) {
            $responseData = [
                'code' => "01",
                'message' => 'Harap isi seluruh kolom!',
            ];
            return response()->json($responseData, 401);
        }

        $rumah = KeteranganRumah::where('no_kk', $request->no_kk)->first();
        if ($rumah == null) {
            return response()->json([
                'code' => '00',
                'message' => 'No Kartu Keluarga Tidak Terdaftar!'
            ], 401);
        }

        $warga = User::where('nik', $nik)->first();

        // $warga = User::where('nik', $request->nik)->first();
        // if ($warga != null) {
        //     return response()->json([
        //         'code' => '00',
        //         'message' => 'NIK Sudah Terdaftar!'
        //     ], 401);
        // }

        if ($warga != null) {
            // Warga::where('nik',$nik)->update($request->all());

            $updateData = [];

            if ($request->nik != null) {
                $updateData['nik'] = $request->nik;
            }

            if ($request->nama != null) {
                $updateData['nama'] = $request->nama;
            }

            if ($request->tanggal_lahir != null) {
                $updateData['tanggal_lahir'] = $request->tanggal_lahir;
            }

            if ($request->jenis_kelamin != null) {
                $updateData['jenis_kelamin'] = $request->jenis_kelamin;
            }

            if ($request->role != null) {
                $updateData['role'] = $request->role;
            }
            if ($request->no_kk != null) {
                $kk = KeteranganRumah::where('no_kk', $request->no_kk)->first();
                if ($kk != null) {
                    $updateData['no_kk'] = $request->no_kk;
                    KeteranganRumah::where('no_kk', $request->no_kk)->update(['no_kk' => $request->no_kk]);
                } else {
                    $responseData = [
                        'code' => "01",
                        'message' => 'No KK Not Found'
                    ];
                    return $responseData;
                }
            }

            $update = User::where('nik', $nik)->update($updateData);

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

    public function destroy($id)
    {
        $warga = User::where('nik', $id)->delete();

        if ($warga) {
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
