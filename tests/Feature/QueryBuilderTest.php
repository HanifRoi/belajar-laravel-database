<?php

namespace Tests\Feature;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    public function testSelect()
    {
        $this->testInsert();

        $collection = DB::table('Categories')->select(['id','name'])->get();
        self::assertNotNull($collection);

        $collection->each(function ($value, $key) {
            Log::info(json_encode($value));
        });
    }

    public function insertCategories()
    {
        DB::table('Categories')->insert([
            'id' => 'HP',
            'name' => 'Hp',
            'created_at'=> '2020-10-10 10:10:10'
        ]);
        DB::table('Categories')->insert([
            'id' => 'LAPTOP',
            'name' => 'Laptop',
            'created_at'=> '2020-10-10 10:10:10'
        ]);
        DB::table('Categories')->insert([
            'id' => 'GUNTING',
            'name' => 'Gunting',
            'created_at'=> '2020-10-10 10:10:10'
        ]);
        DB::table('Categories')->insert([
            'id' => 'SISIR',
            'name' => 'Sisir',
            'created_at'=> '2020-10-10 10:10:10'
        ]);
    }

    public function testWhere()
    {
        $this->insertCategories();

        $collection = DB::table('Categories')->where(function(Builder $builder){
            $builder->where('id', '=', 'HP');
            $builder->orWhere('id', '=', 'LAPTOP');
            //SELECT * FROM Categories WHERE (id = HP or id = LAPTOP);
        })->get();

        self::assertCount(2, $collection);
        $collection->each(function ($value, $key) {
            Log::info(json_encode($value));
        });
    }
    
    public function testWhereBetween()
    {
        $this->insertCategories();
        $collection = DB::table('Categories')->whereBetween('created_at', ['2020-10-10 10:10:10', '2020-10-10 11:11:11'])->get();

        self::assertCount(4, $collection);
        $collection->each(function ($value, $key) {
            Log::info(json_encode($value));
        });
    }

    public function testWhereIn()
    {
        $this->insertCategories();
        $collection = DB::table('Categories')->whereIn('id', ['HP', 'LAPTOP'])->get();

        self::assertCount(2, $collection);
        $collection->each(function ($value, $key) {
            Log::info(json_encode($value));
        });
    }

    public function testWhereNull()
    {
        $this->insertCategories();
        $collection = DB::table('Categories')->whereNull('description')->get();

        self::assertCount(4, $collection);
        $collection->each(function ($value, $key) {
            Log::info(json_encode($value));
        });
    }

    public function testWhereDate()
    {
        $this->insertCategories();
        $collection = DB::table('Categories')->whereDate('created_at', '2020-10-10')->get();

        self::assertCount(4, $collection);
        $collection->each(function ($value, $key) {
            Log::info(json_encode($value));
        });
    }

    public function testUpdate()
    {
        $this->insertCategories();

        DB::table('Categories')->where('id','=','HP')->update(['name' => 'Handphone']);

        $collection = DB::table('Categories')->where('name','=','Handphone')->get();

        self::assertCount(1, $collection);
        $collection->each(function ($value, $key) {
            Log::info(json_encode($value));
        });
    }

    public function testUpdateOrInsert()
    {
        DB::table('Categories')->updateOrInsert([
            'id'=>'VOUCHER'
        ], [
            'name' => 'Vouvher',
            'description'=>'Tiket dan Voucher',
            'created_at'=>'2020-10-10 10:10:10'
        ]);

        $collection = DB::table('Categories')->where('id','=','VOUCHER')->get();

        self::assertCount(1, $collection);
        $collection->each(function ($value, $key) {
            Log::info(json_encode($value));
        });
    }

    public function testIncrement()
    {
        DB::table('counters')->where('id', '=', 'sample')->increment('counter', 5);

        $collection = DB::table('counters')->where('id', '=', 'sample')->get();
        self::assertCount(1, $collection);
        $collection->each(function ($value, $key) {
            Log::info(json_encode($value));
        });
    }public function testDecrement()
    {
        DB::table('counters')->where('id', '=', 'sample')->decrement('counter', 5);

        $collection = DB::table('counters')->where('id', '=', 'sample')->get();
        self::assertCount(1, $collection);
        $collection->each(function ($value, $key) {
            Log::info(json_encode($value));
        });
    }

    public function testDelete()
    {
        $this->insertCategories();
        DB::table('categories')->where('id','=','HP')->delete();

        $collection = DB::table('categories')->where('id','=','HP')->get();

        self::assertCount(0, $collection);
        $collection->each(function ($value, $key) {
            Log::info(json_encode($value));
        });
    }



    








}
