<?php

namespace App\Tests\Api;

use App\Tests\Support\ApiTester;

class ApiTokenCest
{
    public function invalidTokenTest(ApiTester $I):void
    {
        $I->amBearerAuthenticated('INVALID_TOKEN');
        $I->wantTo('SENDING CSV FILE WITH INVALID TOKEN');

        $files = [
            'file' => [
                'name' => 'test.csv',
                'type' => 'text/csv',
                'size' => filesize(codecept_data_dir('test.csv')),
                'tmp_name' => codecept_data_dir('test.csv'),
                'error' => 0,
            ]
        ];

        $I->sendPost('/csv2json', [], $files);

        $I->seeResponseCodeIs(401);
    }
}
