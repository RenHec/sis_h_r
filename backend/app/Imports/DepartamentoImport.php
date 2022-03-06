<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\Models\V1\Catalogo\Departamento;
use Maatwebsite\Excel\Concerns\ToCollection;

class DepartamentoImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $value) {
            if (!is_null($value)) {
                $deparamento = new Departamento();
                $deparamento->nombre = $value[0];
                $deparamento->save();
                echo "{$value[0]} {$value[1]}" . PHP_EOL;
            }
        }
    }
}
