<?php

namespace App\Content;

/**
 * Small reference map from curated study suggestions to relevant CHED program outcomes.
 * The mapping is Duno's editorial association; CHED does not prescribe these study methods.
 */
final class StudyGuideOutcomes
{
    public const SOURCE_URL = 'https://legacy.ched.gov.ph/wp-content/uploads/2017/10/CMO-no.-25-s.-2015.pdf';
    public const SOURCE_NAME = 'CHED CMO No. 25, s. 2015 (BSCS, BSIS, BSIT)';

    /** @var array<string, string> */
    public const PROGRAMS = [
        'bsit-web-mobile' => 'it',
        'bsit-network-design' => 'it',
        'bsis-service-management' => 'is',
        'bsis-business-analytics' => 'is',
        'bscs-game-development' => 'cs',
        'bscs-robotics' => 'cs',
    ];

    /** @var array<string, array<string, array{code: string, label: string}>> */
    private const OUTCOMES = [
        'cs' => [
            'lifelong' => ['code' => 'CS10', 'label' => 'Engage in independent learning for continual development as a computing professional.'],
            'application' => ['code' => 'CS01', 'label' => 'Apply computing and related knowledge to model defined problems and requirements.'],
            'analysis' => ['code' => 'CS02', 'label' => 'Analyze and solve complex computing problems using relevant principles.'],
            'communication' => ['code' => 'CS08', 'label' => 'Communicate effectively about complex computing activities.'],
            'design' => ['code' => 'CS05', 'label' => 'Design and evaluate solutions for complex computing problems.'],
        ],
        'is' => [
            'lifelong' => ['code' => 'IS10', 'label' => 'Engage in independent learning for continual development as a computing professional.'],
            'application' => ['code' => 'IS01', 'label' => 'Apply computing and related knowledge to address organizational problems.'],
            'analysis' => ['code' => 'IS02–IS03', 'label' => 'Analyze business problems and identify information-system requirements.'],
            'communication' => ['code' => 'IS08', 'label' => 'Communicate effectively about information-systems activities.'],
            'design' => ['code' => 'IS04–IS05', 'label' => 'Design and develop information-system solutions from a system-level perspective.'],
        ],
        'it' => [
            'lifelong' => ['code' => 'IT13', 'label' => 'Engage in independent learning for continual development as an IT professional.'],
            'application' => ['code' => 'IT01', 'label' => 'Apply computing and related knowledge to address organizational needs.'],
            'analysis' => ['code' => 'IT03', 'label' => 'Analyze technological needs and define requirements for IT solutions.'],
            'communication' => ['code' => 'IT10', 'label' => 'Communicate effectively about complex IT activities.'],
            'design' => ['code' => 'IT05', 'label' => 'Design and develop IT solutions using a system-level perspective.'],
        ],
    ];

    public static function programFamily(?string $programId): ?string
    {
        return $programId === null ? null : (self::PROGRAMS[$programId] ?? null);
    }

    /** @return list<array{code: string, label: string}> */
    public static function forSuggestion(string $suggestionId, string $programFamily): array
    {
        $outcomeKeys = match ($suggestionId) {
            'space-reviews' => ['lifelong'],
            'recall-from-memory' => ['application'],
            'explain-the-idea' => ['analysis', 'communication'],
            'example-then-practice' => ['analysis', 'design'],
            'interleave-practice' => ['analysis'],
            'confidence-check' => ['lifelong'],
            default => [],
        };
        $programOutcomes = self::OUTCOMES[$programFamily] ?? [];

        return array_values(array_filter(array_map(
            static fn (string $key): ?array => $programOutcomes[$key] ?? null,
            $outcomeKeys,
        )));
    }
}
