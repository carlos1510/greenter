<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\SunatService;

use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company as ModelCompany;

use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Report\XmlUtils;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Facades\JWTAuth;

class InvoiceController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->all();
        
        $company = Company::where('user_id', JWTAuth::user()->id)
            ->where('ruc', $data['company']['ruc'])
            ->firstOrFail();

        $sunat = new SunatService();

        $see = $sunat->getSee($company);
        $invoice = $sunat->getInvoice($data);

        $result = $see->send($invoice);

        $response['xml'] = $see->getFactory()->getLastXml();
        $response['hash'] = (new XmlUtils())->getHashSign($response['xml']);
        $response['sunatResponse'] = $sunat->sunatResponse($result);

        return response()->json($response, 200);
    }
}
