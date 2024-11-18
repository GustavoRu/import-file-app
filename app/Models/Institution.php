<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Aws\DynamoDb\DynamoDbClient;
use App\Services\DynamoDbService;

class Institution extends Model
{
    protected $fillable = ['institution_code', 'loan_amounts'];

    protected static function getDynamoDbClient()
    {
        
        $dynamoDbService = app(DynamoDbService::class); //IoC para obtener la instancia del servicio
        return $dynamoDbService->getClient(); 
    }

    public static function createTable()
    {
        $client = self::getDynamoDbClient();

        // Verificar si la tabla ya existe
        $existingTables = $client->listTables()['TableNames'];

        if (in_array('Institutions', $existingTables)) {
            // echo "La tabla 'Institutions' ya existe.\n";
            return;
        }

        $result = $client->createTable([
            'TableName' => 'Institutions',
            'KeySchema' => [
                [
                    'AttributeName' => 'institution_code',
                    'KeyType' => 'HASH', // Partition key
                ],
            ],
            'AttributeDefinitions' => [
                [
                    'AttributeName' => 'institution_code',
                    'AttributeType' => 'N',
                ],
            ],
            'ProvisionedThroughput' => [
                'ReadCapacityUnits' => 5,
                'WriteCapacityUnits' => 5,
            ],
        ]);

        return $result;
    }
}
