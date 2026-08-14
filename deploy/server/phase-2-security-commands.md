# Phase 2 Security Commands: navkwa-prod-01

Run these on the Hetzner server after the first SSH login.

Server:

```text
Hostname: navkwa-prod-01
Public IP: 49.12.103.75
OS: Ubuntu 24.04 LTS
```

## 1. Update Ubuntu

What: refresh package lists and install available patches.

Why: new servers can already have updates waiting.

```bash
sudo apt update
sudo apt upgrade -y
```

Verify:

```bash
sudo apt list --upgradable
```

## 2. Set Hostname

What: set the server's system name.

Why: logs, alerts, and future monitoring should identify the machine clearly.

```bash
sudo hostnamectl set-hostname navkwa-prod-01
hostnamectl
```

Verify:

```bash
hostname
```

Expected:

```text
navkwa-prod-01
```

## 3. Create Deploy User

What: a non-root user for deployments and administration.

Why: root should not be the normal day-to-day login.

```bash
sudo adduser deploy
sudo usermod -aG sudo deploy
```

Copy your SSH key to the deploy user:

```bash
sudo mkdir -p /home/deploy/.ssh
sudo cp ~/.ssh/authorized_keys /home/deploy/.ssh/authorized_keys
sudo chown -R deploy:deploy /home/deploy/.ssh
sudo chmod 700 /home/deploy/.ssh
sudo chmod 600 /home/deploy/.ssh/authorized_keys
```

Verify from your laptop:

```bash
ssh deploy@49.12.103.75
```

## 4. Enable UFW

What: allow only SSH, HTTP, and HTTPS.

Why: unused open ports should not be public.

```bash
sudo apt install -y ufw
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow OpenSSH
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
sudo ufw status verbose
```

## 5. Harden SSH

What: disable root login and password login.

Why: force key-based access and reduce brute-force risk.

Create `/etc/ssh/sshd_config.d/navkwa.conf`:

```text
PermitRootLogin no
PasswordAuthentication no
PubkeyAuthentication yes
MaxAuthTries 3
LoginGraceTime 30
AllowUsers deploy
```

Apply:

```bash
sudo sshd -t
sudo systemctl reload ssh
```

Important: keep your current SSH session open, then test a new SSH session before closing it.

## 6. Enable Automatic Security Updates

What: install security updates automatically.

Why: critical patches should not wait for manual work.

```bash
sudo apt install -y unattended-upgrades
sudo dpkg-reconfigure --priority=low unattended-upgrades
systemctl status unattended-upgrades
```

## 7. Install Fail2Ban

What: block repeated failed login attempts.

Why: reduce brute-force and noisy attack traffic.

```bash
sudo apt install -y fail2ban
sudo tee /etc/fail2ban/jail.local >/dev/null <<'EOF'
[DEFAULT]
bantime = 1h
findtime = 10m
maxretry = 5
backend = systemd

[sshd]
enabled = true
port = ssh
logpath = %(sshd_log)s
EOF
sudo systemctl enable --now fail2ban
sudo fail2ban-client status
```

After Nginx is installed, extend this file with the web protections from `deploy/fail2ban/jail.local.example`.

## 8. Verify Time Sync

What: confirm accurate server time.

Why: SSL, logs, queues, scheduler, and payment callbacks depend on correct time.

```bash
timedatectl
```

Expected:

```text
System clock synchronized: yes
```

## 9. Final Phase 2 Check

```bash
hostname
sudo ufw status verbose
sudo systemctl status ssh --no-pager
sudo systemctl status unattended-upgrades --no-pager
sudo systemctl status fail2ban --no-pager
timedatectl
```
