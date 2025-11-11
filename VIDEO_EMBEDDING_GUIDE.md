# Hero Section Video Embedding - Implementation Guide

## Overview

Video embedding has been successfully implemented in the hero section of your AluMaster website. This feature allows you to display professional YouTube or Vimeo videos as a background in the hero section, enhancing visual appeal and engagement.

## Features Implemented

### 1. **Admin Interface Updates**
- ✅ Video enable/disable toggle
- ✅ Video type selection (YouTube or Vimeo)
- ✅ Video URL input field
- ✅ Autoplay option (muted for better UX)
- ✅ Fallback to background image when video is disabled

### 2. **Frontend Display**
- ✅ Responsive video player with 16:9 aspect ratio
- ✅ Automatic video ID extraction from URLs
- ✅ Professional styling with rounded corners and shadows
- ✅ Subtle overlay for better text readability
- ✅ Mobile-optimized display

### 3. **Supported Video Platforms**
- **YouTube**: Full support with autoplay, loop, and muted options
- **Vimeo**: Full support with autoplay, loop, and muted options

## Setup Instructions

### Step 1: Update Database

Run the database update script to add video fields to the hero section:

```bash
php database/add_hero_video.php
```

This will add the following fields to your hero section:
- `video_url`: The URL of the video
- `video_type`: Either 'youtube' or 'vimeo'
- `show_video`: Boolean to enable/disable video
- `video_autoplay`: Boolean for autoplay (muted)

### Step 2: Configure Video in Admin Panel

1. **Log into Admin Panel**
   - Navigate to: `admin/pages/homepage.php`

2. **Locate Hero Section**
   - Scroll to the "Hero Section" card

3. **Enable Video**
   - Check the "Enable Video Background" checkbox

4. **Select Video Type**
   - Choose either "YouTube" or "Vimeo" from the dropdown

5. **Enter Video URL**
   - **YouTube Example**: `https://www.youtube.com/watch?v=dQw4w9WgXcQ`
   - **Vimeo Example**: `https://vimeo.com/123456789`

6. **Configure Autoplay**
   - Check "Autoplay Video (muted)" for automatic playback
   - Video will be muted to comply with browser autoplay policies

7. **Save Changes**
   - Click "Update Section" button

### Step 3: Test the Implementation

1. Visit your homepage: `index.php`
2. The video should appear in the hero section
3. Test on mobile devices for responsive behavior
4. Verify autoplay works (video should be muted)

## Video URL Formats Supported

### YouTube URLs:
- `https://www.youtube.com/watch?v=VIDEO_ID`
- `https://youtu.be/VIDEO_ID`
- `https://www.youtube.com/embed/VIDEO_ID`

### Vimeo URLs:
- `https://vimeo.com/VIDEO_ID`
- `https://player.vimeo.com/video/VIDEO_ID`

## Best Practices

### 1. **Video Selection**
- ✅ Use high-quality videos (1080p or higher)
- ✅ Keep videos between 30-60 seconds for hero sections
- ✅ Choose videos with good lighting and professional quality
- ✅ Ensure video content is relevant to your brand

### 2. **Performance Optimization**
- ✅ Videos are lazy-loaded for better performance
- ✅ Use YouTube/Vimeo hosting (don't self-host large files)
- ✅ Enable autoplay with mute for better UX
- ✅ Provide fallback image for when video is disabled

### 3. **Accessibility**
- ✅ Always provide alternative content (text overlay)
- ✅ Ensure text is readable over video (overlay gradient included)
- ✅ Don't rely solely on video for important information

### 4. **Mobile Considerations**
- ✅ Video aspect ratio adjusts for mobile (75% padding)
- ✅ Rounded corners scale appropriately
- ✅ Video loads efficiently on mobile connections

## Styling Customization

### Video Container Styling
Located in `assets/css/style.css`:

```css
.hero-video-container {
    border-radius: var(--radius-2xl); /* Adjust roundness */
    box-shadow: var(--shadow-xl);     /* Adjust shadow */
}
```

### Video Overlay
Customize the overlay gradient for better text readability:

```css
.video-overlay {
    background: linear-gradient(
        135deg,
        rgba(59, 130, 246, 0.1) 0%,    /* Blue tint */
        rgba(26, 32, 44, 0.2) 100%     /* Dark overlay */
    );
}
```

### Aspect Ratio
Adjust video dimensions:

```css
.video-wrapper {
    padding-bottom: 56.25%; /* 16:9 ratio */
    /* Use 75% for 4:3 ratio */
    /* Use 100% for 1:1 ratio */
}
```

## Troubleshooting

### Video Not Displaying
1. **Check video URL**: Ensure it's a valid YouTube or Vimeo URL
2. **Verify "Enable Video" is checked**: In admin panel
3. **Check browser console**: Look for iframe errors
4. **Test video URL**: Open the URL directly in browser

### Autoplay Not Working
1. **Mute is required**: Browsers block unmuted autoplay
2. **Check autoplay checkbox**: Must be enabled in admin
3. **Browser restrictions**: Some browsers block all autoplay

### Video Quality Issues
1. **Use HD videos**: Upload 1080p or higher to YouTube/Vimeo
2. **Check source quality**: Ensure original video is high quality
3. **Network speed**: Slow connections may load lower quality

### Mobile Display Issues
1. **Test on real devices**: Emulators may not show true behavior
2. **Check aspect ratio**: Mobile uses 75% padding by default
3. **Network considerations**: Mobile may load lower quality

## Privacy & GDPR Compliance

### YouTube Privacy Mode
To use YouTube's privacy-enhanced mode, update the embed URL in `index.php`:

```php
$embed_url = "https://www.youtube-nocookie.com/embed/{$video_id}?...";
```

### Vimeo Privacy
Vimeo videos respect privacy settings from your Vimeo account.

## Advanced Features (Optional)

### Add Play/Pause Button
Uncomment the play button overlay in CSS:

```css
.video-play-overlay {
    /* Already styled in CSS */
}
```

Add JavaScript to control playback:

```javascript
document.querySelector('.video-play-overlay').addEventListener('click', function() {
    // Toggle video playback
});
```

### Add Video Controls
Modify the embed URL to show/hide controls:

```php
// Show controls
$embed_url .= "&controls=1";

// Hide controls
$embed_url .= "&controls=0";
```

## Files Modified

1. **admin/pages/homepage.php** - Added video input fields
2. **index.php** - Added video rendering logic
3. **assets/css/style.css** - Added video styling
4. **database/add_hero_video.php** - Database update script

## Example Configuration

Here's a complete example of hero section with video:

```json
{
  "title": "Where Quality",
  "highlight": "Meets Affordability",
  "description": "Professional aluminum and glass solutions in Ghana",
  "primary_button_text": "Get a Quote",
  "primary_button_link": "contact.php",
  "secondary_button_text": "View Projects",
  "secondary_button_link": "projects.php",
  "background_image": "assets/images/hero-bg.jpg",
  "video_url": "https://www.youtube.com/watch?v=YOUR_VIDEO_ID",
  "video_type": "youtube",
  "show_video": true,
  "video_autoplay": true
}
```

## Support & Maintenance

### Regular Checks
- ✅ Verify video URLs are still active
- ✅ Test on different browsers and devices
- ✅ Monitor page load performance
- ✅ Update video content periodically

### Performance Monitoring
- Check page load times with video enabled
- Monitor bandwidth usage
- Test on slow connections
- Optimize video settings if needed

## Next Steps

1. **Create Professional Video Content**
   - Film your aluminum/glass installations
   - Show before/after transformations
   - Highlight your team at work
   - Showcase completed projects

2. **Upload to YouTube/Vimeo**
   - Create a branded channel
   - Optimize video titles and descriptions
   - Add relevant tags for SEO
   - Enable appropriate privacy settings

3. **Test Thoroughly**
   - Test on desktop browsers (Chrome, Firefox, Safari, Edge)
   - Test on mobile devices (iOS, Android)
   - Test with slow internet connections
   - Verify autoplay behavior

4. **Monitor Performance**
   - Use Google PageSpeed Insights
   - Check Core Web Vitals
   - Monitor user engagement
   - Adjust settings as needed

## Conclusion

Your hero section now supports professional video embedding with:
- ✅ Easy admin management
- ✅ Responsive design
- ✅ Professional styling
- ✅ Performance optimization
- ✅ Mobile-friendly display

The video feature enhances your website's visual appeal and helps showcase your aluminum and glass work in action, creating a more engaging experience for visitors.
