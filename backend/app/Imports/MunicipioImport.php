<?php

namespace App\Imports;

use App\Models\V1\Catalogo\Municipio;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class MunicipioImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $value) {
            if (!is_null($value)) {
                $municipality = new Municipio();
                $municipality->nombre = $value[0];
                $municipality->departamento_id = $value[1];
                $municipality->save();
                echo "{$value[0]}" . PHP_EOL;
            }
        }
    }
}
