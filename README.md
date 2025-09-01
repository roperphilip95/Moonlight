# Moonlight PRO (Frontend + Admin + Waiter)

## Install (cPanel)
1. Upload repo ZIP to `public_html` and **Extract**.
2. Create MySQL DB + user, assign **All Privileges**.
3. Visit `https://yourdomain/install/` → enter DB + admin email/password → **Install**.
4. Admin: `https://yourdomain/admin/login.php`
5. Waiter: `https://yourdomain/waiter/` (use a user with **Waiter** role)

## Features
- **Menu** with daily prices
- **POS** (waiter creates orders; cash/transfer/card/paystack; cash needs mark-as-paid)
- **Finance**: income, expenses, sales totals, date filters
- **HR**: attendance & leave approvals
- **Blog/Events**: simple publishing
- **Livestream**: manage YouTube/Facebook/Instagram links
- **Customizer**: site name, WhatsApp phone, Paystack keys

## Notes
- Set **Paystack keys** in Admin → Customizer. Demo return handler marks paid on success (add real API verification for production).
- Anti-theft basics: orders record payment methods; cash requires confirmation; audit tables available for extension.
- To re-run installer: delete `lib/config.php` and revisit `/install/`.
