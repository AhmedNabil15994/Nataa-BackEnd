<?php

namespace Modules\Setting\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Area\Entities\Country;
use Modules\Setting\Http\Requests\Dashboard\SettingRequest;
use Modules\Area\Repositories\Dashboard\CountryRepository;
use Modules\Order\Repositories\Dashboard\OrderStatusRepository;
use Modules\Setting\Repositories\Dashboard\SettingRepository as Setting;
use Monarobase\CountryList\CountryListFacade as Countries;

class SettingController extends Controller
{
    protected $setting;
    protected $country;
    protected $orderStatus;

    function __construct(Setting $setting, CountryRepository $country, OrderStatusRepository $orderStatus)
    {
        $this->setting = $setting;
        $this->country = $country;
        $this->orderStatus = $orderStatus;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('setting::dashboard.index');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     */
    public function update(SettingRequest $request)
    {
        DB::beginTransaction();

        try {
            ####################### START Sync Supported Countries With Stored Countries In DB #####################
            if ($request->countries) {

                $countries = [];
                $res = $this->syncRelation(Country::query(), $request->countries);
                $added = array_merge($res['deleted'], $res['updated']);
                $res['added'] = [];

                if (count($request->countries) > 0) {
                    foreach ($request->countries as $key => $countryCode) {
                        if (!in_array($countryCode, $added)) {
                            $res['added'][] = $countryCode;
                        }
                    }
                }

                if ($res['deleted']) {
                    $deleted = Country::whereIn('code', $res['deleted'])->pluck('id')->toArray();
                    if (count($deleted) > 0) {
                        Country::whereIn('id', $deleted)->delete();
                    }
                }

                if (count($res['added']) > 0) {
                    foreach ($res['added'] as $key => $countryCode) {
                        foreach (config('translatable.locales') as $k => $code) {
                            $countries[$key]['code'] = $countryCode;
                            $countries[$key]['status'] = 1;
                            $countries[$key]['title'][$code] = Countries::getOne($countryCode, $code);
                        }
                    }
                }

                if (count($countries) > 0)
                    $this->country->createFromSettings($countries);
            }

            ####################### END Sync Supported Countries With Stored Countries In DB #####################

            ### Start - Update Order Status In Model ###
            if ($request->order_status) {
                $this->orderStatus->updateColorInSettings($request->order_status);
            }
            ### End - Update Order Status In Model ###

            $this->setting->set($request);

            DB::commit();
            return redirect()->back()->with(['msg' => __('setting::dashboard.settings.form.messages.settings_updated_successfully'), 'alert' => 'success']);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function syncRelation($model, $incomingValues = null)
    {
        $oldIds = $model->pluck('code')->toArray();
        $data['deleted'] = array_values(array_diff($oldIds, $incomingValues));
        $data['updated'] = array_values(array_intersect($oldIds, $incomingValues));
        return $data;
    }
}
