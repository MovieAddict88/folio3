import os
from playwright.sync_api import sync_playwright

def run(playwright):
    browser = playwright.chromium.launch()
    page = browser.new_page()

    # Get the absolute path to the mockups directory
    base_path = os.path.abspath('mockups')

    # Screenshot Login Page
    page.goto(f"file://{base_path}/login.html")
    page.screenshot(path="jules-scratch/verification/login.png")
    print("Took screenshot of login.html")

    # Screenshot User Dashboard
    page.goto(f"file://{base_path}/user_dashboard.html")
    page.screenshot(path="jules-scratch/verification/user_dashboard.png")
    print("Took screenshot of user_dashboard.html")

    # Screenshot Admin Products Page
    page.goto(f"file://{base_path}/admin_products.html")
    page.screenshot(path="jules-scratch/verification/admin_products.png")
    print("Took screenshot of admin_products.html")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)