<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Aws\DynamoDb\DynamoDbClient;

class Institution extends Model
{
    protected $fillable = ['institution_code', 'loan_amounts'];

    protected static function getDynamoDbClient()
    {
        return new DynamoDbClient([
            'region' => env('AWS_DEFAULT_REGION', 'us-west-2'),
            'version' => 'latest',
            'endpoint' => env('DYNAMODB_LOCAL_ENDPOINT', 'http://localhost:5000'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
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
