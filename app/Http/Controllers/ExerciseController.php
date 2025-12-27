<?php

namespace App\Http\Controllers;

use App\Http\Requests\Exercise\SaveExerciseRequest;
use App\Http\Requests\Exercise\SaveAIExerciseRequest;
use App\Http\Requests\Exercise\UpdateExerciseRequest;
use App\Http\Resources\ExerciseResource;
use App\Models\ExerciseLog;
use App\Models\DailySync;
use App\Services\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExerciseController extends Controller
{
    public function __construct(
        protected AIService $aiService
    ) {}

    public function getExerciseLogs(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        $date = $request->input('date', today()->toDateString());

        $exercises = $user->exerciseLogs()
            ->whereDate('log_date', $date)
            ->orderBy('start_time', 'asc')
            ->get();

        return ExerciseResource::collection($exercises);
    }

    public function saveExercise(SaveExerciseRequest $request): ExerciseResource
    {
        $data = $request->validated();
        $user = $request->user();

        $exercise = ExerciseLog::query()->create(array_merge($data, [
            'user_id' => $user->id,
            'intensity' => $data['intensity'] ?? 'mid',
            'calories_burned' => $data['calories_burned'] ?? 0,
            'source' => 'manual',
        ]));

        DailySync::updateOrCreateForDate($user->id, $data['log_date']);

        return new ExerciseResource($exercise);
    }

    public function saveAIExercise(SaveAIExerciseRequest $request): ExerciseResource
    {
        $data = $request->validated();
        $user = $request->user();

        $result = $this->aiService->analyzeExercise(
            $data['text'],
            $request->file('image')
        );

        $exercise = ExerciseLog::query()->create([
            'user_id' => $user->id,
            'type' => [
                'ar' => $result['type'],
                'en' => $result['type'],
            ],
            'log_date' => $data['log_date'],
            'start_time' => $data['start_time'] ?? null,
            'end_time' => $data['end_time'] ?? null,
            'duration' => $result['duration'] ?? null,
            'intensity' => $result['intensity'] ?? 'mid',
            'calories_burned' => $result['calories_burned'] ?? 0,
            'source' => 'ai',
            'ai_response' => $result,
            'ai_confidence' => $result['confidence'] ?? 0,
        ]);

        DailySync::updateOrCreateForDate($user->id, $data['log_date']);

        return new ExerciseResource($exercise);
    }

    public function updateExercise(UpdateExerciseRequest $request, int $id): ExerciseResource
    {
        $data = $request->validated();
        $user = $request->user();
        $exercise = $user->exerciseLogs()->findOrFail($id);

        $exercise->update($data);
        DailySync::updateOrCreateForDate($user->id, $exercise->log_date);

        return new ExerciseResource($exercise->fresh());
    }

    public function deleteExercise(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $exercise = $user->exerciseLogs()->findOrFail($id);
        $logDate = $exercise->log_date;

        $exercise->delete();
        DailySync::updateOrCreateForDate($user->id, $logDate);

        return response()->json(['message' => 'Exercise deleted successfully']);
    }
}

