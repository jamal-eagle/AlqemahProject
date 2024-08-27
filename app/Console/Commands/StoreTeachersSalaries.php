<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Models\Teacher;
use App\Models\Out_Of_Work_Employee;
use App\Models\Salary;
use App\Models\Teacher_Schedule;
use App\Models\Academy;
class StoreTeachersSalaries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'salaries:store-teachers-salaries';

    protected $description = 'Calculate and store monthly salaries for teachers';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;

        // استرجاع جميع المدرسين
        $teachers = Teacher::all();

        foreach ($teachers as $teacher) {
            $this->calculateAndStoreSalary($teacher->id, $year, $month);
        }

        $this->info('Monthly salaries calculated and stored successfully.');
    }

private function calculateAndStoreSalary($teacher_id, $year, $month)
    {
    // استرجاع برنامج الدوام الأسبوعي للأستاذ
    $teacherSchedule = Teacher_Schedule::where('teacher_id', $teacher_id)->get();

    // استرجاع قائمة أيام العطل والغيابات في الشهر
    $holidays = Out_Of_Work_Employee::where('teacher_id', $teacher_id)
        ->whereYear('date', $year)
        ->whereMonth('date', $month)
        ->pluck('date')->toArray();

    // حساب عدد الأيام في الشهر
    $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

    // استرجاع أجر الساعة للأستاذ
    $teacher = Teacher::findOrFail($teacher_id);
    $hourlyRate = $teacher->cost_hour;
    $addedHour = $teacher->totalHoursAdded();

    $totalWorkingHours = 0;

    // حساب عدد ساعات العمل في الشهر
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $date = Carbon::createFromDate($year, $month, $day);
        $dayOfWeek = $date->format('l');

        if (in_array($date->toDateString(), $holidays) || in_array($dayOfWeek, ['Friday', 'Saturday'])) {
            continue; // تخطي أيام العطل والغيابات
        }

        // حساب مجموع ساعات العمل لكل فترة في اليوم
        $dailyWorkingHours = 0;
        foreach ($teacherSchedule as $schedule) {
            if ($schedule->day_of_week == $dayOfWeek) {
                $workingHours = $this->getWorkingHoursForDays($schedule);
                $dailyWorkingHours += $workingHours; // جمع الساعات لكل فترة
            }
        }

        $totalWorkingHours += $dailyWorkingHours; // إضافة ساعات اليوم الواحد للإجمالي
    }



        // حساب الراتب الشهري
        $monthlySalary = ($totalWorkingHours + $addedHour) * $hourlyRate;
        $academy = Academy::find(1);
        $academy =$academy->year;
        // تخزين الراتب الشهري في جدول salary
        Salary::create([
            'salary_of_teacher' => $monthlySalary,
            'month' => Carbon::createFromDate($year, $month, 1),
            'year'=>$academy,
            'teacher_id' => $teacher_id,
            'employee_id' => null, // يجعل قيمة employee_id null
            'status' => 0,
        ]);
    }

    private function getWorkingHoursForDays($schedule)
{
    // حساب عدد ساعات العمل بين وقت البداية ووقت النهاية
    $startTime = Carbon::createFromFormat('H:i:s', $schedule->start_time);
    $endTime = Carbon::createFromFormat('H:i:s', $schedule->end_time);
    $workingHours = $endTime->diffInHours($startTime);
    return $workingHours;
}
}
