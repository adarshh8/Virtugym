@extends('layouts.app')

@section('title', 'AI Coach')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-400 to-purple-200 bg-clip-text text-transparent">
            🤖 AI Fitness Coach
        </h1>
        <p class="text-gray-400 mt-2">Your personal AI-powered fitness assistant</p>
    </div>
    
    <!-- AI Chat Section -->
    <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-xl overflow-hidden mb-8 border border-gray-700 relative">
        <div class="bg-gray-800/80 p-4 border-b border-purple-500/20 flex justify-between items-center">
            <div>
                <h2 class="font-bold text-lg text-purple-100 flex items-center gap-2"><i data-lucide="bot" class="w-5 h-5 text-purple-400"></i> Chat with VirtuCoach</h2>
                <p class="text-sm text-gray-400">Ask me anything about fitness, workouts, or nutrition!</p>
            </div>
            <button class="text-gray-400 hover:text-white flex items-center gap-1.5 text-sm bg-gray-700/50 px-3 py-1.5 rounded-lg border border-gray-600 transition" onclick="toggleChatHistory()">
                <i data-lucide="history" class="w-4 h-4"></i> <span class="hidden sm:inline">History</span>
            </button>
        </div>
        
        <!-- History Panel -->
        <div id="historyPanel" class="absolute inset-y-0 right-0 w-64 bg-gray-900/95 border-l border-gray-700 transform translate-x-full transition-transform z-20 flex flex-col backdrop-blur-md">
            <div class="p-3 border-b border-gray-700 flex justify-between items-center">
                <h3 class="font-bold text-white text-sm">Recent Chats</h3>
                <button onclick="toggleChatHistory()" class="text-gray-400 hover:text-white"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
            <div class="flex-1 overflow-y-auto p-2 space-y-1">
                <button class="w-full text-left p-2 hover:bg-gray-700 rounded text-sm text-gray-300 transition truncate"><i data-lucide="message-square" class="w-3 h-3 inline mr-1 text-purple-400"></i> Yesterday: Workout plan...</button>
                <button class="w-full text-left p-2 hover:bg-gray-700 rounded text-sm text-gray-300 transition truncate"><i data-lucide="message-square" class="w-3 h-3 inline mr-1 text-purple-400"></i> May 14: Macros for bulk...</button>
                <button class="w-full text-left p-2 hover:bg-gray-700 rounded text-sm text-gray-300 transition truncate"><i data-lucide="message-square" class="w-3 h-3 inline mr-1 text-purple-400"></i> May 10: Lower back pain...</button>
            </div>
        </div>
        
        <div id="chatMessages" class="h-96 overflow-y-auto p-4 space-y-3 bg-gray-900/40 relative z-10">
            <div class="flex justify-start">
                <div class="bg-gray-700 rounded-2xl p-3 max-w-[80%] border border-gray-600/50 shadow-lg shadow-black/20">
                    <p class="text-sm text-gray-200">👋 Hi! I'm VirtuCoach, your AI fitness trainer. Ask me about:</p>
                    <ul class="text-sm mt-1 ml-4 text-gray-300">
                        <li>💪 Personalized workouts</li>
                        <li>🥗 Nutrition advice</li>
                        <li>📊 Progress tips</li>
                        <li>🎯 Motivation & accountability</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Quick Prompts -->
        <div class="px-4 pb-2 bg-gray-800/30 pt-3 flex gap-2 overflow-x-auto relative z-10" id="quickPrompts" style="scrollbar-width: none;">
            <button class="whitespace-nowrap px-3 py-1.5 bg-gray-700/80 hover:bg-purple-600 border border-gray-600 rounded-full text-xs text-gray-200 transition" onclick="setPrompt('Create a workout plan')">✨ Create a workout plan</button>
            <button class="whitespace-nowrap px-3 py-1.5 bg-gray-700/80 hover:bg-purple-600 border border-gray-600 rounded-full text-xs text-gray-200 transition" onclick="setPrompt('What should I eat today?')">🥗 What should I eat today?</button>
            <button class="whitespace-nowrap px-3 py-1.5 bg-gray-700/80 hover:bg-purple-600 border border-gray-600 rounded-full text-xs text-gray-200 transition" onclick="setPrompt('Analyze my progress')">📈 Analyze my progress</button>
        </div>
        
        <div class="p-4 border-t border-gray-700 bg-gray-800/50 relative z-10">
            <div class="flex space-x-2">
                <div class="relative flex-1">
                    <input type="text" id="chatInput" placeholder="Ask your fitness question..." 
                           class="w-full pl-4 pr-10 py-3 bg-gray-900 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition shadow-inner">
                    <button id="voiceInput" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-purple-400 transition" title="Voice Input">
                        <i data-lucide="mic" class="w-5 h-5"></i>
                    </button>
                </div>
                <button id="sendChat" class="bg-purple-600 text-white px-5 py-3 rounded-xl hover:bg-purple-500 transition flex items-center justify-center shadow-lg shadow-purple-500/20">
                    <span class="hidden sm:inline mr-1">Send</span>
                    <i data-lucide="send" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Features Grid -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Workout Recommendation -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-gray-700">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-purple-900/50 rounded-full flex items-center justify-center text-xl">💪</div>
                <h3 class="font-bold text-lg text-white">AI Workout</h3>
            </div>
            <div id="workoutRecommendation" class="text-sm text-gray-300">
                <button onclick="getWorkoutRecommendation()" class="text-purple-400 hover:text-purple-300 transition">
                    🔄 Generate Personalized Workout
                </button>
            </div>
        </div>
        
        <!-- Nutrition Advice -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-gray-700">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-green-900/50 rounded-full flex items-center justify-center text-xl">🥗</div>
                <h3 class="font-bold text-lg text-white">Nutrition Guide</h3>
            </div>
            <div id="nutritionAdvice" class="text-sm text-gray-300">
                <button onclick="getNutritionAdvice()" class="text-green-400 hover:text-green-300 transition">
                    🔄 Get Personalized Nutrition
                </button>
            </div>
        </div>
        
        <!-- Progress Prediction -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-gray-700">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-blue-900/50 rounded-full flex items-center justify-center text-xl">📊</div>
                <h3 class="font-bold text-lg text-white">Progress Forecast</h3>
            </div>
            <div id="progressPrediction" class="text-sm text-gray-300">
                <button onclick="getProgressPrediction()" class="text-blue-400 hover:text-blue-300 transition">
                    🔄 Predict My Progress
                </button>
            </div>
        </div>
        
        <!-- Form Analysis -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-gray-700">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-yellow-900/50 rounded-full flex items-center justify-center text-xl">📹</div>
                <h3 class="font-bold text-lg text-white">Form Check</h3>
            </div>
            <div class="space-y-3">
                <input type="text" id="formExercise" placeholder="Exercise name" 
                       class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-yellow-500">
                <textarea id="formDescription" rows="2" placeholder="Describe how you perform the exercise..." 
                          class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-yellow-500"></textarea>
                <button onclick="analyzeForm()" class="w-full bg-purple-600 text-white py-2 rounded-lg text-sm hover:bg-purple-500 transition shadow-lg shadow-purple-500/20">
                    Analyze My Form
                </button>
            </div>
            <div id="formAnalysisResult" class="mt-3 text-sm text-gray-300"></div>
        </div>
        
        <!-- Custom Plan -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-gray-700">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-orange-900/50 rounded-full flex items-center justify-center text-xl">📋</div>
                <h3 class="font-bold text-lg text-white">Custom Plan</h3>
            </div>
            <div class="space-y-3">
                <select id="planGoal" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-orange-500">
                    <option value="weight_loss">Weight Loss</option>
                    <option value="muscle_gain">Muscle Gain</option>
                    <option value="endurance">Endurance</option>
                    <option value="general_fitness">General Fitness</option>
                </select>
                <input type="number" id="planDuration" placeholder="Duration (minutes)" value="30" 
                       class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-orange-500">
                <button onclick="generateCustomPlan()" class="w-full bg-purple-600 text-white py-2 rounded-lg text-sm hover:bg-purple-500 transition shadow-lg shadow-purple-500/20">
                    Generate Plan
                </button>
            </div>
            <div id="customPlanResult" class="mt-3 text-sm text-gray-300"></div>
        </div>
        
        <!-- Motivation -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-gray-700">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-red-900/50 rounded-full flex items-center justify-center text-xl">⚡</div>
                <h3 class="font-bold text-lg text-white">Daily Motivation</h3>
            </div>
            <div id="motivation" class="text-sm text-gray-300 italic">
                <button onclick="getMotivation()" class="text-red-400 hover:text-red-300 transition">
                    🔄 Get Motivation
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // Chat functionality
    const chatInput = document.getElementById('chatInput');
    const sendButton = document.getElementById('sendChat');
    const chatMessages = document.getElementById('chatMessages');
    
    function setPrompt(text) {
        chatInput.value = text;
        chatInput.focus();
    }
    
    function toggleChatHistory() {
        const panel = document.getElementById('historyPanel');
        if (panel.classList.contains('translate-x-full')) {
            panel.classList.remove('translate-x-full');
        } else {
            panel.classList.add('translate-x-full');
        }
    }
    
    function addMessage(message, isUser = false) {
        const div = document.createElement('div');
        div.className = `flex ${isUser ? 'justify-end' : 'justify-start'} mb-3`;
        div.innerHTML = `
            <div class="${isUser ? 'bg-purple-600 text-white' : 'bg-gray-700 text-gray-200'} rounded-2xl p-3 max-w-[80%]">
                <p class="text-sm whitespace-pre-wrap">${escapeHtml(message)}</p>
            </div>
        `;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    async function sendMessage() {
        const message = chatInput.value.trim();
        if (!message) return;
        
        addMessage(message, true);
        chatInput.value = '';
        
        try {
            const response = await fetch('/ai/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: message })
            });
            
            const data = await response.json();
            
            if (data.success) {
                addMessage(data.response);
            } else {
                addMessage("Sorry, I encountered an error. Please try again.");
            }
        } catch (error) {
            console.error('Error:', error);
            addMessage("Network error. Please check your connection.");
        }
    }
    
    sendButton.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });
    
    // Workout Recommendation
    async function getWorkoutRecommendation() {
        const container = document.getElementById('workoutRecommendation');
        container.innerHTML = '<div class="animate-pulse text-purple-300">🤔 Analyzing your profile...</div>';
        
        try {
            const response = await fetch('/ai/recommend-workout');
            const data = await response.json();
            if (data.success && data.data) {
                displayWorkoutRecommendation(data.data);
            } else {
                container.innerHTML = '<p class="text-red-400">Unable to generate recommendation</p><button onclick="getWorkoutRecommendation()" class="text-purple-400 mt-2">Try Again</button>';
            }
        } catch (error) {
            container.innerHTML = '<p class="text-red-400">Error loading recommendation</p>';
        }
    }
    
    function displayWorkoutRecommendation(workout) {
        let html = '<div class="space-y-3">';
        html += `<h4 class="font-bold text-purple-400">${workout.workout_name || 'Your Personalized Workout'}</h4>`;
        
        if (workout.warmup) {
            html += '<div><strong class="text-gray-300">🔥 Warm-up:</strong><ul class="ml-4 mt-1 text-gray-400">';
            workout.warmup.forEach(w => {
                html += `<li>• ${w.exercise}: ${w.duration} sec</li>`;
            });
            html += '</ul></div>';
        }
        
        if (workout.exercises) {
            html += '<div><strong class="text-gray-300">💪 Main Workout:</strong><ul class="ml-4 mt-1 text-gray-400">';
            workout.exercises.forEach(ex => {
                html += `<li>• <strong>${ex.name}</strong>: ${ex.sets} sets × ${ex.reps} reps (rest ${ex.rest} sec)</li>`;
            });
            html += '</ul></div>';
        }
        
        if (workout.cooldown) {
            html += '<div><strong class="text-gray-300">🧘 Cool-down:</strong><ul class="ml-4 mt-1 text-gray-400">';
            workout.cooldown.forEach(c => {
                html += `<li>• ${c.exercise}: ${c.duration} sec</li>`;
            });
            html += '</ul></div>';
        }
        
        if (workout.motivation) {
            html += `<p class="text-purple-400 italic mt-2">✨ "${workout.motivation}"</p>`;
        }
        
        html += '<button onclick="getWorkoutRecommendation()" class="text-purple-400 text-sm mt-3 hover:text-purple-300">⟳ Generate New</button>';
        html += '</div>';
        
        document.getElementById('workoutRecommendation').innerHTML = html;
    }
    
    // Nutrition Advice
    async function getNutritionAdvice() {
        const container = document.getElementById('nutritionAdvice');
        container.innerHTML = '<div class="animate-pulse text-green-300">🥗 Analyzing your needs...</div>';
        
        try {
            const response = await fetch('/ai/nutrition-advice');
            const data = await response.json();
            if (data.success && data.data) {
                displayNutritionAdvice(data.data);
            } else {
                container.innerHTML = '<p class="text-red-400">Unable to load advice</p>';
            }
        } catch (error) {
            container.innerHTML = '<p class="text-red-400">Error loading advice</p>';
        }
    }
    
    function displayNutritionAdvice(nutrition) {
        let html = '<div class="space-y-2 text-gray-300">';
        html += `<p><strong class="text-gray-200">🔥 Calories:</strong> ${nutrition.daily_calories || '2000-2200'}</p>`;
        html += `<p><strong class="text-gray-200">🥩 Protein:</strong> ${nutrition.protein || '150-180g'} | <strong class="text-gray-200">🍚 Carbs:</strong> ${nutrition.carbs || '200-250g'} | <strong class="text-gray-200">🥑 Fats:</strong> ${nutrition.fats || '50-60g'}</p>`;
        if (nutrition.meal_ideas) {
            html += '<p><strong class="text-gray-200">🍽️ Meal Ideas:</strong></p><ul class="ml-4">';
            nutrition.meal_ideas.forEach(meal => {
                html += `<li>• ${meal}</li>`;
            });
            html += '</ul>';
        }
        html += `<p class="text-green-400 italic mt-2">💧 ${nutrition.hydration || 'Drink 2-3L water daily'}</p>`;
        html += '<button onclick="getNutritionAdvice()" class="text-green-400 text-sm mt-2 hover:text-green-300">⟳ Refresh</button>';
        html += '</div>';
        
        document.getElementById('nutritionAdvice').innerHTML = html;
    }
    
    // Progress Prediction
    async function getProgressPrediction() {
        const container = document.getElementById('progressPrediction');
        container.innerHTML = '<div class="animate-pulse text-blue-300">📊 Analyzing your data...</div>';
        
        try {
            const response = await fetch('/ai/predict-progress');
            const data = await response.json();
            if (data.success && data.data) {
                displayProgressPrediction(data.data);
            } else {
                container.innerHTML = '<p class="text-red-400">Unable to predict</p>';
            }
        } catch (error) {
            container.innerHTML = '<p class="text-red-400">Error loading prediction</p>';
        }
    }
    
    function displayProgressPrediction(prediction) {
        let html = '<div class="space-y-2 text-gray-300">';
        html += `<p><strong class="text-gray-200">⏰ Weeks to goal:</strong> ${prediction.weeks_to_goal || '8-12'} weeks</p>`;
        html += `<p><strong class="text-gray-200">📈 Confidence:</strong> ${prediction.confidence_percentage || '75'}%</p>`;
        html += `<p><strong class="text-gray-200">💪 Recommended frequency:</strong> ${prediction.recommended_frequency || '4'} days/week</p>`;
        if (prediction.suggestions) {
            html += '<p><strong class="text-gray-200">💡 Suggestions:</strong></p><ul class="ml-4">';
            prediction.suggestions.forEach(s => {
                html += `<li>• ${s}</li>`;
            });
            html += '</ul>';
        }
        html += `<p class="text-blue-400 italic mt-2">✨ "${prediction.motivation_quote || 'Stay consistent, you got this!'}"</p>`;
        html += '<button onclick="getProgressPrediction()" class="text-blue-400 text-sm mt-2 hover:text-blue-300">⟳ Refresh</button>';
        html += '</div>';
        
        document.getElementById('progressPrediction').innerHTML = html;
    }
    
    // Form Analysis
    async function analyzeForm() {
        const exercise = document.getElementById('formExercise').value;
        const description = document.getElementById('formDescription').value;
        
        if (!exercise || !description) {
            alert('Please enter both exercise name and description');
            return;
        }
        
        const resultDiv = document.getElementById('formAnalysisResult');
        resultDiv.innerHTML = '<div class="animate-pulse text-center text-yellow-300">🔍 Analyzing your form...</div>';
        
        try {
            const response = await fetch('/ai/analyze-form', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ exercise, description })
            });
            const data = await response.json();
            if (data.success && data.data) {
                displayFormAnalysis(data.data);
            } else {
                resultDiv.innerHTML = '<p class="text-red-400">Analysis failed</p>';
            }
        } catch (error) {
            resultDiv.innerHTML = '<p class="text-red-400">Error analyzing form</p>';
        }
    }
    
    function displayFormAnalysis(analysis) {
        let html = '<div class="mt-3 space-y-2 border-t border-gray-700 pt-3">';
        html += `<p><strong class="text-gray-200">✅ Form Quality:</strong> <span class="font-semibold text-yellow-400">${analysis.form_quality || 'Good'}</span></p>`;
        
        if (analysis.correct_points) {
            html += '<p><strong class="text-gray-200">👍 What you\'re doing right:</strong></p><ul class="ml-4">';
            analysis.correct_points.forEach(p => {
                html += `<li class="text-green-400">✓ ${p}</li>`;
            });
            html += '</ul>';
        }
        
        if (analysis.corrections) {
            html += '<p><strong class="text-gray-200">📝 How to improve:</strong></p><ul class="ml-4">';
            analysis.corrections.forEach(c => {
                html += `<li class="text-blue-400">→ ${c}</li>`;
            });
            html += '</ul>';
        }
        
        if (analysis.tips) {
            html += '<p><strong class="text-gray-200">💡 Pro Tips:</strong></p><ul class="ml-4">';
            analysis.tips.forEach(t => {
                html += `<li>• ${t}</li>`;
            });
            html += '</ul>';
        }
        
        html += `<p class="text-purple-400 italic mt-2">✨ ${analysis.encouragement || 'Keep practicing, you\'re improving!'}</p>`;
        html += '</div>';
        
        document.getElementById('formAnalysisResult').innerHTML = html;
    }
    
    // Custom Plan
    async function generateCustomPlan() {
        const goal = document.getElementById('planGoal').value;
        const duration = document.getElementById('planDuration').value;
        
        const resultDiv = document.getElementById('customPlanResult');
        resultDiv.innerHTML = '<div class="animate-pulse text-center text-orange-300">📋 Generating your plan...</div>';
        
        try {
            const response = await fetch('/ai/generate-plan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ goal, duration })
            });
            const data = await response.json();
            if (data.success && data.data) {
                displayCustomPlan(data.data);
            } else {
                resultDiv.innerHTML = '<p class="text-red-400">Plan generation failed</p>';
            }
        } catch (error) {
            resultDiv.innerHTML = '<p class="text-red-400">Error generating plan</p>';
        }
    }
    
    function displayCustomPlan(plan) {
        let html = '<div class="mt-3 space-y-2 border-t border-gray-700 pt-3">';
        html += `<h4 class="font-bold text-orange-400">${plan.plan_name || 'Your Custom Plan'}</h4>`;
        html += `<p class="text-gray-300"><strong class="text-gray-200">⚡ Difficulty:</strong> ${plan.difficulty || 'Intermediate'}</p>`;
        
        if (plan.circuits) {
            plan.circuits.forEach((circuit, idx) => {
                html += `<div class="mt-2"><strong class="text-gray-200">Circuit ${idx + 1}:</strong> ${circuit.rounds} rounds</div><ul class="ml-4 text-gray-300">`;
                circuit.exercises.forEach(ex => {
                    html += `<li>• ${ex.name}: ${ex.reps} reps (rest ${ex.rest} sec)</li>`;
                });
                html += '</ul>';
            });
        }
        
        if (plan.tips) {
            html += '<p><strong class="text-gray-200">💡 Tips for success:</strong></p><ul class="ml-4 text-gray-300">';
            plan.tips.forEach(tip => {
                html += `<li>• ${tip}</li>`;
            });
            html += '</ul>';
        }
        
        html += '<button onclick="generateCustomPlan()" class="text-orange-400 text-sm mt-2 hover:text-orange-300">⟳ Generate New Plan</button>';
        html += '</div>';
        
        document.getElementById('customPlanResult').innerHTML = html;
    }
    
    // Motivation
    async function getMotivation() {
        const container = document.getElementById('motivation');
        container.innerHTML = '<div class="animate-pulse text-center text-red-300">✨ Finding inspiration...</div>';
        
        try {
            const response = await fetch('/ai/motivation');
            const data = await response.json();
            if (data.success) {
                container.innerHTML = `<div class="text-center">
                    <p class="text-red-400 italic text-base font-medium">"${data.quote}"</p>
                    <button onclick="getMotivation()" class="text-red-400 text-sm mt-3 hover:text-red-300">⟳ New Quote</button>
                </div>`;
            } else {
                container.innerHTML = '<p class="text-red-400">Unable to get quote</p>';
            }
        } catch (error) {
            container.innerHTML = '<p class="text-red-400">Error loading motivation</p>';
        }
    }
</script>
@endsection