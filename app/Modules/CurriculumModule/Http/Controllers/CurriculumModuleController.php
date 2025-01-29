<?php

namespace App\Modules\CurriculumModule\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\CurriculumModule\Http\Requests\CurriculumModuleStoreRequest;
use App\Modules\CurriculumModule\Http\Requests\CurriculumModuleUpdateRequest;
use App\Modules\CurriculumModule\Http\Resources\CurriculumModuleDataTableItemsResource;
use App\Modules\CurriculumModule\Http\Resources\CurriculumModuleItemResource;
use App\Modules\CurriculumModule\Models\CurriculumModule;
use Illuminate\Http\Request;

class CurriculumModuleController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {

            $items = CurriculumModule::select(
                'curriculum_modules.id',
                'curriculum_modules.order',
                'curriculum_modules.is_enabled',
                'curriculum_modules.is_extracurricular',
                'areas.name as area',
                'modules.name as module',
                'curriculums.name as curriculum',
            )
                ->join('curriculums', 'curriculums.id', '=', 'curriculum_modules.curriculum_id')
                ->join('areas', 'areas.id', '=', 'curriculum_modules.area_id')
                ->leftJoin('modules', 'modules.id', '=', 'curriculum_modules.module_id')
                ->where('curriculums.id', $request->id)
                ->orderBy('curriculum_modules.order', 'asc')
                ->dataTable($request);
            CurriculumModuleDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function store(CurriculumModuleStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $item = CurriculumModule::create($data);
            return ApiResponse::success($item, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    //update
    public function update(CurriculumModuleUpdateRequest $request)
    {
        try {
            $data = $request->validated();
            $item = CurriculumModule::find($request->id);
            $item->update($data);
            return ApiResponse::success($request->all(), 'Registro actualizado correctamente', 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    //destroy
    public function destroy(Request $request)
    {
        try {
            $item = CurriculumModule::find($request->id);
            $item->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function getItemById(Request $request)
    {
        try {
            $item = CurriculumModule::select(
                'curriculum_modules.id',
                'curriculum_modules.order',
                'curriculum_modules.area_id',
                'curriculum_modules.module_id',
                'curriculum_modules.is_extracurricular',
                'curriculum_modules.is_enabled',
            )
                ->where('curriculum_modules.id', $request->id)
                ->first();

            $item =  CurriculumModuleItemResource::make($item);
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function getDataItem(Request $request)
    {
        try {
            $item = CurriculumModule::select(
                'curriculum_modules.id',
                'curriculum_modules.order',
                'curriculum_modules.is_enabled',
                'curriculum_modules.is_extracurricular',
                'areas.name as area',
                'modules.name as module',
                'curriculums.name as curriculum',
            )
                ->join('curriculums', 'curriculums.id', '=', 'curriculum_modules.curriculum_id')
                ->join('areas', 'areas.id', '=', 'curriculum_modules.area_id')
                ->leftJoin('modules', 'modules.id', '=', 'curriculum_modules.module_id')
                ->where('curriculum_modules.id', $request->id)
                ->first();

            $item = CurriculumModuleDataTableItemsResource::make($item);
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
