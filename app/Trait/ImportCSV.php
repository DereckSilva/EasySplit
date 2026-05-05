<?php

namespace App\Trait;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use League\Csv\AbstractCsv;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\Response;

trait ImportCSV {

    use ResponseHttp;

    protected $data;

    public function uplaod($header, $fileName, $content, $delimiter = ',', $totalLines = 0) {

        // salvando arquivo temporariamente
        Storage::disk('public')->put($fileName, $content);

        $csv = Reader::from(Storage::disk('public')->path($fileName));
        $csv->setHeaderOffset(0);


        dd($content);
        // realiza validação do header


        // valida delimitador de espaço
        $csv->setDelimiter(';');
        $regex = '/['.$delimiter.']/';
        if (!preg_match($regex, $csv->getDelimiter())) {

            // ajustar
            return $this->returnExceptionErrorRequest(false, "O delimitador do arquivo CSV precisa ser ({$delimiter})", Response::HTTP_BAD_REQUEST, false);
        }

        $csv->setEscape('');
        if ($csv->count() > $totalLines) {

            // ajustar
            return $this->returnExceptionErrorRequest(false, "O CSV deve possuir apenas 25 linhas de dados sem considerar o CABEÇALHO.", Response::HTTP_BAD_REQUEST, false);
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

    public function importHeaders(array $headers) {
        return  Writer::fromString(implode(',', $headers));
    }

}
