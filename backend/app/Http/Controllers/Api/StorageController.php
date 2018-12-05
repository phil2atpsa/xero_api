<?php
/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/12/04
 * Time: 11:46
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

class StorageController extends Controller
{

    public function invoice_template_path()
    {
       return response()->download(storage_path('templates').config('export_templates.path.invoice'));
    }

}