<?php

namespace App\Exports;

use App\Models\SuratMasuk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SuratMasukExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return SuratMasuk::with([
            'tags.user'
        ])->latest()->get();
    }

    public function headings(): array
    {
        return [
            'TGL SURAT',
            'TGL SURAT DITERIMA',
            'NO. SURAT',
            'NOMOR AGENDA',
            'PERIHAL',
            'DARI',
            'DISPOSISI',
        ];
    }

    public function map($surat): array
    {
        $disposisi = $surat->tags
            ->filter(function ($tag) {

                return in_array(
                    $tag->user->id_jabatan ?? null,
                    [1, 2]
                );

            })
            ->map(function ($tag) {

                if ($tag->user->id_jabatan == 1) {
                    return 'Direktur';
                }

                if ($tag->user->id_jabatan == 2) {
                    return 'Kabag';
                }

            })
            ->unique()
            ->implode(', ');

        return [

            $surat->tanggal_surat
                ? \Carbon\Carbon::parse($surat->tanggal_surat)
                    ->format('d/m/Y')
                : '-',

            $surat->tanggal_masuk
                ? \Carbon\Carbon::parse($surat->tanggal_masuk)
                    ->format('d/m/Y')
                : '-',

            $surat->nomor_surat ?? '-',

            $surat->nomor_agenda ?? '-',

            $surat->perihal ?? '-',

            $surat->asal_surat ?? '-',

            $disposisi ?: '-',
        ];
    }
}