# Admin Interface Preview - Video Settings

## What You'll See in Admin Panel

When you navigate to **Admin → Pages → Homepage**, the Hero Section will now include:

```
┌─────────────────────────────────────────────────────────┐
│ Hero Section                              [Active]      │
├─────────────────────────────────────────────────────────┤
│                                                          │
│ Title                                                    │
│ ┌────────────────────────────────────────────────────┐ │
│ │ Where Quality                                       │ │
│ └────────────────────────────────────────────────────┘ │
│                                                          │
│ Highlight Text                                           │
│ ┌────────────────────────────────────────────────────┐ │
│ │ Meets Affordability                                 │ │
│ └────────────────────────────────────────────────────┘ │
│                                                          │
│ Description                                              │
│ ┌────────────────────────────────────────────────────┐ │
│ │ Professional aluminum and glass solutions...        │ │
│ │                                                      │ │
│ └────────────────────────────────────────────────────┘ │
│                                                          │
│ Primary Button Text                                      │
│ ┌────────────────────────────────────────────────────┐ │
│ │ Get a Quote                                         │ │
│ └────────────────────────────────────────────────────┘ │
│                                                          │
│ Primary Button Link                                      │
│ ┌────────────────────────────────────────────────────┐ │
│ │ contact.php                                         │ │
│ └────────────────────────────────────────────────────┘ │
│                                                          │
│ Secondary Button Text                                    │
│ ┌────────────────────────────────────────────────────┐ │
│ │ View Projects                                       │ │
│ └────────────────────────────────────────────────────┘ │
│                                                          │
│ Secondary Button Link                                    │
│ ┌────────────────────────────────────────────────────┐ │
│ │ projects.php                                        │ │
│ └────────────────────────────────────────────────────┘ │
│                                                          │
│ Background Image                                         │
│ ┌────────────────────────────────────────────────────┐ │
│ │ assets/images/hero-bg.jpg                           │ │
│ └────────────────────────────────────────────────────┘ │
│ Path to background image (shown when video is disabled) │
│                                                          │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│ Video Settings                                           │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│                                                          │
│ ☑ Enable Video Background                               │
│                                                          │
│ Video Type                                               │
│ ┌────────────────────────────────────────────────────┐ │
│ │ YouTube                              ▼              │ │
│ └────────────────────────────────────────────────────┘ │
│                                                          │
│ Video URL                                                │
│ ┌────────────────────────────────────────────────────┐ │
│ │ https://www.youtube.com/watch?v=VIDEO_ID            │ │
│ └────────────────────────────────────────────────────┘ │
│ Full YouTube or Vimeo URL                               │
│                                                          │
│ ☑ Autoplay Video (muted)                                │
│ Video will autoplay muted for better UX                 │
│                                                          │
│ ┌──────────────────┐  ┌──────────────────┐            │
│ │ Update Section   │  │ Preview          │            │
│ └──────────────────┘  └──────────────────┘            │
└─────────────────────────────────────────────────────────┘
```

## Field Descriptions

### Video Settings Section

| Field | Description | Example |
|-------|-------------|---------|
| **Enable Video Background** | Checkbox to turn video on/off | ☑ Checked = Video shows |
| **Video Type** | Dropdown: YouTube or Vimeo | YouTube |
| **Video URL** | Full URL of your video | `https://www.youtube.com/watch?v=dQw4w9WgXcQ` |
| **Autoplay Video** | Auto-start video (muted) | ☑ Recommended |

## How It Works

1. **When Video is Enabled** (checkbox checked):
   - Video displays in hero section
   - Background image is hidden
   - Video autoplays if autoplay is checked
   - Video is muted for browser compatibility

2. **When Video is Disabled** (checkbox unchecked):
   - Background image displays instead
   - Video settings are saved but not used
   - Fallback to traditional image hero

## Example Configurations

### Configuration 1: YouTube Autoplay
```
☑ Enable Video Background
Video Type: YouTube
Video URL: https://www.youtube.com/watch?v=abc123
☑ Autoplay Video (muted)
```
**Result**: YouTube video plays automatically (muted) in hero section

### Configuration 2: Vimeo Manual Play
```
☑ Enable Video Background
Video Type: Vimeo
Video URL: https://vimeo.com/123456789
☐ Autoplay Video (muted)
```
**Result**: Vimeo video shows with play button, user must click to play

### Configuration 3: Image Only (No Video)
```
☐ Enable Video Background
Video Type: YouTube
Video URL: (any URL)
☐ Autoplay Video (muted)
```
**Result**: Background image displays, video is ignored

## Visual Result on Frontend

### Desktop View (with video):
```
┌─────────────────────────────────────────────────────────────┐
│                                                              │
│  ┌──────────────────┐    ┌──────────────────────────────┐  │
│  │                   │    │                               │  │
│  │  Where Quality    │    │   [VIDEO PLAYING]            │  │
│  │  Meets            │    │                               │  │
│  │  Affordability    │    │   Professional aluminum      │  │
│  │                   │    │   installation footage       │  │
│  │  Professional     │    │                               │  │
│  │  aluminum and     │    │   [Rounded corners]          │  │
│  │  glass solutions  │    │   [Subtle overlay]           │  │
│  │                   │    │                               │  │
│  │  [Get a Quote]    │    │                               │  │
│  │  [View Projects]  │    │                               │  │
│  │                   │    │                               │  │
│  └──────────────────┘    └──────────────────────────────┘  │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Mobile View (with video):
```
┌──────────────────────────┐
│                           │
│  Where Quality            │
│  Meets Affordability      │
│                           │
│  Professional aluminum    │
│  and glass solutions      │
│                           │
│  [Get a Quote]            │
│  [View Projects]          │
│                           │
│  ┌────────────────────┐  │
│  │                     │  │
│  │  [VIDEO PLAYING]   │  │
│  │                     │  │
│  │  Responsive video  │  │
│  │  with adjusted     │  │
│  │  aspect ratio      │  │
│  │                     │  │
│  └────────────────────┘  │
│                           │
└──────────────────────────┘
```

## Benefits of This Implementation

✅ **Professional Appearance**: Modern video backgrounds enhance credibility  
✅ **Easy Management**: No code editing required  
✅ **Flexible**: Switch between video and image anytime  
✅ **Performance**: Uses YouTube/Vimeo hosting (no server load)  
✅ **Responsive**: Works perfectly on all devices  
✅ **SEO Friendly**: Text content remains accessible  
✅ **User-Friendly**: Autoplay with mute for better UX  

## Best Video Content Ideas

For your aluminum and glass business:

1. **Time-lapse Installation** - Show a project from start to finish
2. **Showcase Reel** - Highlight multiple completed projects
3. **Team at Work** - Professional crew installing aluminum/glass
4. **Before/After** - Transformation of buildings with your work
5. **Product Highlights** - Close-ups of quality materials and finishes
6. **Client Testimonials** - Video reviews from satisfied customers
7. **Factory/Workshop Tour** - Behind-the-scenes of your operations

## Technical Details

- **Aspect Ratio**: 16:9 (standard widescreen)
- **Mobile Aspect**: 4:3 (adjusted for mobile screens)
- **Autoplay**: Muted (browser requirement)
- **Loading**: Lazy-loaded for performance
- **Fallback**: Background image when video disabled
- **Styling**: Rounded corners, shadow, overlay gradient
