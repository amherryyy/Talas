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
            'body' => 'Pick one topic to review in a later study session instead of doing every review in one sitting.',
            'recommendation' => 'Recommendation 1: Space learning over time.',
        ],
        [
            'id' => 'recall-from-memory',
            'title' => 'Try recalling before rereading',
            'body' => 'Close your notes and write or say what you remember, then check your materials for gaps.',
            'recommendation' => 'Recommendation 5b: Use quizzes to revisit key content.',
        ],
        [
            'id' => 'explain-the-idea',
            'title' => 'Explain why or how',
            'body' => 'Ask yourself why a key idea works or how two ideas connect, then write a short explanation.',
            'recommendation' => 'Recommendation 7: Ask deep explanatory questions.',
        ],
        [
            'id' => 'example-then-practice',
            'title' => 'Read an example, then try one',
            'body' => 'Review a worked solution, try a similar problem on your own, and check your steps afterward.',
            'recommendation' => 'Recommendation 2: Alternate worked examples and problem-solving.',
        ],
    ];
}
