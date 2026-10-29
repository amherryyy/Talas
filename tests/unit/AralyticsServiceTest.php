<?php

use App\Services\AralyticsService;
use CodeIgniter\Test\CIUnitTestCase;
/**
 * @internal
 */
final class AralyticsServiceTest extends CIUnitTestCase
{
    public function testSevenDaySummaryIncludesTodayAndSixPreviousLocalDates(): void
    {
        $timezone = new DateTimeZone('Asia/Manila');
        $sessions = [
            $this->session(1, '2026-10-16 09:00:00', 25, 10, 'Math', 20, 'Algebra'),
            $this->session(1, '2026-10-10 23:59:59', 35, 10, 'Math', null, null),
            $this->session(1, '2026-10-09 23:59:59', 90, 10, 'Math', 20, 'Algebra'),
        ];

        $summary = (new AralyticsService())->summarize(
            $sessions,
            '7_days',
            new DateTimeImmutable('2026-10-16 12:00:00', $timezone),
            $timezone,
        );

        $this->assertSame('2026-10-10', $summary['start_date']);
        $this->assertSame(2, $summary['session_count']);
        $this->assertSame(60, $summary['total_minutes']);
        $this->assertSame(30.0, $summary['average_minutes']);
        $this->assertSame(60, $summary['subject_totals'][0]['total_minutes']);
        $this->assertSame('No topic', $summary['topic_totals'][0]['topic_title']);
        $this->assertSame('Algebra', $summary['topic_totals'][1]['topic_title']);
    }

    public function testTodayAndEmptyPeriodsHaveClearZeroTotals(): void
    {
        $timezone = new DateTimeZone('UTC');
        $service = new AralyticsService();
        $sessions = [$this->session(1, '2026-10-15 23:59:59', 15, 10, 'Science', null, null)];
        $summary = $service->summarize($sessions, 'today', new DateTimeImmutable('2026-10-16 00:00:00', $timezone), $timezone);

        $this->assertSame([], $summary['sessions']);
        $this->assertSame(0, $summary['total_minutes']);
        $this->assertNull($summary['average_minutes']);
    }

    public function testThirtyDayPeriodIncludesItsFirstDateAndExcludesThePriorDate(): void
    {
        $timezone = new DateTimeZone('UTC');
        $sessions = [
            $this->session(1, '2026-10-16 12:00:00', 20, 10, 'Science', null, null),
            $this->session(2, '2026-09-17 23:59:59', 40, 10, 'Science', null, null),
            $this->session(3, '2026-09-16 23:59:59', 60, 10, 'Science', null, null),
        ];

        $summary = (new AralyticsService())->summarize(
            $sessions,
            '30_days',
            new DateTimeImmutable('2026-10-16 12:00:00', $timezone),
            $timezone,
        );

        $this->assertSame('2026-09-17', $summary['start_date']);
        $this->assertSame(2, $summary['session_count']);
        $this->assertSame(60, $summary['total_minutes']);
    }

    /** @return array<string, mixed> */
    private function session(int $id, string $completedAt, int $minutes, int $subjectId, string $subjectName, ?int $topicId, ?string $topicTitle): array
    {
        return [
            'id' => $id,
            'completed_at' => $completedAt,
            'duration_minutes' => $minutes,
            'subject_id' => $subjectId,
            'subject_name' => $subjectName,
            'topic_id' => $topicId,
            'topic_title' => $topicTitle,
        ];
    }
}
