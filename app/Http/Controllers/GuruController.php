<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Role;
use Illuminate\Validation\Rule;

class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $userRole = $user->role->name ?? null;
        
        $guru = User::with(['role'])
            ->whereHas('role', function($q) {
                $q->whereIn('name', ['guru', 'kepala_tatausaha', 'tata_usaha']);
            })
            ->paginate(10);

        return Inertia::render('Guru/Index', [
            'guru' => $guru,
            'userRole' => $userRole,
            'permissions' => [
                'canCreate' => in_array($userRole, ['kepala_tatausaha', 'tata_usaha']),
                'canEdit' => in_array($userRole, ['kepala_tatausaha', 'tata_usaha']),
                'canDelete' => in_array($userRole, ['kepala_tatausaha', 'tata_usaha']),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userRole = auth()->user()->role->name ?? null;
        
        // Cek akses untuk guru
        if ($userRole === 'guru') {
            return redirect()->route('guru.index')
                ->with('error', 'Anda tidak memiliki akses untuk menambah data guru.');
        }
        
        $roles = Role::whereIn('name', ['guru', 'tata_usaha'])->get();
        
        return Inertia::render('Guru/Create', [
            'roles' => $roles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $userRole = auth()->user()->role->name ?? null;
        
        // Cek akses untuk guru
        if ($userRole === 'guru') {
            return redirect()->route('guru.index')
                ->with('error', 'Anda tidak memiliki akses untuk menambah data guru.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'nip' => 'required|string|unique:users,nip',
            'no_telepon' => 'nullable|string',
            'alamat' => 'nullable|string',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'nullable|date',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id,
            'nip' => $request->nip,
            'no_telepon' => $request->no_telepon,
            'alamat' => $request->alamat,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
        ]);

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['role', 'kelasAsWali', 'jadwalPelajaran.mataPelajaran', 'jadwalPelajaran.kelas']);
        
        return Inertia::render('Guru/Show', [
            'guru' => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $userRole = auth()->user()->role->name ?? null;
        
        // Cek akses untuk guru
        if ($userRole === 'guru') {
            return redirect()->route('guru.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit data guru.');
        }
        
        $roles = Role::whereIn('name', ['guru', 'tata_usaha'])->get();
        
        return Inertia::render('Guru/Edit', [
            'guru' => $user->load('role'),
            'roles' => $roles
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $userRole = auth()->user()->role->name ?? null;
        
        // Cek akses untuk guru
        if ($userRole === 'guru') {
            return redirect()->route('guru.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit data guru.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'nip' => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'no_telepon' => 'nullable|string',
            'alamat' => 'nullable|string',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'nullable|date',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'nip' => $request->nip,
            'no_telepon' => $request->no_telepon,
            'alamat' => $request->alamat,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        $user->update($updateData);

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $userRole = auth()->user()->role->name ?? null;
        
        // Cek akses untuk guru
        if ($userRole === 'guru') {
            return redirect()->route('guru.index')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus data guru.');
        }
        
        // Prevent deleting if user is assigned as wali kelas
        if ($user->kelasAsWali()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus guru yang masih menjadi wali kelas.');
        }

        $user->delete();

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil dihapus.');
    }
}