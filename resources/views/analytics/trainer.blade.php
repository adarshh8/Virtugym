@extends('layouts.app')

@section('title', 'Trainer Analytics')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Trainer Analytics 📊</h1>
    
    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <p class="text-gray-500 text-sm">Total Clients</p>
            <p class="text-3xl font-bold text-purple-600">{{ $totalClients ?? 0 }}</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6">
            <p class="text-gray-500 text-sm">Total Sessions</p>
            <p class="text-3xl font-bold text-green-600">{{ $totalSessions ?? 0 }}</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6">
            <p class="text-gray-500 text-sm">Total Revenue</p>
            <p class="text-3xl font-bold text-yellow-600">₹{{ number_format($totalRevenue ?? 0) }}</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6">
            <p class="text-gray-500 text-sm">Average Rating</p>
            <p class="text-3xl font-bold text-orange-600">{{ number_format($averageRating ?? 0, 1) }} ⭐</p>
        </div>
    </div>
    
    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-lg font-bold mb-4">Upcoming Sessions</h2>
            <p class="text-4xl font-bold text-purple-600">{{ $upcomingSessions ?? 0 }}</p>
            <p class="text-gray-500 text-sm mt-2">sessions scheduled</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-lg font-bold mb-4">Performance Summary</h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Completion Rate</span>
                    <span class="font-semibold text-green-600">
                        @php
                            $completionRate = ($totalSessions ?? 0) > 0 ? round((($totalSessions ?? 0) - ($upcomingSessions ?? 0)) / ($totalSessions ?? 0) * 100) : 0;
                        @endphp
                        {{ $completionRate }}%
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Average per Client</span>
                    <span class="font-semibold text-blue-600">
                        @php
                            $avgPerClient = ($totalClients ?? 0) > 0 ? round(($totalSessions ?? 0) / ($totalClients ?? 0)) : 0;
                        @endphp
                        {{ $avgPerClient }} sessions
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Average Revenue/Client</span>
                    <span class="font-semibold text-purple-600">
                        @php
                            $avgRevenue = ($totalClients ?? 0) > 0 ? round(($totalRevenue ?? 0) / ($totalClients ?? 0)) : 0;
                        @endphp
                        ₹{{ number_format($avgRevenue) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-6 bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg p-6">
        <h2 class="text-lg font-bold mb-2 text-purple-300">💡 Pro Tip</h2>
        <p class="text-gray-300">Keep your calendar updated and respond to booking requests quickly to maintain a high rating!</p>
    </div>
</div>
@endsection
