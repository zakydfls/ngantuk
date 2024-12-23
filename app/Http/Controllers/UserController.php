<?php

namespace App\Http\Controllers;

use App\Models\Opd;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        $data['judul'] = "User";
        $data['sub_judul'] = "Data User";


        if (Auth::user()->role == 'super') {
            return view('super.data-user', $data);
        } else {
            return view('data-user', $data);
        }
    }

    public function create(Request $request)
    {
        $opd_id = Auth::user()->opd_id;

        if ($request->id) {
            $data = User::find($request->id);
            $data->name     = $request->name;
            $data->username = $request->username;
            if ($request->password) {
                $data->password = Hash::make($request->password);
            }
            $data->role     = $request->role;
            $data->created_at = now();
            $data->save();
        } else {
            $data = new User();
            $data->name     = $request->name;
            $data->username = $request->username;
            $data->password = Hash::make($request->password);
            $data->role     = $request->role;
            $data->opd_id   = $opd_id;
            $data->created_at = now();
            $data->save();
        }
        return redirect()->route('user.data')->with('success', 'Data Berhasil disimpan.');

        // return $data;         
        // if ($data) {
        //     return response()->json([
        //         'status'     => 'success']);
        // } else {
        //     return response()->json([
        //         'status' => 'error']);
        // }
    }

    public function create_byopd(Request $request)
    {
        if ($request->id) {
            $data = User::find($request->id);
            $data->name     = $request->name;
            $data->username = $request->username;
            if ($request->password) {
                $data->password = Hash::make($request->password);
            }
            $data->role     = $request->role;
            $data->created_at = now();
            $data->save();
        } else {
            $data = new User();
            $data->name     = $request->name;
            $data->username = $request->username;
            $data->password = Hash::make($request->password);
            $data->role     = $request->role;
            $data->opd_id   = $request->opd_id;
            $data->created_at = now();
            $data->save();
        }

        // return $data;         
        if ($data) {
            return response()->json([
                'status'     => 'success'
            ]);
        } else {
            return response()->json([
                'status' => 'error'
            ]);
        }
    }

    public function cek_username($cek)
    {
        $data = DB::select("SELECT * FROM users WHERE username = '$cek'");
        if ($data) {
            return response()->json($data, 200);
        }
    }



    public function get_user(Request $request)
    {
        $opd_id = Auth::user()->opd_id;

        if ($request->ajax()) {
            $data = DB::select("SELECT users.*, opd.singkatan FROM users JOIN opd ON users.opd_id=opd.id_opd WHERE opd_id='$opd_id' ");
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '
                    <button class="btn btn-icon btn-success ubah" data-bs-toggle="modal" data-bs-target="#modalUbah" 
                    data-id="' . $row->id_user . '" 
                    data-name="' . $row->name . '"
                    data-username="' . $row->username . '"
                    data-password="' . $row->password . '"
                    data-role="' . $row->role . '"
                    >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3" fill-rule="evenodd" clip-rule="evenodd" d="M2 4.63158C2 3.1782 3.1782 2 4.63158 2H13.47C14.0155 2 14.278 2.66919 13.8778 3.04006L12.4556 4.35821C11.9009 4.87228 11.1726 5.15789 10.4163 5.15789H7.1579C6.05333 5.15789 5.15789 6.05333 5.15789 7.1579V16.8421C5.15789 17.9467 6.05333 18.8421 7.1579 18.8421H16.8421C17.9467 18.8421 18.8421 17.9467 18.8421 16.8421V13.7518C18.8421 12.927 19.1817 12.1387 19.7809 11.572L20.9878 10.4308C21.3703 10.0691 22 10.3403 22 10.8668V19.3684C22 20.8218 20.8218 22 19.3684 22H4.63158C3.1782 22 2 20.8218 2 19.3684V4.63158Z" fill="white"/>
                    <path d="M10.9256 11.1882C10.5351 10.7977 10.5351 10.1645 10.9256 9.77397L18.0669 2.6327C18.8479 1.85165 20.1143 1.85165 20.8953 2.6327L21.3665 3.10391C22.1476 3.88496 22.1476 5.15129 21.3665 5.93234L14.2252 13.0736C13.8347 13.4641 13.2016 13.4641 12.811 13.0736L10.9256 11.1882Z" fill="white"/>
                    <path d="M8.82343 12.0064L8.08852 14.3348C7.8655 15.0414 8.46151 15.7366 9.19388 15.6242L11.8974 15.2092C12.4642 15.1222 12.6916 14.4278 12.2861 14.0223L9.98595 11.7221C9.61452 11.3507 8.98154 11.5055 8.82343 12.0064Z" fill="white"/>
                    </svg>
                    </button> 
                    <button class="btn btn-icon btn-danger hapus" data-bs-toggle="modal" data-id="' . $row->id_user . '"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="white"/>
                    <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="white"/>
                    <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="white"/>
                    </svg></button>
                    ';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function get_user_super(Request $request, $opd_id)
    {
        if ($request->ajax()) {
            $data = DB::select("SELECT users.*, opd.singkatan FROM users JOIN opd ON users.opd_id=opd.id_opd WHERE opd_id='$opd_id' ");
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '
                    <button class="btn btn-icon btn-success ubah" data-bs-toggle="modal" data-bs-target="#modalUbah" 
                    data-id="' . $row->id_user . '" 
                    data-name="' . $row->name . '"
                    data-username="' . $row->username . '"
                    data-password="' . $row->password . '"
                    data-role="' . $row->role . '"
                    >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3" fill-rule="evenodd" clip-rule="evenodd" d="M2 4.63158C2 3.1782 3.1782 2 4.63158 2H13.47C14.0155 2 14.278 2.66919 13.8778 3.04006L12.4556 4.35821C11.9009 4.87228 11.1726 5.15789 10.4163 5.15789H7.1579C6.05333 5.15789 5.15789 6.05333 5.15789 7.1579V16.8421C5.15789 17.9467 6.05333 18.8421 7.1579 18.8421H16.8421C17.9467 18.8421 18.8421 17.9467 18.8421 16.8421V13.7518C18.8421 12.927 19.1817 12.1387 19.7809 11.572L20.9878 10.4308C21.3703 10.0691 22 10.3403 22 10.8668V19.3684C22 20.8218 20.8218 22 19.3684 22H4.63158C3.1782 22 2 20.8218 2 19.3684V4.63158Z" fill="white"/>
                    <path d="M10.9256 11.1882C10.5351 10.7977 10.5351 10.1645 10.9256 9.77397L18.0669 2.6327C18.8479 1.85165 20.1143 1.85165 20.8953 2.6327L21.3665 3.10391C22.1476 3.88496 22.1476 5.15129 21.3665 5.93234L14.2252 13.0736C13.8347 13.4641 13.2016 13.4641 12.811 13.0736L10.9256 11.1882Z" fill="white"/>
                    <path d="M8.82343 12.0064L8.08852 14.3348C7.8655 15.0414 8.46151 15.7366 9.19388 15.6242L11.8974 15.2092C12.4642 15.1222 12.6916 14.4278 12.2861 14.0223L9.98595 11.7221C9.61452 11.3507 8.98154 11.5055 8.82343 12.0064Z" fill="white"/>
                    </svg>
                    </button> 
                    <button class="btn btn-icon btn-danger hapus" data-bs-toggle="modal" data-id="' . $row->id_user . '"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="white"/>
                    <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="white"/>
                    <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="white"/>
                    </svg></button>
                    ';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        User::destroy($id);
        // return "hmmmm";
        return response()->json(['status' => 'success']);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string',
            'role' => 'required|string',
        ]);

        $user = User::findOrFail($request->id);

        $user->name = $request->name;
        $user->username = $request->username;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->role = $request->role;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'User berhasil diperbarui'
        ]);
    }
}
