<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\Absensi;

class AbsensiFeatureTest extends TestCase
{

    public function test_guru_hanya_bisa_melihat_siswa_yang_diajar()
    {
        // Asumsi: role_id 2 = guru
        $guru = User::create();
        $kelas = Kelas::create();
        $mapel = MataPelajaran::create();
        $jadwal = JadwalPelajaran::create([
            'guru_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mata_pelajaran_id' => $mapel->id,
            'status' => true
        ]);
        $siswa = Siswa::create(['kelas_id' => $kelas->id]);

        $this->actingAs($guru);

        $response = $this->get(route('absensi.rekap-siswa', [
            'kelas_id' => $kelas->id,
            'mata_pelajaran_id' => $mapel->id
        ]));
        $response->assertStatus(200);
        $response->assertSee($siswa->nama_lengkap);

        $kelasLain = Kelas::create();
        $mapelLain = MataPelajaran::create();
        $response = $this->get(route('absensi.rekap-siswa', [
            'kelas_id' => $kelasLain->id,
            'mata_pelajaran_id' => $mapelLain->id
        ]));
        $response->assertStatus(200);
        $response->assertDontSee($siswa->nama_lengkap);
    }

    public function test_kepala_tata_usaha_bisa_melihat_semua_siswa()
    {
        // Asumsi: role_id 1 = kepala tata usaha
        $ktu = User::create();
        $kelas = Kelas::create();
        $mapel = MataPelajaran::create();
        $siswa = Siswa::create(['kelas_id' => $kelas->id]);

        $this->actingAs($ktu);
        $response = $this->get(route('absensi.rekap-siswa', [
            'kelas_id' => $kelas->id,
            'mata_pelajaran_id' => $mapel->id
        ]));
        $response->assertStatus(200);
        $response->assertSee($siswa->nama_lengkap);
    }
}
