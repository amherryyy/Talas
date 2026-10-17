<?php

namespace App\Content;

final class StudySuggestions
{
    public const SOURCE_URL = 'https://ies.ed.gov/ncee/wwc/practiceguide/1';
    public const SOURCE_NAME = 'What Works Clearinghouse practice guide (2007)';

    /** @var list<array{id: string, title: string, body: string, recommendation: string}> */
    public const ITEMS = [
        [
            'id' => 'space-reviews',
            'title' => 'Revisit a topic later',
            'body' => 'Review one topic again in a later study session.',
            'recommendation' => 'Recommendation 1: Space learning over time.',
        ],
        [
            'id' => 'recall-from-memory',
            'title' => 'Try recalling before rereading',
            'body' => 'Recall the key points before checking your notes for gaps.',
            'recommendation' => 'Recommendation 5b: Use quizzes to revisit key content.',
        ],
        [
            'id' => 'explain-the-idea',
            'title' => 'Explain why or how',
            'body' => 'Explain why an idea works or how two ideas connect.',
            'recommendation' => 'Recommendation 7: Ask deep explanatory questions.',
        ],
        [
            'id' => 'example-then-practice',
            'title' => 'Read an example, then try one',
            'body' => 'Study a worked example, solve a similar problem, then check your steps.',
            'recommendation' => 'Recommendation 2: Alternate worked examples and problem-solving.',
        ],
    ];
}
