<?php


namespace App;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $table = 'guests';
    protected $fillable = ['name','phone','email','address','password','status'];
    public $timestamps = true;
}
