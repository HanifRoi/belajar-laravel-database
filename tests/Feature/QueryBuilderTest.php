<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QueryBuilderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('DELETE FROM Categories');
    }
    public function testInsert()
    {
        DB::table('Categories')->insert([
            'id'=>'GADGET',
            'name'=>'Gadget'
        ]);

        DB::table('Categories')->insert([
            'id'=>'FOOD',
            'name'=>'Food'
        ]);

        $result = DB::select('SELECT count(id) as total FROM Categories');

        self::assertEquals(2, $result[0]->total);

    }
}
