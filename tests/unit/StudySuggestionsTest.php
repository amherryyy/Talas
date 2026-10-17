<?php

use App\Content\StudySuggestions;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class StudySuggestionsTest extends CIUnitTestCase
{
    public function testCuratedSuggestionsHaveUniqueIdsAndAReputableHttpsSource(): void
    {
        $this->assertCount(4, StudySuggestions::ITEMS);
        $this->assertSame('https://ies.ed.gov/ncee/wwc/practiceguide/1', StudySuggestions::SOURCE_URL);
        $this->assertStringContainsString('What Works Clearinghouse', StudySuggestions::SOURCE_NAME);

        $ids = [];
        foreach (StudySuggestions::ITEMS as $suggestion) {
            $this->assertNotEmpty($suggestion['title']);
            $this->assertNotEmpty($suggestion['body']);
            $this->assertNotEmpty($suggestion['recommendation']);
            $ids[] = $suggestion['id'];
        }

        $this->assertCount(count(array_unique($ids)), $ids);
    }
}
