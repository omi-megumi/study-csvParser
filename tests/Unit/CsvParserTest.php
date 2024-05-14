<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class CsvParserTest extends TestCase
{
    /**
     * @return void
     */
    public function testCsvParser()
    {
        // サンプルデータ(5行) の読込
        $firstHeader = ['csv_header1', 'csv_header2', 'csv_header3', 'csv_header4', 'csv_header5'];
        $firstData = [
            ['v1', 'v2', 'v3', 'v4', 'v5'],
            ['v11', 'v12', 'v13', 'v14', 'v15'],
            ['v21', 'v22', 'v23', 'v24', 'v25'],
            ['v31', 'v32', 'v33', 'v34', 'v35'],
            ['v41', 'v42', 'v43', 'v44', 'v45'],
        ];
        /** @var CsvParser $csvParser */
        $csvParser = (new CsvParser($firstHeader))->load($firstData);
        $this->assertEquals(5, $csvParser->count());
        $this->assertEquals('Tests\Unit\CsvRow', get_class($csvParser->find(1)));
        $this->assertEquals('v2', $csvParser->find(1)->csv_header2);
        $this->assertEquals('v23', $csvParser->find(3)->csv_header3);
        $this->assertEquals('v45', $csvParser->find(5)->csv_header5);

        // サンプルデータ(1行) の読込
        $csvParser->load([
            ['v11', 'v12', 'v13', 'v14', 'v15'],
        ]);
        $this->assertEquals(1, $csvParser->count());

        // 住所データ(6行)の読込
        $secondHeader = ['code', 'name', 'post', 'prefecture', 'city', 'address1', 'address2'];
        $secondData = [
            ['TAISHI', '株式会社大自然', '078-8306', '北海道', '旭川市', '神旭町29番地26', ''],
            ['RINB', '株式会社リカベース', '990-0831', '山形県', '山形市', '田西35丁目25番3号', ''],
            ['C195', '株式会社クリエイティブダイブ', '963-0725', '福島県', '郡山市', '田村町金屋大字19番地', ''],
            ['DPK', 'デイボーク株式会社', '330-0841', '埼玉県', 'さいたま市大宮区', '東町13丁目231番地4', ''],
            ['AMAZON', 'アマゾンジャパン合同会社', '272-0193', '千葉県', '市川市', '塩浜2-13-1', ''],
            ['RISE', '株式会社ライズエージェンツー', '108-0073', '東京都', '港区', '三田13丁目7番18号', 'ザ・ヤマイトビル72F'],
        ];
        $csvParser = (new CsvParser($secondHeader))->load($secondData);
        $this->assertEquals(6, $csvParser->count());
        $this->assertEquals('078-8306', $csvParser->find(1)->post);
        $this->assertEquals('C195', $csvParser->find(3)->code);
        $this->assertEquals('塩浜2-13-1', $csvParser->find(5)->address1);
    }
}


class CsvParser
{
    private $header;
    private $data = [];

    public function __construct(array $header)
    {
        $this->header = $header;
    }
    // 必須要件：csvデータを読み込む
    public function load(array $data)
    {
        $this->data = [];
        foreach($data as $rowData){
            $this->data[] = new CsvRow($this->header, $rowData);
        }
        return $this;
    }

    // 必須要件：読み込んだcsvデータの件数を返す
    public function count(): int
    {
        return count($this->data);
    }
    // 必須要件：引数で指定された行のデータを CsvRow のインスタンスとして返す
    public function find(int $index) // ？？型
    {
        return $this->data[$index - 1];
    }
}

class CsvRow
{
    private $data = [];

    public function __construct(array $header, array $rowData)
    {
        foreach ($header as $index => $column) {
            $this->data[$column] = $rowData[$index];
        }
    }

    public function __get(string $name)
    {
        return $this->data[$name] ?? null;
    }
}
