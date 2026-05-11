<?php

// namespace App\Http\Controllers;

// use App\Models\User;
// use Illuminate\Http\Request;

// class AkunController extends Controller
// {
//     public function index()
//     {
//         $users = User::latest()->get();
//         return view('pages.tambah-akun', compact('users'));
//     }

//     public function create()
//     {
//         return view('pages.tambah-akun');
//     }

//     public function store(Request $request)
//     {
//         $request->validate([
//             'NIK' => 'required|unique:users,NIK',
//             'nama_lengkap' => 'required',
//             'jabatan' => 'required',
//             'unit_kerja' => 'required',
//             'role' => 'required|in:admin,sekretaris,karyawan',
//             'password' => 'required|confirmed|min:6'
//         ]);

//         User::create([
//             'NIK' => $request->NIK,
//             'nama_lengkap' => $request->nama_lengkap,
//             'jabatan' => $request->jabatan,
//             'unit_kerja' => $request->unit_kerja,
//             'role' => $request->role,
//             'password' => $request->password
//         ]);

//         return redirect()->route('akun.index')
//             ->with('success','Akun berhasil ditambahkan');
//     }

//     public function edit($id)
//     {
//         $user = User::findOrFail($id);
//         return view('pages.Akun.editakun', compact('user'));
//     }

//     public function update(Request $request, $id)
//     {
//         $user = User::findOrFail($id);

//         $user->update([
//             'nama_lengkap' => $request->nama_lengkap,
//             'jabatan' => $request->jabatan,
//             'unit_kerja' => $request->unit_kerja,
//             'role' => $request->role
//         ]);

//         return redirect()->route('akun.index')
//             ->with('success','Akun berhasil diupdate');
//     }

//     public function destroy($id)
//     {
//         User::findOrFail($id)->delete();

//         return redirect()->route('akun.index')
//             ->with('success','Akun berhasil dihapus');
//     }
// }

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AkunController extends Controller
{
    public function index(Request $request)
    {
        $query = User::latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($q2) use ($q) {
                $q2->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('NIK', 'like', "%{$q}%")
                    ->orWhere('unit_kerja', 'like', "%{$q}%")
                    ->orWhere('jabatan', 'like', "%{$q}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('pages.KelolahAkun.kelolah_akun', compact('users'));
    }

    public function create()
    {
        // $units = User::where('role', 'unit')
        //     ->orderBy('unit_kerja')
        //     ->get();

        $units = collect([
            // Kabid Keperawatan
            (object) ['id_user' => 1,  'unit_kerja' => 'Unit Poliklinik Rawat Jalan',    'kategori_unit' => 'Kabid Keperawatan'],
            (object) ['id_user' => 2,  'unit_kerja' => 'Instalasi Gawat Darurat',        'kategori_unit' => 'Kabid Keperawatan'],
            (object) ['id_user' => 3,  'unit_kerja' => 'Unit Rawat Inap Ruang Lotus',    'kategori_unit' => 'Kabid Keperawatan'],
            (object) ['id_user' => 4,  'unit_kerja' => 'Unit Rawat Inap Ruang Rosalina', 'kategori_unit' => 'Kabid Keperawatan'],
            (object) ['id_user' => 5,  'unit_kerja' => 'Unit Rawat Inap Ruang Alamanda', 'kategori_unit' => 'Kabid Keperawatan'],
            (object) ['id_user' => 6,  'unit_kerja' => 'Unit Rawat Inap Ruang Teratai',  'kategori_unit' => 'Kabid Keperawatan'],
            (object) ['id_user' => 7,  'unit_kerja' => 'Unit Rawat Inap Ruang Anturium', 'kategori_unit' => 'Kabid Keperawatan'],
            (object) ['id_user' => 8,  'unit_kerja' => 'Unit Rawat Inap Ruang Tulip',    'kategori_unit' => 'Kabid Keperawatan'],
            (object) ['id_user' => 9,  'unit_kerja' => 'Unit Kamar Operasi',             'kategori_unit' => 'Kabid Keperawatan'],
            (object) ['id_user' => 10, 'unit_kerja' => 'Unit ICU',                       'kategori_unit' => 'Kabid Keperawatan'],
            (object) ['id_user' => 11, 'unit_kerja' => 'Unit Hemodialisis',              'kategori_unit' => 'Kabid Keperawatan'],
            (object) ['id_user' => 12, 'unit_kerja' => 'Unit Kamar Bersalin',            'kategori_unit' => 'Kabid Keperawatan'],
            (object) ['id_user' => 13, 'unit_kerja' => 'Unit Perinatologi',              'kategori_unit' => 'Kabid Keperawatan'],

            // Kabid Pelayanan Medis
            (object) ['id_user' => 101, 'unit_kerja' => 'Unit Poliklinik Rawat Jalan',    'kategori_unit' => 'Kabid Pelayanan Medis'],
            (object) ['id_user' => 102, 'unit_kerja' => 'Instalasi Gawat Darurat',        'kategori_unit' => 'Kabid Pelayanan Medis'],
            (object) ['id_user' => 103, 'unit_kerja' => 'Unit Rawat Inap Ruang Lotus',    'kategori_unit' => 'Kabid Pelayanan Medis'],
            (object) ['id_user' => 104, 'unit_kerja' => 'Unit Rawat Inap Ruang Rosalina', 'kategori_unit' => 'Kabid Pelayanan Medis'],
            (object) ['id_user' => 105, 'unit_kerja' => 'Unit Rawat Inap Ruang Alamanda', 'kategori_unit' => 'Kabid Pelayanan Medis'],
            (object) ['id_user' => 106, 'unit_kerja' => 'Unit Rawat Inap Ruang Teratai',  'kategori_unit' => 'Kabid Pelayanan Medis'],
            (object) ['id_user' => 107, 'unit_kerja' => 'Unit Rawat Inap Ruang Anturium', 'kategori_unit' => 'Kabid Pelayanan Medis'],
            (object) ['id_user' => 108, 'unit_kerja' => 'Unit Rawat Inap Ruang Tulip',    'kategori_unit' => 'Kabid Pelayanan Medis'],
            (object) ['id_user' => 109, 'unit_kerja' => 'Unit Kamar Operasi',             'kategori_unit' => 'Kabid Pelayanan Medis'],
            (object) ['id_user' => 110, 'unit_kerja' => 'Unit ICU',                       'kategori_unit' => 'Kabid Pelayanan Medis'],
            (object) ['id_user' => 111, 'unit_kerja' => 'Unit Hemodialisis',              'kategori_unit' => 'Kabid Pelayanan Medis'],
            (object) ['id_user' => 112, 'unit_kerja' => 'Unit Kamar Bersalin',            'kategori_unit' => 'Kabid Pelayanan Medis'],
            (object) ['id_user' => 113, 'unit_kerja' => 'Unit Perinatologi',              'kategori_unit' => 'Kabid Pelayanan Medis'],

            // Kabid Penunjang Medis
            (object) ['id_user' => 14, 'unit_kerja' => 'Unit Radiologi',    'kategori_unit' => 'Kabid Penunjang Medis'],
            (object) ['id_user' => 15, 'unit_kerja' => 'Unit Laboratorium', 'kategori_unit' => 'Kabid Penunjang Medis'],
            (object) ['id_user' => 16, 'unit_kerja' => 'Unit Gizi',         'kategori_unit' => 'Kabid Penunjang Medis'],
            (object) ['id_user' => 17, 'unit_kerja' => 'Unit Farmasi',      'kategori_unit' => 'Kabid Penunjang Medis'],
            (object) ['id_user' => 18, 'unit_kerja' => 'Unit Rekam Medik',  'kategori_unit' => 'Kabid Penunjang Medis'],

            // Kabag Umum & Keuangan
            (object) ['id_user' => 19, 'unit_kerja' => 'Unit Umum Rumah Tangga',    'kategori_unit' => 'Kabag Umum & Keuangan'],
            (object) ['id_user' => 20, 'unit_kerja' => 'Unit Informasi & TI',       'kategori_unit' => 'Kabag Umum & Keuangan'],
            (object) ['id_user' => 21, 'unit_kerja' => 'Unit Keuangan',             'kategori_unit' => 'Kabag Umum & Keuangan'],
            (object) ['id_user' => 22, 'unit_kerja' => 'Unit Pajak',                'kategori_unit' => 'Kabag Umum & Keuangan'],
            (object) ['id_user' => 23, 'unit_kerja' => 'Unit Akuntansi',            'kategori_unit' => 'Kabag Umum & Keuangan'],
            (object) ['id_user' => 24, 'unit_kerja' => 'Unit Kepegawaian & Diklat', 'kategori_unit' => 'Kabag Umum & Keuangan'],
        ]);

        return view('pages.KelolahAkun.tambah_akun', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'NIK' => 'required|unique:users,NIK',
            'nama_lengkap' => 'required',
            'jabatan' => 'required',
            'unit_id' => 'required|exists:units,id',
            'role' => 'required|in:super_admin,admin,sekretaris,karyawan,unit',
            'password' => 'required|confirmed|min:6',
        ]);

        $unit = Unit::findOrFail($request->unit_id);

        User::create([
            'NIK' => $request->NIK,
            'nama_lengkap' => $request->nama_lengkap,
            'jabatan' => $request->jabatan,
            'unit_kerja' => $unit->nama_unit,
            'unit_id' => $request->unit_id,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('akun.index')
            ->with('success', 'Akun berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('pages.KelolahAkun.edit_akun', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required',
            'jabatan' => 'required',
            'unit_kerja' => 'required',
            'role' => 'required|in:super_admin,admin,sekretaris,karyawan,unit',
            'password' => 'nullable|confirmed|min:6',
        ]);

        $data = [
            'nama_lengkap' => $request->nama_lengkap,
            'jabatan' => $request->jabatan,
            'unit_kerja' => $request->unit_kerja,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('akun.index')
            ->with('success', 'Akun berhasil diupdate.');
    }

    public function destroy($id)
    {
        if ($id == Auth::user()->id_user) {
            return redirect()->route('akun.index')
                ->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        User::findOrFail($id)->delete();

        return redirect()->route('akun.index')
            ->with('success', 'Akun berhasil dihapus.');
    }

    public function resetAllPassword(Request $request)
    {
        $request->validate([
            'password_baru' => 'required|min:6',
        ]);

        User::where('id_user', '!=', Auth::user()->id_user)
            ->update(['password' => Hash::make($request->password_baru)]);

        return redirect()->route('akun.index')
            ->with('success', 'Password seluruh akun berhasil direset.');
    }
}
