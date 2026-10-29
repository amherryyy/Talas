<?php

namespace App\Services;

/**
 * NVSU course titles transcribed from university curriculum PDFs and its
 * published course-description and curriculum-map document. These are dated
 * reference plans, not a confirmation of the currently approved curriculum.
 */
class NvsuCurriculumCatalog
{
    public const SOURCE = 'https://sdg.nvsu.edu.ph/storage/files/OaEbS4Z7GeKxTX5c9o1kvzmC0rplTKzWi9fj2W5b.pdf';
    private const BSIS_SERVICE_SOURCE = 'https://drive.google.com/file/d/1vSODcMZjce_WQYQGeC1VxpOYxbgUw0Jb/view';
    private const BSIS_ANALYTICS_SOURCE = 'https://drive.google.com/file/d/16HEb258jDQRyIA9KoShbUDW-wNaau0jT/view';
    private const BSIT_WEB_SOURCE = 'https://drive.google.com/file/d/1O0npu1qhAoUucQGYoYdvuusqQQEGsynD/view';
    private const BSIT_NETWORK_SOURCE = 'https://drive.google.com/file/d/1ojtNnsOeKt3XWMMB0wO47IIZHt_OMvxC/view';
    private const BSCS_SOURCE = 'https://drive.google.com/file/d/1BdfT7tFkGUIbMLlDqngdmuqtoISX5Vmu/view';

    /** @return list<array{id: string, title: string, campus: string, source: string, source_label: string, publication_date: string, verified_on: string, note: string, courses: list<array{id: string, title: string}>}> */
    public function all(): array
    {
        return [
            $this->program('bs-forestry', 'Bachelor of Science in Forestry', $this->forestryCourses()),
            $this->program('bs-agroforestry', 'Bachelor of Science in Agroforestry', $this->agroforestryCourses()),
            $this->program('bs-ecotourism', 'Bachelor of Science in Ecotourism', $this->ecotourismCourses()),
            $this->program('bsis-service-management', 'BS Information Systems — Service Management', $this->bsisServiceManagementCourses(), self::BSIS_SERVICE_SOURCE, 'Effective S.Y. 2019–2020', 'NVSU BSIS Service Management curriculum (PDF)', 'Transcribed from the NVSU Bayombong curriculum. The source cites CMO No. 25, s. 2015. Course titles and codes are a historical reference; confirm the currently approved plan with NVSU.'),
            $this->program('bsis-business-analytics', 'BS Information Systems — Business Analytics', $this->bsisBusinessAnalyticsCourses(), self::BSIS_ANALYTICS_SOURCE, 'Effective S.Y. 2019–2020', 'NVSU BSIS Business Analytics curriculum (PDF)', 'Transcribed from the NVSU Bayombong curriculum. The source cites CMO No. 25, s. 2015. Course titles and codes are a historical reference; confirm the currently approved plan with NVSU.'),
            $this->program('bsit-web-mobile', 'BS Information Technology — Web and Mobile App Development', $this->bsitWebAppCourses(), self::BSIT_WEB_SOURCE, 'Effective S.Y. 2019–2020', 'NVSU BSIT Web and Mobile App Development curriculum (PDF)', 'Transcribed from the NVSU Bayombong curriculum. The source cites CMO No. 25, s. 2015 and CMO No. 20, s. 2013 for general education. Course titles and codes are a historical reference; confirm the currently approved plan with NVSU.'),
            $this->program('bsit-network-design', 'BS Information Technology — Network Design and Management', $this->bsitNetworkCourses(), self::BSIT_NETWORK_SOURCE, 'Effective S.Y. 2019–2020', 'NVSU BSIT Network Design and Management curriculum (PDF)', 'Transcribed from the NVSU Bayombong curriculum. The source cites CMO No. 25, s. 2015 and CMO No. 20, s. 2013 for general education. Course titles and codes are a historical reference; confirm the currently approved plan with NVSU.'),
            $this->program('bscs-game-development', 'BS Computer Science — Game Development', $this->bscsGameDevelopmentCourses(), self::BSCS_SOURCE, 'Effective S.Y. 2019–2020', 'NVSU BSCS Game Development curriculum (PDF)', 'Transcribed from the NVSU Bayombong curriculum. The source cites CMO No. 25, s. 2015 and CMO No. 20, s. 2013 for general education. Course titles and codes are a historical reference; confirm the currently approved plan with NVSU.'),
            $this->program('bscs-robotics', 'BS Computer Science — Robotics', $this->bscsRoboticsCourses(), self::BSCS_SOURCE, 'Effective S.Y. 2019–2020', 'NVSU BSCS Robotics curriculum (PDF)', 'Transcribed from the NVSU Bayombong curriculum. The source cites CMO No. 25, s. 2015 and CMO No. 20, s. 2013 for general education. Course titles and codes are a historical reference; confirm the currently approved plan with NVSU.'),
        ];
    }

    /** @return array{id: string, title: string, campus: string, source: string, source_label: string, publication_date: string, verified_on: string, note: string, courses: list<array{id: string, title: string}>}|null */
    public function find(string $id): ?array
    {
        foreach ($this->all() as $program) {
            if ($program['id'] === $id) {
                return $program;
            }
        }

        return null;
    }

    /** @return list<array{id: string, title: string}> */
    public function selectedCourses(string $programId, array $courseIds): array
    {
        $program = $this->find($programId);
        if ($program === null) {
            return [];
        }

        $requested = array_fill_keys(array_filter($courseIds, 'is_string'), true);
        $selected = [];
        foreach ($program['courses'] as $course) {
            if (isset($requested[$course['id']])) {
                $selected[] = $course;
            }
        }

        return $selected;
    }

    /** @param list<array{code: string, title: string}> $courses
     *  @return array{id: string, title: string, campus: string, source: string, source_label: string, publication_date: string, verified_on: string, note: string, courses: list<array{id: string, title: string}>}
     */
    private function program(
        string $id,
        string $title,
        array $courses,
        string $source = self::SOURCE,
        string $publicationDate = 'Undated source document',
        string $sourceLabel = 'NVSU course descriptions and curriculum map (PDF)',
        ?string $note = null,
    ): array
    {
        return [
            'id' => $id,
            'title' => $title,
            'campus' => 'Bayombong Campus',
            'source' => $source,
            'source_label' => $sourceLabel,
            'publication_date' => $publicationDate,
            'verified_on' => '2026-10-27',
            'note' => $note ?? 'These titles are transcribed from the NVSU document. It does not identify a curriculum year or establish the current school-approved course sequence. Course codes are included for reference; confirm the current curriculum with NVSU before relying on it.'
                . ($id === 'bs-ecotourism' ? ' The source map labels Protected Area Management as ECOTOUR 35, while the course-description section labels it ECOTOUR 36.' : ''),
            'courses' => array_map(static fn (array $course): array => [
                'id' => strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $id . '-' . $course['code'] . '-' . $course['title']) ?? $course['code']),
                'title' => $course['code'] . ' — ' . $course['title'],
            ], $courses),
        ];
    }

    /** @return list<array{code: string, title: string}> */
    private function bsitWebAppCourses(): array
    {
        return [
            ['code' => 'GE ENVI', 'title' => 'Environmental Science'], ['code' => 'GE MATH', 'title' => 'Mathematics in the Modern World'],
            ['code' => 'GE SELF', 'title' => 'Understanding the Self'], ['code' => 'GE ARTS', 'title' => 'Art Appreciation'],
            ['code' => 'IT ECC 1', 'title' => 'Introduction to Computing'], ['code' => 'IT ECC 2', 'title' => 'Computer Programming 1'],
            ['code' => 'PATHF 1', 'title' => 'Physical Activities Towards Health and Fitness 1'], ['code' => 'NSTP 1', 'title' => 'CWTS 1 / ROTC 1 / LTS 1'],
            ['code' => 'IT ECC 3', 'title' => 'Computer Programming 2'], ['code' => 'IT ECC 4', 'title' => 'Data Structures and Algorithms'],
            ['code' => 'IT PC 1', 'title' => 'Discrete Mathematics'], ['code' => 'GE PIC', 'title' => 'Philippine Indigenous Communities'],
            ['code' => 'GE STS', 'title' => 'Science, Technology and Society'], ['code' => 'GE WORLD', 'title' => 'The Contemporary World'],
            ['code' => 'PATHF 2', 'title' => 'Physical Activities Towards Health and Fitness 2'], ['code' => 'NSTP 2', 'title' => 'CWTS 2 / ROTC 2 / LTS 2'],
            ['code' => 'IT T1-1', 'title' => 'Webpage Design using HTML 5'], ['code' => 'IT ECC 5', 'title' => 'Information Management'],
            ['code' => 'IT PEC 1', 'title' => 'IT Elective 1'], ['code' => 'IT PEC 2', 'title' => 'IT Elective 2'],
            ['code' => 'GE ETHICS', 'title' => 'Ethics'], ['code' => 'GE HIST', 'title' => 'Readings in Philippine History'],
            ['code' => 'GE ENTREP', 'title' => 'The Entrepreneurial Mind'], ['code' => 'PATHF 3', 'title' => 'Physical Activities Towards Health and Fitness 3'],
            ['code' => 'IT T1-2', 'title' => 'Responsive Web App Design and Development using JavaScript, CSS and Bootstrap'],
            ['code' => 'IT PC 2', 'title' => 'Integrative Programming and Technologies 1'], ['code' => 'IT PC 3', 'title' => 'Systems Integration and Architecture 1'],
            ['code' => 'STAT 1', 'title' => 'Statistics'], ['code' => 'GE COMM', 'title' => 'Purposive Communication'],
            ['code' => 'GE MS RIZAL', 'title' => 'Life and Works of Rizal'], ['code' => 'PATHF 4', 'title' => 'Physical Activities Towards Health and Fitness 4'],
            ['code' => 'IT T1-3', 'title' => 'Dynamic Web App Development'], ['code' => 'IT T1-4', 'title' => 'Basic Mobile App Programming'],
            ['code' => 'IT PEC 3', 'title' => 'IT Elective 3'], ['code' => 'IT PEC 4', 'title' => 'IT Elective 4'],
            ['code' => 'IT PC 4', 'title' => 'Software Engineering'], ['code' => 'IT PC 5', 'title' => 'Quantitative Methods (Including Modeling and Simulation)'],
            ['code' => 'IT PC 6', 'title' => 'Networking 1'], ['code' => 'IT T1-5', 'title' => 'Advanced Mobile App Programming'],
            ['code' => 'IT ECC 6', 'title' => 'Application Development and Emerging Technologies'], ['code' => 'IT PC 7', 'title' => 'Networking 2'],
            ['code' => 'IT PC 8', 'title' => 'Human Computer Interaction'], ['code' => 'IT PC 9', 'title' => 'Information Assurance and Security 1'],
            ['code' => 'IT PC 100A', 'title' => 'Capstone Project Research 1'], ['code' => 'IT T1-6', 'title' => 'Mobile Web Development'],
            ['code' => 'IT PC 10', 'title' => 'Information Assurance and Security 2'], ['code' => 'IT PC 11', 'title' => 'Systems Administration and Maintenance'],
            ['code' => 'IT PC 12', 'title' => 'Social Issues and Professional Practice'], ['code' => 'IT PC 100B', 'title' => 'Capstone Project and Research 2'],
            ['code' => 'IT PC 99', 'title' => 'Practicum (486 Hours)'], ['code' => 'IT Elective', 'title' => 'Object Oriented Programming'],
            ['code' => 'IT Elective', 'title' => 'Platform Technologies'], ['code' => 'IT Elective', 'title' => 'Integrative Programming and Technologies 2'],
            ['code' => 'IT Elective', 'title' => 'Systems Integration and Architecture 2'],
        ];
    }

    /** @return list<array{code: string, title: string}> */
    private function bsitNetworkCourses(): array
    {
        $courses = $this->bsitWebAppCourses();
        $majorCodes = ['IT T1-1', 'IT T1-2', 'IT T1-3', 'IT T1-4', 'IT T1-5', 'IT T1-6'];
        $courses = array_values(array_filter($courses, static fn (array $course): bool => !in_array($course['code'], $majorCodes, true)));
        $courses[] = ['code' => 'IT T2-1', 'title' => 'Ethical Hacking'];
        $courses[] = ['code' => 'IT T2-2', 'title' => 'Network Programming'];
        $courses[] = ['code' => 'IT T2-3', 'title' => 'Network Design and Management'];
        $courses[] = ['code' => 'IT T2-4', 'title' => 'Advanced Internet Services'];
        $courses[] = ['code' => 'IT T2-5', 'title' => 'Wireless and WAN Technologies'];
        $courses[] = ['code' => 'IT T2-6', 'title' => 'Network Security and Services'];

        return $courses;
    }

    /** @return list<array{code: string, title: string}> */
    private function bsisBusinessAnalyticsCourses(): array
    {
        return [
            ['code' => 'IT ECC 1', 'title' => 'Introduction to Computing'], ['code' => 'IT ECC 2', 'title' => 'Computer Programming 1'],
            ['code' => 'GE ENVI', 'title' => 'Environmental Science'], ['code' => 'GE MATH', 'title' => 'Mathematics in the Modern World'],
            ['code' => 'GE SELF', 'title' => 'Understanding the Self'], ['code' => 'GE ARTS', 'title' => 'Art Appreciation'],
            ['code' => 'PATHF 1', 'title' => 'Physical Activities Towards Health and Fitness 1'], ['code' => 'NSTP 1', 'title' => 'ROTC 1 / CWTS 1 / LTS 1'],
            ['code' => 'IT ECC 3', 'title' => 'Computer Programming 2'], ['code' => 'IT ECC 4', 'title' => 'Data Structures and Algorithms'],
            ['code' => 'IS PC 1', 'title' => 'Fundamentals of Information Systems'], ['code' => 'GE INDI', 'title' => 'Philippine Indigenous Community'],
            ['code' => 'GE STS', 'title' => 'Science, Technology, and Society'], ['code' => 'GE WORLD', 'title' => 'The Contemporary World'],
            ['code' => 'PATHF 2', 'title' => 'Physical Activities Towards Health and Fitness 2'], ['code' => 'NSTP 2', 'title' => 'ROTC 2 / CWTS 2 / LTS 2'],
            ['code' => 'IT ECC 5', 'title' => 'Information Management'], ['code' => 'IS PC 2', 'title' => 'Professional Issues in Information Systems'],
            ['code' => 'IS PC 3', 'title' => 'IT Infrastructure and Network Technologies'], ['code' => 'GE ETHICS', 'title' => 'Ethics'],
            ['code' => 'GE HIST', 'title' => 'Readings in Philippine History'], ['code' => 'PATHF 3', 'title' => 'Physical Activities Towards Health and Fitness 3'],
            ['code' => 'GE ENTREP', 'title' => 'The Entrepreneurial Mind'], ['code' => 'IST1-1', 'title' => 'Fundamentals of Business Analytics'],
            ['code' => 'IST1-2', 'title' => 'Enterprise Data Management'], ['code' => 'IS PC 4', 'title' => 'Systems Analysis and Design'],
            ['code' => 'IS PC 5', 'title' => 'Enterprise Architecture'], ['code' => 'GE COMM', 'title' => 'Purposive Communication'],
            ['code' => 'GE MSRIZAL', 'title' => 'Life and Works of Rizal'], ['code' => 'PATHF 4', 'title' => 'Physical Activities Towards Health and Fitness 4'],
            ['code' => 'IS PEC 1', 'title' => 'IS Elective 1 — IT Audit and Controls'], ['code' => 'IST1-3', 'title' => 'Analytic Modelling, Techniques, and Tools'],
            ['code' => 'IST1-4', 'title' => 'Enterprise Resource Planning'], ['code' => 'IS PC 6', 'title' => 'IS Project Management'],
            ['code' => 'IS PC 7', 'title' => 'Organization and Management Concepts'], ['code' => 'STAT 1', 'title' => 'Statistics'],
            ['code' => 'IS PEC 2', 'title' => 'IS Elective 2 — IT Security and Management'], ['code' => 'IS PC 100A', 'title' => 'Capstone Project 1'],
            ['code' => 'IS PC 8', 'title' => 'Quantitative Methods'], ['code' => 'IS PC 9', 'title' => 'IS Strategy, Management, and Acquisition'],
            ['code' => 'IS PC 10', 'title' => 'Financial Management'], ['code' => 'IS PEC 3', 'title' => 'IS Elective 3 — Business Process Management'],
            ['code' => 'IST1-5', 'title' => 'Data Warehousing'], ['code' => 'IS PC 100B', 'title' => 'Capstone Project 2'],
            ['code' => 'IT ECC 6', 'title' => 'Application Development and Emerging Technologies'], ['code' => 'IS PC 11', 'title' => 'Business Intelligence'],
            ['code' => 'IST1-6', 'title' => 'Data Mining'], ['code' => 'IS PC 12', 'title' => 'Evaluation of Business Performance'],
            ['code' => 'IS PEC 4', 'title' => 'IS Elective 4 — Customer Relationship Management'], ['code' => 'IS PC 99', 'title' => 'Practicum (486 Hours)'],
        ];
    }

    /** @return list<array{code: string, title: string}> */
    private function bsisServiceManagementCourses(): array
    {
        $courses = $this->bsisBusinessAnalyticsCourses();
        $analyticsCodes = ['IST1-1', 'IST1-2', 'IST1-3', 'IST1-4', 'IST1-5', 'IST1-6'];
        $courses = array_values(array_filter($courses, static fn (array $course): bool => !in_array($course['code'], $analyticsCodes, true)));
        $courses[] = ['code' => 'IST2-1', 'title' => 'IT Service Management'];
        $courses[] = ['code' => 'IST2-2', 'title' => 'Business Communication'];
        $courses[] = ['code' => 'IST2-3', 'title' => 'Service Culture'];
        $courses[] = ['code' => 'IST2-4', 'title' => 'Business Process Outsourcing 1'];
        $courses[] = ['code' => 'IST2-5', 'title' => 'Business Process Outsourcing 2'];
        $courses[] = ['code' => 'IST2-6', 'title' => 'Principles of Systems Thinking'];

        return $courses;
    }

    /** @return list<array{code: string, title: string}> */
    private function bscsCommonCourses(): array
    {
        return [
            ['code' => 'GE ARTS', 'title' => 'Art Appreciation'], ['code' => 'GE SELF', 'title' => 'Understanding the Self'],
            ['code' => 'GE ENVI', 'title' => 'Environmental Science'], ['code' => 'GE MATH', 'title' => 'Mathematics in the Modern World'],
            ['code' => 'IT ECC 1', 'title' => 'Introduction to Computing'], ['code' => 'IT ECC 2', 'title' => 'Fundamentals of Programming'],
            ['code' => 'PATHF 1', 'title' => 'Physical Activities Towards Health and Fitness I'], ['code' => 'NSTP 1', 'title' => 'CWTS 1 / ROTC 1 / LTS 1'],
            ['code' => 'GE INDI', 'title' => 'Philippine Indigenous Communities'], ['code' => 'GE COMM', 'title' => 'Purposive Communication'],
            ['code' => 'GE STS', 'title' => 'Science, Technology, and Society'], ['code' => 'GE WORLD', 'title' => 'The Contemporary World'],
            ['code' => 'CSPC 1', 'title' => 'Discrete Structures 1'], ['code' => 'IT ECC 3', 'title' => 'Intermediate Programming'],
            ['code' => 'PATHF 2', 'title' => 'Physical Activities Towards Health and Fitness II'], ['code' => 'NSTP 2', 'title' => 'CWTS 2 / ROTC 2 / LTS 2'],
            ['code' => 'GE ENTREP', 'title' => 'The Entrepreneurial Mind'], ['code' => 'GE HIST', 'title' => 'Readings in Philippine History'],
            ['code' => 'CSPC 2', 'title' => 'Discrete Structures 2'], ['code' => 'CSPC 3', 'title' => 'Object-Oriented Programming'],
            ['code' => 'IT ECC 4', 'title' => 'Data Structures and Algorithm'], ['code' => 'PATHF 3', 'title' => 'Physical Activities Towards Health and Fitness III'],
            ['code' => 'GE MS RIZAL', 'title' => 'Life and Works of Rizal'], ['code' => 'CSPC 4', 'title' => 'Algorithms and Complexities'],
            ['code' => 'IT ECC 5', 'title' => 'Information Management'], ['code' => 'CSPC 5', 'title' => 'Architecture and Organization'],
            ['code' => 'CSMATH', 'title' => 'Analytic Geometry and Calculus'], ['code' => 'PATHF 4', 'title' => 'Physical Activities Towards Health and Fitness IV'],
            ['code' => 'GE ETHICS', 'title' => 'Ethics'], ['code' => 'CSPC 6', 'title' => 'Automata Theory and Formal Languages'],
            ['code' => 'CSPC 7', 'title' => 'Information Assurance and Security'], ['code' => 'IT ECC 6', 'title' => 'Application Development and Emerging Technologies'],
            ['code' => 'CSPEC 1', 'title' => 'System Fundamentals'], ['code' => 'CSPC 8', 'title' => 'Programming Languages'],
            ['code' => 'CSPC 9', 'title' => 'Software Engineering 1'], ['code' => 'CSPC 10', 'title' => 'Social Issues and Professional Practice 1'],
            ['code' => 'CSPC 11', 'title' => 'Operating Systems'], ['code' => 'CSPEC 2', 'title' => 'Intelligent Systems'],
            ['code' => 'CSPC 99', 'title' => 'Practicum / OJT (162 Hours)'], ['code' => 'CSPC 12', 'title' => 'Human Computer Interaction'],
            ['code' => 'CSPC 13', 'title' => 'Networks and Communications'], ['code' => 'CSPC 14', 'title' => 'Software Engineering 2'],
            ['code' => 'CSPEC 3', 'title' => 'Graphics and Visual Computing'], ['code' => 'CSPC 100A', 'title' => 'CS Thesis 1'],
            ['code' => 'CSPC 100B', 'title' => 'CS Thesis 2'],
        ];
    }

    /** @return list<array{code: string, title: string}> */
    private function bscsGameDevelopmentCourses(): array
    {
        return array_merge($this->bscsCommonCourses(), [
            ['code' => 'CST1-1', 'title' => 'Game Theory and Design'], ['code' => 'CST1-2', 'title' => 'Computer Graphics'],
            ['code' => 'CST1-3', 'title' => '2D and 3D Modelling'], ['code' => 'CST1-4', 'title' => 'Game Development 1'],
            ['code' => 'CST1-5', 'title' => 'Game Development 2'], ['code' => 'CST1-6', 'title' => 'Game Development 3'],
        ]);
    }

    /** @return list<array{code: string, title: string}> */
    private function bscsRoboticsCourses(): array
    {
        $courses = array_values(array_filter($this->bscsCommonCourses(), static fn (array $course): bool => $course['code'] !== 'CSPC 99'));

        return array_merge($courses, [
            ['code' => 'CST2-1', 'title' => 'Basic Electronic Circuits'], ['code' => 'CST2-2', 'title' => 'Fundamentals of Robotics'],
            ['code' => 'CST2-3', 'title' => 'Microcontroller / Microprocessor Technology'], ['code' => 'CST2-4', 'title' => 'Artificial Intelligence'],
            ['code' => 'CST2-5', 'title' => 'Robotics 1'], ['code' => 'CST2-6', 'title' => 'Robotics 2'],
            ['code' => 'CSPFC 12', 'title' => 'Practicum / OJT (162 Hours)'],
        ]);
    }

    /** @return list<array{code: string, title: string}> */
    private function forestryCourses(): array
    {
        return [
            ['code' => 'FBS 11', 'title' => 'Morphology, Taxonomy and Dendrology'],
            ['code' => 'FBS 12', 'title' => 'Forest Ecology'],
            ['code' => 'FBS 13', 'title' => 'Tree Physiology'],
            ['code' => 'FBS 14', 'title' => 'Forest Entomology'],
            ['code' => 'FBS 15', 'title' => 'Forest Pathology'],
            ['code' => 'FBS 16', 'title' => 'Forest Biodiversity Conservation'],
            ['code' => 'FBS 17', 'title' => 'Forest Genetics and Tree Improvement'],
            ['code' => 'FBS 18', 'title' => 'Plant Anatomy'],
            ['code' => 'FBS 19', 'title' => 'Freshwater Ecosystem Analysis'],
            ['code' => 'FBS 20', 'title' => 'Clonal Propagation Techniques'],
            ['code' => 'FRM 10', 'title' => 'Introduction to Forestry'],
            ['code' => 'FRM 11', 'title' => 'Geology and Forest Soils'],
            ['code' => 'FRM 12', 'title' => 'Forest Surveying and Engineering'],
            ['code' => 'FRM 13', 'title' => 'Silviculture 1 - Forest Nurseries and Plantation'],
            ['code' => 'FRM 14', 'title' => 'Forest Ecosystems and Resources, Inventory and Assessment'],
            ['code' => 'FRM 15', 'title' => 'Silviculture 2 - Silvicultural Systems'],
            ['code' => 'FRM 16', 'title' => 'Forest Resource Economics and Valuation'],
            ['code' => 'FRM 17', 'title' => 'Technical Writing in Forestry'],
            ['code' => 'FRM 18', 'title' => 'Integrated Forest Resource Management'],
            ['code' => 'FRM 19', 'title' => 'Watershed Management'],
            ['code' => 'FRM 20', 'title' => 'Protected Area Management'],
            ['code' => 'FRM 21', 'title' => 'Statistics and Experimental Design in Forestry'],
            ['code' => 'FRM 22', 'title' => 'Geospatial Methods in Forestry'],
            ['code' => 'FRM 23', 'title' => 'Forest Enterprise Development and Management'],
            ['code' => 'FRM 24', 'title' => 'Problem Analysis and Research Method'],
            ['code' => 'FRM 25', 'title' => 'Range Management'],
            ['code' => 'FRM 26', 'title' => 'Urban Forestry and Landscaping'],
            ['code' => 'FRM 27', 'title' => 'Ecotourism and Outdoor Recreation Management'],
            ['code' => 'FRM 28', 'title' => 'Restoration of Degraded Ecosystems'],
            ['code' => 'FRM 29', 'title' => 'Forest Land Use Planning'],
            ['code' => 'FRM 30', 'title' => 'Introduction to Remote Sensing'],
            ['code' => 'FRM 31', 'title' => 'Carbon Stock and Carbon Sequestration Assessment'],
            ['code' => 'FRM 32', 'title' => 'Raster Geographic Information System'],
            ['code' => 'FRM 33', 'title' => 'Landscape Assessment Using Drone'],
            ['code' => 'FRM 34', 'title' => 'Entrepreneurial Opportunities in Forestry'],
            ['code' => 'FRM 35', 'title' => 'Forest Tree Seeds'],
            ['code' => 'FRM 36', 'title' => 'Information Technology for Forestry'],
            ['code' => 'SFFG 11', 'title' => 'Forest Governance and Policy'],
            ['code' => 'SFFG 18', 'title' => 'Forest Protection'],
        ];
    }

    /** @return list<array{code: string, title: string}> */
    private function agroforestryCourses(): array
    {
        return [
            ['code' => 'AN SC 2', 'title' => 'Introduction to Livestock and Poultry Production'],
            ['code' => 'AN SCI 1', 'title' => 'Introduction to Animal Science'],
            ['code' => 'CROP SC 1', 'title' => 'Principles of Crop Protection'],
            ['code' => 'CROP SC 2', 'title' => 'Practices of Crop Production'],
            ['code' => 'SOIL SC 1', 'title' => 'Principles of Soil Science'],
            ['code' => 'AF 20', 'title' => 'Morphology, Taxonomy and Dendrology'],
            ['code' => 'AF 21', 'title' => 'Introduction to Agroforestry'],
            ['code' => 'AF 22', 'title' => 'Forest Ecology'],
            ['code' => 'AF 23', 'title' => 'Plant Physiology'],
            ['code' => 'AF 24', 'title' => 'Wood Structure and Identification'],
            ['code' => 'AF 25', 'title' => 'Forest Surveying and Engineering'],
            ['code' => 'AF 26', 'title' => 'Introduction to Environmental Management'],
            ['code' => 'AF 27', 'title' => 'Social Forestry and Community Based Natural Resources Management'],
            ['code' => 'AF 28', 'title' => 'Forest Health'],
            ['code' => 'AF 29', 'title' => 'Nursery and Plantation'],
            ['code' => 'AF 30', 'title' => 'Agroforestry Extension and Communications'],
            ['code' => 'AF 31', 'title' => 'Resources Inventory and Assessment'],
            ['code' => 'AF 32', 'title' => 'Biodiversity Conservation in Agroforestry Landscape'],
            ['code' => 'AF 33', 'title' => 'Principles of Crop Protection and Management'],
            ['code' => 'AF 34', 'title' => 'Silvicultural Systems'],
            ['code' => 'AF 35', 'title' => 'Utilization of Wood and Non-Wood Forest Products'],
            ['code' => 'AF 36', 'title' => 'Resource Economics and Valuation'],
            ['code' => 'AF 37', 'title' => 'Domestication and Tree Improvement'],
            ['code' => 'AF 38', 'title' => 'Agroforestry Laws and Regulations'],
            ['code' => 'AF 39', 'title' => 'Applied Sociology'],
            ['code' => 'AF 40', 'title' => 'Production Management in Agroforestry-Based Industries'],
            ['code' => 'AF 41', 'title' => 'Integrated Resource Management'],
            ['code' => 'AF 42', 'title' => 'Statistics and Experimental Design in Agroforestry'],
            ['code' => 'AF 43', 'title' => 'Geospatial Methods in Forestry'],
            ['code' => 'AF 44', 'title' => 'Tropical Forestry and Climate Change'],
            ['code' => 'AF 45', 'title' => 'Technical Writing in Agroforestry'],
            ['code' => 'AF 46', 'title' => 'Diagnosis, Design and Appraisal of Agroforestry System'],
            ['code' => 'AF 47', 'title' => 'Problem Analysis and Research Method'],
            ['code' => 'AF 48', 'title' => 'Industrial Agroforestry Project Planning and Management'],
            ['code' => 'AF 49', 'title' => 'Watershed Management'],
            ['code' => 'AF 51', 'title' => 'Livelihood 1 - Mushroom, Vermicomposting, Urban Gardening'],
            ['code' => 'AF 52', 'title' => 'Livelihood 2 - Soft Broom Making, Medicinal Plants, Livestock'],
            ['code' => 'AF 99', 'title' => 'Seminar in Agroforestry'],
            ['code' => 'AF 100', 'title' => 'Agroforestry Practicum'],
            ['code' => 'AF 102-A', 'title' => 'Thesis A'],
            ['code' => 'AF 102-B', 'title' => 'Thesis B'],
        ];
    }

    /** @return list<array{code: string, title: string}> */
    private function ecotourismCourses(): array
    {
        return [
            ['code' => 'ECOTOUR 01', 'title' => 'Principles of Management'],
            ['code' => 'ECOTOUR 02', 'title' => 'Principles of Tourism'],
            ['code' => 'ECOTOUR 03', 'title' => 'Ecology in Ecotourism'],
            ['code' => 'ECOTOUR 04', 'title' => 'Basic Accounting and Finance in Ecotourism'],
            ['code' => 'ECOTOUR 05', 'title' => 'Philippine Tourism'],
            ['code' => 'ECOTOUR 06', 'title' => 'World Tourism'],
            ['code' => 'ECOTOUR 07', 'title' => 'Philippine Indigenous Communities in Ecotourism Perspective'],
            ['code' => 'ECOTOUR 08', 'title' => 'Plant Species Identification'],
            ['code' => 'ECOTOUR 09', 'title' => 'Ecotourism Promotions and Marketing'],
            ['code' => 'ECOTOUR 10', 'title' => 'Biodiversity Conservation Management'],
            ['code' => 'ECOTOUR 11', 'title' => 'Ecotourism Laws and Regulations'],
            ['code' => 'ECOTOUR 12', 'title' => 'Foreign Language'],
            ['code' => 'ECOTOUR 13', 'title' => 'Bird Watching'],
            ['code' => 'ECOTOUR 14', 'title' => 'Land Use Planning'],
            ['code' => 'ECOTOUR 15', 'title' => 'Resort Management'],
            ['code' => 'ECOTOUR 16', 'title' => 'Geospatial Methods in Ecotourism'],
            ['code' => 'ECOTOUR 17', 'title' => 'Ecotourism Statistics'],
            ['code' => 'ECOTOUR 18', 'title' => 'Ecotourism Management 1'],
            ['code' => 'ECOTOUR 19', 'title' => 'Hospitality Organizational and Operational Management'],
            ['code' => 'ECOTOUR 20', 'title' => 'Ecotourism Valuation'],
            ['code' => 'ECOTOUR 21', 'title' => 'Ecotourism Research Methods and Techniques'],
            ['code' => 'ECOTOUR 22', 'title' => 'Ecotourism Management 2'],
            ['code' => 'ECOTOUR 23', 'title' => 'Environmental Assessment and Monitoring'],
            ['code' => 'ECOTOUR 24', 'title' => 'Tour Guiding and Escorting'],
            ['code' => 'ECOTOUR 25', 'title' => 'Entrepreneurship and Business Planning'],
            ['code' => 'ECOTOUR 26', 'title' => 'Travel Management'],
            ['code' => 'ECOTOUR 27', 'title' => 'Digital Photography'],
            ['code' => 'ECOTOUR 28', 'title' => 'Pollution and Waste Management'],
            ['code' => 'ECOTOUR 29', 'title' => 'Ecotourism Planning and Development'],
            ['code' => 'ECOTOUR 30', 'title' => 'Broadcasting'],
            ['code' => 'ECOTOUR 31', 'title' => 'Applied Ecotourism Research'],
            ['code' => 'ECOTOUR 32', 'title' => 'Landscaping'],
            ['code' => 'ECOTOUR 33', 'title' => 'Nature-Based Tourism in Karst Landscape'],
            ['code' => 'ECOTOUR 34', 'title' => 'Community-Based Ecotourism'],
            ['code' => 'ECOTOUR 35/36', 'title' => 'Protected Area Management'],
            ['code' => 'ECOTOUR 99', 'title' => 'Seminar in Ecotourism'],
            ['code' => 'ECOTOUR 100', 'title' => 'On-the-Job Training'],
        ];
    }
}
