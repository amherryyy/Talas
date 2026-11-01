<?php

namespace App\Content;

final class StudySuggestions
{
    public const SOURCE_URL = 'https://ies.ed.gov/ncee/wwc/practiceguide/1';
    public const SOURCE_NAME = 'What Works Clearinghouse practice guide (2007)';

    /** @var list<array{id: string, title: string, body: string, recommendation: string, steps: list<string>}> */
    public const ITEMS = [
        [
            'id' => 'space-reviews',
            'title' => 'Revisit a topic later',
            'body' => 'Review one topic again in a later study session.',
            'recommendation' => 'Recommendation 1: Space learning over time.',
            'steps' => [
                'Choose one idea you studied today.',
                'Close your notes and recall the main points.',
                'Check your notes, then schedule a short revisit for another day.',
            ],
        ],
        [
            'id' => 'recall-from-memory',
            'title' => 'Try recalling before rereading',
            'body' => 'Recall the key points before checking your notes for gaps.',
            'recommendation' => 'Recommendation 5b: Use quizzes to revisit key content.',
            'steps' => [
                'Close your notes and write three things you remember.',
                'Turn one key idea into a question and answer it from memory.',
                'Reopen your notes and correct one gap or mistake.',
            ],
        ],
        [
            'id' => 'explain-the-idea',
            'title' => 'Explain why or how',
            'body' => 'Explain why an idea works or how two ideas connect.',
            'recommendation' => 'Recommendation 7: Ask deep explanatory questions.',
            'steps' => [
                'Pick one concept that feels unclear or important.',
                'Ask “Why does this work?” or “How does it connect?”',
                'Explain it in your own words and check the explanation against your material.',
            ],
        ],
        [
            'id' => 'example-then-practice',
            'title' => 'Read an example, then try one',
            'body' => 'Study a worked example, solve a similar problem, then check your steps.',
            'recommendation' => 'Recommendation 2: Alternate worked examples and problem-solving.',
            'steps' => [
                'Read a worked example and note what each step is doing.',
                'Try a similar problem without looking at the solution.',
                'Compare your steps and find the first point that differs.',
            ],
        ],
    ];
}
