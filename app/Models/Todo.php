<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'todos';
    protected $primaryKey = 'id';

    protected $guarded = [];

    public static function list($gets){
        $obj = self::select('todos.id','todos.todo','todos.done','todos.datetime');
        if(isset($gets['key']) and $gets['key']!=''){
            $obj->where('todos.id','=',$gets['key']);
        }
        $list = $obj->get();
        return $list;
    }

    public static function add($post){
        try{
            unset($post['_token']);
            self::insert([
                ['todo'=>$post['todo'],'done'=>false,'datetime'=>now()]
            ]);
            // self::create($post);
            $arr=['error'=>0,'msg'=>'success'];
        }catch(\Exception $e){
            $arr=['error'=>1,'msg'=>'failed','eMsg'=>$e->getMessage()];
        }
        return $arr;
    }

    public static function find($id){
        $list = self::select('todos.id','todos.todo','todos.done','todos.datetime')
        ->where('todos.id','=',$id)
        ->first();
        return $list;
    }

    public static function del($id){
        try{
            self::destroy($id);
            $arr=['error'=>0,'msg'=>'success'];
        }catch(\Exception $e){
            $arr=['error'=>1,'msg'=>'failed','eMsg'=>$e->getMessage()];
        }
        return $arr;
    }

}
