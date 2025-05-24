<?php

namespace Modules\Order\Traits;
use Modules\Order\Mail\Dashboard\UpdateOrderStatusMail;
use Modules\User\Entities\User;
use Modules\User\Entities\UserFireBaseToken;
use Modules\Notification\Traits\SendNotificationTrait;


trait NewOrderNotificationTrait
{
    use SendNotificationTrait;

    private function fireNotificationToDrivers($order)
    {
        // fire notification to drivers that have the same order state
        $orderStateId = optional($order->orderAddress)->state_id ?? null;
        $matchedDrivers = User::whereHas('driverStates', function ($query) use ($orderStateId) {
            $query->where('driver_state.status', 1);
            $query->where('driver_state.state_id', $orderStateId);
        })->get();

        if ($matchedDrivers->count() > 0) {
            foreach ($matchedDrivers as $key => $driver) {
                $this->sendMobileNotify($order, $driver->id, 'driver_app');
                $driverEmail = $driver->email ?? null;
                if (!is_null($driverEmail)) {
                    $this->sendEmailNotify($order, $driverEmail);
                }
            }
        }
    }

    private function fireNotificationToDriver($order,$driver)
    {
        $this->sendMobileNotify($order, $driver->id, 'driver_app');
        $driverEmail = $driver->email ?? null;
        if (!is_null($driverEmail)) {
            $this->sendEmailNotify($order, $driverEmail);
        }
    }

    public function sendMobileNotify($order, $userId, $googleAPIKeyType = 'main_app')
    {
        $tokens = $this->getAllUserTokens($userId);
        if (count($tokens) > 0) {
            $data = [
                'title' => __('order::dashboard.orders.notification.new_order_title'),
                'body' => __('order::dashboard.orders.notification.new_order_body'),
                'type' => 'order',
                'id' => $order->id,
            ];
            $this->send($data, $tokens, $googleAPIKeyType);
        }
        return true;
    }

    public function sendEmailNotify($order, $email)
    {
        \Mail::to($email)->send(new UpdateOrderStatusMail($order));
        return true;
    }

    private function getAllUserTokens($userId)
    {
        return UserFireBaseToken::where('user_id', $userId)->pluck('firebase_token')->toArray();
    }
}
