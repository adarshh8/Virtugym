@extends('layouts.app')

@section('title', 'Manage Availability')

@section('content')
<div style="max-width:1280px;margin:0 auto;padding-bottom: 4rem;">

    {{-- Header --}}
    <div style="margin-bottom:2.5rem;" class="fade-in-up">
        <h1 style="font-size:2.4rem;font-weight:900;background:linear-gradient(135deg,#fff 20%,#c4b5fd 60%,#f9a8d4 90%);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.5rem;">
            Manage Your Schedule ⏰
        </h1>
        <p style="color:rgba(255,255,255,.4);font-size:1rem;font-weight:500;">Configure your available slots and keep your trainees informed.</p>
    </div>

    {{-- Top Row: Stats & Add Slot --}}
    <div style="display:grid;grid-template-columns:1fr 1.3fr;gap:2rem;margin-bottom:3rem;">
        
        {{-- Booking Summary --}}
        <div class="fade-in-up delay-1" style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.15);border-radius:28px;padding:2rem;display:flex;flex-direction:column;justify-content:flex-start;position:relative;overflow:hidden;">
            <div style="position:absolute;top:-20px;right:-20px;width:120px;height:120px;background:rgba(139,92,246,.05);border-radius:50%;filter:blur(40px);"></div>
            <h2 style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:1.5rem;display:flex;align-items:center;gap:10px;padding-top:0.5rem;">
                <i data-lucide="bar-chart-3" style="color:var(--vg-accent);"></i> This Week's Summary
            </h2>
            
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem;">
                <div style="background:rgba(255,255,255,0.02);padding:1rem;border-radius:16px;border:1px solid rgba(255,255,255,0.05);">
                    <p style="font-size:.65rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Earnings</p>
                    <p style="font-size:1.4rem;font-weight:900;color:#10b981;">₹{{ number_format($weeklyEarnings ?? 0) }}</p>
                </div>
                <div style="background:rgba(255,255,255,0.02);padding:1rem;border-radius:16px;border:1px solid rgba(255,255,255,0.05);">
                    <p style="font-size:.65rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Cancelled</p>
                    <p style="font-size:1.4rem;font-weight:900;color:#f43f5e;">{{ $cancelledBookingsCount ?? 0 }}</p>
                </div>
                <div style="background:rgba(255,255,255,0.02);padding:1rem;border-radius:16px;border:1px solid rgba(255,255,255,0.05);">
                    <p style="font-size:.65rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Filled</p>
                    <p style="font-size:1.4rem;font-weight:900;color:#fff;">{{ $weeklyBookingsCount ?? 0 }}</p>
                </div>
                <div style="background:rgba(255,255,255,0.02);padding:1rem;border-radius:16px;border:1px solid rgba(255,255,255,0.05);">
                    <p style="font-size:.65rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Capacity</p>
                    <p style="font-size:1.4rem;font-weight:900;color:var(--vg-accent);">{{ $totalSlotsCount ?? 0 }}</p>
                </div>
            </div>

            {{-- Today's Brief --}}
            <div style="margin-bottom:1.5rem;">
                <h3 style="font-size:.8rem;font-weight:700;color:rgba(255,255,255,.5);text-transform:uppercase;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                    <i data-lucide="clock" style="width:14px;height:14px;"></i> Today's Schedule
                </h3>
                @if(isset($todaysBookings) && $todaysBookings->count() > 0)
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        @foreach($todaysBookings as $booking)
                            <div style="background:rgba(255,255,255,0.03);padding:8px 12px;border-radius:12px;display:flex;justify-content:space-between;align-items:center;border-left:3px solid #10b981;">
                                <span style="font-size:.8rem;color:#fff;font-weight:600;">{{ $booking->trainee->name ?? 'Trainee' }}</span>
                                <span style="font-size:.75rem;color:rgba(255,255,255,0.4);">{{ \Carbon\Carbon::parse($booking->session_date)->format('h:i A') }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="font-size:.8rem;color:rgba(255,255,255,.2);font-style:italic;">No bookings for today yet.</p>
                @endif
            </div>
            
            <div style="margin-top:auto;padding-top:1.5rem;border-top:1px solid rgba(255,255,255,.05);padding-bottom:1rem;">
                <div style="display:flex;justify-content:space-between;font-size:.8rem;color:rgba(255,255,255,.5);margin-bottom:8px;">
                    <span>Utilization Rate</span>
                    <span style="font-weight:700;color:#fff;">{{ $totalSlotsCount > 0 ? min(100, round(($weeklyBookingsCount / $totalSlotsCount) * 100)) : 0 }}%</span>
                </div>
                <div style="width:100%;height:6px;background:rgba(255,255,255,.05);border-radius:3px;overflow:hidden;">
                    <div style="width:{{ $totalSlotsCount > 0 ? min(100, ($weeklyBookingsCount / $totalSlotsCount) * 100) : 0 }}%;height:100%;background:linear-gradient(90deg,var(--vg-accent),#10b981);"></div>
                </div>
            </div>
        </div>

        {{-- Add Time Slot Form --}}
        <div class="fade-in-up delay-2" style="background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:28px;padding:2rem;">
            <h2 style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:1.5rem;display:flex;align-items:center;gap:10px;">
                <i data-lucide="plus-circle" style="color:#6ee7b7;"></i> Add New Slots
            </h2>
            
            <form method="POST" action="{{ route('trainer.availability.store') }}" onsubmit="return validateForm()">
                @csrf
                
                {{-- Multi-Day Selection --}}
                <div style="margin-bottom:1.5rem;">
                    <label style="display:block;font-size:.75rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:10px;letter-spacing:.05em;">Select Days <span style="color:#f43f5e;">*</span></label>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;" id="day-selector">
                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $idx => $day)
                            <label style="cursor:pointer;">
                                <input type="checkbox" name="day_of_week[]" value="{{ $idx }}" style="display:none;" class="day-checkbox" id="day-{{ $idx }}">
                                <div class="day-pill" style="padding:8px 16px;border-radius:12px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.5);font-size:.85rem;font-weight:600;transition:all .2s;">
                                    {{ $day }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">
                    <div style="position:relative;">
                        <label style="display:block;font-size:.75rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:10px;">Start Time <span style="color:#f43f5e;">*</span></label>
                        <input type="time" name="start_time" id="start_time" required
                               style="width:100%;background:rgba(0,0,0,.2);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:12px;color:#fff;font-family:inherit;font-size:1rem;outline:none;transition:border-color .2s;"
                               onfocus="this.style.borderColor='var(--vg-accent)'" onblur="this.style.borderColor='rgba(255,255,255,.1)'">
                        <span style="position:absolute;right:40px;top:40px;font-size:.7rem;color:rgba(255,255,255,.2);pointer-events:none;">(e.g. 09:00 AM)</span>
                    </div>
                    <div style="position:relative;">
                        <label style="display:block;font-size:.75rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:10px;">End Time <span style="color:#f43f5e;">*</span></label>
                        <input type="time" name="end_time" id="end_time" required
                               style="width:100%;background:rgba(0,0,0,.2);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:12px;color:#fff;font-family:inherit;font-size:1rem;outline:none;transition:border-color .2s;"
                               onfocus="this.style.borderColor='var(--vg-accent)'" onblur="this.style.borderColor='rgba(255,255,255,.1)'">
                        <span style="position:absolute;right:40px;top:40px;font-size:.7rem;color:rgba(255,255,255,.2);pointer-events:none;">(e.g. 10:00 AM)</span>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1.5fr 1fr;gap:1.5rem;margin-bottom:2rem;align-items:end;">
                    <div>
                        <label style="display:block;font-size:.75rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:10px;">Session Type</label>
                        <select name="session_type" style="width:100%;background:rgba(0,0,0,.2);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:12px;color:#fff;outline:none;cursor:pointer;">
                            <option value="General">General Training</option>
                            <option value="Strength">Strength & Conditioning</option>
                            <option value="Yoga">Yoga / Flexibility</option>
                            <option value="HIIT">HIIT / Cardio</option>
                            <option value="Meditation">Mindfulness / Meditation</option>
                        </select>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;height:48px;">
                        <input type="checkbox" name="is_recurring" id="is_recurring" style="width:20px;height:20px;accent-color:var(--vg-accent);">
                        <label for="is_recurring" style="font-size:.9rem;color:rgba(255,255,255,.7);font-weight:500;cursor:pointer;">Repeat weekly</label>
                    </div>
                </div>

                <button type="submit" style="width:100%;background:var(--vg-gradient);color:#fff;padding:14px;border-radius:16px;font-weight:800;font-size:1rem;border:none;box-shadow:0 10px 20px var(--vg-accent-glow);cursor:pointer;transition:all .3s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 15px 30px var(--vg-accent-glow)'" onmouseout="this.style.transform='';this.style.boxShadow='0 10px 20px var(--vg-accent-glow)'">
                    Create Time Slots
                </button>
            </form>
        </div>
    </div>

    {{-- Weekly Calendar View --}}
    <div class="fade-in-up delay-3" style="background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:28px;padding:2rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;">
            <h2 style="font-size:1.25rem;font-weight:800;color:#fff;display:flex;align-items:center;gap:12px;">
                <i data-lucide="calendar-days" style="color:#fbbf24;"></i> Weekly Calendar
            </h2>
            <div style="display:flex;gap:8px;">
                <button onclick="openBlockDatesModal()" style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.6);padding:8px 16px;border-radius:12px;font-size:.85rem;font-weight:600;cursor:pointer;">
                    Block Specific Dates
                </button>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(7, 1fr);gap:1rem;min-height:400px;">
            @php
                $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                $todayIdx = (int)date('w');
            @endphp
            @foreach($days as $idx => $dayName)
                @php $isToday = $idx === $todayIdx; @endphp
                <div onclick="selectDay({{ $idx }})" style="background:{{ $isToday ? 'rgba(139,92,246,0.08)' : 'rgba(255,255,255,.02)' }};border:1px solid {{ $isToday ? 'rgba(139,92,246,0.4)' : 'rgba(255,255,255,.04)' }};border-radius:20px;padding:1rem;display:flex;flex-direction:column;gap:12px;cursor:pointer;transition:all .2s;position:relative;" class="calendar-col">
                    @if($isToday)
                        <div style="position:absolute;top:-10px;left:50%;transform:translateX(-50%);background:var(--vg-accent);color:#fff;font-size:.6rem;font-weight:900;padding:2px 8px;border-radius:4px;text-transform:uppercase;letter-spacing:.05em;box-shadow:0 0 10px var(--vg-accent-glow);">Today</div>
                    @endif
                    <div style="text-align:center;padding-bottom:.8rem;border-bottom:1px solid rgba(255,255,255,.03);pointer-events:none;">
                        <p style="font-size:.7rem;color:{{ $isToday ? 'var(--vg-accent)' : 'rgba(255,255,255,.3)' }};font-weight:800;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">{{ substr($dayName, 0, 3) }}</p>
                        <p style="font-size:1rem;font-weight:700;color:{{ $isToday ? '#fff' : 'rgba(255,255,255,.8)' }};">{{ $dayName }}</p>
                    </div>

                    <div style="flex:1;display:flex;flex-direction:column;gap:10px;pointer-events:all;">
                        @if(isset($groupedAvailabilities[$idx]))
                            @foreach($groupedAvailabilities[$idx] as $slot)
                                @php $isBooked = $slot->is_booked_this_week; @endphp
                                <div class="slot-card" style="background:rgba(255,255,255,.03);border:1px solid {{ $isBooked ? 'rgba(139,92,246,.4)' : 'rgba(16,185,129,.3)' }};border-radius:16px;padding:12px;position:relative;transition:all .2s;">
                                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                                        <span style="font-size:.75rem;font-weight:800;color:{{ $isBooked ? 'var(--vg-accent)' : '#10b981' }};">{{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}</span>
                                        <div style="display:flex;gap:6px;">
                                            <button onclick="event.stopPropagation(); editSlot('{{ $slot->id }}', '{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}', '{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}', '{{ $slot->session_type }}', {{ $slot->is_recurring ? 'true' : 'false' }})" style="background:none;border:none;padding:0;cursor:pointer;color:rgba(255,255,255,.3);transition:color .2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.3)'">
                                                <i data-lucide="edit-3" style="width:12px;height:12px;"></i>
                                            </button>
                                            <form action="{{ route('trainer.availability.destroy', $slot->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this slot?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="event.stopPropagation()" style="background:none;border:none;padding:0;cursor:pointer;color:rgba(244,63,94,.4);transition:color .2s;" onmouseover="this.style.color='#f43f5e'" onmouseout="this.style.color='rgba(244,63,94,.4)'">
                                                    <i data-lucide="trash-2" style="width:12px;height:12px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <p style="font-size:.7rem;color:rgba(255,255,255,.4);font-weight:700;margin-bottom:8px;text-transform:uppercase;">{{ $slot->session_type }}</p>
                                    
                                    @if($isBooked)
                                        <div style="display:flex;align-items:center;gap:4px;font-size:.6rem;color:var(--vg-accent);font-weight:800;background:rgba(139,92,246,.1);padding:4px 8px;border-radius:6px;width:fit-content;">
                                            <i data-lucide="user" style="width:10px;height:10px;"></i>
                                            Booked
                                        </div>
                                    @else
                                        <div style="display:flex;align-items:center;gap:4px;font-size:.6rem;color:#10b981;font-weight:800;background:rgba(16,185,129,.1);padding:4px 8px;border-radius:6px;width:fit-content;">
                                            <i data-lucide="check" style="width:10px;height:10px;"></i>
                                            Available
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div style="flex:1;display:flex;align-items:center;justify-content:center;border:1px dashed rgba(255,255,255,.05);border-radius:16px;">
                                <p style="font-size:.65rem;color:rgba(255,255,255,.15);font-weight:700;text-transform:uppercase;">Empty</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

{{-- Edit Slot Modal --}}
<div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.8);backdrop-filter:blur(10px);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:rgba(20,20,20,.95);border:1px solid rgba(255,255,255,.1);border-radius:28px;padding:2.5rem;width:100%;max-width:480px;position:relative;">
        <button onclick="closeEditModal()" style="position:absolute;top:1.5rem;right:1.5rem;background:none;border:none;color:rgba(255,255,255,.4);cursor:pointer;">
            <i data-lucide="x"></i>
        </button>
        <h2 style="font-size:1.5rem;font-weight:900;color:#fff;margin-bottom:2rem;">Edit Time Slot</h2>
        <form id="editForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">
                <div>
                    <label style="display:block;font-size:.75rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:10px;">Start Time</label>
                    <input type="time" name="start_time" id="edit_start_time" required
                           style="width:100%;background:rgba(0,0,0,.4);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:12px;color:#fff;font-family:inherit;">
                </div>
                <div>
                    <label style="display:block;font-size:.75rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:10px;">End Time</label>
                    <input type="time" name="end_time" id="edit_end_time" required
                           style="width:100%;background:rgba(0,0,0,.4);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:12px;color:#fff;font-family:inherit;">
                </div>
            </div>
            <div style="margin-bottom:1.5rem;">
                <label style="display:block;font-size:.75rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:10px;">Session Type</label>
                <select name="session_type" id="edit_session_type" style="width:100%;background:rgba(0,0,0,.4);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:12px;color:#fff;outline:none;">
                    <option value="General">General Training</option>
                    <option value="Strength">Strength & Conditioning</option>
                    <option value="Yoga">Yoga / Flexibility</option>
                    <option value="HIIT">HIIT / Cardio</option>
                    <option value="Meditation">Mindfulness / Meditation</option>
                </select>
            </div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:2rem;">
                <input type="checkbox" name="is_recurring" id="edit_is_recurring" style="width:20px;height:20px;accent-color:var(--vg-accent);">
                <label for="edit_is_recurring" style="font-size:.9rem;color:rgba(255,255,255,.7);font-weight:500;">Repeat every week</label>
            </div>
            <button type="submit" style="width:100%;background:var(--vg-gradient);color:#fff;padding:14px;border-radius:16px;font-weight:800;font-size:1rem;border:none;">Save Changes</button>
        </form>
    </div>
</div>

<style>
    .day-checkbox:checked + .day-pill {
        background: var(--vg-accent) !important;
        border-color: var(--vg-accent) !important;
        color: #fff !important;
        box-shadow: 0 4px 12px var(--vg-accent-glow);
    }
    .slot-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
    .calendar-col:hover {
        border-color: rgba(255,255,255,0.1) !important;
        background: rgba(255,255,255,0.04) !important;
    }
    input[type="time"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
        cursor: pointer;
    }
</style>

<script>
    function selectDay(idx) {
        const checkbox = document.getElementById('day-' + idx);
        checkbox.checked = !checkbox.checked;
        
        // Trigger scroll to form if mobile
        if (window.innerWidth < 768) {
            document.querySelector('form').scrollIntoView({ behavior: 'smooth' });
        }
    }

    function validateForm() {
        const checkedDays = document.querySelectorAll('.day-checkbox:checked');
        if (checkedDays.length === 0) {
            alert('Please select at least one day.');
            return false;
        }

        const start = document.getElementById('start_time').value;
        const end = document.getElementById('end_time').value;

        if (start && end && start >= end) {
            alert('End time must be after start time.');
            return false;
        }

        return true;
    }

    function editSlot(id, start, end, type, recurring) {
        const form = document.getElementById('editForm');
        form.action = `/trainer/availability/${id}`;
        document.getElementById('edit_start_time').value = start;
        document.getElementById('edit_end_time').value = end;
        document.getElementById('edit_session_type').value = type;
        document.getElementById('edit_is_recurring').checked = recurring;
        
        document.getElementById('editModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function openBlockDatesModal() {
        alert('Block Dates feature coming soon! You will be able to select specific calendar dates to mark as unavailable.');
    }

    window.onclick = function(event) {
        const modal = document.getElementById('editModal');
        if (event.target == modal) {
            closeEditModal();
        }
    }
</script>
@endsection
