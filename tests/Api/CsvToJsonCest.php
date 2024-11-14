<?php

namespace App\Tests\Api;

use App\Tests\Support\ApiTester;

class CsvToJsonCest
{
    public function conversionFilterByTeamNameTest(ApiTester $I): void
    {
        $I->amBearerAuthenticated('TEST');
        $I->wantTo('SENDING CSV FILE WITH QUERY');

        $files = [
            'csv' => [
                'name' => 'csv',
                'type' => 'text/csv',
                'size' => filesize(codecept_data_dir('test.csv')),
                'tmp_name' => codecept_data_dir('test.csv'),
                'error' => 0,
            ]
        ];

        $I->sendPost('/csv2json?_q=Sales', [], $files);

        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseEquals(
            json_encode(
                json_decode(
                    file_get_contents(__DIR__ . '/../Support/Data/filtered.json')
                )
            )
        );
    }

    public function conversionTest(ApiTester $I): void
    {
        $I->amBearerAuthenticated('TEST');
        $I->wantTo('SENDING CSV FILE');

        $files = [
            'csv' => [
                'name' => 'csv',
                'type' => 'text/csv',
                'size' => filesize(codecept_data_dir('test.csv')),
                'tmp_name' => codecept_data_dir('test.csv'),
                'error' => 0,
            ]
        ];

        $I->sendPost('/csv2json', [], $files);

        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseEquals(
            json_encode(
                json_decode(
                    file_get_contents(__DIR__ . '/../Support/Data/unfiltered.json')
                )
            )
        );
    }
}
