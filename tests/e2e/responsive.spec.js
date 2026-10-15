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
  }
});
