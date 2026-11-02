<?php

declare(strict_types=1);

namespace App\Services;

/** NVSU program choices plus saved school/program details for external schools. */
final class AcademicProgramCatalog
{
    public const MANUAL_ENTRY_ID = 'manual-entry';
    public const NVSU_BAYOMBONG_SCHOOL = 'Nueva Vizcaya State University (NVSU), Bayombong Campus';

    /** @var array<string, string> */
    private const CURRICULA = [
        'Bayombong Campus|College of Forestry, Environment and Resources Management|Bachelor of Science in Forestry|' => 'bs-forestry',
        'Bayombong Campus|College of Forestry, Environment and Resources Management|Bachelor of Science in Agroforestry|' => 'bs-agroforestry',
        'Bayombong Campus|College of Forestry, Environment and Resources Management|Bachelor of Science in Ecotourism|' => 'bs-ecotourism',
        'Bayombong Campus|College of Information Technology Education|Bachelor of Science in Information Systems|Service Management' => 'bsis-service-management',
        'Bayombong Campus|College of Information Technology Education|Bachelor of Science in Information Systems|Business Analytics' => 'bsis-business-analytics',
        'Bayombong Campus|College of Information Technology Education|Bachelor of Science in Information Technology|Web & Mobile App Development' => 'bsit-web-mobile',
        'Bayombong Campus|College of Information Technology Education|Bachelor of Science in Information Technology|Network Design & Management' => 'bsit-network-design',
        'Bayombong Campus|College of Information Technology Education|Bachelor of Science in Computer Science|Game Development' => 'bscs-game-development',
        'Bayombong Campus|College of Information Technology Education|Bachelor of Science in Computer Science|Robotics' => 'bscs-robotics',
    ];

    /** These options were first saved while the three programs were listed under CAS. */
    private const LEGACY_COLLEGE_BY_PROGRAM = [
        'Bachelor of Science in Computer Science' => 'College of Arts and Sciences',
        'Bachelor of Science in Information Technology' => 'College of Arts and Sciences',
        'Bachelor of Science in Information Systems' => 'College of Arts and Sciences',
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
                        // Preserve IDs already stored on student accounts when CITE became
                        // the current college grouping for programs formerly listed under CAS.
                        $identityCollege = $campus['campus'] === 'Bayombong Campus'
                            ? (self::LEGACY_COLLEGE_BY_PROGRAM[$program['title']] ?? $college['name'])
                            : $college['name'];
                        $identity = implode('|', [$campus['campus'], $identityCollege, $title]);
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

    /** @return array{id: string, title: string, campus: string, college: string, curriculum_id: ?string, school: string}|null */
    public function forUser(int $userId): ?array
    {
        $user = db_connect()->table('users')->select('academic_program_id, academic_program_title, academic_school_name')
            ->where('id', $userId)->get()->getRowArray();
        if ($user === null || ! is_string($user['academic_program_id'])) {
            return null;
        }

        if ($user['academic_program_id'] === self::MANUAL_ENTRY_ID) {
            $title = trim((string) ($user['academic_program_title'] ?? ''));
            $school = trim((string) ($user['academic_school_name'] ?? ''));
            if ($title === '' || $school === '') {
                return null;
            }

            return [
                'id' => self::MANUAL_ENTRY_ID,
                'title' => $title,
                'campus' => 'Other school',
                'college' => $school,
                'curriculum_id' => null,
                'school' => $school,
            ];
        }

        $program = $this->find($user['academic_program_id']);
        if ($program === null) {
            return null;
        }

        $program['school'] = trim((string) ($user['academic_school_name'] ?? ''))
            ?: 'Nueva Vizcaya State University (NVSU), ' . $program['campus'];

        return $program;
    }
}
