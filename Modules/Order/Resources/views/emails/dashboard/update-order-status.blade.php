@component('mail::message')

    <div dir="rtl" style="text-align:right;">
        <h2>
            <center>تم تغيير حالة الطلب</center>
        </h2>

        @if($order->user)
        <p style="text-align:right;"> اسم العضو : {{ optional($order->user)->name ?? '' }}</p>
        @endif
        <p style="text-align:right;"> تاريخ الطلب : {{ $order->created_at }}</p>
        <p style="text-align:right;"> حالة الطلب : {{ optional(optional($order->orderStatus)->translate('ar'))->title ?? '' }}</p>

        @if($order->order_notes)
            <p style="text-align:right;"> ملاحظات : {{ $order->order_notes }}</p>
        @endif

        <br>
        <p style="text-align:right;">تطبيق {{ config('app.name') }} يرحب بكم دائما</p>


        <center>
            Thanks,
            <br>
            {{ config('app.name') }}
        </center>
    </div>


@endcomponent
