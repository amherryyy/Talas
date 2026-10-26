<?php

namespace App\Services;

/**
 * Versioned, locally curated CHED curriculum references.
 *
 * Keep entries traceable to official CHED publications. Do not infer that a
 * sample course is mandatory unless the cited source says so.
 */
class ChedCurriculumCatalog
{
    /** @return list<array{id: string, title: string, source: string, source_label: string, cmo_source: string, cmo: string, publication_date: string, status: string, verified_on: string, note: string, terms: list<array{id: string, label: string, courses: list<array{id: string, title: string, status: string}>}>}> */
    public function all(): array
    {
        return [
            $this->baCommunication(),
            $this->bsAccountancy(),
            $this->bsBusinessAdministration(),
        ];
    }

    /** @return array{id: string, title: string, source: string, source_label: string, cmo_source: string, cmo: string, publication_date: string, status: string, verified_on: string, note: string, terms: list<array{id: string, label: string, courses: list<array{id: string, title: string, status: string}>}>}|null */
    public function find(string $id): ?array
    {
        foreach ($this->all() as $program) {
            if ($program['id'] === $id) {
                return $program;
            }
        }

        return null;
    }

    /** @return list<array{id: string, title: string, status: string}> */
    public function selectedCourses(string $programId, array $courseIds): array
    {
        $program = $this->find($programId);
        if ($program === null) {
            return [];
        }

        $requested = array_fill_keys(array_filter($courseIds, 'is_string'), true);
        $selected = [];

        foreach ($program['terms'] as $term) {
            foreach ($term['courses'] as $course) {
                if (isset($requested[$course['id']])) {
                    $selected[$course['id']] = $course;
                }
            }
        }

        return array_values($selected);
    }

    /** @return array{id: string, title: string, source: string, source_label: string, cmo_source: string, cmo: string, publication_date: string, status: string, verified_on: string, note: string, terms: list<array{id: string, label: string, courses: list<array{id: string, title: string, status: string}>}>} */
    private function baCommunication(): array
    {
        $term = static function (string $id, string $label, array $courses): array {
            return [
                'id'      => $id,
                'label'   => $label,
                'courses' => array_map(
                    static fn (array $course): array => [
                        'id'     => $course[0],
                        'title'  => $course[1],
                        'status' => $course[2],
                    ],
                    $courses,
                ),
            ];
        };

        return [
            'id'              => 'ba-communication',
            'title'           => 'Bachelor of Arts in Communication',
            'source'          => 'https://legacy.ched.gov.ph/wp-content/uploads/2017/10/Sample-Curricula-Bachelor-of-Arts-in-Communication1.pdf',
            'source_label'    => 'CHED sample or suggested curriculum (PDF)',
            'cmo_source'      => 'https://ched.gov.ph/wp-content/uploads/2017/10/CMO-35-s-2017.pdf',
            'cmo'             => 'CMO No. 35, series of 2017',
            'publication_date'=> '2017',
            'status'          => 'sample',
            'verified_on'     => '2026-10-26',
            'note'            => 'This is a CHED sample program of study. The source says HEIs may choose required courses and adapt elective clusters. Required-course slots and electives need to be matched to your school\'s curriculum.',
            'terms'           => [
                $term('y1-s1', 'Year 1 · Semester 1', [
                    ['y1s1-english', 'English (GE)', 'sample'],
                    ['y1s1-filipino', 'Filipino (GE)', 'sample'],
                    ['y1s1-math', 'Mathematics (GE)', 'sample'],
                    ['y1s1-science', 'Natural Sciences (GE)', 'sample'],
                    ['y1s1-history', 'Philippine History (GE)', 'sample'],
                    ['y1s1-pe', 'Physical Education (PE)', 'sample'],
                    ['y1s1-nstp', 'National Service Training Program (NSTP)', 'sample'],
                ]),
                $term('y1-s2', 'Year 1 · Semester 2', [
                    ['y1s2-english', 'English (GE)', 'sample'],
                    ['y1s2-filipino', 'Filipino (GE)', 'sample'],
                    ['y1s2-math', 'Mathematics (GE)', 'sample'],
                    ['y1s2-science', 'Natural Sciences (GE)', 'sample'],
                    ['y1s2-arts', 'Humanities - Arts (GE)', 'sample'],
                    ['y1s2-pe', 'Physical Education (PE)', 'sample'],
                ]),
                $term('y2-s1', 'Year 2 · Semester 1', [
                    ['y2s1-english', 'English', 'sample'],
                    ['y2s1-intro-communication', 'Introduction to Communication Media', 'core'],
                    ['y2s1-literature', 'Literature (GE)', 'sample'],
                    ['y2s1-science-elective', 'Science Elective (GE)', 'elective'],
                    ['y2s1-politics', 'Politics and Governance (GE)', 'sample'],
                    ['y2s1-pe', 'Physical Education (PE)', 'sample'],
                    ['y2s1-nstp', 'National Service Training Program (NSTP)', 'sample'],
                ]),
                $term('y2-s2', 'Year 2 · Semester 2', [
                    ['y2s2-literature', 'Literature (GE)', 'sample'],
                    ['y2s2-communication-theory', 'Communication Theory', 'core'],
                    ['y2s2-sociology', 'Sociology (GE)', 'sample'],
                    ['y2s2-philosophy', 'Humanities - Philosophy (GE)', 'sample'],
                    ['y2s2-communication-research', 'Communication Research', 'core'],
                    ['y2s2-required-slot-1', 'Required Course Slot 1', 'required-slot'],
                    ['y2s2-pe', 'Physical Education (PE)', 'sample'],
                ]),
                $term('y3-s1', 'Year 3 · Semester 1', [
                    ['y3s1-communication-culture', 'Communication, Culture, and Society', 'core'],
                    ['y3s1-required-slot-2', 'Required Course Slot 2', 'required-slot'],
                    ['y3s1-communication-elective-1', 'Communication Elective 1', 'elective'],
                    ['y3s1-filipino', 'Filipino (GE)', 'sample'],
                    ['y3s1-required-slot-3', 'Required Course Slot 3', 'required-slot'],
                    ['y3s1-social-elective-1', 'Social Science/Humanities Elective 1', 'elective'],
                ]),
                $term('y3-s2', 'Year 3 · Semester 2', [
                    ['y3s2-required-slot-4', 'Required Course Slot 4', 'required-slot'],
                    ['y3s2-required-slot-5', 'Required Course Slot 5', 'required-slot'],
                    ['y3s2-communication-elective-2', 'Communication Elective 2', 'elective'],
                    ['y3s2-psychology', 'General Psychology (GE)', 'sample'],
                    ['y3s2-economics', 'Basic Economics (GE)', 'sample'],
                    ['y3s2-social-elective-2', 'Social Science/Humanities Elective 2', 'elective'],
                ]),
                $term('y3-summer', 'Year 3 · Summer', [
                    ['y3summer-internship', 'Internship', 'sample'],
                ]),
                $term('y4-s1', 'Year 4 · Semester 1', [
                    ['y4s1-rizal', 'Life and Works of Rizal (GE)', 'sample'],
                    ['y4s1-communication-planning', 'Communication Planning', 'core'],
                    ['y4s1-required-slot-6', 'Required Course Slot 6', 'required-slot'],
                    ['y4s1-communication-elective-3', 'Communication Elective 3', 'elective'],
                    ['y4s1-social-elective-3', 'Social Science/Humanities Elective 3', 'elective'],
                    ['y4s1-social-elective-4', 'Social Science/Humanities Elective 4', 'elective'],
                ]),
                $term('y4-s2', 'Year 4 · Semester 2', [
                    ['y4s2-thesis', 'Thesis', 'sample'],
                    ['y4s2-communication-management', 'Communication Management', 'core'],
                    ['y4s2-communication-elective-4', 'Communication Elective 4', 'elective'],
                    ['y4s2-communication-laws', 'Communication Media Laws and Ethics', 'core'],
                    ['y4s2-social-elective-5', 'Social Science/Humanities Elective 5', 'elective'],
                ]),
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function bsAccountancy(): array
    {
        $term = static function (string $id, string $label, array $courses): array {
            return [
                'id' => $id,
                'label' => $label,
                'courses' => array_map(static fn (array $course): array => [
                    'id' => $course[0],
                    'title' => $course[1],
                    'status' => $course[2] ?? 'sample',
                ], $courses),
            ];
        };

        return [
            'id' => 'bs-accountancy',
            'title' => 'Bachelor of Science in Accountancy',
            'source' => 'https://legacy.ched.gov.ph/wp-content/uploads/2017/10/Sample-Curricula-Bachelor-of-Science-in-Accountancy.pdf',
            'source_label' => 'CHED sample or suggested curriculum (PDF)',
            'cmo_source' => 'https://ched.gov.ph/wp-content/uploads/2017/10/CMO-27-s-2017.pdf',
            'cmo' => 'CMO No. 27, series of 2017',
            'publication_date' => '2017',
            'status' => 'sample',
            'verified_on' => '2026-10-26',
            'note' => 'This is a CHED sample program of study. GE entries are generic placeholders; check your school curriculum for exact course titles and sequencing.',
            'terms' => [
                $term('bsa-y1-s1', 'Year 1 · Semester 1', [
                    ['bsa-y1s1-ge1', 'General Education Course Slot 1', 'required-slot'],
                    ['bsa-y1s1-ge2', 'General Education Course Slot 2', 'required-slot'],
                    ['bsa-y1s1-ge3', 'General Education Course Slot 3', 'required-slot'],
                    ['bsa-y1s1-ge4', 'General Education Course Slot 4', 'required-slot'],
                    ['bsa-y1s1-finance', 'Basic Finance'],
                    ['bsa-y1s1-accounting-1', 'Fundamentals of Accounting I'],
                    ['bsa-y1s1-pe', 'Physical Education'],
                ]),
                $term('bsa-y1-s2', 'Year 1 · Semester 2', [
                    ['bsa-y1s2-ge1', 'General Education Course Slot 1', 'required-slot'],
                    ['bsa-y1s2-ge2', 'General Education Course Slot 2', 'required-slot'],
                    ['bsa-y1s2-ge3', 'General Education Course Slot 3', 'required-slot'],
                    ['bsa-y1s2-management', 'Principles of Management'],
                    ['bsa-y1s2-database', 'Database Theory and Applications'],
                    ['bsa-y1s2-accounting-2', 'Fundamentals of Accounting II'],
                    ['bsa-y1s2-pe', 'Physical Education'],
                ]),
                $term('bsa-y1-summer', 'Year 1 · Summer', [
                    ['bsa-y1sum-ge1', 'General Education Course Slot 1', 'required-slot'],
                    ['bsa-y1sum-ge2', 'General Education Course Slot 2', 'required-slot'],
                    ['bsa-y1sum-nstp', 'National Service Training Program'],
                ]),
                $term('bsa-y2-s1', 'Year 2 · Semester 1', [
                    ['bsa-y2s1-ge1', 'General Education Course Slot 1', 'required-slot'],
                    ['bsa-y2s1-ge2', 'General Education Course Slot 2', 'required-slot'],
                    ['bsa-y2s1-ge3', 'General Education Course Slot 3', 'required-slot'],
                    ['bsa-y2s1-calculus', 'Calculus for Business'],
                    ['bsa-y2s1-governance', 'Good Governance and Social Responsibility'],
                    ['bsa-y2s1-cost-accounting-1', 'Cost Accounting and Management I'],
                    ['bsa-y2s1-financial-accounting-1', 'Financial Accounting and Reporting I'],
                    ['bsa-y2s1-pe', 'Physical Education'],
                ]),
                $term('bsa-y2-s2', 'Year 2 · Semester 2', [
                    ['bsa-y2s2-ge', 'General Education Course Slot 1', 'required-slot'],
                    ['bsa-y2s2-marketing', 'Principles of Marketing'],
                    ['bsa-y2s2-microeconomics', 'Microeconomic Theory and Practice'],
                    ['bsa-y2s2-statistics', 'Business Statistics with Computer Applications'],
                    ['bsa-y2s2-financial-accounting-2', 'Financial Accounting and Reporting II'],
                    ['bsa-y2s2-pe', 'Physical Education'],
                ]),
                $term('bsa-y2-summer', 'Year 2 · Summer', [
                    ['bsa-y2sum-ge1', 'General Education Course Slot 1', 'required-slot'],
                    ['bsa-y2sum-ge2', 'General Education Course Slot 2', 'required-slot'],
                    ['bsa-y2sum-nstp', 'National Service Training Program'],
                ]),
                $term('bsa-y3-s1', 'Year 3 · Semester 1', [
                    ['bsa-y3s1-research', 'Business Research and Technical Writing'],
                    ['bsa-y3s1-cost-accounting-2', 'Cost Accounting and Management II'],
                    ['bsa-y3s1-financial-accounting-3', 'Financial Accounting and Reporting III'],
                    ['bsa-y3s1-hbo', 'Human Behavior in Organizations'],
                    ['bsa-y3s1-macroeconomics', 'Macroeconomic Theory and Practice'],
                    ['bsa-y3s1-it-systems', 'IT Concepts and Systems Analysis and Design Development'],
                    ['bsa-y3s1-quantitative', 'Quantitative Techniques in Business'],
                    ['bsa-y3s1-obligations', 'Law on Obligations and Contracts'],
                ]),
                $term('bsa-y3-s2', 'Year 3 · Semester 2', [
                    ['bsa-y3s2-ge', 'General Education Course Slot 1', 'required-slot'],
                    ['bsa-y3s2-financial-management-1', 'Financial Management I'],
                    ['bsa-y3s2-operations', 'Productions and Operations Management'],
                    ['bsa-y3s2-management-accounting-1', 'Management Accounting I'],
                    ['bsa-y3s2-advanced-accounting-1', 'Advanced Financial Accounting and Reporting I'],
                    ['bsa-y3s2-business-organizations', 'Law on Business Organizations'],
                    ['bsa-y3s2-assurance', 'Assurance Principles, Professional Ethics and Good Governance'],
                ]),
                $term('bsa-y3-summer', 'Year 3 · Summer', [
                    ['bsa-y3sum-internship-thesis', 'Internship or Accounting Thesis'],
                ]),
                $term('bsa-y4-s1', 'Year 4 · Semester 1', [
                    ['bsa-y4s1-ge', 'General Education Course Slot 1', 'required-slot'],
                    ['bsa-y4s1-ais', 'Accounting Information Systems'],
                    ['bsa-y4s1-management-accounting-2', 'Management Accounting II'],
                    ['bsa-y4s1-applied-auditing', 'Applied Auditing'],
                    ['bsa-y4s1-income-taxation', 'Income Taxation'],
                    ['bsa-y4s1-advanced-accounting-2', 'Advanced Financial Accounting and Reporting II'],
                    ['bsa-y4s1-negotiable-instruments', 'Law on Negotiable Instruments'],
                    ['bsa-y4s1-humanities-elective', 'Humanities Elective', 'elective'],
                ]),
                $term('bsa-y4-s2', 'Year 4 · Semester 2', [
                    ['bsa-y4s2-management-accounting-3', 'Management Accounting III'],
                    ['bsa-y4s2-transfer-tax', 'Business and Transfer Tax'],
                    ['bsa-y4s2-strategic-management', 'Strategic Management'],
                    ['bsa-y4s2-auditing-cis', 'Auditing in a CIS Environment'],
                    ['bsa-y4s2-advanced-accounting-3', 'Advanced Financial Accounting and Reporting III'],
                    ['bsa-y4s2-sales-agency-labor', 'Sales, Agency, Labor and Other Relevant Laws'],
                    ['bsa-y4s2-financial-management-2', 'Financial Management II'],
                    ['bsa-y4s2-synthesis', 'Synthesis'],
                ]),
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function bsBusinessAdministration(): array
    {
        $term = static function (string $id, string $label, array $courses): array {
            return [
                'id' => $id,
                'label' => $label,
                'courses' => array_map(static fn (array $course): array => [
                    'id' => $course[0],
                    'title' => $course[1],
                    'status' => $course[2] ?? 'sample',
                ], $courses),
            ];
        };

        return [
            'id' => 'bs-business-administration',
            'title' => 'Bachelor of Science in Business Administration',
            'source' => 'https://legacy.ched.gov.ph/wp-content/uploads/2017/10/Sample-Curricula-Bachelor-of-Science-in-Business-Administrat.pdf',
            'source_label' => 'CHED sample or suggested curriculum (PDF)',
            'cmo_source' => 'https://ched.gov.ph/wp-content/uploads/2017/10/CMO-17-s-2017.pdf',
            'cmo' => 'CMO No. 17, series of 2017',
            'publication_date' => '2017',
            'status' => 'sample',
            'verified_on' => '2026-10-26',
            'note' => 'This is a CHED sample program of study. Professional subject and elective entries are slots; your school selects courses for its major or concentration.',
            'terms' => [
                $term('bsba-y1-s1', 'Year 1 · Semester 1', [
                    ['bsba-y1s1-english-1', 'Communication Arts - English 1'],
                    ['bsba-y1s1-filipino-1', 'Communication Arts - Filipino 1'],
                    ['bsba-y1s1-math-1', 'Mathematics I'],
                    ['bsba-y1s1-science-1', 'Natural Science I'],
                    ['bsba-y1s1-psychology', 'Social Science I (Psychology)'],
                    ['bsba-y1s1-management-1', 'Management 1'],
                    ['bsba-y1s1-pe-1', 'Physical Education 1'],
                    ['bsba-y1s1-nstp', 'National Service Training Program'],
                ]),
                $term('bsba-y1-s2', 'Year 1 · Semester 2', [
                    ['bsba-y1s2-english-2', 'Communication Arts - English 2'],
                    ['bsba-y1s2-filipino-2', 'Communication Arts - Filipino 2'],
                    ['bsba-y1s2-math-2', 'Mathematics II (Statistics)'],
                    ['bsba-y1s2-science-2', 'Natural Science II'],
                    ['bsba-y1s2-marketing-1', 'Marketing I'],
                    ['bsba-y1s2-computer-1', 'Computer I'],
                    ['bsba-y1s2-pe-2', 'Physical Education 2'],
                    ['bsba-y1s2-nstp', 'National Service Training Program'],
                ]),
                $term('bsba-y2-s1', 'Year 2 · Semester 1', [
                    ['bsba-y2s1-accounting-1', 'Accounting I'],
                    ['bsba-y2s1-social-2', 'Social Science II'],
                    ['bsba-y2s1-computer-2', 'Computer II'],
                    ['bsba-y2s1-english-3', 'English III (Business Communication)'],
                    ['bsba-y2s1-humanities-1', 'Humanities I'],
                    ['bsba-y2s1-finance-1', 'Finance I'],
                    ['bsba-y2s1-pe-3', 'Physical Education 3'],
                ]),
                $term('bsba-y2-s2', 'Year 2 · Semester 2', [
                    ['bsba-y2s2-accounting-2', 'Accounting II'],
                    ['bsba-y2s2-microeconomics', 'Micro Economics'],
                    ['bsba-y2s2-social-3', 'Social Science III'],
                    ['bsba-y2s2-hbo', 'Human Behavior in Organizations'],
                    ['bsba-y2s2-humanities-2', 'Humanities II'],
                    ['bsba-y2s2-governance', 'Good Governance and Social Responsibility'],
                    ['bsba-y2s2-pe-4', 'Physical Education 4'],
                ]),
                $term('bsba-y3-s1', 'Year 3 · Semester 1', [
                    ['bsba-y3s1-humanities-3', 'Humanities III'],
                    ['bsba-y3s1-social-4', 'Social Science IV'],
                    ['bsba-y3s1-taxation', 'Business Taxation'],
                    ['bsba-y3s1-professional-1', 'Professional Subject 1', 'required-slot'],
                    ['bsba-y3s1-professional-2', 'Professional Subject 2', 'required-slot'],
                    ['bsba-y3s1-hrm', 'Human Resource Management'],
                ]),
                $term('bsba-y3-s2', 'Year 3 · Semester 2', [
                    ['bsba-y3s2-business-law', 'Business Law I'],
                    ['bsba-y3s2-rizal', 'Life and Works of Rizal'],
                    ['bsba-y3s2-quality', 'Total Quality Management'],
                    ['bsba-y3s2-professional-3', 'Professional Subject 3', 'required-slot'],
                    ['bsba-y3s2-professional-4', 'Professional Subject 4', 'required-slot'],
                    ['bsba-y3s2-elective-1', 'Elective 1', 'elective'],
                ]),
                $term('bsba-y4-s1', 'Year 4 · Semester 1', [
                    ['bsba-y4s1-professional-5', 'Professional Subject 5', 'required-slot'],
                    ['bsba-y4s1-professional-6', 'Professional Subject 6', 'required-slot'],
                    ['bsba-y4s1-professional-7', 'Professional Subject 7', 'required-slot'],
                    ['bsba-y4s1-elective-2', 'Elective 2', 'elective'],
                    ['bsba-y4s1-elective-3', 'Elective 3', 'elective'],
                ]),
                $term('bsba-y4-s2', 'Year 4 · Semester 2', [
                    ['bsba-y4s2-professional-8', 'Professional Subject 8', 'required-slot'],
                    ['bsba-y4s2-elective-4', 'Elective 4', 'elective'],
                    ['bsba-y4s2-elective-5', 'Elective 5', 'elective'],
                    ['bsba-y4s2-practicum', 'Practicum/Work Integrated Learning'],
                ]),
            ],
        ];
    }
}
