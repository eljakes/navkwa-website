# navkwa-prod-01

Production server inventory for the Navkwa production environment.

## Server

| Field | Value |
| --- | --- |
| Provider | Hetzner Cloud |
| Hetzner project | Navkwa Production |
| Server name | navkwa-prod-01 |
| Public IPv4 | 49.12.103.75 |
| Location | Falkenstein, Germany |
| Region | eu-central |
| Plan | CPX22 |
| Architecture | x86 |
| Disk | 80 GB |
| Operating system | Ubuntu 24.04 LTS |

## Production Domains

| Hostname | Target | Purpose |
| --- | --- | --- |
| navkwa.com | 49.12.103.75 | Main website |
| www.navkwa.com | 49.12.103.75 | Main website alias |
| build.navkwa.com | 49.12.103.75 | Future Navkwa Build app |
| api.navkwa.com | 49.12.103.75 | Future Navkwa Build API |
| console.navkwa.com | 49.12.103.75 | Future Navkwa cloud console |

## Recommended DNS Records

Configure these in Cloudflare after the domain nameservers point to Cloudflare:

| Type | Name | Content | Proxy |
| --- | --- | --- | --- |
| A | @ | 49.12.103.75 | Proxied |
| CNAME | www | navkwa.com | Proxied |
| A | build | 49.12.103.75 | Proxied when ready |
| A | api | 49.12.103.75 | Proxied when ready |
| A | console | 49.12.103.75 | Proxied when ready |

## GitHub Actions Secrets

When CI/CD is enabled, add these repository secrets in GitHub:

| Secret | Suggested value |
| --- | --- |
| PRODUCTION_HOST | 49.12.103.75 |
| PRODUCTION_USER | deploy |
| PRODUCTION_SSH_KEY | Private SSH key for the deploy user |

Do not commit SSH private keys, passwords, API tokens, database credentials, Cloudflare tokens, or payment gateway secrets.

## Server Paths

```text
/var/www/navkwa-website/current
/var/www/navkwa-website/releases
/var/www/navkwa-website/shared/.env
/var/www/navkwa-website/shared/storage
/var/www/navkwa-website/shared/logs

/var/www/navkwa-build/current
/var/www/navkwa-build/releases
/var/www/navkwa-build/shared/.env
/var/www/navkwa-build/shared/storage
/var/www/navkwa-build/shared/logs
```

## First SSH Check

Use the deployment user once it exists:

```bash
ssh deploy@49.12.103.75
hostnamectl
timedatectl
```

Expected hostname:

```text
navkwa-prod-01
```
