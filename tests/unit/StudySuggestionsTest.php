<?php

use App\Content\StudySuggestions;
use App\Content\StudyGuideOutcomes;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class StudySuggestionsTest extends CIUnitTestCase
{
    public function testCuratedSuggestionsHaveUniqueIdsAndAReputableHttpsSource(): void
    {
        $this->assertCount(7, StudySuggestions::ITEMS);
        $this->assertSame('https://ies.ed.gov/ncee/wwc/practiceguide/1', StudySuggestions::SOURCE_URL);
        $this->assertStringContainsString('What Works Clearinghouse', StudySuggestions::SOURCE_NAME);

        $ids = [];
        foreach (StudySuggestions::ITEMS as $suggestion) {
            $this->assertNotEmpty($suggestion['title']);
            $this->assertNotEmpty($suggestion['body']);
            $this->assertNotEmpty($suggestion['rationale']);
            $this->assertNotEmpty($suggestion['recommendation']);
            $this->assertStringStartsWith('https://', $suggestion['source_url']);
            $this->assertNotEmpty($suggestion['source_label']);
            $this->assertCount(3, $suggestion['steps']);
            $ids[] = $suggestion['id'];
        }

        $this->assertCount(count(array_unique($ids)), $ids);
    }

    public function testOutcomeMappingsUseOnlyTheSupportedOfficialProgramFamilies(): void
    {
        $this->assertSame('cs', StudyGuideOutcomes::programFamily('bscs-game-development'));
        $this->assertSame('cs', StudyGuideOutcomes::programFamily('bscs-robotics'));
        $this->assertSame('is', StudyGuideOutcomes::programFamily('bsis-service-management'));
        $this->assertSame('is', StudyGuideOutcomes::programFamily('bsis-business-analytics'));
        $this->assertSame('it', StudyGuideOutcomes::programFamily('bsit-web-mobile'));
        $this->assertSame('it', StudyGuideOutcomes::programFamily('bsit-network-design'));
        $this->assertNull(StudyGuideOutcomes::programFamily('ba-communication'));

        $computerScience = StudyGuideOutcomes::forSuggestion('space-reviews', 'cs');
        $this->assertSame('CS10', $computerScience[0]['code']);
        $this->assertSame('IS02–IS03', StudyGuideOutcomes::forSuggestion('explain-the-idea', 'is')[0]['code']);
        $this->assertSame('IT05', StudyGuideOutcomes::forSuggestion('example-then-practice', 'it')[1]['code']);
        $this->assertSame([], StudyGuideOutcomes::forSuggestion('unknown', 'cs'));
        $this->assertSame([], StudyGuideOutcomes::forSuggestion('space-reviews', 'unknown'));
        $this->assertStringContainsString('ched.gov.ph', StudyGuideOutcomes::SOURCE_URL);
    }
}
