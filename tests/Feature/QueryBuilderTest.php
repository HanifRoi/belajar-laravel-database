<?php

namespace Tests\Feature;

use Database\Seeders\CategorySeeder;
use Database\Seeders\CounterSeeder;
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
        DB::delete('DELETE FROM products');
        DB::delete('DELETE FROM categories');
        DB::delete('DELETE FROM counters');
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
        $this->seed(CategorySeeder::class);
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
        $this->seed(CounterSeeder::class);
        DB::table('counters')->where('id', '=', 'sample')->increment('counter', 5);

        $collection = DB::table('counters')->where('id', '=', 'sample')->get();
        self::assertCount(1, $collection);
        $collection->each(function ($value, $key) {
            Log::info(json_encode($value));
        });
    }public function testDecrement()
    {
        $this->seed(CounterSeeder::class);
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

    public function insertTableProduct()
    {
        $this->insertCategories();

        DB::table('products')->insert([
            'id'=>'1',
            'name'=>'Realme 5',
            'description'=>'Hp terlama gw',
            'price'=>'2700000',
            'category_id'=>'HP'
        ]);
        DB::table('products')->insert([
            'id'=>'2',
            'name'=>'Realme 6',
            'description'=>'Belum pegang',
            'price'=>'3000000',
            'category_id'=>'HP'
        ]);
    }

    public function testJoin()
    {
        $this->insertTableProduct();

        $collection = DB::table('products')
        ->join('categories', 'products.category_id','=','categories.id')
        ->select('products.id', 'products.name', 'categories.name as category_name', 'products.price')->get();

        self::assertCount(2, $collection);
        $collection->each(function ($value, $key) {
            Log::info(json_encode($value));
        });
    }
    
    public function testOrdering()
    {
        $this->insertTableProduct();
        $collection = DB::table('products')
        ->whereNotNull('id')
        ->orderBy('price', 'desc')
        ->orderBy('name', 'asc')->get();

        self::assertCount(2, $collection);
        $collection->each(function ($value, $key) {
            Log::info(json_encode($value));
        });
    }

    public function testPaging()
    {
        $this->insertTableProduct();

        $collection = DB::table('categories')
        ->skip(2)
        ->take(2)
        ->get();

        self::assertCount(2, $collection);
        $collection->each(function ($value, $key) {
            Log::info(json_encode($value));
        });

    }

    public function insertManyCategories()
    {

        for ($i = 0; $i < 100; $i++){
            DB::table('categories')->insert([
                'id' => "CATEGORY $i",
                'name' => "Category $i",
                'created_at'=> '2020-10-10 10:10:10'
            ]);
        }
    }

    public function testChunk()
    {
        $this->insertManyCategories();

        DB::table('categories')->orderBy('id')
        ->chunk(10, function($categories){
            self::assertNotNull($categories);
            Log::info('Start Chunk');
            $categories->each(function($category){
                Log::info(json_encode($category));
            });
            Log::info('End Chunk');
        });
    }

    public function testLazy()
    {
        $this->insertManyCategories();

        $collection = DB::table('categories')->orderBy("id")->lazy(10)->take(3);
        self::assertNotNull($collection);
        $collection->each(function ($value, $key) {
            Log::info(json_encode($value));
        });

    }

    public function testCursor()
    {
        $this->insertManyCategories();

        $collection = DB::table('categories')->orderBy("id")->cursor();
        self::assertNotNull($collection);
        $collection->each(function ($value, $key) {
            Log::info(json_encode($value));
        });

    }

    public function testAggregate()
    {
        $this->insertTableProduct();

        $result = DB::table('products')->count("id");
        self::assertEquals(2, $result);

        $result = DB::table('products')->max("price");
        self::assertEquals("3000000", $result);

        $result = DB::table('products')->min("price");
        self::assertEquals("2700000", $result);

        $result = DB::table('products')->sum("price");
        self::assertEquals("5700000", $result);

    }

    public function testRawAggregate()
    {
        $this->insertTableProduct();

        $collection = DB::table('products')
        ->select(
            DB::raw('count(*) as total_product'),
            DB::raw('min(price) as min_price'),
            DB::raw('max(price) as max_price')
        )->get();

        self::assertEquals(2, $collection[0]->total_product);
        self::assertEquals(2700000, $collection[0]->min_price);
        self::assertEquals(3000000, $collection[0]->max_price);
    }

    public function insertProductLaptop()
    {
        DB::table('products')->insert([
            'id'=>'3',
            'name'=>'Asus',
            'description'=>'Hp terlama gw',
            'price'=>'7000000',
            'category_id'=>'LAPTOP'
        ]);
        DB::table('products')->insert([
            'id'=>'4',
            'name'=>'Acer',
            'description'=>'Belum pegang',
            'price'=>'11000000',
            'category_id'=>'LAPTOP'
        ]);
    }

    public function testGroupBy()
    {
        $this->insertTableProduct();
        $this->insertProductLaptop();

        $collection = DB::table("products")
        ->select("category_id", DB::raw("count(*) as total_product"))
        ->groupBy("category_id")
        ->orderBy("category_id", "desc")
        ->get();

        self::assertCount(2, $collection);
        self::assertEquals("LAPTOP", $collection[0]->category_id);
        self::assertEquals("HP", $collection[1]->category_id);
        self::assertEquals(2, $collection[0]->total_product);
        self::assertEquals(2, $collection[1]->total_product);
    }

    public function testGroupByHaving()
    {
        $this->insertTableProduct();
        $this->insertProductLaptop();

        $collection = DB::table("products")
        ->select("category_id", DB::raw("count(*) as total_product"))
        ->groupBy("category_id")
        ->having(DB::raw("count(*)"), ">", 2)
        ->orderBy("category_id", "desc")
        ->get();

        self::assertCount(0, $collection);
    }

    public function testLocking()
    {
       $this->insertTableProduct();
        $collection = DB::table("products")
        ->where("id", "=", "1")
        ->lockForUpdate()
        ->get();

        self::assertCount(1, $collection);
    }

    public function testPagination()
    {
        $this->insertCategories();

        $paginate = DB::table("categories")->paginate(perPage: 2, page: 2);

        self::assertEquals(2, $paginate->currentPage());
        self::assertEquals(2, $paginate->perPage());
        self::assertEquals(2, $paginate->lastPage());
        self::assertEquals(4, $paginate->total());

        $collection = $paginate->items();
        self::assertCount(2, $collection);
        foreach ($collection as $item){
            Log::info(json_encode($item));
        }
    }

    public function testIteratreAllPagination()
    {
        $this->insertCategories();
        $page = 1;
        while (true) {
            $paginate = DB::table("categories")->paginate(perPage: 2, page: $page);

            if ($paginate->isEmpty()) {
                break;
            }else{
                $page++;
                $collection = $paginate->items();
                self::assertCount(2, $collection);
                foreach ($collection as $item){
                    Log::info(json_encode($item));
                }
            }

            
        }
        
    }

    public function testCursorPagination()
    {
        $this->insertCategories();
        $cursor = "id";
        while (true) {
            $paginate = DB::table("categories")->orderBy("id")->cursorPaginate(perPage:2, cursor:$cursor);

            foreach ($paginate->items() as $item) {
                self::assertNotNull($item);
                Log::info(json_encode($item));
            }

            $cursor = $paginate->nextCursor();

            if($cursor == null){
                break;
            }
        }
    }
}