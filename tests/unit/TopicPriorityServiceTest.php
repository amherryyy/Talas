<?php

use App\Services\TopicPriorityService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class TopicPriorityServiceTest extends CIUnitTestCase
{
    public function testPriorityFormulaAndExclusionOfCompletedTopics(): void
    {
        $service = new TopicPriorityService();
        $topics = $service->rank([
            $this->topic(1, 'Later high importance', '2026-10-23', 'high', 'not_started'),
            $this->topic(2, 'Today low importance', '2026-10-15', 'low', 'not_started'),
            $this->topic(3, 'Overdue low importance', '2026-10-14', 'low', 'in_progress'),
            $this->topic(4, 'Soon medium importance', '2026-10-22', 'medium', 'in_progress'),
            $this->topic(5, 'Undated high importance', null, 'high', 'not_started'),
            $this->topic(6, 'Completed topic', '2026-10-14', 'high', 'done'),
        ], '2026-10-15');

        $this->assertSame([
            'Today low importance',
            'Later high importance',
            'Overdue low importance',
            'Soon medium importance',
            'Undated high importance',
        ], array_column($topics, 'title'));
        $this->assertSame([7, 7, 6, 6, 6], array_column($topics, 'priority_score'));
        $this->assertSame([
            'Importance: Low (+1)',
            'Difficulty: Medium (+2)',
            'Due today (+3)',
            'Not started (+1)',
        ], $topics[0]['priority_reasons']);
        $this->assertSame([
            'Importance: High (+3)',
            'Difficulty: Medium (+2)',
            'No due date (+0)',
            'Not started (+1)',
        ], $topics[4]['priority_reasons']);
    }

    public function testHigherDifficultyRaisesTopicPriority(): void
    {
        $service = new TopicPriorityService();
        $topics = $service->rank([
            $this->topic(1, 'Lower difficulty', null, 'medium', 'in_progress', 'low'),
            $this->topic(2, 'Higher difficulty', null, 'medium', 'in_progress', 'high'),
        ], '2026-10-15');

        $this->assertSame(['Higher difficulty', 'Lower difficulty'], array_column($topics, 'title'));
        $this->assertSame([5, 3], array_column($topics, 'priority_score'));
        $this->assertSame('Difficulty: High (+3)', $topics[0]['priority_reasons'][1]);
        $this->assertSame('Difficulty: Low (+1)', $topics[1]['priority_reasons'][1]);
    }

    public function testTieBreakersUseNearestDueDateThenTitle(): void
    {
        $service = new TopicPriorityService();
        $topics = $service->rank([
            $this->topic(3, 'C topic', '2026-10-17', 'medium', 'in_progress'),
            $this->topic(2, 'B topic', '2026-10-16', 'medium', 'in_progress'),
            $this->topic(5, 'Same title', '2026-10-16', 'medium', 'in_progress'),
            $this->topic(4, 'Same title', '2026-10-16', 'medium', 'in_progress'),
            $this->topic(1, 'A topic', '2026-10-16', 'medium', 'in_progress'),
        ], '2026-10-15');

        $this->assertSame([
            'A topic',
            'B topic',
            'Same title',
            'Same title',
            'C topic',
        ], array_column($topics, 'title'));
        $this->assertSame([1, 2, 4, 5, 3], array_column($topics, 'id'));
    }

    public function testEmptyAndCompletedOnlyInputsReturnAnEmptyList(): void
    {
        $service = new TopicPriorityService();

        $this->assertSame([], $service->rank([], '2026-10-15'));
        $this->assertSame([], $service->rank([
            $this->topic(1, 'Done', null, 'low', 'done'),
        ], '2026-10-15'));
    }

    /** @return array<string, int|string|null> */
    private function topic(int $id, string $title, ?string $dueDate, string $importance, string $progress, string $difficulty = 'medium'): array
    {
        return [
            'id' => $id,
            'subject_id' => 1,
            'subject_name' => 'Synthetic subject',
            'title' => $title,
            'due_date' => $dueDate,
            'importance' => $importance,
            'difficulty' => $difficulty,
            'progress' => $progress,
        ];
    }
}
