<?php

namespace App\Http\Controllers;

use App\Services\GeminiAIService;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{
    protected $aiService;
    
    public function __construct(GeminiAIService $aiService)
    {
        $this->aiService = $aiService;
    }
    
    public function index()
    {
        $user = Auth::user();
        $aiAvailable = $this->aiService->isAvailable();
        
        return view('ai.dashboard', compact('aiAvailable'));
    }
    
    public function liveCoach()
    {
        return view('ai.live-coach');
    }
    
    public function recommendWorkout()
    {
        $user = Auth::user();
        $recommendation = $this->aiService->recommendWorkout($user);
        return response()->json(['success' => true, 'data' => $recommendation]);
    }
    
    public function analyzeForm(Request $request)
    {
        $request->validate([
            'exercise' => 'required|string',
            'description' => 'required|string|min:10'
        ]);
        
        $analysis = $this->aiService->analyzeForm(
            $request->exercise,
            $request->description
        );
        
        return response()->json(['success' => true, 'data' => $analysis]);
    }
    
    public function generatePlan(Request $request)
    {
        $request->validate([
            'goal' => 'required|string',
            'duration' => 'required|integer|min:15|max:120'
        ]);
        
        $user = Auth::user();
        $equipment = $request->equipment ?? ($user->equipment ?? []);
        
        $plan = $this->aiService->generateWorkoutPlan(
            $request->goal,
            $request->duration,
            $equipment,
            $user
        );
        
        return response()->json(['success' => true, 'data' => $plan]);
    }
    
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500'
        ]);
        
        $user = Auth::user();
        $response = $this->aiService->chat($request->message, $user);
        
        return response()->json([
            'success' => true,
            'response' => $response,
            'timestamp' => now()
        ]);
    }
    
    public function nutritionAdvice()
    {
        $user = Auth::user();
        $advice = $this->aiService->getNutritionAdvice($user);
        
        return response()->json(['success' => true, 'data' => $advice]);
    }
    
    public function predictProgress()
    {
        $user = Auth::user();
        $prediction = $this->aiService->predictProgress($user);
        
        return response()->json(['success' => true, 'data' => $prediction]);
    }
    
    public function getMotivation()
    {
        $quote = $this->aiService->getMotivation();
        return response()->json(['success' => true, 'quote' => $quote]);
    }
    
    public function workoutSummary($id)
    {
        $workout = Workout::where('user_id', Auth::id())
            ->orWhere('trainee_id', Auth::id())
            ->findOrFail($id);
            
        $summary = $this->aiService->generateWorkoutSummary($workout);
        
        return response()->json(['success' => true, 'data' => $summary]);
    }
    
    public function debug()
    {
        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-1.0-pro');
        
        $debugInfo = [
            'api_key_exists' => !empty($apiKey),
            'api_key_length' => strlen($apiKey ?? ''),
            'api_key_prefix' => substr($apiKey ?? '', 0, 10) . '...',
            'model' => $model,
            'ai_enabled' => env('AI_ENABLED', true),
            'service_available' => $this->aiService->isAvailable(),
        ];
        
        // Test actual API call
        try {
            $testResponse = Http::timeout(10)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => 'Say "API working" in one word']
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 50
                    ]
                ]
            );
            
            if ($testResponse->successful()) {
                $debugInfo['api_test'] = '✅ WORKING';
                $data = $testResponse->json();
                $debugInfo['api_response'] = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';
            } else {
                $debugInfo['api_test'] = '❌ FAILED';
                $debugInfo['api_error'] = 'Status: ' . $testResponse->status();
                $debugInfo['api_error_body'] = $testResponse->body();
            }
        } catch (\Exception $e) {
            $debugInfo['api_test'] = '❌ ERROR';
            $debugInfo['api_error'] = $e->getMessage();
        }
        
        $debugInfo['env_file_exists'] = file_exists(base_path('.env'));
        $debugInfo['env_file_readable'] = is_readable(base_path('.env'));
        
        return response()->json($debugInfo);
    }
}