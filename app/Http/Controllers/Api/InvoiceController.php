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

use Illuminate\Http\Request;

use Tymon\JWTAuth\Facades\JWTAuth;

class InvoiceController extends Controller
{
    public function send(Request $request)
    {
        $company = Company::where('user_id', JWTAuth::user()->id)->first();

        $sunat = new SunatService();

        $see = $sunat->getSee($company);
        $invoice = $sunat->getInvoice();

        $result = $see->send($invoice);

        // Verificamos que la conexión con SUNAT fue exitosa.
        if (!$result->isSuccess()) {
            // Mostrar error al conectarse a SUNAT.
            echo 'Codigo Error: '.$result->getError()->getCode();
            echo 'Mensaje Error: '.$result->getError()->getMessage();
            exit();
        }

        $cdr = $result->getCdrResponse();

        $code = (int)$cdr->getCode();

        if ($code === 0) {
            echo 'ESTADO: ACEPTADA'.PHP_EOL;
            if (count($cdr->getNotes()) > 0) {
                echo 'OBSERVACIONES:'.PHP_EOL;
                // Corregir estas observaciones en siguientes emisiones.
                var_dump($cdr->getNotes());
            }  
        } else if ($code >= 2000 && $code <= 3999) {
            echo 'ESTADO: RECHAZADA'.PHP_EOL;
        } else {
            /* Esto no debería darse, pero si ocurre, es un CDR inválido que debería tratarse como un error-excepción. */
            /*code: 0100 a 1999 */
            echo 'Excepción';
        }

        echo $cdr->getDescription().PHP_EOL;
    }
}
