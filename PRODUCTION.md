# Production Domain & Tenancy Setup

## Domains
- Apex: `obhrm.com` (or your chosen apex)
- DNS records (point to your load balancer / server):
  - `obhrm.com` → A/AAAA to your server
  - `*.obhrm.com` → A/AAAA to your server (wildcard for tenants)

## Laravel Tenancy Config (env overrides)
Set these in `.env` for production:
- `TENANT_PRODUCTION_BASE_DOMAIN=obhrm.com`
- `TENANT_URL_SCHEME=https` (or `http` if not behind TLS)
- `TENANT_URL_PORT=` (leave blank unless you serve on a non-standard port)

Local/dev suggestions (optional, for subdomain-first hostnames):
- Use a wildcard-friendly base like `hrm.com` or `127.0.0.1.nip.io`.
- Example: `TENANT_LOCAL_BASE_DOMAIN=hrm.com`, `TENANT_LOCAL_PREFIX=` (empty), so `obseque.hrm.com:8000` works locally.

## Tenant Domains
- Each tenant must have a Domain record storing the full hostname you want to serve (e.g., `obseque.obhrm.com`).
- After DNS and env are in place, visiting that hostname will route to the tenant.

## Auth/Login Routing
- Fortify routes are scoped with tenancy middleware, so login is only reachable on tenant hosts (e.g., `https://obseque.obhrm.com/login`). Central `/login` will 404.

## Checklist
1) Set env vars above on production.
2) Create DNS apex + wildcard records.
3) Ensure each tenant has its domain stored (e.g., `obseque.obhrm.com`).
4) Deploy; hit `https://<tenant>.obhrm.com/login` → dashboard after auth.

## Notes
- If using a load balancer/ingress, forward `Host` header unchanged.
- For SSL, issue a wildcard cert for `*.obhrm.com` (and `obhrm.com`).
