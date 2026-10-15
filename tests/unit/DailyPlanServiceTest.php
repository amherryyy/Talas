<?php

use App\Services\DailyPlanService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class DailyPlanServiceTest extends CIUnitTestCase
{
    public function testBuildsOneTwentyFiveMinuteBlockPerRankedTopicAndShortensLastBlock(): void
    {
        $items = (new DailyPlanService())->buildItems([
            $this->topic(8, 'First', 'Biology'),
            $this->topic(3, 'Second', 'Math'),
            $this->topic(1, 'Done', 'Art', 'done'),
            $this->topic(9, 'Third', 'Physics'),
        ], 60);

        $this->assertSame([8, 3, 9], array_column($items, 'topic_id'));
        $this->assertSame([25, 25, 10], array_column($items, 'duration_minutes'));
        $this->assertSame([1, 2, 3], array_column($items, 'position'));
        $this->assertSame(60, (new DailyPlanService())->totalMinutes($items));
    }

    public function testEmptyTopicListCreatesAnEmptyPlan(): void
    {
        $this->assertSame([], (new DailyPlanService())->buildItems([], 15));
    }

    public function testRejectsAvailableTimeOutsideApprovedBounds(): void
    {
        $service = new DailyPlanService();
        foreach ([0, 14, 481] as $minutes) {
            try {
                $service->buildItems([], $minutes);
                $this->fail('Out-of-range plan time must be rejected.');
            } catch (InvalidArgumentException) {
                $this->assertTrue(true);
            }
        }
    }

    /** @return array<string, int|string> */
    private function topic(int $id, string $title, string $subject, string $progress = 'not_started'): array
    {
        return ['id' => $id, 'title' => $title, 'subject_name' => $subject, 'progress' => $progress];
    }
}
