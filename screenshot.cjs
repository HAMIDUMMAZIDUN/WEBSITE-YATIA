const puppeteer = require('puppeteer');
(async () => {
    const browser = await puppeteer.launch();
    const page = await browser.newPage();
    await page.setViewport({ width: 1280, height: 800 });
    await page.goto('http://localhost/APLIKASI/SIMSYATIA/public/login');
    
    // Login
    await page.type('#email', 'superadmin@gmail.com');
    await page.type('#password', 'password');
    await page.click('button[type="submit"]');
    await page.waitForNavigation();

    // Go to another page to see if sidebar covers it
    await page.goto('http://localhost/APLIKASI/SIMSYATIA/public/admin/kelas');
    await page.screenshot({ path: 'screenshot.png' });
    
    await browser.close();
})();
