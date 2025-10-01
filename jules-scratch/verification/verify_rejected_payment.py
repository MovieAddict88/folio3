import re
from playwright.sync_api import sync_playwright, expect

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    # Log in
    page.goto("http://127.0.0.1:8000/login.php")
    page.get_by_label("Username").fill("testuser")
    page.get_by_label("Password").fill("password")
    page.get_by_role("button", name="Login").click()

    # Go to dashboard
    page.goto("http://127.0.0.1:8000/dashboard.php")

    # Find the cell with "Rejected" status, then find its parent row, then the link.
    rejected_cell = page.get_by_text("Rejected", exact=True)
    invoice_row = rejected_cell.locator("xpath=./ancestor::tr")
    # The "Pay Balance" is an anchor tag styled as a button
    pay_link = invoice_row.get_by_role("link", name="Pay Balance")

    # Click the "Pay Balance" link
    pay_link.click()

    # On the payment page, select a payment method and proceed
    expect(page).to_have_url(re.compile(r".*payment\.php\?id=\d+"))
    # The payment methods are radio buttons inside labels
    page.get_by_label("GCASH").check()
    page.get_by_role("button", name="Proceed to Payment Gateway").click()

    # Verify that we are on the process_payment page
    expect(page).to_have_url(re.compile(r".*process_payment\.php.*"))
    expect(page.get_by_text("Scan to Pay")).to_be_visible()

    # Take a screenshot
    page.screenshot(path="jules-scratch/verification/verification.png")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)