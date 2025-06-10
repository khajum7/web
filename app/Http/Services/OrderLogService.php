<?php

namespace App\Http\Services;

use App\Models\OrderLog;
use Illuminate\Support\Facades\Auth;

class OrderLogService
{
    public function saveOrderLog($data): void
    {
        try {
            $user   = Auth::user();
            $log    = new OrderLog();

            $log->sale_id       = data_get($data, 'sale_id');
            $log->comment       = data_get($data, 'comment');
            $log->created_by    = $user->id;
            $log->updated_by    = $user->id;

            $log->save();
        }catch (\Exception $exception){

        }
    }

}
