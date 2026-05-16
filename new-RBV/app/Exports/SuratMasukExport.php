<?php

namespace App\Exports;

use App\Models\SuratMasuk;
use Maatwebsite\Excel\Concerns\FromCollection;

class SuratMasukExport implements FromCollection
{
    public function collection()
    {
        return SuratMasuk::select(
            'nomor_agenda',
            'nomor_surat',
            'asal_surat',
            'perihal',
            'prioritas',
            'status',
            'tanggal_masuk'
        )->get();
    }
}