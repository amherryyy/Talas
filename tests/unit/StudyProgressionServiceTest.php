<?php

use App\Services\StudyProgressionService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class StudyProgressionServiceTest extends CIUnitTestCase
{
    private DateTimeZone $timezone;

    protected function setUp(): void
    {
        parent::setUp();
        $this->timezone = new DateTimeZone('Asia/Manila');
    }

    public function testLevelThresholdsAndUnlocksAreDeterministic(): void
    {
        $service = new StudyProgressionService();
        $expected = [
            [0, 1],
            [119, 1],
            [120, 2],
            [299, 2],
            [300, 3],
            [599, 3],
            [600, 4],
            [1199, 4],
            [1200, 5],
        ];

        foreach ($expected as [$minutes, $level]) {
            $summary = $service->summarize(
                $minutes === 0 ? [] : [$this->session('2026-10-25 12:00:00', $minutes)],
                new DateTimeImmutable('2026-10-25 13:00:00', $this->timezone),
                $this->timezone,
            );

            $this->assertSame($minutes * StudyProgressionService::XP_PER_STUDY_MINUTE, $summary['xp']);
            $this->assertSame($level, $summary['level']);
        }

        $atFirstUnlock = $service->summarize(
            [$this->session('2026-10-25 12:00:00', 120)],
            new DateTimeImmutable('2026-10-25 13:00:00', $this->timezone),
            $this->timezone,
        );

        $this->assertTrue($atFirstUnlock['unlocks'][0]['unlocked']);
        $this->assertFalse($atFirstUnlock['unlocks'][1]['unlocked']);
        $this->assertSame('Weekday patterns', $atFirstUnlock['next_unlock']['name']);
    }

    public function testProgressAndBadgesComeOnlyFromSavedSessionMinutesAndDates(): void
    {
        $summary = (new StudyProgressionService())->summarize([
            $this->session('2026-10-25 09:00:00', 40),
            $this->session('2026-10-25 10:00:00', 80),
            $this->session('2026-10-24 10:00:00', 15),
            $this->session('2026-10-23 10:00:00', 15),
            $this->session('2026-10-22 10:00:00', 15),
            $this->session('2026-10-21 10:00:00', 15),
        ], new DateTimeImmutable('2026-10-25 13:00:00', $this->timezone), $this->timezone);

        $this->assertSame(18000, $summary['xp']);
        $this->assertSame(2, $summary['level']);
        $this->assertSame(6000, $summary['level_progress_xp']);
        $this->assertSame(18000, $summary['level_progress_max']);
        $this->assertSame(5, $summary['study_days_all_time']);
        $this->assertSame(5, $summary['study_days_last_30']);
        $this->assertTrue($summary['badges'][0]['unlocked']);
        $this->assertTrue($summary['badges'][1]['unlocked']);
        $this->assertTrue($summary['badges'][2]['unlocked']);
        $this->assertFalse($summary['badges'][3]['unlocked']);
    }

    public function testThirtyDayComparisonUsesInclusiveLocalDateBoundaries(): void
    {
        $summary = (new StudyProgressionService())->summarize([
            $this->session('2026-08-26 23:59:59', 100), // Outside both periods.
            $this->session('2026-08-27 00:00:00', 5), // First previous-period date.
            $this->session('2026-09-25 23:59:59', 15), // Last previous-period date.
            $this->session('2026-09-26 00:00:00', 10), // First current-period date.
            $this->session('2026-10-25 23:59:59', 20), // Today, inclusive.
            $this->session('2026-10-26 00:00:00', 200), // Future date, outside periods.
        ], new DateTimeImmutable('2026-10-25 12:00:00', $this->timezone), $this->timezone);

        $this->assertSame(30, $summary['current_30_day_minutes']);
        $this->assertSame(20, $summary['previous_30_day_minutes']);
        $this->assertSame(10, $summary['30_day_minutes_change']);
        $this->assertSame('2026-09-26', $summary['current_30_day_start']);
        $this->assertSame('2026-09-25', $summary['previous_30_day_end']);
    }

    public function testActivitySummariesIncludeSixLocalMonthsAndZeroFillEmptyDays(): void
    {
        $summary = (new StudyProgressionService())->summarize([
            $this->session('2026-04-30 23:59:59', 100),
            $this->session('2026-05-01 00:00:00', 20),
            $this->session('2026-10-25 09:00:00', 30),
        ], new DateTimeImmutable('2026-10-25 12:00:00', $this->timezone), $this->timezone);

        $this->assertCount(30, $summary['daily_minutes_last_30']);
        $this->assertSame(0, $summary['daily_minutes_last_30'][0]['minutes']);
        $this->assertCount(6, $summary['monthly_minutes_last_6']);
        $this->assertSame('2026-05', $summary['monthly_minutes_last_6'][0]['month']);
        $this->assertSame(20, $summary['monthly_minutes_last_6'][0]['minutes']);
        $this->assertSame('2026-10', $summary['monthly_minutes_last_6'][5]['month']);
        $this->assertSame(30, $summary['monthly_minutes_last_6'][5]['minutes']);
    }

    /** @return array{duration_minutes: int, completed_at: string} */
    private function session(string $completedAt, int $minutes): array
    {
        return ['duration_minutes' => $minutes, 'completed_at' => $completedAt];
    }
}
