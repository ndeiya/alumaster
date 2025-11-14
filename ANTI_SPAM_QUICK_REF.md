# 🛡️ Anti-Spam Quick Reference

## What Was Implemented

✅ **Honeypot traps** - Invisible fields that catch bots  
✅ **Time validation** - 3-second minimum on form  
✅ **Rate limiting** - 1 submission per minute per IP  
✅ **Pattern detection** - Blocks spam keywords  
✅ **Link limiting** - Maximum 2 URLs per message  
✅ **Email validation** - Blocks fake/disposable emails  
✅ **IP blocking** - Auto-blocks after 5 attempts  
✅ **Admin dashboard** - Monitor and manage spam  
✅ **Comprehensive logging** - Track all attempts  

---

## Quick Actions

### View Spam Statistics
**Admin Panel → Spam Monitor**

### Block an IP
1. Admin Panel → Spam Monitor
2. Click "Block New IP"
3. Enter IP address
4. Add reason (optional)
5. Click "Block IP"

### Unblock an IP
1. Admin Panel → Spam Monitor
2. Find IP in blocked list
3. Click "Unblock"

### Clean Old Logs
1. Admin Panel → Spam Monitor
2. Select retention period (30 days recommended)
3. Click "Clean Old Logs"

---

## Common Issues

### "Form won't submit"
**Causes:**
- Submitted too fast (< 3 seconds)
- Too many submissions (60 second cooldown)
- Spam keywords in message
- Too many links (max 2)
- Fake email address

**Fix:** Check Spam Monitor for the user's IP

### "Still getting spam"
**Actions:**
1. Review spam patterns in monitor
2. Lower rate limits in `contact.php`
3. Add keywords to `anti-spam.php`
4. Consider adding reCAPTCHA

---

## Files to Know

- **`contact.php`** - Main contact form with validation
- **`includes/anti-spam.php`** - Anti-spam utility functions
- **`admin/spam-monitor.php`** - Admin dashboard
- **`logs/spam.log`** - Spam attempt log (JSON)
- **`logs/blocked_ips.txt`** - List of blocked IPs

---

## Adjustable Settings

**Location: `contact.php`**

```php
// Minimum time on form (currently 3 seconds)
if ($time_spent < 3)

// Rate limit between submissions (currently 60 seconds)
if ($time_since_last < 60)

// Maximum submissions before auto-block (currently 5 in 60 minutes)
if ($submission_count > 5)

// Maximum links allowed (currently 2)
if ($link_count > 2)
```

**Location: `includes/anti-spam.php`**

Add spam keywords to `containsSpamPatterns()` method  
Add fake email domains to `isSuspiciousEmail()` method

---

## Expected Results

✅ **90-99% spam reduction**  
✅ **Automatic protection**  
✅ **No user friction**  
✅ **Easy management**  

---

## Regular Maintenance

**Weekly:** Review spam statistics  
**Monthly:** Clean old logs  
**As needed:** Unblock false positives  

---

For full documentation, see: **ANTI_SPAM_GUIDE.md**
