import { defineConfig } from '@playwright/test';

const baseURL = (process.env.TALAS_BASE_URL ?? 'http://localhost:8080/index.php').replace(/\/+$/, '') + '/';

export default defineConfig({
  testDir: './tests/e2e',
  fullyParallel: false,
  reporter: 'list',
  use: {
    baseURL,
    browserName: 'chromium',
    screenshot: 'only-on-failure',
    trace: 'retain-on-failure',
  },
  outputDir: 'test-results',
});
