<?php

namespace App\Services;

/** NVSU undergraduate offerings transcribed from the university's campus pages. */
class NvsuProgramCatalog
{
    /** @return list<array{campus: string, source: string, colleges: list<array{name: string, programs: list<array{title: string, majors?: list<string>, note?: string}>}>}> */
    public function all(): array
    {
        return [
            [
                'campus' => 'Bayombong Campus',
                'source' => 'https://www.nvsu.edu.ph/academics/degree-programs-bay',
                'colleges' => [
                    ['name' => 'College of Agriculture', 'programs' => [
                        ['title' => 'Certificate of Agricultural Science'],
                        ['title' => 'Bachelor of Science in Agriculture', 'majors' => ['Animal Science', 'Crop Science (Agronomy/Horticulture)', 'Crop Protection (Plant Pathology/Entomology)', 'Soil Science', 'Agricultural Economics & Marketing', 'Agriculture Extension & Communication']],
                        ['title' => 'Bachelor of Science in Fisheries', 'note' => 'Ladderized program'],
                    ]],
                    ['name' => 'College of Arts and Sciences', 'programs' => [
                        ['title' => 'Bachelor of Science in Mathematics'],
                        ['title' => 'Bachelor of Science in Computer Science', 'majors' => ['Game Development', 'Robotics']],
                        ['title' => 'Bachelor of Science in Information Technology', 'majors' => ['Web & Mobile App Development', 'Network Design & Management']],
                        ['title' => 'Bachelor of Science in Information Systems', 'majors' => ['Business Analytics', 'Service Management']],
                    ]],
                    ['name' => 'College of Business Education', 'programs' => [
                        ['title' => 'Bachelor of Science in Agribusiness'],
                        ['title' => 'Bachelor of Science in Business Administration', 'majors' => ['Financial Management', 'Marketing Management', 'Human Resource Management', 'Business Economics']],
                    ]],
                    ['name' => 'College of Engineering', 'programs' => [
                        ['title' => 'Bachelor of Science in Civil Engineering', 'majors' => ['Construction Engineering & Management', 'Structural Engineering', 'Transportation Engineering', 'Water Resources Engineering']],
                        ['title' => 'Bachelor of Science in Geodetic Engineering'],
                        ['title' => 'Bachelor of Science in Agricultural and Biosystems Engineering'],
                    ]],
                    ['name' => 'College of Forestry, Environment and Resources Management', 'programs' => [
                        ['title' => 'Bachelor of Science in Agroforestry'],
                        ['title' => 'Bachelor of Science in Forestry'],
                        ['title' => 'Bachelor of Science in Environmental Science', 'majors' => ['Environmental Management', 'Ecosystem Management']],
                        ['title' => 'Bachelor of Science in Ecotourism'],
                    ]],
                    ['name' => 'College of Human Ecology', 'programs' => [
                        ['title' => 'Bachelor of Science in Hospitality Management'],
                        ['title' => 'Bachelor of Science in Tourism Management'],
                        ['title' => 'Bachelor of Science in Home Technology', 'majors' => ['Food and Nutrition']],
                    ]],
                    ['name' => 'College of Teacher Education', 'programs' => [
                        ['title' => 'Bachelor of Physical Education'],
                        ['title' => 'Bachelor of Culture and Arts Education'],
                        ['title' => 'Bachelor of Elementary Education'],
                        ['title' => 'Bachelor of Early Childhood Education'],
                        ['title' => 'Bachelor of Secondary Education', 'majors' => ['English', 'Filipino', 'Mathematics', 'Science', 'Social Science'], 'note' => 'The NVSU page repeats “Early Childhood Education” at this position; this title is cross-checked against CHED Region II’s recognized-program listing.'],
                        ['title' => 'Bachelor of Technical Vocational Teacher Education', 'majors' => ['Automotive Technology', 'Civil Technology', 'Drafting Technology', 'Electronics Technology', 'Electrical Technology', 'Food Service Management', 'Furniture and Cabinet', 'Welding Technology']],
                        ['title' => 'Bachelor of Technology and Livelihood Education', 'majors' => ['Industrial Arts', 'Home Economics', 'Agri-Fishery Arts', 'Information and Communication Technology']],
                    ]],
                    ['name' => 'College of Veterinary Medicine', 'programs' => [
                        ['title' => 'Bachelor in Animal Science'],
                        ['title' => 'Doctor of Veterinary Medicine', 'note' => 'Ladderized program'],
                    ]],
                ],
            ],
            [
                'campus' => 'Bambang Campus',
                'source' => 'https://www.nvsu.edu.ph/academics/degree-programs-bam',
                'colleges' => [
                    ['name' => 'College of Arts & Sciences', 'programs' => [
                        ['title' => 'Bachelor of Arts in English Language'],
                        ['title' => 'Bachelor of Science in Biology', 'majors' => ['Animal Biology', 'Plant Biology']],
                        ['title' => 'Bachelor of Science in Cooperative Development'],
                        ['title' => 'Bachelor of Science in Public Administration'],
                    ]],
                    ['name' => 'College of Engineering', 'programs' => [
                        ['title' => 'Bachelor of Science in Mechanical Engineering'],
                        ['title' => 'Bachelor of Science in Electrical Engineering'],
                        ['title' => 'Bachelor of Science in Electronics Engineering'],
                        ['title' => 'Bachelor of Science in Computer Engineering'],
                    ]],
                    ['name' => 'College of Teacher Education', 'programs' => [
                        ['title' => 'Bachelor of Elementary Education'],
                        ['title' => 'Bachelor of Secondary Education', 'majors' => ['English', 'Filipino', 'Mathematics', 'Sciences', 'Social Studies']],
                        ['title' => 'Bachelor of Physical Education'],
                        ['title' => 'Bachelor of Technology and Livelihood Education', 'majors' => ['Agri-Fishery Arts', 'Home Economics', 'Industrial Arts', 'Information and Communications Technology']],
                        ['title' => 'Bachelor of Technical-Vocational Teacher Education', 'majors' => ['Automotive Technology', 'Civil and Construction Technology', 'Drafting Technology', 'Electronics Technology', 'Electrical Technology', 'Food & Service Management', 'Furniture & Cabinet Making Technology', 'Garments, Fashion & Design', 'Heating, Ventilating & Air-Conditioning Technology', 'Mechanical Technology', 'Welding & Fabrication Technology']],
                    ]],
                    ['name' => 'College of Industrial Technology', 'programs' => [
                        ['title' => 'Bachelor of Science in Computer Science'],
                        ['title' => 'Bachelor of Science in Hospitality Management'],
                        ['title' => 'Bachelor of Science in Industrial Technology', 'majors' => ['Automotive Technology', 'Civil Technology', 'Computer Technology', 'Cosmetology', 'Drafting Technology', 'Electrical Technology', 'Electronics Technology', 'Food Technology', 'Garments Technology', 'Mechanical Technology', 'Refrigeration & Air-Conditioning Technology', 'Welding & Fabrication Technology']],
                        ['title' => 'Bachelor of Science in Information Technology'],
                    ]],
                ],
            ],
        ];
    }
}
