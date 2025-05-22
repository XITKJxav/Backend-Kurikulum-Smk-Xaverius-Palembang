<?php

namespace App\Http\Controllers\AgendaUpacara;

use App\Http\Common\Helper\ReportGenerator;
use App\Http\Common\Utils\ApiResponse;
use App\Http\Common\Utils\Filtering;
use App\Http\Controllers\Controller;
use App\Models\AgendaUpacara;
use App\Models\StatusAgendaUpacara;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AgendaUpacaraController extends Controller
{

    public function index(Request $request)
    {
        try {
            $data = (new Filtering($request))
                ->setBuilder(AgendaUpacara::with('statusAgendaUpacara'), 'jam_upacara', 'kd_agendaupacara')
                ->apply();

            return (new ApiResponse(200, [$data], "Agenda Upacara fetched successfully"))->send();
        } catch (\Exception $e) {
            Log::error('Error fetching agenda upacara: ' . $e->getMessage());
            return (new ApiResponse(500, [],  'Failed to fetch agenda upacara'))->send();
        }
    }

    public function fetchStatusUpacara()
    {
        try {
            $data = StatusAgendaUpacara::get();

            return (new ApiResponse(200, [$data], "Status Agenda Upacara fetched successfully"))->send();
        } catch (\Exception $e) {
            Log::error('Error fetching agenda upacara status: ' . $e->getMessage());
            return (new ApiResponse(500, [],  'Failed to fetch status agenda upacara' . $e->getMessage()))->send();
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tanggal_upacara'    => 'required|date',
                'id_status_upacara'  => 'required|exists:statusagendaupacara,id_status_upacara',
            ]);

            $generator = new ReportGenerator();
            $kdAgendaupacara = $generator->generator("kdAgendaUpacara");

            $data = AgendaUpacara::create([
                'kd_agendaupacara'   => $kdAgendaupacara,
                'tanggal_upacara'    => $validated['tanggal_upacara'],
                'id_status_upacara'  => $validated['id_status_upacara'],
            ]);

            return (new ApiResponse(200, [$data], 'Agenda Upacara successfully'))->send();
        } catch (ValidationException $e) {
            return (new ApiResponse(422, [], $e->getMessage()))->send();
        } catch (\Exception $e) {
            Log::error('Agenda upacara error: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Failed to agenda upacara'))->send();
        }
    }

    public function show(string $id)
    {
        try {
            $data = AgendaUpacara::findOrFail($id);

            return (new ApiResponse(200, [$data], 'Detail Agenda Upacara'))->send();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return (new ApiResponse(404, [], 'Agenda Upacara not found'))->send();
        } catch (\Exception $e) {
            Log::error('Show agenda error: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Failed to retrieve agenda upacara'))->send();
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'tanggal_upacara'    => 'required|date',
                'id_status_upacara'  => 'required|exists:statusagendaupacara,id_status_upacara',
                'jam_upacara'        => 'nullable|date_format:H:i',
            ]);

            $agenda = AgendaUpacara::findOrFail($id);

            $agenda->update([
                'tanggal_upacara'    => $validated['tanggal_upacara'],
                'id_status_upacara'  => $validated['id_status_upacara'],
                'jam_upacara'        => $validated['jam_upacara'] ?? $agenda->jam_upacara,
            ]);

            return (new ApiResponse(200, [$agenda], 'Agenda Upacara updated successfully'))->send();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return (new ApiResponse(422, [], $e->validator->errors()->first()))->send();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return (new ApiResponse(404, [], 'Agenda Upacara not found'))->send();
        } catch (\Exception $e) {
            Log::error('Update agenda error: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Failed to update agenda upacara'))->send();
        }
    }

    public function destroy(string $id)
    {
        //
    }
}
