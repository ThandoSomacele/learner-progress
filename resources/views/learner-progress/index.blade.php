<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Learner Progress Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Learner Progress Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Track learner enrolments and progress across courses</p>
        </header>

        <div x-data="progressDashboard(@js($learners), @js($courses), @js($selectedCourse), '{{ $sortDirection }}')"
             class="space-y-6">

            <!-- Filters and Controls -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Course Filter -->
                    <div>
                        <label for="course-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Filter by Course
                        </label>
                        <select id="course-filter"
                                x-model="selectedCourse"
                                @change="applyFilters()"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ $selectedCourse == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort Control -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Sort by Progress
                        </label>
                        <div class="flex gap-2">
                            <button @click="sortProgress('asc')"
                                    :class="sortDirection === 'asc' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                                    class="flex-1 px-4 py-2 rounded-md font-medium transition-colors hover:opacity-80">
                                Low to High
                            </button>
                            <button @click="sortProgress('desc')"
                                    :class="sortDirection === 'desc' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                                    class="flex-1 px-4 py-2 rounded-md font-medium transition-colors hover:opacity-80">
                                High to Low
                            </button>
                            <button @click="clearSort()"
                                    x-show="sortDirection"
                                    class="px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium transition-colors hover:opacity-80">
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Reset All Filters Button -->
                <div x-show="selectedCourse || sortDirection" class="mt-4 flex justify-center">
                    <button @click="resetAllFilters()"
                            class="px-6 py-2 rounded-md bg-red-600 text-white font-medium transition-colors hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        Reset All Filters
                    </button>
                </div>

                <!-- Stats Summary -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" x-text="filteredLearners.length"></div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Learners</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400" x-text="totalEnrolments"></div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Enrolments</div>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400" x-text="averageProgress + '%'"></div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Average Progress</div>
                    </div>
                </div>
            </div>

            <!-- Learners List -->
            <div class="space-y-4">
                <!-- Empty State -->
                <template x-if="filteredLearners.length === 0">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No learners found</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Try adjusting your filters to see results.</p>
                    </div>
                </template>

                <!-- Learner Cards -->
                <template x-for="learner in filteredLearners" :key="learner.id">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white" x-text="learner.full_name"></h3>
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Courses:</span>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white"
                                              x-text="learner.enrolments.length">
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Avg Progress:</span>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                              :class="getProgressColorClass(learner.average_progress)"
                                              x-text="learner.average_progress + '%'">
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Enrolments -->
                            <div class="space-y-3">
                                <template x-if="learner.enrolments.length === 0">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">No enrolments</p>
                                </template>

                                <template x-for="enrolment in learner.enrolments" :key="enrolment.course_name">
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300" x-text="enrolment.course_name"></span>
                                        <div class="flex items-center gap-3">
                                            <!-- Progress Bar -->
                                            <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                <div class="h-2 rounded-full transition-all"
                                                     :class="getProgressBarColorClass(parseFloat(enrolment.progress))"
                                                     :style="`width: ${Math.min(parseFloat(enrolment.progress), 100)}%`">
                                                </div>
                                            </div>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white w-12 text-right" x-text="enrolment.progress"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('progressDashboard', (initialLearners, allCourses, selectedCourseId, initialSort) => ({
                learners: initialLearners,
                courses: allCourses,
                selectedCourse: selectedCourseId ?? '',
                sortDirection: initialSort || '',

                get filteredLearners() {
                    let filtered = this.learners;

                    // Apply course filter
                    if (this.selectedCourse) {
                        filtered = filtered.filter(learner =>
                            learner.enrolments.some(e =>
                                this.courses.find(c => c.id == this.selectedCourse)?.name === e.course_name
                            )
                        );
                    }

                    // Apply sorting
                    if (this.sortDirection) {
                        filtered = [...filtered].sort((a, b) => {
                            return this.sortDirection === 'asc'
                                ? a.average_progress - b.average_progress
                                : b.average_progress - a.average_progress;
                        });
                    }

                    return filtered;
                },

                get totalEnrolments() {
                    return this.filteredLearners.reduce((sum, learner) => sum + learner.enrolments.length, 0);
                },

                get averageProgress() {
                    if (this.filteredLearners.length === 0) return 0;
                    const total = this.filteredLearners.reduce((sum, learner) => sum + learner.average_progress, 0);
                    return (total / this.filteredLearners.length).toFixed(2);
                },

                applyFilters() {
                    const url = new URL(window.location);
                    if (this.selectedCourse) {
                        url.searchParams.set('course_id', this.selectedCourse);
                    } else {
                        url.searchParams.delete('course_id');
                    }
                    if (this.sortDirection) {
                        url.searchParams.set('sort', this.sortDirection);
                    }
                    window.location.href = url.toString();
                },

                sortProgress(direction) {
                    this.sortDirection = direction;
                    const url = new URL(window.location);
                    url.searchParams.set('sort', direction);
                    window.location.href = url.toString();
                },

                clearSort() {
                    this.sortDirection = '';
                    const url = new URL(window.location);
                    url.searchParams.delete('sort');
                    if (this.selectedCourse) {
                        url.searchParams.set('course_id', this.selectedCourse);
                    }
                    window.location.href = url.toString();
                },

                resetAllFilters() {
                    window.location.href = '/learner-progress';
                },

                getProgressColorClass(progress) {
                    const p = parseFloat(progress);
                    if (p >= 75) return 'bg-green-500 text-green-50';
                    if (p >= 50) return 'bg-blue-500 text-blue-50';
                    if (p >= 25) return 'bg-yellow-500 text-yellow-50';
                    return 'bg-red-500 text-red-50';
                },

                getProgressBarColorClass(progress) {
                    const p = parseFloat(progress);
                    if (p >= 75) return 'bg-green-500';
                    if (p >= 50) return 'bg-blue-500';
                    if (p >= 25) return 'bg-yellow-500';
                    return 'bg-red-500';
                }
            }));
        });
    </script>
</body>
</html>
