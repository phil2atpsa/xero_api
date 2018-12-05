<?php
/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/11/22
 * Time: 12:35
 */

namespace App\Http\Controllers\Api;
use App\Services\XeroService;
use XeroPHP\Application\PrivateApplication;
use  App\Http\Controllers\Controller;

class OrganisationController extends Controller
{
    private $xero;
    public function __construct(XeroService $xeroService)
    {
        $this->xero = $xeroService;
    }

    public function index()
    {
        $organisation = $this->xero->load('Accounting\\Organisation')
            ->execute()
            ->first();
        return response()->json($organisation->toStringArray(), 200);

    }

}
