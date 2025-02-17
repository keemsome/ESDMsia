const puppeteer = require('puppeteer');

async function generatePdf() {
  const browser = await puppeteer.launch({
    args: [
      '--allow-file-access-from-files',
      '--disable-web-security',
      '--disable-features=Isolate origins,site-per-process',
    ]
  });
  const page = await browser.newPage();
  await page.setViewport({width: 1280, height: 720});
  try {
    await page.goto('file:///Users/keemyap/Documents/clinetest/index.html', {waitUntil: 'networkidle0'});
    await page.pdf({ path: 'website.pdf', format: 'A4' });
    console.log('PDF generated successfully!');
  } catch (error) {
    console.error('Error generating PDF:', error);
  }

  await browser.close();
}

generatePdf();
