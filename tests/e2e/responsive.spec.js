import { test, expect } from '@playwright/test';

const viewportWidths = [320, 400, 768, 1280];
const email = process.env.TALAS_E2E_EMAIL;
const password = process.env.TALAS_E2E_PASSWORD;

async function expectNoHorizontalOverflow(page, route) {
  const dimensions = await page.evaluate(() => ({
    viewport: document.documentElement.clientWidth,
    content: document.documentElement.scrollWidth,
  }));

  expect(dimensions.content, `${route} should fit within its ${dimensions.viewport}px viewport`).toBeLessThanOrEqual(dimensions.viewport);
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
  test.skip(!email || !password, 'Set TALAS_E2E_EMAIL and TALAS_E2E_PASSWORD to a synthetic student account to run authenticated page checks.');

  await page.setViewportSize({ width: 400, height: 850 });
  await page.goto('login');
  await page.getByLabel('Email').fill(email);
  await page.getByLabel('Password').fill(password);
  await page.getByRole('button', { name: 'Login' }).click();
  await page.waitForURL(
    url => !url.pathname.endsWith('/login'),
    { timeout: 10_000, waitUntil: 'domcontentloaded' },
  );

  for (const width of viewportWidths) {
    await page.setViewportSize({ width, height: 850 });

    await page.goto('student');
    await expect(page.getByRole('heading', { name: 'Your Talas workspace' })).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Study ideas' })).toBeVisible();
    await expect(page.getByRole('link', { name: 'What Works Clearinghouse practice guide (2007)' })).toHaveCount(4);
    await expectNoHorizontalOverflow(page, '/student');

    await page.goto('student/subjects');
    await expect(page.getByRole('heading', { name: 'Your subjects' })).toBeVisible();
    await expectNoHorizontalOverflow(page, '/student/subjects');

    await page.goto('student/topics');
    await expect(page.getByRole('heading', { name: 'Search topics' })).toBeVisible();
    await expect(page.getByLabel('Search')).toBeVisible();
    await expect(page.getByLabel('Importance')).toBeVisible();
    await expect(page.getByLabel('Progress')).toBeVisible();
    await expect(page.getByLabel('Due date')).toBeVisible();
    await expectNoHorizontalOverflow(page, '/student/topics');

    await page.goto('student/priority');
    await expect(page.getByRole('heading', { name: 'Priority topics' })).toBeVisible();
    await expectNoHorizontalOverflow(page, '/student/priority');

    await page.goto('student/sessions');
    await expect(page.getByRole('heading', { name: 'Study sessions' })).toBeVisible();
    await expectNoHorizontalOverflow(page, '/student/sessions');

    await page.goto('student/aralytics');
    await expect(page.getByRole('heading', { name: 'Aralytics' })).toBeVisible();
    await expect(page.getByRole('button', { name: 'Print report' })).toBeVisible();
    await expectNoHorizontalOverflow(page, '/student/aralytics');
  }

  await page.goto('student');
  for (let tab = 0; tab < 10; tab++) {
    await page.keyboard.press('Tab');
  }
  await expect(page.locator('#study-ideas-heading').locator('..').getByRole('link').first()).toBeFocused();

  await page.goto('student/aralytics');
  await page.getByRole('link', { name: 'Last 30 days' }).click();
  await expect(page.getByRole('heading', { name: 'Study activity: Last 30 days' })).toBeVisible();
  await page.getByRole('link', { name: 'All time' }).click();
  await expect(page.getByRole('heading', { name: 'Study activity: All time' })).toBeVisible();

  await page.goto('student/aralytics');
  for (let tab = 0; tab < 13; tab++) {
    await page.keyboard.press('Tab');
  }
  await expect(page.getByRole('button', { name: 'Print report' })).toBeFocused();
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
