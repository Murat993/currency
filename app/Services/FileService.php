<?php

namespace App\Services;

class FileService
{
    public function saveRatesToFile(array $ratesData): void
    {
        $filename = public_path('cbr_rates_' . date('Y-m-d_H-i-s') . '.csv');
        $file = fopen($filename, 'w');
        fputcsv($file, ['Date', 'Rate']);

        foreach ($ratesData as $date => $rate) {
            fputcsv($file, [$date, $rate]);
        }

        fclose($file);
    }
}
