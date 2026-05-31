<x-app-layout>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">

        {{-- Hero Timer Section --}}
        <div class="lg:col-span-2">
            <div class="bg-white border-4 border-black p-6 sm:p-8 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
                <div class="text-center">
                    {{-- Session Type Indicator --}}
                    <div class="flex flex-col items-center gap-3 mb-4">
                        <div class="flex justify-center gap-2">
                            <button type="button" data-session="focus" data-duration="1500"
                                class="session-btn btn-neubrutalism px-4 py-2 font-bold text-xs uppercase tracking-wider bg-[#4D96FF] text-white">
                                Focus
                            </button>
                            <button type="button" data-session="short_break" data-duration="300"
                                class="session-btn btn-neubrutalism px-4 py-2 font-bold text-xs uppercase tracking-wider bg-white text-black hover:bg-[#FFD93D]">
                                Short Break
                            </button>
                            <button type="button" data-session="long_break" data-duration="900"
                                class="session-btn btn-neubrutalism px-4 py-2 font-bold text-xs uppercase tracking-wider bg-white text-black hover:bg-[#6BCB77]">
                                Long Break
                            </button>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="number" id="customMinutes" min="1" max="999" value="25"
                                class="btn-neubrutalism w-20 px-3 py-2 font-bold text-sm text-center bg-white [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                            <span class="font-bold text-sm text-black/50">minutes</span>
                        </div>
                    </div>

                    {{-- Timer Display --}}
                    <div class="py-4 sm:py-8">
                        <div id="timerDisplay" class="text-7xl sm:text-8xl lg:text-9xl font-black tracking-tighter leading-none select-none tabular-nums">
                            25:00
                        </div>
                        <p id="timerLabel" class="mt-3 text-sm font-bold uppercase tracking-[0.2em] text-black/50">Focus Time</p>
                    </div>

                    {{-- Timer Controls --}}
                    <div class="flex justify-center gap-3 sm:gap-4">
                        <button id="startBtn"
                            class="btn-neubrutalism btn-neubrutalism-lg px-8 py-3 bg-[#6BCB77] font-black text-sm uppercase tracking-wider text-white">
                            Start
                        </button>
                        <button id="pauseBtn" disabled
                            class="btn-neubrutalism btn-neubrutalism-lg px-8 py-3 bg-[#FFD93D] font-black text-sm uppercase tracking-wider text-black">
                            Pause
                        </button>
                        <button id="resetBtn"
                            class="btn-neubrutalism btn-neubrutalism-lg px-6 py-3 bg-[#FF6B6B] font-black text-sm uppercase tracking-wider text-white">
                            Reset
                        </button>
                    </div>
                </div>

                {{-- Motivational Quote --}}
                <div class="mt-6 sm:mt-8 pt-6 border-t-4 border-black">
                    <p id="quoteText" class="text-center font-bold text-sm sm:text-base italic text-black/70">"The secret of getting ahead is getting started."</p>
                    <p id="quoteAuthor" class="text-center font-bold text-xs text-black/40 mt-1">— Mark Twain</p>
                </div>
            </div>
        </div>

        {{-- Sidebar: Stats + Current Task --}}
        <div class="flex flex-col gap-6">
            {{-- Current Task Card --}}
            <div class="bg-white border-4 border-black p-5 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
                <h3 class="font-black text-xs uppercase tracking-wider mb-3">Current Focus</h3>
                @php
                    $activeTask = $tasks->firstWhere('completed', false);
                @endphp
                @if ($activeTask)
                    <p class="font-bold text-lg truncate">{{ $activeTask->title }}</p>
                    <span class="inline-block mt-2 px-3 py-1 border-4 border-black text-xs font-black uppercase
                        @if($activeTask->priority === 'high') bg-[#FF6B6B] text-white
                        @elseif($activeTask->priority === 'medium') bg-[#FFD93D] text-black
                        @else bg-[#6BCB77] text-white @endif">
                        {{ $activeTask->priority }}
                    </span>
                @else
                    <p class="font-bold text-sm text-black/50">No active task selected</p>
                    <p class="text-xs font-semibold text-black/40 mt-1">Add a task below to get started</p>
                @endif
            </div>

            {{-- Statistics Card --}}
            <div class="bg-white border-4 border-black p-5 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
                <h3 class="font-black text-xs uppercase tracking-wider mb-4">Today's Stats</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="text-center p-3 border-4 border-black bg-[#FFF8E8]">
                        <p class="text-2xl font-black">{{ $stats['total_tasks'] }}</p>
                        <p class="text-xs font-bold uppercase text-black/60">Total Tasks</p>
                    </div>
                    <div class="text-center p-3 border-4 border-black bg-[#FFF8E8]">
                        <p class="text-2xl font-black">{{ $stats['completed_tasks'] }}</p>
                        <p class="text-xs font-bold uppercase text-black/60">Completed</p>
                    </div>
                    <div class="text-center p-3 border-4 border-black bg-[#FFF8E8]">
                        <p class="text-2xl font-black">{{ $stats['focus_sessions_today'] }}</p>
                        <p class="text-xs font-bold uppercase text-black/60">Sessions</p>
                    </div>
                    <div class="text-center p-3 border-4 border-black bg-[#FFF8E8]">
                        <p class="text-2xl font-black" id="focusTimeDisplay">{{ sprintf('%02d:%02d', floor($stats['total_focus_time_today'] / 60), $stats['total_focus_time_today'] % 60) }}</p>
                        <p class="text-xs font-bold uppercase text-black/60">Focus Time</p>
                    </div>
                </div>
            </div>

            {{-- Streak Card --}}
            <div class="bg-white border-4 border-black p-5 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-black text-xs uppercase tracking-wider">Focus Streak</h3>
                        <p class="text-3xl font-black mt-1">{{ $stats['streak'] }} <span class="text-lg font-bold text-black/50">days</span></p>
                    </div>
                    <span class="text-4xl">🔥</span>
                </div>
            </div>
        </div>

        {{-- Todo List Section --}}
        <div class="lg:col-span-2">
            <div class="bg-white border-4 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
                <div class="p-5 sm:p-6 border-b-4 border-black">
                    <h2 class="font-black text-lg uppercase tracking-tight">Todo List</h2>
                </div>

                {{-- Add Task Form --}}
                <form method="POST" action="{{ route('tasks.store') }}" class="p-5 sm:p-6 border-b-4 border-black">
                    @csrf
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="text" name="title" placeholder="What do you need to do?" required
                            class="flex-1 border-4 border-black px-4 py-3 font-semibold text-sm focus:outline-none focus:ring-0 placeholder:text-black/40">
                        <select name="priority"
                            class="border-4 border-black px-4 py-3 font-bold text-sm focus:outline-none focus:ring-0 bg-white">
                            <option value="high">🔥 High</option>
                            <option value="medium" selected>📋 Medium</option>
                            <option value="low">💚 Low</option>
                        </select>
                        <input type="date" name="due_date"
                            class="border-4 border-black px-4 py-3 font-bold text-sm focus:outline-none focus:ring-0 bg-white">
                        <button type="submit"
                            class="btn-neubrutalism px-6 py-3 bg-[#4D96FF] font-black text-sm uppercase tracking-wider text-white whitespace-nowrap">
                            + Add
                        </button>
                    </div>
                    @error('title')
                        <p class="mt-2 text-sm font-bold text-[#FF6B6B]">{{ $message }}</p>
                    @enderror
                </form>

                {{-- Filters --}}
                <div class="flex gap-2 px-5 sm:px-6 py-4 border-b-4 border-black bg-[#FFF8E8]">
                    @foreach (['all' => 'All', 'active' => 'Active', 'completed' => 'Completed'] as $key => $label)
                        <a href="{{ route('dashboard', ['filter' => $key]) }}"
                            class="btn-neubrutalism-sm px-4 py-2 font-bold text-xs uppercase tracking-wider
                            {{ $filter === $key ? 'bg-black text-white' : 'bg-white text-black hover:bg-[#FFD93D]' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                {{-- Task List --}}
                <div class="divide-y-4 divide-black">
                    @forelse ($tasks as $task)
                        <div class="flex items-center gap-3 sm:gap-4 p-4 sm:px-6 hover:bg-[#FFF8E8] transition-colors {{ $task->completed ? 'opacity-60' : '' }}">
                            {{-- Toggle Complete --}}
                            <button type="button"
                                onclick="toggleTask({{ $task->id }})"
                                class="btn-neubrutalism w-7 h-7 flex-shrink-0 flex items-center justify-center font-bold text-lg hover:bg-[#6BCB77] {{ $task->completed ? 'bg-[#6BCB77]' : 'bg-white' }}">
                                @if ($task->completed)
                                    ✓
                                @endif
                            </button>

                            {{-- Task Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-sm sm:text-base truncate {{ $task->completed ? 'line-through' : '' }}">
                                    {{ $task->title }}
                                </p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-block px-2 py-0.5 border-2 border-black text-xs font-bold uppercase
                                        @if($task->priority === 'high') bg-[#FF6B6B] text-white
                                        @elseif($task->priority === 'medium') bg-[#FFD93D] text-black
                                        @else bg-[#6BCB77] text-white @endif">
                                        {{ $task->priority }}
                                    </span>
                                    @if ($task->due_date)
                                        <span class="text-xs font-bold text-black/50">{{ $task->due_date->format('M d') }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <button type="button"
                                    onclick="editTask(this)"
                                    data-id="{{ $task->id }}"
                                    data-title="{{ $task->title }}"
                                    data-priority="{{ $task->priority }}"
                                    data-due-date="{{ $task->due_date?->format('Y-m-d') ?? '' }}"
                                    class="btn-neubrutalism px-3 py-2 font-bold text-xs uppercase bg-white hover:bg-[#FFD93D]">
                                    ✏️
                                </button>
                                <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn-neubrutalism px-3 py-2 font-bold text-xs uppercase bg-white hover:bg-[#FF6B6B]">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <p class="font-bold text-black/40 text-lg">No tasks yet</p>
                            <p class="font-semibold text-black/30 text-sm mt-1">Add a task above to start tracking your focus</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Progress Section --}}
        <div>
            <div class="bg-white border-4 border-black p-5 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
                <h3 class="font-black text-xs uppercase tracking-wider mb-4">Daily Progress</h3>
                <div class="w-full bg-[#FFF8E8] border-4 border-black h-6 overflow-hidden">
                    <div id="progressBar" class="h-full bg-[#6BCB77] transition-all duration-500"
                        style="width: {{ $progressPercentage }}%"></div>
                </div>
                <p class="mt-3 text-center font-black text-lg">
                    {{ $progressPercentage }}% <span class="text-sm font-bold text-black/50">completed</span>
                </p>
            </div>
        </div>
    </div>

    {{-- Edit Task Modal --}}
    <div id="editModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 hidden z-50">
        <div class="bg-white border-4 border-black p-6 w-full max-w-md shadow-[12px_12px_0px_0px_rgba(0,0,0,1)]">
            <h3 class="font-black text-lg uppercase mb-4">Edit Task</h3>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block font-bold mb-2 text-sm uppercase">Title</label>
                    <input type="text" name="title" id="editTitle" required
                        class="w-full border-4 border-black px-4 py-3 font-semibold text-sm focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2 text-sm uppercase">Priority</label>
                    <select name="priority" id="editPriority"
                        class="w-full border-4 border-black px-4 py-3 font-bold text-sm focus:outline-none bg-white">
                        <option value="high">🔥 High</option>
                        <option value="medium">📋 Medium</option>
                        <option value="low">💚 Low</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label class="block font-bold mb-2 text-sm uppercase">Due Date</label>
                    <input type="date" name="due_date" id="editDueDate"
                        class="w-full border-4 border-black px-4 py-3 font-bold text-sm focus:outline-none bg-white">
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                        class="btn-neubrutalism flex-1 py-3 bg-[#4D96FF] font-black text-sm uppercase tracking-wider text-white">
                        Save
                    </button>
                    <button type="button" onclick="closeEditModal()"
                        class="btn-neubrutalism flex-1 py-3 bg-white font-black text-sm uppercase tracking-wider">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const sessionTimes = { focus: 1500, short_break: 300, long_break: 900 };
        const sessionLabels = { focus: 'Focus Time', short_break: 'Short Break', long_break: 'Long Break' };
        const quotes = [
            { text: "The secret of getting ahead is getting started.", author: "Mark Twain" },
            { text: "Focus on being productive instead of busy.", author: "Tim Ferriss" },
            { text: "You don't have to be extreme, just consistent.", author: "Unknown" },
            { text: "The way to get started is to quit talking and begin doing.", author: "Walt Disney" },
            { text: "Small daily improvements over time lead to stunning results.", author: "Robin Sharma" },
            { text: "It's not about having time. It's about making time.", author: "Unknown" },
            { text: "The best time to plant a tree was 20 years ago. The second best time is now.", author: "Chinese Proverb" },
            { text: "Success is the sum of small efforts repeated day in and day out.", author: "Robert Collier" },
            { text: "Don't watch the clock; do what it does. Keep going.", author: "Sam Levenson" },
            { text: "The only limit to our realization of tomorrow will be our doubts of today.", author: "Franklin D. Roosevelt" },
        ];
    </script>
    @endpush
</x-app-layout>
