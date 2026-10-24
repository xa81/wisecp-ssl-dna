# Changelog

## 1.0.1 — 2026-10-24

- WISECP Marketplace manifest (product 207), so marketplace installs and updates are recognised.

## 1.0.0 — 2026-10-24

First release.

- WISECP 5 product module `DNASSL` for Domain Name API SSL certificates, built on the
  core SSL framework.
- Catalogue import as products and daily cost sync, priced from the reseller's price
  group; duplicate product codes collapsed.
- Order opened and configured from the order form (CSR, validation method, approver
  e-mail, additional domains up to the package limit).
- Standard SSL client dashboard with the exact validation file or CNAME record, derived
  from the CSR; certificate download and reissue.
- Renewal as a new order, import of existing certificates by domain, optional revoke on
  cancellation (packages that cannot be revoked do not block it).
- API key and private keys masked in every log line; TLS verified on every request.
- Requests Cloudflare could not deliver to the gateway (521/522/523/525/526) are retried.
- Catalogue tab shows prices in the catalogue currency and explains an empty catalogue.
- SSL dashboard shows the real key size and signature algorithm.
