import { test, expect } from '@playwright/test';
import { existsSync, readFileSync } from 'node:fs';
import { resolve } from 'node:path';

const viewportWidths = [320, 400, 768, 1280];
const localAccountPath = resolve(process.cwd(), '.tools/review-account.txt');
const localAccount = existsSync(localAccountPath)
  ? Object.fromEntries(
      readFileSync(localAccountPath, 'utf8')
        .split(/\r?\n/)
        .filter(line => line.includes('='))
        .map(line => {
          const separator = line.indexOf('=');
          return [line.slice(0, separator).trim(), line.slice(separator + 1).trim()];
        }),
    )
  : {};
const email = process.env.TALAS_E2E_EMAIL || localAccount.email;
const password = process.env.TALAS_E2E_PASSWORD || localAccount.password;

async function expectNoHorizontalOverflow(page, route) {
  const dimensions = await page.evaluate(() => ({
    viewport: document.documentElement.clientWidth,
    content: document.documentElement.scrollWidth,
  }));

  expect(dimensions.content, `${route} should fit within its ${dimensions.viewport}px viewport`).toBeLessThanOrEqual(dimensions.viewport);
}

async function tabUntilFocused(page, locator, maxTabs = 20) {
  for (let tab = 0; tab < maxTabs; tab++) {
    await page.keyboard.press('Tab');
    if (await locator.evaluate(element => element === document.activeElement)) {
      return;
    }
  }

  throw new Error('The target control was not reachable by keyboard tab navigation.');
}

test('login page fits common mobile, tablet, and desktop widths', async ({ page }) => {
  for (const width of viewportWidths) {
    await page.setViewportSize({ width, height: 850 });
    await page.goto('login');

    await expect(page.getByLabel('Email')).toBeVisible();
    await expect(page.getByLabel('Password')).toBeVisible();
    await expectNoHorizontalOverflow(page, '/login');
  }
});

test('subject and topic search pages fit common widths for a student', async ({ page }) => {
  test.setTimeout(120_000);
  test.skip(!email || !password, 'Provide a synthetic student account in .tools/review-account.txt or set TALAS_E2E_EMAIL and TALAS_E2E_PASSWORD to run authenticated page checks.');

  await page.setViewportSize({ width: 400, height: 850 });
  await page.goto('login');
  await page.getByLabel('Email').fill(email);
  await page.getByLabel('Password').fill(password);
  await page.getByRole('button', { name: 'Login' }).click();
  await page.waitForURL(
    url => !url.pathname.endsWith('/login'),
    { timeout: 10_000, waitUntil: 'domcontentloaded' },
  );

  const adminResponse = await page.goto('admin');
  expect(adminResponse?.status()).toBe(403);

  for (const width of viewportWidths) {
    await page.setViewportSize({ width, height: 850 });

    await page.goto('student');
    await expect(page.getByRole('heading', { name: 'Make a little progress today.' })).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Duno Guide' })).toBeVisible();
    await expect(page.getByRole('link', { name: 'What Works Clearinghouse practice guide (2007)' })).toHaveCount(4);
    await expectNoHorizontalOverflow(page, '/student');

    await page.goto('student/subjects');
    await expect(page.getByRole('heading', { name: 'Your subjects' })).toBeVisible();
    await expectNoHorizontalOverflow(page, '/student/subjects');

    await page.goto('student/topics');
    await expect(page.getByRole('heading', { name: 'Find a topic' })).toBeVisible();
    await expect(page.getByLabel('Search topics and subjects')).toBeVisible();
    await expect(page.getByLabel('Importance')).toBeVisible();
    await expect(page.getByLabel('Progress')).toBeVisible();
    await expect(page.getByLabel('Due date')).toBeVisible();
    await expectNoHorizontalOverflow(page, '/student/topics');

    await page.goto('student/priority');
    await expect(page.getByRole('heading', { name: 'Duno Focus' })).toBeVisible();
    await expectNoHorizontalOverflow(page, '/student/priority');

    await page.goto('student/sessions');
    await expect(page.getByRole('heading', { name: 'Duno Timer' })).toBeVisible();
    await expectNoHorizontalOverflow(page, '/student/sessions');

    await page.goto('student/plan');
    await expect(page.getByRole('heading', { name: 'Duno Plan' })).toBeVisible();
    await expect(page.getByLabel('Available study time today (minutes)')).toBeVisible();
    await expectNoHorizontalOverflow(page, '/student/plan');

    await page.goto('student/check-ins');
    await expect(page.getByRole('heading', { name: 'Private check-ins' })).toBeVisible();
    await expect(page.getByLabel('Workload')).toBeVisible();
    await expect(page.getByLabel('Energy')).toBeVisible();
    await expectNoHorizontalOverflow(page, '/student/check-ins');

    await page.goto('student/aralytics');
    await expect(page.getByRole('heading', { name: 'Aralytics' })).toBeVisible();
    await expect(page.getByRole('button', { name: 'Print report' })).toBeVisible();
    await expectNoHorizontalOverflow(page, '/student/aralytics');
  }

  await page.goto('student');
  const sourceLink = page.locator('#study-ideas-heading').locator('..').getByRole('link').first();
  await tabUntilFocused(page, sourceLink);
  await expect(sourceLink).toBeFocused();

  await page.goto('student/plan');
  const availableTime = page.getByLabel('Available study time today (minutes)');
  await tabUntilFocused(page, availableTime);
  await expect(availableTime).toBeFocused();

  await page.goto('student/aralytics');
  await page.getByRole('link', { name: 'Last 30 days' }).click();
  await expect(page.getByRole('heading', { name: 'Study activity: Last 30 days' })).toBeVisible();
  await page.getByRole('link', { name: 'All time' }).click();
  await expect(page.getByRole('heading', { name: 'Study activity: All time' })).toBeVisible();

  await page.goto('student/aralytics');
  const printButton = page.getByRole('button', { name: 'Print report' });
  await tabUntilFocused(page, printButton);
  await expect(printButton).toBeFocused();
  const focusRing = await printButton.evaluate(element => {
    const style = getComputedStyle(element);
    return { outlineStyle: style.outlineStyle, outlineWidth: style.outlineWidth };
  });
  expect(focusRing).toEqual({ outlineStyle: 'solid', outlineWidth: '3px' });

  await page.emulateMedia({ reducedMotion: 'reduce' });
  const reducedTransition = await printButton.evaluate(element => getComputedStyle(element).transitionDuration);
  expect(Number.parseFloat(reducedTransition)).toBeLessThan(0.001);
  await page.emulateMedia({ reducedMotion: 'no-preference' });

  await page.evaluate(() => {
    window.__talasPrintCalled = false;
    window.print = () => { window.__talasPrintCalled = true; };
  });
  await page.keyboard.press('Enter');
  expect(await page.evaluate(() => window.__talasPrintCalled)).toBe(true);

  await page.emulateMedia({ media: 'print' });
  await expect(page.locator('header')).toBeHidden();
  await expect(page.getByRole('button', { name: 'Print report' })).toBeHidden();
  await expect(page.getByRole('heading', { name: 'Dated sessions' })).toBeVisible();
  await page.emulateMedia({ media: 'screen' });

  await page.setViewportSize({ width: 400, height: 850 });
  await page.goto('student/check-ins');
  await page.locator('#workload-rating').focus();
  await page.keyboard.press('Tab');
  await expect(page.locator('#energy-rating')).toBeFocused();
  await page.getByLabel('Workload').selectOption('4');
  await page.getByLabel('Energy').selectOption('3');
  await page.getByRole('button', { name: 'Save private check-in' }).click();
  await expect(page.getByText('Private check-in saved.')).toBeVisible();
  const checkIn = page.locator('.subject-list li').first();
  await expect(checkIn).toContainText('Workload: 4/5 · Energy: 3/5');
  const savedTime = await checkIn.locator('time').getAttribute('datetime');
  await page.goto('student/aralytics');
  await expect(page.getByRole('heading', { name: 'Your private check-in pattern' })).toBeVisible();
  const ratingChart = page.getByRole('img', { name: /Workload and energy ratings/ });
  await expect(ratingChart).toBeVisible();
  const ratingTable = page.getByRole('table', { name: 'Check-in counts by rating' });
  await expect(ratingTable).toBeVisible();
  await expect(ratingTable.getByRole('row', { name: /Workload/ })).toBeVisible();
  await expect(ratingTable.getByRole('row', { name: /Energy/ })).toBeVisible();
  await expectNoHorizontalOverflow(page, '/student/aralytics');
  await page.setViewportSize({ width: 1280, height: 850 });
  await page.reload();
  await expect(ratingChart).toBeVisible();
  await expectNoHorizontalOverflow(page, '/student/aralytics');
  await page.emulateMedia({ media: 'print' });
  await expect(page.getByRole('heading', { name: 'Your private check-in pattern' })).toBeHidden();
  await expect(ratingChart).toBeHidden();
  await expect(ratingTable).toBeHidden();
  await expect(page.getByRole('heading', { name: 'Dated sessions' })).toBeVisible();
  await page.emulateMedia({ media: 'screen' });
  await page.goto('student/check-ins');
  await checkIn.getByRole('link', { name: 'Edit' }).click();
  await page.getByLabel('Workload').selectOption('2');
  await page.getByRole('button', { name: 'Save changes' }).click();
  await expect(page.locator('.subject-list li').first()).toContainText('Workload: 2/5 · Energy: 3/5');
  await expect(page.locator('.subject-list li').first().locator('time')).toHaveAttribute('datetime', savedTime);
  await page.locator('.subject-list li').first().getByRole('link', { name: 'Delete' }).click();
  await page.getByRole('button', { name: 'Delete check-in' }).click();
  await expect(page.getByText('Private check-in deleted.')).toBeVisible();
  await expect(page.locator(`.subject-list li time[datetime="${savedTime}"]`)).toHaveCount(0);

  await page.setViewportSize({ width: 400, height: 850 });
  await page.goto('student/sessions');
  const subjects = page.getByLabel('Subject');
  if (await subjects.count() > 0 && await subjects.locator('option').count() > 1) {
    await subjects.selectOption({ index: 1 });
    await page.getByRole('button', { name: 'Start timer' }).click();
    await page.getByRole('button', { name: 'Pause' }).click();
    await page.getByRole('button', { name: 'Resume' }).click();
    await page.getByRole('button', { name: 'Stop and review' }).click();
    await expect(page.getByRole('group', { name: 'Review before saving' })).toBeVisible();
    await expect(page.getByRole('button', { name: 'Save session' })).toBeEnabled();
    await page.getByRole('button', { name: 'Discard this result' }).click();
    await expect(page.getByText('Timer discarded. No session was saved.')).toBeVisible();
  }
});

test('topic detail and add/edit forms are usable at phone and desktop widths', async ({ page }) => {
  test.setTimeout(60_000);
  test.skip(!email || !password, 'Provide a synthetic student account in .tools/review-account.txt or set TALAS_E2E_EMAIL and TALAS_E2E_PASSWORD to run authenticated page checks.');

  await page.setViewportSize({ width: 400, height: 850 });
  await page.goto('login');
  await page.getByLabel('Email').fill(email);
  await page.getByLabel('Password').fill(password);
  await page.getByRole('button', { name: 'Login' }).click();
  await page.waitForURL(url => !url.pathname.endsWith('/login'), { timeout: 10_000, waitUntil: 'domcontentloaded' });
  await page.goto('student/subjects');

  const subjects = await page.locator('.subject-list a').evaluateAll(links => links.map(link => ({
    href: link.getAttribute('href'),
    name: link.textContent.trim(),
  })));
  const reviewSubject = subjects.find(subject => subject.name.length >= 2);
  test.skip(!reviewSubject, 'The synthetic review account needs a named subject before topic detail and forms can be reviewed.');
  const subjectHref = reviewSubject.href;
  const subjectId = subjectHref?.match(/subjects\/(\d+)/)?.[1];
  expect(subjectId).toBeTruthy();
  const topicTitle = `Review topic ${Date.now()}`;
  let createdTopicHref = null;

  try {
    await page.goto(`student/subjects/${subjectId}/topics/new`);
    await expect(page.getByRole('heading', { name: /Add a topic in/ })).toBeVisible();
    await expectNoHorizontalOverflow(page, '/student/topics/new');
    const titleField = page.getByLabel('Topic title');
    await tabUntilFocused(page, titleField, 30);
    await expect(titleField).toBeFocused();
    const focusOutline = await titleField.evaluate(element => getComputedStyle(element).outlineWidth);
    expect(focusOutline).toBe('3px');

    await titleField.fill(topicTitle);
    await page.getByRole('button', { name: 'Create topic' }).click();
    createdTopicHref = page.url();
    await expect(page.getByRole('heading', { name: topicTitle })).toBeVisible();

    for (const width of [400, 1280]) {
      await page.setViewportSize({ width, height: 850 });
      await page.goto(createdTopicHref);
      await expect(page.getByRole('heading', { name: topicTitle })).toBeVisible();
      await expect(page.locator('.topic-detail-card')).toBeVisible();
      await expect(page.getByText('A topic used by a saved study session cannot be deleted.')).toBeVisible();
      await expectNoHorizontalOverflow(page, '/student/topics/detail');

      await page.getByRole('link', { name: 'Edit' }).click();
      await expect(page.getByLabel('Topic title')).toHaveValue(topicTitle);
      await expectNoHorizontalOverflow(page, '/student/topics/edit');
    }

    await page.getByRole('button', { name: 'Save changes' }).click();
    await page.getByRole('button', { name: 'Delete topic' }).click();
    await expect(page.locator('.subject-list a').filter({ hasText: topicTitle })).toHaveCount(0);
  } finally {
    if (createdTopicHref) {
      await page.goto(createdTopicHref).catch(() => {});
      const deleteTopic = page.getByRole('button', { name: 'Delete topic' });
      if (await deleteTopic.count().catch(() => 0)) {
        await deleteTopic.click().catch(() => {});
      }
    }
  }
});
