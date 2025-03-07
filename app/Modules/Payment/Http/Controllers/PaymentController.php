<?php

namespace App\Modules\Payment\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Http\Responses\ApiResponse;
use App\Modules\Payment\Models\Payment;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct()
    {
        $this->paymentService = new PaymentService();
    }

    public function validate(Request $request)
    {
        try {
            $data = $request->all();
            $request['date'] = Carbon::createFromTimestampMs($request->date)->format('Y-m-d');
            $validate = $this->paymentService::validatePaymentBank($data);

            if (!$validate) {
                throw new \Exception('El pago no es válido, verifique los datos y asegúrese de la fecha de pago sea con un día de antisipación');
            }
            $payment = Payment::where('amount', $data['amount'])
                ->where('date', $data['date'])
                ->where('sequence_number', $data['sequenceNumber'])
                // ->where('payment_type_id', $data['paymentTypeId'])
                // ->where('student_id', $data['studentId'])
                ->first();

            if ($payment) {
                if ($payment->student_id != $data['studentId']) throw new \Exception('El pago ya fue registrado por otro estudiante');
                if ($payment->is_used == true) throw new \Exception('El pago ya fue utilizado');
            } else {
                $payment = Payment::registerItem($data);
            }

            $paymentToken = Crypt::encrypt($payment->id);

            $payment = [
                'token' => $paymentToken,
            ];
            return ApiResponse::success($payment, 'Pago validado correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al validar el pago');
        }
    }
}
