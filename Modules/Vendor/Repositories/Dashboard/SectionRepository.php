<?php

namespace Modules\Vendor\Repositories\Dashboard;

use Modules\Vendor\Entities\Section;
use Hash;
use DB;

class SectionRepository
{

    function __construct(Section $section)
    {
        $this->section = $section;
    }

    /*
    * Frontend Queries
    */
    public function getAllActive($order = 'id', $sort = 'desc')
    {
        $sections = $this->section->active()->orderBy($order, $sort)->get();
        return $sections;
    }

    public function getAllActiveWithVendors()
    {
        $sections = $this->section->whereHas('vendors', function ($query) {

            $query->active()->whereHas('subbscription', function ($query) {

                $query->active()->unexpired()->started();

            });

        })->active()->inRandomOrder()->take(10)->get();

        return $sections;
    }

    public function findBySlug($slug)
    {
        $section = $this->section->whereHas('vendors', function ($query) {

            $query->active()->whereHas('subbscription', function ($query) {

                $query->active()->unexpired()->started();

            });

        })->whereTranslation('slug', $slug)->first();

        return $section;
    }

    public function checkRouteLocale($model, $slug)
    {
        if ($model->translate()->where('slug', $slug)->first()->locale != locale())
            return false;

        return true;
    }

    /*
    * Dashboard Queries
    */
    public function getAll($order = 'id', $sort = 'desc')
    {
        $sections = $this->section->orderBy($order, $sort)->get();
        return $sections;
    }

    public function findById($id)
    {
        $section = $this->section->withDeleted()->find($id);
        return $section;
    }

    public function create($request)
    {
        DB::beginTransaction();

        try {

            $section = $this->section->create([
                'status' => $request->status ? 1 : 0,
                'image' => $request->image ? path_without_domain($request->image) : null,
            ]);

            $this->translateTable($section, $request);

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

        $section = $this->findById($id);
        $restore = $request->restore ? $this->restoreSoftDelte($section) : null;

        try {

            $section->update([
                'status' => $request->status ? 1 : 0,
                'image' => $request->image ? path_without_domain($request->image) : $section->image,
            ]);

            $this->translateTable($section, $request);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function restoreSoftDelte($model)
    {
        $model->restore();
    }

    public function translateTable($model, $request)
    {
        foreach ($request['title'] as $locale => $value) {
            $model->translateOrNew($locale)->title = $value;
            $model->translateOrNew($locale)->description = $request['description'][$locale];
            $model->translateOrNew($locale)->seo_description = $request['seo_description'][$locale];
            $model->translateOrNew($locale)->seo_keywords = $request['seo_keywords'][$locale];
        }

        $model->save();
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {

            $model = $this->findById($id);

            if ($model->trashed()):
                $model->forceDelete();
            else:
                $model->delete();
            endif;

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

    public function QueryTable($request)
    {
        $query = $this->section->with('translations');

        $query->where(function ($query) use ($request) {
            $query
                ->where('id', 'like', '%' . $request->input('search.value') . '%')
                ->orWhere(function ($query) use ($request) {
                    $query->whereHas('translations', function ($query) use ($request) {
                        $query->where('description', 'like', '%' . $request->input('search.value') . '%');
                        $query->orWhere('title', 'like', '%' . $request->input('search.value') . '%');
                        $query->orWhere('slug', 'like', '%' . $request->input('search.value') . '%');
                    });
                });
        });

        return $this->filterDataTable($query, $request);
    }

    public function filterDataTable($query, $request)
    {
        // Search Pages by Created Dates
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
