<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('DELETE FROM Categories');
    }

    public function testTransactionSuccess()
    {
        DB::transaction(function(){
            DB::insert('INSERT INTO Categories(id, name, description, created_at)values(?,?,?,?)',[
            'GADGET', 'Gadget', 'Gadget Category', '2020-10-10 10:10:10'
            ]);

            DB::insert('INSERT INTO Categories(id, name, description, created_at)values(?,?,?,?)',[
            'FOOD', 'Food', 'Food Category', '2020-10-10 10:10:10'
        ]);
        });

       $result =  DB::select('SELECT * FROM Categories');
        self::assertCount(2, $result);
    }

    public function testTransactionFailed()
    {
        
        try{
            DB::transaction(function(){
                DB::insert('INSERT INTO Categories(id, name, description, created_at)values(?,?,?,?)',[
                'GADGET', 'Gadget', 'Gadget Category', '2020-10-10 10:10:10'
                ]);

                DB::insert('INSERT INTO Categories(id, name, description, created_at)values(?,?,?,?)',[
                'GADGET', 'Food', 'Food Category', '2020-10-10 10:10:10'
            ]);
        });
        }catch(QueryException $error){

        }
       $result =  DB::select('SELECT * FROM Categories');
        self::assertCount(0, $result);
    }

    public function testManualTransactionSuccess()
    {
        
        try{
            DB::beginTransaction();
            DB::transaction(function(){
                DB::insert('INSERT INTO Categories(id, name, description, created_at)values(?,?,?,?)',[
                'GADGET', 'Gadget', 'Gadget Category', '2020-10-10 10:10:10'
                ]);

                DB::insert('INSERT INTO Categories(id, name, description, created_at)values(?,?,?,?)',[
                'FOOD', 'Food', 'Food Category', '2020-10-10 10:10:10'
                ]);
            });
            DB::commit();
        }catch(QueryException $error){
            DB::rollBack();
        }
       $result =  DB::select('SELECT * FROM Categories');
        self::assertCount(2, $result);
    }

    public function testManualTransactionFailed()
    {
        
        try{
            DB::beginTransaction();
            DB::transaction(function(){
                DB::insert('INSERT INTO Categories(id, name, description, created_at)values(?,?,?,?)',[
                'GADGET', 'Gadget', 'Gadget Category', '2020-10-10 10:10:10'
                ]);

                DB::insert('INSERT INTO Categories(id, name, description, created_at)values(?,?,?,?)',[
                'GADGET', 'Food', 'Food Category', '2020-10-10 10:10:10'
                ]);
            });
            DB::commit();
        }catch(QueryException $error){
            DB::rollBack();
        }
       $result =  DB::select('SELECT * FROM Categories');
        self::assertCount(0, $result);
    }
    
}
