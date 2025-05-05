<?php

namespace App\Modules\Module\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Module\Http\Requests\ModuleStoreRequest;
use App\Modules\Module\Http\Requests\ModuleUpdateRequest;
use App\Modules\Module\Models\Module;
use App\Modules\Module\Http\Resources\ModuleDataTableItemsResource;

class ModuleController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {
            $items = Module::select(
                'modules.id',
                'modules.name',
                'modules.code',
                'modules.level',
                'modules.description',
                'modules.curriculum_id',
                'modules.is_extracurricular',
                'modules.is_enabled',
                'curriculums.name as curriculum',
            )
                ->join('curriculums', 'modules.curriculum_id', '=', 'curriculums.id')
                ->where('modules.curriculum_id', 'LIKE', '%' .   $request->filters['curriculumId'] . '%')
                ->dataTable($request);
            ModuleDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function store(ModuleStoreRequest $request)
    {
        try {
            $data =  $request->validated();
            $item = Module::create($data);
            return ApiResponse::success($item, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function update(ModuleUpdateRequest $request)
    {
        try {
            $data = $request->validated();
            $item = Module::find($request->id);
            $item->update($data);
            return ApiResponse::success($request->all(), 'Registro actualizado correctamente', 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $item = Module::find($request->id);
            $item->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function getItemsForSelect(Request $request)
    {
        try {
            $item = Module::select('id as value', 'name as label')
                ->when($request->has('curriculumId'), function ($query) use ($request) {
                    return $query->where('curriculum_id', $request->curriculumId);
                })
                ->enabled()->get();
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function getItemsCurriculumForSelect(Request $request)
    {
        try {
            $items = Module::select('id as value', 'name as label')
                ->where('curriculum_id', $request->id)
                ->where('is_enabled', 1)
                ->get();
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
