<?php

namespace App\Trait;

use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Statement;

trait ImportCSV {

    protected $data;

    public function import($header, $fileName, $content, $delimiter = ',', $totalLines = 0) {

        // salvando arquivo temporariamente
        Storage::disk('public')->put($fileName, $content);

        $csv = Reader::from(Storage::disk('public')->path($fileName));
        $csv->setHeaderOffset(0);

        // realiza validação do header


        // valida delimitador de espaço
        $csv->setDelimiter(';');
        $regex = '/['.$delimiter.']/';
        if (!preg_match($regex, $csv->getDelimiter())) {

            // ajustar
            return response()->json([
                'status' => false,
                'message' => "O delimitador do arquivo CSV precisa ser ({$delimiter})."
            ], 400);
        }

        $csv->setEscape('');
        if ($csv->count() > $totalLines) {

            // ajustar
            return response()->json([
                'status' => false,
                'message' => 'O CSV deve possuir apenas 25 linhas de dados sem considerar o CABEÇALHO.'
            ], 400);
        }


        $stmt    = new Statement();
        $records = $stmt->process($csv);

        foreach ($records as $record) {


            collect($record)->each(function ($row) {
                $rowExpense = explode(",", $row);
                $this->validatedRow($rowExpense);

                // realiza validação dos dados
                dd('parei');

            });
        }

        Storage::disk('public')->delete($fileName);
    }

}
