<?php

declare(strict_types=1);

namespace App\Services;

/** Account-level NVSU program choices, derived from the existing campus directory. */
final class AcademicProgramCatalog
{
    /** @var array<string, string> */
    private const CURRICULA = [
        'Bayombong Campus|College of Forestry, Environment and Resources Management|Bachelor of Science in Forestry|' => 'bs-forestry',
        'Bayombong Campus|College of Forestry, Environment and Resources Management|Bachelor of Science in Agroforestry|' => 'bs-agroforestry',
        'Bayombong Campus|College of Forestry, Environment and Resources Management|Bachelor of Science in Ecotourism|' => 'bs-ecotourism',
        'Bayombong Campus|College of Arts and Sciences|Bachelor of Science in Information Systems|Service Management' => 'bsis-service-management',
        'Bayombong Campus|College of Arts and Sciences|Bachelor of Science in Information Systems|Business Analytics' => 'bsis-business-analytics',
        'Bayombong Campus|College of Arts and Sciences|Bachelor of Science in Information Technology|Web & Mobile App Development' => 'bsit-web-mobile',
        'Bayombong Campus|College of Arts and Sciences|Bachelor of Science in Information Technology|Network Design & Management' => 'bsit-network-design',
        'Bayombong Campus|College of Arts and Sciences|Bachelor of Science in Computer Science|Game Development' => 'bscs-game-development',
        'Bayombong Campus|College of Arts and Sciences|Bachelor of Science in Computer Science|Robotics' => 'bscs-robotics',
    ];

    /** @return list<array{id: string, title: string, campus: string, college: string, curriculum_id: ?string}> */
    public function all(): array
    {
        $choices = [];
        foreach ((new NvsuProgramCatalog())->all() as $campus) {
            foreach ($campus['colleges'] as $college) {
                foreach ($college['programs'] as $program) {
                    $majors = $program['majors'] ?? [null];
                    foreach ($majors as $major) {
                        $curriculumId = self::CURRICULA[implode('|', [
                            $campus['campus'], $college['name'], $program['title'], $major ?? '',
                        ])] ?? null;
                        $title = $program['title'] . ($major === null ? '' : ' — ' . $major);
                        $identity = implode('|', [$campus['campus'], $college['name'], $title]);
                        $choices[] = [
                            'id' => 'nvsu-' . substr(hash('sha256', $identity), 0, 32),
                            'title' => $title,
                            'campus' => $campus['campus'],
                            'college' => $college['name'],
                            'curriculum_id' => $curriculumId,
                        ];
                    }
                }
            }
        }

        return $choices;
    }

    /** @return list<array{id: string, title: string, campus: string, college: string, curriculum_id: ?string}> */
    public function forCampus(string $campusName): array
    {
        return array_values(array_filter(
            $this->all(),
            static fn (array $program): bool => $program['campus'] === $campusName,
        ));
    }

    /** @return list<string> */
    public function collegesAtCampus(string $campusName): array
    {
        $colleges = [];
        foreach ($this->forCampus($campusName) as $program) {
            $colleges[$program['college']] = true;
        }

        return array_keys($colleges);
    }

    /** @return array{id: string, title: string, campus: string, college: string, curriculum_id: ?string}|null */
    public function find(string $id): ?array
    {
        foreach ($this->all() as $program) {
            if (hash_equals($program['id'], $id)) {
                return $program;
            }
        }

        return null;
    }

    /** @return array{id: string, title: string, campus: string, college: string, curriculum_id: ?string}|null */
    public function forUser(int $userId): ?array
    {
        $selectedId = db_connect()->table('users')->select('academic_program_id')
            ->where('id', $userId)->get()->getRow('academic_program_id');

        return is_string($selectedId) ? $this->find($selectedId) : null;
    }
}
