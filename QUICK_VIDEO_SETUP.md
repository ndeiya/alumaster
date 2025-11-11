# Quick Video Setup Guide

## 3-Step Setup

### Step 1: Run Database Update
```bash
php database/add_hero_video.php
```

### Step 2: Add Video in Admin
1. Go to: **Admin Panel → Pages → Homepage**
2. Find **Hero Section**
3. Check **"Enable Video Background"**
4. Select video type: **YouTube** or **Vimeo**
5. Paste video URL (e.g., `https://www.youtube.com/watch?v=VIDEO_ID`)
6. Check **"Autoplay Video (muted)"** (recommended)
7. Click **"Update Section"**

### Step 3: View Your Site
Visit your homepage to see the video in action!

---

## Supported URLs

### YouTube
- `https://www.youtube.com/watch?v=VIDEO_ID`
- `https://youtu.be/VIDEO_ID`

### Vimeo
- `https://vimeo.com/VIDEO_ID`

---

## Tips

✅ **Use HD videos** (1080p+) for best quality  
✅ **Keep it short** (30-60 seconds ideal)  
✅ **Enable autoplay** for immediate impact  
✅ **Test on mobile** to ensure responsive display  
✅ **Fallback image** shows when video is disabled  

---

## Troubleshooting

**Video not showing?**
- Verify URL is correct
- Check "Enable Video" is checked
- Ensure database was updated

**Autoplay not working?**
- Must be muted (browser requirement)
- Check autoplay checkbox is enabled

---

For detailed documentation, see: `VIDEO_EMBEDDING_GUIDE.md`
