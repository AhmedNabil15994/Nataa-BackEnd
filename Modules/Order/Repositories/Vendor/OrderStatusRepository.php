<?php
namespace Modules\Order\Repositories\Vendor;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Modules\Core\Traits\CoreTrait;
use Modules\Order\Entities\OrderStatus;
use Illuminate\Support\Facades\DB;
use Modules\Order\Enum\Order as OrderEnums;

class OrderStatusRepository
{
    use CoreTrait;

    protected $orderStatus;

    function __construct()
    {
        $this->orderStatus = new OrderStatus;
    }

    public function getAll($order = 'sort', $sort = 'asc')
    {
        $orderStatuses = $this->orderStatus->orderBy($order, $sort)->get();
        return $orderStatuses;
    }

    public function getAllFinalStatus($order = 'sort', $sort = 'desc')
    {
        $orderStatuses = $this->orderStatus->finalStatus()->orderBy($order, $sort)->get();
        return $orderStatuses;
    }

    public function findById($id)
    {
        $orderStatus = $this->orderStatus->find($id);
        return $orderStatus;
    }

    public function create($request)
    {
        DB::beginTransaction();

        try {
            $data = [
                'flag' => $request->flag,
                'color_label' => \GuzzleHttp\json_encode($this->setOrderStatusColorValue($request->color_label)),
                'is_success' => $request->is_success,
                'sort' => $request->sort ?? 0,
            ];
            if (!is_null($request->image)) {
                $imgName = $this->uploadImage(public_path(config('core.config.orders_img_path')), $request->image);
                $data['image'] = config('core.config.orders_img_path') . '/' . $imgName;
            } else {
                $data['image'] = url(config('setting.logo'));
            }
            $orderStatus = $this->orderStatus->create($data);
            $this->translateTable($orderStatus, $request);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();

        $orderStatus = $this->findById($id);
        $restore = $request->restore ? $this->restoreSoftDelete($orderStatus) : null;

        try {
            $data = [
                'color_label' => \GuzzleHttp\json_encode($this->setOrderStatusColorValue($request->color_label)),
                'is_success' => $request->is_success,
                'sort' => $request->sort ?? 0,
            ];
            $newFlag = Str::slug($request->title['en'], '_');
            if (!in_array($orderStatus->flag, ['pending', 'cancelled', 'refund', 'success', 'failed', 'delivered', 'new_order']))
                $data['flag'] = $newFlag;

            if ($request->image) {
                if (!empty($orderStatus->image) && !in_array($orderStatus->image, config('core.config.special_images'))) {
                    File::delete($orderStatus->image); ### Delete old image
                }
                $imgName = $this->uploadImage(public_path(config('core.config.orders_img_path')), $request->image);
                $data['image'] = config('core.config.orders_img_path') . '/' . $imgName;
            } else {
                $data['image'] = $orderStatus->image;
            }

            $orderStatus->update($data);
            $this->translateTable($orderStatus, $request);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function setOrderStatusColorValue($colorLabel = '')
    {
        $color = [];
        $color['text'] = $colorLabel;
        switch ($colorLabel) {
            case "danger":
                $color['value'] = '#F8D7DA';
                break;
            case "success":
                $color['value'] = '#D4EDDA';
                break;
            case "warning":
                $color['value'] = '#FCF3CD';
                break;
            case "info":
                $color['value'] = '#D1EBF1';
                break;
            default:
                $color['value'] = '#000000';
        }
        return $color;
    }

    public function restoreSoftDelete($model)
    {
        $model->restore();
        return true;
    }

    public function translateTable($model, $request)
    {
        foreach ($request['title'] as $locale => $value) {
            $model->translateOrNew($locale)->title = $value;
        }

        $model->save();
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {

            $model = $this->findById($id);
            if ($model && !empty($model->image) && !in_array($model->image, config('core.config.special_images'))) {
                File::delete($model->image); ### Delete old image
            }

            if (!in_array($model->flag, ['pending', 'cancelled', 'refund', 'success', 'failed', 'delivered', 'new_order']))
                $model->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function deleteSelected($request)
    {
        DB::beginTransaction();

        try {

            foreach ($request['ids'] as $id) {
                $model = $this->delete($id);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function updateColorInSettings($items)
    {
        try {

            foreach ($items as $k => $value) {
                $this->orderStatus->where('flag', $k)->update([
                    'color' => $value ?? null
                ]);
            }

            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function QueryTable($request)
    {
        $query = $this->orderStatus->where(function ($query) use ($request) {
            $query->where('id', 'like', '%' . $request->input('search.value') . '%');
            $query->orWhereHas('translations', function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->input('search.value') . '%');
            });
        });

        $query = $this->filterDataTable($query, $request);

        return $query;
    }

    public function filterDataTable($query, $request)
    {
        if (isset($request['req']['from']) && $request['req']['from'] != '')
            $query->whereDate('created_at', '>=', $request['req']['from']);

        if (isset($request['req']['to']) && $request['req']['to'] != '')
            $query->whereDate('created_at', '<=', $request['req']['to']);

        if (isset($request['req']['deleted']) && $request['req']['deleted'] == 'only')
            $query->onlyDeleted();

        if (isset($request['req']['deleted']) && $request['req']['deleted'] == 'with')
            $query->withDeleted();

        if (isset($request['req']['status']) && $request['req']['status'] == '1')
            $query->active();

        if (isset($request['req']['status']) && $request['req']['status'] == '0')
            $query->unactive();

        return $query;
    }
}
