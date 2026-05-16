@extends('layouts.app')

@section('title', 'Withdrawals')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2 text-left bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">💰 Withdrawals</h1>
        <p class="text-gray-400 text-left">Request payouts for your earnings and track history</p>
    </div>
    
    <!-- Balance Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
        <div class="bg-gradient-to-r from-green-900/40 to-green-800/20 rounded-xl p-5 border border-green-500/30 shadow-lg">
            <p class="text-gray-400 text-xs uppercase tracking-wider font-bold mb-1">Total Earnings (Lifetime)</p>
            <p class="text-2xl font-bold text-green-400">₹{{ number_format($totalEarnings ?? 0) }}</p>
        </div>
        <div class="bg-gradient-to-r from-emerald-900/30 to-teal-900/20 rounded-xl p-5 border border-emerald-500/30 shadow-lg">
            <p class="text-gray-400 text-xs uppercase tracking-wider font-bold mb-1">This Month's Earnings</p>
            <p class="text-2xl font-bold text-emerald-400">₹{{ number_format($monthlyEarnings ?? 0) }}</p>
        </div>
        <div class="bg-gradient-to-r from-purple-900/40 to-purple-800/20 rounded-xl p-5 border border-purple-500/30 shadow-lg col-span-2 md:col-span-1">
            <p class="text-gray-400 text-xs uppercase tracking-wider font-bold mb-1">Available Balance</p>
            <p class="text-3xl font-bold text-purple-400">₹{{ number_format($availableBalance ?? 0) }}</p>
        </div>
    </div>
    
    <div class="grid grid-cols-2 gap-4 mb-8">
        <div class="bg-gradient-to-r from-gray-900/60 to-gray-800/40 rounded-xl p-5 border border-gray-600/30 shadow-lg">
            <p class="text-gray-400 text-xs uppercase tracking-wider font-bold mb-1">Total Withdrawn</p>
            <p class="text-2xl font-bold text-gray-300">₹{{ number_format($totalWithdrawn ?? 0) }}</p>
        </div>
        <div class="bg-gradient-to-r from-yellow-900/30 to-orange-900/20 rounded-xl p-5 border border-yellow-500/30 shadow-lg">
            <p class="text-gray-400 text-xs uppercase tracking-wider font-bold mb-1">Pending Amount</p>
            <p class="text-2xl font-bold text-yellow-400">₹{{ number_format($pendingAmount ?? 0) }}</p>
        </div>
    </div>
    
    <!-- Prominent Pending Status -->
    @if(isset($hasPending) && $hasPending)
        <div class="bg-gradient-to-r from-yellow-900/40 to-yellow-800/20 rounded-xl p-6 mb-8 border-l-4 border-yellow-500 shadow-lg flex items-center gap-6">
            <div class="text-4xl hidden sm:block">⏳</div>
            <div>
                <h3 class="text-lg font-bold text-yellow-400 mb-1">Withdrawal Pending</h3>
                <p class="text-yellow-200/70 text-sm">You have a pending withdrawal request of <strong class="text-yellow-300">₹{{ number_format($pendingAmount ?? 0) }}</strong>. Please wait for the admin to process it before requesting another.</p>
            </div>
        </div>
    @endif
    
    <!-- Request Withdrawal Form -->
    <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 mb-8 border border-gray-700 shadow-lg">
        <h2 class="text-xl font-bold mb-6 text-white flex items-center gap-2">
            <i data-lucide="banknote" class="w-5 h-5 text-purple-400"></i> Request New Withdrawal
        </h2>
        
        @if(isset($hasPending) && $hasPending)
            <div class="opacity-50 pointer-events-none">
        @elseif(!isset($availableBalance) || $availableBalance < 100)
            <div class="bg-gray-700/50 rounded-lg p-4 text-center border border-gray-600 mb-4">
                <p class="text-gray-400">You need a minimum balance of <span class="text-purple-400 font-bold">₹100</span> to request a withdrawal.</p>
            </div>
            <div class="opacity-50 pointer-events-none">
        @else
            <div>
        @endif
        
            <form method="POST" action="{{ route('trainer.withdrawal.request') }}">
                @csrf
                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <label class="block text-gray-300 font-semibold text-sm">Amount (₹)</label>
                            @if(isset($availableBalance) && $availableBalance > 0 && (!$hasPending))
                                <button type="button" onclick="document.getElementById('amount_input').value={{ $availableBalance }}" class="text-[10px] text-purple-400 hover:text-purple-300 font-bold bg-purple-900/30 px-2 py-1 rounded uppercase tracking-wider transition">Withdraw All</button>
                            @endif
                        </div>
                        <input type="number" id="amount_input" name="amount" step="100" min="100" max="{{ $availableBalance ?? 0 }}" required 
                               class="w-full px-4 py-3 bg-gray-900/50 border border-gray-600 rounded-lg text-white font-bold placeholder-gray-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 transition">
                        <p class="text-gray-500 text-xs mt-2 font-medium">Min payout: ₹100 | Available max: ₹{{ number_format($availableBalance ?? 0) }}</p>
                    </div>
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <label class="block text-gray-300 font-semibold text-sm">UPI ID</label>
                            @if(isset($savedUpis) && $savedUpis->count() > 0)
                                <span class="text-[10px] text-gray-400 uppercase tracking-wider">Select saved or type new</span>
                            @endif
                        </div>
                        <input type="text" name="upi_id" placeholder="yourname@okaxis" required list="saved_upis_list" autocomplete="off"
                               class="w-full px-4 py-3 bg-gray-900/50 border border-gray-600 rounded-lg text-white font-bold placeholder-gray-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 transition">
                        @if(isset($savedUpis) && $savedUpis->count() > 0)
                            <datalist id="saved_upis_list">
                                @foreach($savedUpis as $upi)
                                    <option value="{{ $upi }}">
                                @endforeach
                            </datalist>
                        @endif
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold text-lg px-6 py-3 rounded-xl hover:shadow-[0_0_20px_rgba(168,85,247,0.4)] transition transform hover:-translate-y-0.5">
                    Submit Withdrawal Request
                </button>
                <p class="text-gray-500 text-xs text-center mt-4 font-medium flex items-center justify-center gap-1">
                    <i data-lucide="info" class="w-3 h-3"></i> Funds are typically released and settled within 3-5 business days.
                </p>
            </form>
        </div>
    </div>
    
    <!-- Withdrawal History -->
    <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl overflow-hidden border border-gray-700 shadow-lg">
        <h2 class="text-xl font-bold p-6 border-b border-gray-700 text-white flex items-center gap-2">
            <i data-lucide="history" class="w-5 h-5 text-gray-400"></i> Withdrawal History
        </h2>
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead class="bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Date Requested</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Transaction ID</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">UPI Destination</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Processing Time</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @forelse($requests as $req)
                    <tr class="hover:bg-gray-700/30 transition">
                        <td class="px-6 py-4 text-sm text-gray-300 font-medium">{{ \Carbon\Carbon::parse($req->created_at)->format('M d, Y • h:i A') }}</td>
                        <td class="px-6 py-4 text-xs text-gray-500 font-mono tracking-wider">WR-{{ strtoupper(substr((string)$req->id, -8)) }}</td>
                        <td class="px-6 py-4 font-bold text-purple-400">₹{{ number_format($req->amount) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-300">{{ $req->upi_id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-400">
                            @if($req->status == 'completed' && $req->updated_at)
                                {{ \Carbon\Carbon::parse($req->updated_at)->diffForHumans(\Carbon\Carbon::parse($req->created_at), true) }}
                            @elseif($req->status == 'rejected' && $req->updated_at)
                                {{ \Carbon\Carbon::parse($req->updated_at)->diffForHumans(\Carbon\Carbon::parse($req->created_at), true) }}
                            @else
                                <span class="text-yellow-500/70 italic text-xs">Processing...</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($req->status == 'pending')
                                <span class="bg-yellow-900/40 border border-yellow-700/50 text-yellow-400 px-3 py-1 rounded-full text-xs font-bold shadow-sm">⏳ Pending</span>
                            @elseif($req->status == 'completed')
                                <span class="bg-green-900/40 border border-green-700/50 text-green-400 px-3 py-1 rounded-full text-xs font-bold shadow-sm">✅ Completed</span>
                            @else
                                <span class="bg-red-900/40 border border-red-700/50 text-red-400 px-3 py-1 rounded-full text-xs font-bold shadow-sm">❌ Rejected</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i data-lucide="receipt" class="w-12 h-12 mb-3 opacity-20"></i>
                                <p>No withdrawal requests found in your history.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection