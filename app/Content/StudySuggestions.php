<?php

namespace App\Content;

final class StudySuggestions
{
    public const SOURCE_URL = 'https://ies.ed.gov/ncee/wwc/practiceguide/1';
    public const SOURCE_NAME = 'What Works Clearinghouse practice guide (2007)';

    /** @var list<array{id: string, title: string, body: string, rationale: string, recommendation: string, source_url: string, source_label: string, steps: list<string>}> */
    public const ITEMS = [
        [
            'id' => 'space-reviews',
            'title' => 'Space the second look',
            'body' => 'A short return later can beat one long cram session. Try spaced retrieval, not just another reread.',
            'rationale' => 'After time has passed, recalling the idea again takes more effort than rereading it right away. That effort can support later recall when you check your answer and correct gaps. The best spacing depends on when you need the material again.',
            'recommendation' => 'Spaced retrieval · useful for facts and concepts you need to revisit.',
            'source_url' => 'https://link.springer.com/article/10.1007/s10648-026-10225-5',
            'source_label' => '2026 school-classroom meta-analysis',
            'steps' => [
                'At the end of this block, write one question about the idea you studied.',
                'Schedule a later day in Duno Plan; close your notes and answer that question first.',
                'Check your answer, fix one gap, and choose the next revisit based on what you missed.',
            ],
        ],
        [
            'id' => 'recall-from-memory',
            'title' => 'Try a two-minute brain dump',
            'body' => '“Blurting” is a popular name for retrieval practice: get ideas out of memory before checking your notes.',
            'rationale' => 'Trying to retrieve information shows what you can produce without cues. Comparing your answer with course material helps separate solid recall from missing or mixed-up details; rereading alone can feel familiar without testing recall.',
            'recommendation' => 'Active recall · works best when you check and correct what you retrieved.',
            'source_url' => 'https://link.springer.com/article/10.1007/s10648-026-10225-5',
            'source_label' => '2026 school-classroom meta-analysis',
            'steps' => [
                'Set a short timer and write, sketch, or say everything you remember about one topic.',
                'Open your material and mark what is accurate, missing, or mixed up.',
                'Turn the missed points into questions for another try later.',
            ],
        ],
        [
            'id' => 'explain-the-idea',
            'title' => 'Teach it back in plain language',
            'body' => 'Commonly called the Feynman technique, this is self-explanation—not a magic shortcut.',
            'rationale' => 'Explaining why a step works or how ideas connect makes you build a link between them instead of repeating a label. Check the explanation against your course material: a fluent explanation can still contain a mistake.',
            'recommendation' => 'Self-explanation · name the principle and connect it to an example.',
            'source_url' => 'https://ies.ed.gov/ncee/wwc/practiceguide/1',
            'source_label' => 'WWC practice guide',
            'steps' => [
                'Choose one idea and explain it aloud as if to a classmate who missed the lesson.',
                'Add why it works, how it connects, or when it would not apply.',
                'Check the course material and repair any part you could not explain clearly.',
            ],
        ],
        [
            'id' => 'example-then-practice',
            'title' => 'Fade the worked example',
            'body' => 'Examples can help when a process is new. Gradually do more of the steps yourself.',
            'rationale' => 'A solved example gives you a model while a procedure is unfamiliar. Covering steps one at a time moves the work back to you, and checking against the solution helps catch a wrong turn before it becomes a habit.',
            'recommendation' => 'Worked examples · especially useful while learning an unfamiliar procedure.',
            'source_url' => 'https://ies.ed.gov/ncee/wwc/practiceguide/1',
            'source_label' => 'WWC practice guide',
            'steps' => [
                'Study a solved example and label why each step is needed.',
                'Cover one step and try to supply it; next, solve a similar problem without the solution.',
                'Compare methods and correct the first step where your reasoning diverged.',
            ],
        ],
        [
            'id' => 'interleave-practice',
            'title' => 'Mix up related problem types',
            'body' => 'Once you know the basics, mix related problems so you practise choosing a method, not just repeating one.',
            'rationale' => 'A mixed set asks you to notice which method fits each question. Research finds that this depends on the material: interleaving is not a blanket replacement for focused practice, especially when the categories are unrelated or the basics are still new.',
            'recommendation' => 'Interleaving · most useful when problem types are related; not a rule for every subject.',
            'source_url' => 'https://pubmed.ncbi.nlm.nih.gov/31556629/',
            'source_label' => '2019 interleaving meta-analysis',
            'steps' => [
                'Pick two or three related problem types you have already studied.',
                'Mix a few questions instead of doing every problem of one type in a row.',
                'For each one, name the clue that helped you choose a method, then check your work.',
            ],
        ],
        [
            'id' => 'draw-from-memory',
            'title' => 'Sketch the idea from memory',
            'body' => 'For processes and connected concepts, pair a simple diagram with your own explanation. Skip decorative copying.',
            'rationale' => 'A clear diagram can make parts, stages, and relationships easier to inspect when you connect it directly to words. Use a visual only when it clarifies the idea; copying or decorating a diagram is not the goal. Classroom findings vary and do not guarantee an effect for every college subject.',
            'recommendation' => 'Words + relevant visuals · keep the diagram clear, connected, and free of clutter.',
            'source_url' => 'https://www.ambition.org.uk/when-and-how-to-use-dual-coding-effectively-in-teaching/',
            'source_label' => '2026 classroom meta-analysis (ages 4–18)',
            'steps' => [
                'Close your notes and sketch the main parts or stages of one idea.',
                'Label each part and explain the arrows or relationships in your own words.',
                'Compare with course material, correct missing links, and revisit it later from memory.',
            ],
        ],
        [
            'id' => 'confidence-check',
            'title' => 'Check your confidence against recall',
            'body' => 'A quick confidence check helps decide what deserves your next few minutes.',
            'rationale' => 'Familiarity can make material seem easier than it is. Rate your confidence, try an answer with notes closed, and compare the result; use the gap to choose what to revisit instead of judging by recognition alone.',
            'recommendation' => 'Metacognitive check · use evidence from recall, not familiarity alone.',
            'source_url' => 'https://www.sciencedirect.com/science/article/pii/S0361476X17305027',
            'source_label' => '2019 undergrad study of recall and confidence',
            'steps' => [
                'Before checking notes, rate how sure you are that you can explain one key idea.',
                'Answer a question or explain the idea with your notes closed.',
                'Compare your answer with the material and plan your next review around the gap.',
            ],
        ],
    ];
}
