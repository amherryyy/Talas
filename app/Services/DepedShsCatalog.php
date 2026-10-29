<?php

namespace App\Services;

/** Published subject guides for DepEd's Strengthened Senior High School curriculum. */
class DepedShsCatalog
{
    public const SOURCE = 'https://www.deped.gov.ph/strengthened-shs-program/';
    public const VERSION = 'DepEd Order No. 017, s. 2026 - Strengthened SHS Curriculum';

    /** @return list<array{id: string, track: string, cluster: string, grade: string, subjects: list<array{id: string, title: string}>}> */
    public function groups(): array
    {
        return [
            $this->group('core', 'Core', 'Core Subjects', 'Grade 11', [
                ['effective-communication', 'Effective Communication / Mabisang Komunikasyon'],
                ['general-mathematics', 'General Mathematics'],
                ['general-science', 'General Science'],
                ['life-career-skills', 'Life and Career Skills'],
                ['kasaysayan-lipunang-pilipino', 'Pag-aaral ng Kasaysayan at Lipunang Pilipino'],
            ]),
            $this->group('academic-arts-social-humanities', 'Academic elective', 'Arts, Social Sciences, and Humanities', 'Grade 11 or 12 as permitted by the curriculum guide', [
                ['arts-1', 'Arts 1 - Creative Industries: Visual Art, Literary Art, Media Art, Applied Art, and Traditional Art'],
                ['arts-2', 'Arts 2 - Creative Industries: Music, Dance, and Theater'],
                ['citizenship-civic-engagement', 'Citizenship and Civic Engagement'],
                ['contemporary-literature-1', 'Contemporary Literature 1'],
                ['contemporary-literature-2', 'Contemporary Literature 2'],
                ['creative-composition-1', 'Creative Composition 1'],
                ['creative-composition-2', 'Creative Composition 2'],
                ['filipino-1', 'Filipino 1 - Wika at Komunikasyon sa Akademikong Filipino'],
                ['filipino-2-technical', 'Filipino 2 - Filipino para sa Larang Teknikal-Propesyonal'],
                ['filipino-2-sports', 'Filipino 2 - Filipino sa Isports'],
                ['filipino-2-arts-design', 'Filipino 2 - Filipino sa Sining at Disenyo'],
                ['filipino-identity-arts', 'Filipino Identity Through the Arts'],
                ['introduction-philosophy', 'Introduction to Philosophy'],
                ['leadership-arts', 'Leadership and Management in the Arts'],
                ['malikhaing-pagsulat', 'Malikhaing Pagsulat'],
                ['philippine-governance', 'Philippine Governance (Philippine Politics and Governance)'],
                ['social-sciences-theory-practice', 'Social Sciences: Theory and Practice'],
            ]),
            $this->group('academic-business', 'Academic elective', 'Business and Entrepreneurship', 'Grade 11 or 12 as permitted by the curriculum guide', [
                ['business-1-accounting', 'Business 1 - Basic Accounting'],
                ['business-2-finance-taxation', 'Business 2 - Business Finance and Income Taxation'],
                ['business-3-economics', 'Business 3 - Business Economics'],
                ['contemporary-marketing', 'Contemporary Marketing'],
                ['entrepreneurship', 'Entrepreneurship'],
                ['organization-management', 'Introduction to Organization and Management'],
            ]),
            $this->group('academic-stem', 'Academic elective', 'Science, Technology, Engineering, and Mathematics', 'Grade 11 or 12 as permitted by the curriculum guide', [
                ['biology-1', 'Biology 1'], ['biology-2', 'Biology 2'],
                ['chemistry-1', 'Chemistry 1'], ['chemistry-2', 'Chemistry 2'],
                ['earth-space-1', 'Earth and Space Science 1'], ['earth-space-2', 'Earth and Space Science 2'],
                ['finite-mathematics-1', 'Finite Mathematics 1'], ['finite-mathematics-2', 'Finite Mathematics 2'],
                ['physics-1', 'Physics 1'], ['physics-2', 'Physics 2'],
            ]),
            $this->group('techpro-aesthetic-care', 'TechPro elective', 'Aesthetic, Wellness, and Human Care', 'Grade 11 or 12 as permitted by the curriculum guide', [
                ['aesthetic-beauty-care', 'Aesthetic Services: Beauty Care'],
                ['caregiving-adult', 'Caregiving: Adult Care'],
                ['caregiving-child', 'Caregiving: Child Care'],
                ['hairdressing', 'Hairdressing'],
            ]),
            $this->group('techpro-agri-fishery', 'TechPro elective', 'Agri-Fishery Business and Food Innovation', 'Grade 11 or 12 as permitted by the curriculum guide', [
                ['agricultural-crops', 'Agricultural Crops Production'], ['agro-entrepreneurship', 'Agro-Entrepreneurship'],
                ['aquaculture', 'Aquaculture'], ['fish-capture', 'Fish Capture'], ['food-processing', 'Food Processing'],
                ['organic-agriculture', 'Organic Agriculture Production'], ['poultry-production', 'Poultry Production: Chicken'],
                ['ruminants-production', 'Ruminants Production'], ['swine-production', 'Swine Production'],
            ]),
            $this->group('techpro-artisanry', 'TechPro elective', 'Artisanry and Creative Enterprise', 'Grade 11 or 12 as permitted by the curriculum guide', [
                ['garments-artisanry', 'Garments Artisanry'], ['handicrafts-weaving', 'Handicrafts: Weaving'],
            ]),
            $this->group('techpro-automotive', 'TechPro elective', 'Automotive and Small Engine Technologies', 'Grade 11 or 12 as permitted by the curriculum guide', [
                ['automotive-electrical', 'Automotive Servicing: Electrical Repair'],
                ['automotive-engine-chassis', 'Automotive Servicing: Engine and Chassis Repairs'],
                ['driving-automotive', 'Driving and Automotive Servicing'],
                ['motorcycle-small-engine', 'Motorcycle and Small Engine Servicing'],
            ]),
            $this->group('techpro-construction', 'TechPro elective', 'Construction and Building Technology', 'Grade 11 or 12 as permitted by the curriculum guide', [
                ['carpentry', 'Carpentry'], ['construction-operation', 'Construction Operation'],
                ['manual-metal-arc-welding', 'Manual Metal Arc Welding'], ['technical-drafting', 'Technical Drafting'],
            ]),
            $this->group('techpro-creative-arts', 'TechPro elective', 'Creative Arts and Design Technology', 'Grade 11 or 12 as permitted by the curriculum guide', [
                ['animation', 'Animation'], ['illustration', 'Illustration'], ['visual-graphic-design', 'Visual Graphic Design'],
            ]),
            $this->group('techpro-hospitality', 'TechPro elective', 'Hospitality and Tourism', 'Grade 11 or 12 as permitted by the curriculum guide', [
                ['bakery-operations', 'Bakery Operations'], ['events-management', 'Events Management Services'],
                ['food-beverage', 'Food and Beverage Operation'], ['hotel-front-office', 'Hotel Operation: Front Office Services'],
                ['hotel-housekeeping', 'Hotel Operation: Housekeeping Services'], ['kitchen-operations', 'Kitchen Operations'],
                ['tourism-services', 'Tourism Services'],
            ]),
            $this->group('techpro-ict', 'TechPro elective', 'ICT Support and Computer Programming Technologies', 'Grade 11 or 12 as permitted by the curriculum guide', [
                ['broadband-installation', 'Broadband Installation'],
                ['computer-programming-dotnet', 'Computer Programming: .NET Technology'],
                ['computer-programming-java', 'Computer Programming: Java'],
                ['computer-programming-oracle', 'Computer Programming: Oracle Database'],
                ['computer-systems-servicing', 'Computer Systems Servicing'], ['contact-center-services', 'Contact Center Services'],
            ]),
            $this->group('techpro-industrial', 'TechPro elective', 'Industrial Technologies', 'Grade 11 or 12 as permitted by the curriculum guide', [
                ['commercial-air-conditioning', 'Commercial Air Conditioning Installation and Servicing'],
                ['domestic-refrigeration-ac', 'Domestic Refrigeration and Air Conditioning Servicing'],
                ['electrical-installation', 'Electrical Installation and Maintenance'],
                ['electronics-assembly-servicing', 'Electronics Product Assembly and Servicing'],
                ['mechatronics', 'Mechatronics'], ['photovoltaic-systems', 'Photovoltaic Systems Installation'],
            ]),
            $this->group('techpro-maritime', 'TechPro elective', 'Maritime', 'Grade 11 or 12 as permitted by the curriculum guide', [
                ['marine-engineering-support', 'Marine Engineering at the Support Level'],
                ['marine-transportation-support', 'Marine Transportation at the Support Level'],
                ['ships-catering', 'Ships Catering Services'],
            ]),
            $this->group('grade12-arts-social-humanities', 'Academic elective guide', 'Arts, Social Sciences, and Humanities', 'New Grade 12 curriculum guides', [
                ['creative-production-presentation', 'Creative Production and Presentation'],
            ]),
            $this->group('grade12-stem', 'Academic elective guide', 'Science, Technology, Engineering, and Mathematics', 'New Grade 12 curriculum guides', [
                ['advanced-mathematics', 'Advanced Mathematics'], ['basic-calculus', 'Basic Calculus'],
                ['biology-3', 'Biology 3'], ['biology-4', 'Biology 4'], ['chemistry-3', 'Chemistry 3'], ['chemistry-4', 'Chemistry 4'],
                ['conceptual-biology-earth-space', 'Conceptual Biology and Earth and Space Science'],
                ['conceptual-physics-chemistry', 'Conceptual Physics and Chemistry in Daily Life'],
                ['database-management', 'Database Management'], ['earth-space-3', 'Earth and Space Science 3'],
                ['earth-space-4', 'Earth and Space Science 4'], ['empowerment-technologies', 'Empowerment Technologies'],
                ['fundamentals-data-analytics', 'Fundamentals of Data Analytics'], ['physics-3', 'Physics 3'],
                ['physics-4', 'Physics 4'], ['pre-calculus', 'Pre-Calculus'],
            ]),
            $this->group('grade12-field-experience', 'Field Experience', 'Arts Apprenticeship', 'New Grade 12 curriculum guides', [
                ['arts-apprenticeship-dance', 'Arts Apprenticeship - Dance'],
                ['arts-apprenticeship-literary', 'Arts Apprenticeship - Literary Arts'],
                ['arts-apprenticeship-media', 'Arts Apprenticeship - Media Arts'],
                ['arts-apprenticeship-music', 'Arts Apprenticeship - Music'],
                ['arts-apprenticeship-theater', 'Arts Apprenticeship - Theater Arts'],
            ]),
        ];
    }

    /** @return list<array{id: string, title: string}> */
    public function selectedSubjects(array $ids): array
    {
        $requested = array_fill_keys(array_filter($ids, 'is_string'), true);
        $selected = [];
        foreach ($this->groups() as $group) {
            foreach ($group['subjects'] as $subject) {
                if (isset($requested[$subject['id']])) {
                    $selected[$subject['id']] = $subject;
                }
            }
        }
        return array_values($selected);
    }

    /** @return array{id: string, track: string, cluster: string, grade: string, subjects: list<array{id: string, title: string}>} */
    private function group(string $id, string $track, string $cluster, string $grade, array $subjects): array
    {
        return [
            'id' => $id,
            'track' => $track,
            'cluster' => $cluster,
            'grade' => $grade,
            'subjects' => array_map(static fn (array $subject): array => ['id' => $subject[0], 'title' => $subject[1]], $subjects),
        ];
    }
}
