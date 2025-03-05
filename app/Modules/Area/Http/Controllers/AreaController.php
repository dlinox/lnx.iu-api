<?php

namespace App\Modules\Area\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\Area\Http\Requests\AreaStoreRequest;
use App\Modules\Area\Http\Requests\AreaUpdateRequest;
use App\Modules\Area\Models\Area;
use App\Modules\Area\Http\Resources\AreaDataTableItemsResource;

class AreaController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {
            $items = Area::select(
                'areas.id',
                'areas.name',
                'areas.is_enabled',
                'areas.curriculum_id',
                'curriculums.name as curriculum'
            )
                ->join('curriculums', 'areas.curriculum_id', '=', 'curriculums.id')
                ->where('areas.curriculum_id', 'LIKE', '%' . $request->filters['curriculumId'] . '%')
                ->dataTable($request);
            AreaDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function store(AreaStoreRequest $request)
    {
        try {
            $data =  $request->validated();
            $item = Area::create($data);
            return ApiResponse::success($item, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function update(AreaUpdateRequest $request)
    {
        try {
            $data = $request->validated();
            $item = Area::find($request->id);
            $item->update($data);
            return ApiResponse::success($request->all(), 'Registro actualizado correctamente', 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $item = Area::find($request->id);
            $item->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function getItemsForSelect(Request $request)
    {
        try {
            $item = Area::select('id as value', 'name as label')
                ->when($request->has('curriculumId'), function ($query) use ($request) {
                    return $query->where('curriculum_id', $request->curriculumId);
                })
                ->enabled()->get();
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
