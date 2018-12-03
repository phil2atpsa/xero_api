<?php
/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/11/23
 * Time: 08:42
 */

namespace App\Http\Controllers\Api;


use App\Services\XeroService;

class ClientsController extends ContactController
{
    public function __construct(XeroService $xeroService)
    {
        parent::__construct($xeroService);
    }
    public function index()
    {
        return response()->json($this->getContactList(config('enums.contact_type.customer')), 200);
    }

}
