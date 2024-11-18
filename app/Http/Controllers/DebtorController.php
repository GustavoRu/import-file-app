<?php

namespace App\Http\Controllers;
use App\Models\Debtor;
use App\Models\Institution;
use Illuminate\Http\Request;
use App\Services\DynamoDbService;

class DebtorController extends Controller
{
    public function showUploadForm()
    {
        return view('debtor.upload');
    }

    public function show()
    {
        // Obtener los deudores desde DynamoDB
        $dynamoDb = new DynamoDbService();
        $debtors = $dynamoDb->scanItems('Debtors');
        $institutions = $dynamoDb->scanItems('Institutions');
        
        // Pasar los deudores a la vista
        return view('debtor.show', compact('debtors', 'institutions'));
    }

    public function processFile(Request $request)
    {
        // Validar que se subió un archivo
        // $request->validate([
        //     'file' => 'required|mimes:txt|max:10240', // Asegurarse de que sea un archivo TXT
        // ]);

        // Obtener el archivo
        $file = $request->file('file');

        $fileContent = file($file->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $debtors = [];
        $institutions = [];
        // Crear tablas si no existen
        $this->createTables();// esto podria haber sido un seeder
        try {
            foreach ($fileContent as $line) {
                $cuit = (int) trim(substr($line, 13, 11));
                $situation = (int) trim(substr($line, 26, 2));
                $loanAmount = (float) str_replace(',', '.', trim(substr($line, 28, 12)));
                $institution_code = (int) trim(substr($line, 0, 5));

                if (!isset($debtors[$cuit])) {
                    $debtors[$cuit] = [
                        'cuit' => $cuit,
                        'worst_situation' => $situation,
                        'loan_sum' => $loanAmount,
                    ];
                } else {
                    $debtors[$cuit]['worst_situation'] = max($debtors[$cuit]['worst_situation'], $situation);
                    $debtors[$cuit]['loan_sum'] += $loanAmount;
                }

                if (!isset($institutions[$institution_code])) {
                    $institutions[$institution_code] = [
                        'institution_code' => $institution_code,
                        'loan_amounts' => $loanAmount
                    ];
                } else {
                    $institutions[$institution_code]['loan_amounts'] += $loanAmount;
                }
            }

            $dynamoDb = new DynamoDbService();
            // Guardar deudores en DynamoDB
            foreach ($debtors as $debtorData) {
                if (!isset($debtorData['cuit']) || !isset($debtorData['worst_situation']) || !isset($debtorData['loan_sum'])) {
                    dd("Faltan campos requeridos para el deudor: " . json_encode($debtorData));
                }
                $dynamoDb->putItem('Debtors', [
                    'cuit' => ['N' => (string) $debtorData['cuit']],
                    'worst_situation' => ['N' => (string) $debtorData['worst_situation']],
                    'loan_sum' => ['N' => (string) $debtorData['loan_sum']],
                ]);
            }

            // Agrupar los deudores en lotes de 25
            // $chunks = array_chunk($debtors, 25);

            // foreach ($chunks as $chunk) {
            //     $requestItems = [];
            //     foreach ($chunk as $debtorData) {
            //         if (!isset($debtorData['cuit']) || !isset($debtorData['worst_situation']) || !isset($debtorData['loan_sum'])) {
            //             dd("Faltan campos requeridos para el deudor: " . json_encode($debtorData));
            //         }
            //         $requestItems = [
            //             'PutRequest' => [
            //                 'Item' => [
            //                     'cuit' => ['N' => (string) $debtorData['cuit']],
            //                     'worst_situation' => ['N' => (string) $debtorData['worst_situation']],
            //                     'loan_sum' => ['N' => (string) $debtorData['loan_sum']],
            //                 ]
            //             ]
            //         ];
            //     }

            //     // Usar batchWriteItem para insertar el lote
            //     $dynamoDb->batchWriteItems('Debtors', $requestItems);
            // }

            // Guardar instituciones en DynamoDB
            foreach ($institutions as $institutionData) {
                if (!isset($institutionData['institution_code']) || !isset($institutionData['loan_amounts'])) {
                    dd("Faltan campos requeridos para el deudor: " . json_encode($institutionData));
                }
                $dynamoDb->putItem('Institutions', [
                    'institution_code' => ['N' => (string) $institutionData['institution_code']],
                    'loan_amounts' => ['N' => (string) $institutionData['loan_amounts']],
                ]);
            }

            session()->put('success', 'Archivo procesado con exito!');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error al procesar archivo: ' . $e->getMessage());
        }
    }

    private function createTables()
    {
        try {
            // Crear tabla Debtors
            Debtor::createTable();
            // Crear tabla Institutions
            Institution::createTable();
        } catch (\Aws\Exception\AwsException $e) {
            throw new \Exception('Error al crear las tablas: ' . $e->getMessage());
        }
    }

}
