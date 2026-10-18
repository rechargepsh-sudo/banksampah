<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\TransaksiSetor;
use App\Models\TransaksiTarikDana;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $data = match ($user->role) {
            'admin' => $this->dataAdmin(),
            'kader' => $this->dataKader($user),
            default => $this->dataNasabah($user),
        };

        return view('dashboard', array_merge(['user' => $user], $data));
    }

    /**
     * Data statistik untuk dashboard Admin.
     *
     * @return array{
     *     totalUser: int,
     *     totalAdmin: int,
     *     totalKader: int,
     *     totalNasabah: int,
     *     totalNasabahTerdaftar: int,
     *     totalSaldoAktif: float,
     *     totalSetoran: int,
     *     totalNominalSetoran: float,
     *     setoranBulanIni: int,
     *     nominalBulanIni: float,
     *     penarikanPending: int,
     *     setoranTerbaru: Collection,
     * }
     */
    private function dataAdmin(): array
    {
        $now = now();

        return [
            'totalUser' => User::count(),
            'totalAdmin' => User::where('role', 'admin')->count(),
            'totalKader' => User::where('role', 'kader')->count(),
            'totalNasabah' => User::where('role', 'nasabah')->count(),
            'totalNasabahTerdaftar' => Nasabah::count(),
            'totalSaldoAktif' => (float) Nasabah::sum('saldo_aktif'),
            'totalSetoran' => TransaksiSetor::count(),
            'totalNominalSetoran' => (float) TransaksiSetor::sum('total_nominal'),
            'setoranBulanIni' => TransaksiSetor::whereMonth('tanggal_setor', $now->month)
                ->whereYear('tanggal_setor', $now->year)
                ->count(),
            'nominalBulanIni' => (float) TransaksiSetor::whereMonth('tanggal_setor', $now->month)
                ->whereYear('tanggal_setor', $now->year)
                ->sum('total_nominal'),
            'penarikanPending' => TransaksiTarikDana::where('status', 'pending')->count(),
            'setoranTerbaru' => TransaksiSetor::with(['nasabah', 'kader'])
                ->latest('tanggal_setor')
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * Data untuk dashboard Kader.
     *
     * @return array{
     *     totalNasabahDibina: int,
     *     totalSetoran: int,
     *     totalNominalSetoran: float,
     *     setoranBulanIni: int,
     *     nominalBulanIni: float,
     *     penarikanPending: int,
     *     setoranTerbaru: Collection,
     * }
     */
    private function dataKader(User $user): array
    {
        $now = now();

        return [
            'totalNasabahDibina' => Nasabah::where('created_by', $user->id)->count(),
            'totalSetoran' => TransaksiSetor::where('kader_id', $user->id)->count(),
            'totalNominalSetoran' => (float) TransaksiSetor::where('kader_id', $user->id)->sum('total_nominal'),
            'setoranBulanIni' => TransaksiSetor::where('kader_id', $user->id)
                ->whereMonth('tanggal_setor', $now->month)
                ->whereYear('tanggal_setor', $now->year)
                ->count(),
            'nominalBulanIni' => (float) TransaksiSetor::where('kader_id', $user->id)
                ->whereMonth('tanggal_setor', $now->month)
                ->whereYear('tanggal_setor', $now->year)
                ->sum('total_nominal'),
            'penarikanPending' => TransaksiTarikDana::whereHas('nasabah', function ($q) use ($user) {
                $q->where('created_by', $user->id);
            })->where('status', 'pending')->count(),
            'setoranTerbaru' => TransaksiSetor::with(['nasabah'])
                ->where('kader_id', $user->id)
                ->latest('tanggal_setor')
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * Data untuk dashboard Nasabah.
     *
     * @return array{
     *     nasabah: Nasabah|null,
     *     saldoAktif: float,
     *     totalSetoran: int,
     *     totalNominalSetoran: float,
     *     setoranTerbaru: Collection,
     *     penarikanTerbaru: Collection,
     * }
     */
    private function dataNasabah(User $user): array
    {
        $nasabah = $user->nasabah;

        if (! $nasabah) {
            return [
                'nasabah' => null,
                'saldoAktif' => 0.0,
                'totalSetoran' => 0,
                'totalNominalSetoran' => 0.0,
                'setoranTerbaru' => collect(),
                'penarikanTerbaru' => collect(),
            ];
        }

        return [
            'nasabah' => $nasabah,
            'saldoAktif' => (float) $nasabah->saldo_aktif,
            'totalSetoran' => $nasabah->transaksiSetor()->count(),
            'totalNominalSetoran' => (float) $nasabah->transaksiSetor()->sum('total_nominal'),
            'setoranTerbaru' => $nasabah->transaksiSetor()
                ->latest('tanggal_setor')
                ->limit(5)
                ->get(),
            'penarikanTerbaru' => $nasabah->transaksiTarikDana()
                ->latest('tanggal_tarik')
                ->limit(5)
                ->get(),
        ];
    }
}
