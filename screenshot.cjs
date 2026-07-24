const puppeteer = require('puppeteer');
(async () => {
    const browser = await puppeteer.launch({
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });
    const page = await browser.newPage();
    
    console.log("Navigating to login...");
    await page.goto('http://127.0.0.1:8000/login');
    
    console.log("Waiting for inputs...");
    await page.waitForSelector('#nik');
    await page.waitForSelector('#password');
    
    console.log("Typing login info...");
    await page.click('#nik');
    await page.type('#nik', '3204461404040001', { delay: 50 });
    await page.click('#password');
    await page.type('#password', 'hamidun123', { delay: 50 });
    
    console.log("Clicking submit...");
    await Promise.all([
        page.click('button[type="submit"]'),
        page.waitForNavigation({ waitUntil: 'networkidle0' })
    ]);
    
    console.log("Navigating to user management...");
    await page.goto('http://127.0.0.1:8000/admin/users');
    
    console.log("Waiting 2s...");
    await new Promise(r => setTimeout(r, 2000));
    
    // User Viewport screenshot (1252px)
    console.log("Setting 1252px viewport...");
    await page.setViewport({ width: 1252, height: 1200 });
    console.log("Taking 1252px screenshot...");
    await page.screenshot({ path: 'screenshot_1252.png' });
    
    // 1024px Viewport screenshot
    console.log("Setting 1024px viewport...");
    await page.setViewport({ width: 1024, height: 1500 });
    console.log("Taking 1024px screenshot...");
    await page.screenshot({ path: 'screenshot_1024.png' });
    
    console.log("Done!");
    await browser.close();
})();
