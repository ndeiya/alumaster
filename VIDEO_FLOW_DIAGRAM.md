# Video Embedding - System Flow Diagram

## Complete System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                         ADMIN PANEL                              │
│                   (admin/pages/homepage.php)                     │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Admin enters video settings:                                   │
│  ┌────────────────────────────────────────────────────────┐    │
│  │ ☑ Enable Video Background                              │    │
│  │ Video Type: [YouTube ▼]                                │    │
│  │ Video URL: https://www.youtube.com/watch?v=abc123      │    │
│  │ ☑ Autoplay Video (muted)                               │    │
│  │                                                          │    │
│  │ [Update Section]                                        │    │
│  └────────────────────────────────────────────────────────┘    │
│                          │                                       │
│                          │ Form Submit (POST)                   │
│                          ▼                                       │
│  ┌────────────────────────────────────────────────────────┐    │
│  │ PHP Processing:                                         │    │
│  │ - Sanitize inputs                                       │    │
│  │ - Create JSON content                                   │    │
│  │ - Validate data                                         │    │
│  └────────────────────────────────────────────────────────┘    │
│                          │                                       │
└──────────────────────────┼───────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│                         DATABASE                                 │
│                   (homepage_sections table)                      │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  UPDATE homepage_sections                                       │
│  SET content = '{                                               │
│    "title": "Where Quality",                                    │
│    "highlight": "Meets Affordability",                          │
│    "video_url": "https://youtube.com/watch?v=abc123",          │
│    "video_type": "youtube",                                     │
│    "show_video": true,                                          │
│    "video_autoplay": true                                       │
│  }'                                                             │
│  WHERE section_key = 'hero'                                     │
│                                                                  │
└──────────────────────────┬───────────────────────────────────────┘
                           │
                           │ Data Stored
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│                      FRONTEND (index.php)                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  1. Query Database                                              │
│  ┌────────────────────────────────────────────────────────┐    │
│  │ SELECT * FROM homepage_sections                         │    │
│  │ WHERE section_key = 'hero'                              │    │
│  └────────────────────────────────────────────────────────┘    │
│                          │                                       │
│                          ▼                                       │
│  2. Parse JSON Content                                          │
│  ┌────────────────────────────────────────────────────────┐    │
│  │ $hero = json_decode($content, true);                    │    │
│  │ $video_url = $hero['video_url'];                        │    │
│  │ $show_video = $hero['show_video'];                      │    │
│  └────────────────────────────────────────────────────────┘    │
│                          │                                       │
│                          ▼                                       │
│  3. Check if Video Enabled                                      │
│  ┌────────────────────────────────────────────────────────┐    │
│  │ if ($show_video && !empty($video_url)) {                │    │
│  │     // Show video                                       │    │
│  │ } else {                                                │    │
│  │     // Show image                                       │    │
│  │ }                                                       │    │
│  └────────────────────────────────────────────────────────┘    │
│                          │                                       │
│                          ▼                                       │
│  4. Extract Video ID                                            │
│  ┌────────────────────────────────────────────────────────┐    │
│  │ YouTube: Extract ID from URL                            │    │
│  │ https://youtube.com/watch?v=abc123 → abc123            │    │
│  │                                                          │    │
│  │ Vimeo: Extract ID from URL                              │    │
│  │ https://vimeo.com/123456789 → 123456789                │    │
│  └────────────────────────────────────────────────────────┘    │
│                          │                                       │
│                          ▼                                       │
│  5. Build Embed URL                                             │
│  ┌────────────────────────────────────────────────────────┐    │
│  │ YouTube:                                                │    │
│  │ https://youtube.com/embed/abc123?                       │    │
│  │   autoplay=1&mute=1&loop=1&controls=1                  │    │
│  │                                                          │    │
│  │ Vimeo:                                                  │    │
│  │ https://player.vimeo.com/video/123456789?              │    │
│  │   autoplay=1&muted=1&loop=1                            │    │
│  └────────────────────────────────────────────────────────┘    │
│                          │                                       │
│                          ▼                                       │
│  6. Render HTML                                                 │
│  ┌────────────────────────────────────────────────────────┐    │
│  │ <div class="hero-video-container">                      │    │
│  │   <div class="video-wrapper">                           │    │
│  │     <iframe src="[embed_url]"></iframe>                 │    │
│  │   </div>                                                │    │
│  │   <div class="video-overlay"></div>                     │    │
│  │ </div>                                                  │    │
│  └────────────────────────────────────────────────────────┘    │
│                          │                                       │
└──────────────────────────┼───────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│                      CSS STYLING                                 │
│                  (assets/css/style.css)                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  .hero-video-container {                                        │
│    border-radius: 24px;                                         │
│    box-shadow: 0 20px 25px rgba(0,0,0,0.1);                    │
│    overflow: hidden;                                            │
│  }                                                              │
│                                                                  │
│  .video-wrapper {                                               │
│    position: relative;                                          │
│    padding-bottom: 56.25%; /* 16:9 aspect ratio */            │
│  }                                                              │
│                                                                  │
│  .video-overlay {                                               │
│    background: linear-gradient(...);                            │
│    pointer-events: none;                                        │
│  }                                                              │
│                                                                  │
└──────────────────────────┬───────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│                    BROWSER DISPLAY                               │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Desktop View:                                                  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                                                           │  │
│  │  ┌─────────────┐        ┌──────────────────────┐        │  │
│  │  │             │        │                       │        │  │
│  │  │  Where      │        │   [VIDEO PLAYING]    │        │  │
│  │  │  Quality    │        │                       │        │  │
│  │  │  Meets      │        │   Rounded corners    │        │  │
│  │  │  Afford...  │        │   Subtle overlay     │        │  │
│  │  │             │        │   Professional       │        │  │
│  │  │  [Buttons]  │        │                       │        │  │
│  │  │             │        │                       │        │  │
│  │  └─────────────┘        └──────────────────────┘        │  │
│  │                                                           │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                  │
│  Mobile View:                                                   │
│  ┌────────────────────┐                                        │
│  │                     │                                        │
│  │  Where Quality      │                                        │
│  │  Meets Afford...    │                                        │
│  │                     │                                        │
│  │  [Buttons]          │                                        │
│  │                     │                                        │
│  │  ┌──────────────┐  │                                        │
│  │  │              │  │                                        │
│  │  │   [VIDEO]    │  │                                        │
│  │  │   PLAYING    │  │                                        │
│  │  │              │  │                                        │
│  │  └──────────────┘  │                                        │
│  │                     │                                        │
│  └────────────────────┘                                        │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

## Data Flow Sequence

```
1. ADMIN INPUT
   │
   ├─→ Admin checks "Enable Video"
   ├─→ Admin selects "YouTube"
   ├─→ Admin pastes URL
   ├─→ Admin checks "Autoplay"
   └─→ Admin clicks "Update"
   
2. FORM PROCESSING
   │
   ├─→ POST data received
   ├─→ Input sanitization
   ├─→ JSON encoding
   └─→ Database update
   
3. DATABASE STORAGE
   │
   ├─→ Content stored as JSON
   ├─→ Settings stored separately
   └─→ Active status saved
   
4. FRONTEND RETRIEVAL
   │
   ├─→ Query database
   ├─→ Parse JSON
   ├─→ Check video enabled
   └─→ Extract video ID
   
5. EMBED GENERATION
   │
   ├─→ Build embed URL
   ├─→ Add parameters
   ├─→ Create iframe
   └─→ Apply styling
   
6. BROWSER RENDERING
   │
   ├─→ Load iframe
   ├─→ Apply CSS
   ├─→ Start autoplay
   └─→ Display to user
```

## Decision Tree

```
                    ┌─────────────────┐
                    │  Load Homepage  │
                    └────────┬────────┘
                             │
                             ▼
                    ┌─────────────────┐
                    │ Get Hero Data   │
                    │ from Database   │
                    └────────┬────────┘
                             │
                             ▼
                    ┌─────────────────┐
                    │  show_video =   │
                    │     true?       │
                    └────┬───────┬────┘
                         │       │
                    YES  │       │  NO
                         │       │
                         ▼       ▼
              ┌──────────────┐  ┌──────────────┐
              │ video_url    │  │ Show Image   │
              │  not empty?  │  │ Fallback     │
              └──┬───────┬───┘  └──────────────┘
                 │       │
            YES  │       │  NO
                 │       │
                 ▼       ▼
      ┌──────────────┐  ┌──────────────┐
      │ Extract      │  │ Show Image   │
      │ Video ID     │  │ Fallback     │
      └──────┬───────┘  └──────────────┘
             │
             ▼
      ┌──────────────┐
      │ video_type = │
      │   youtube?   │
      └──┬───────┬───┘
         │       │
    YES  │       │  NO (Vimeo)
         │       │
         ▼       ▼
  ┌─────────┐  ┌─────────┐
  │ YouTube │  │  Vimeo  │
  │  Embed  │  │  Embed  │
  └────┬────┘  └────┬────┘
       │            │
       └──────┬─────┘
              │
              ▼
      ┌──────────────┐
      │  autoplay =  │
      │    true?     │
      └──┬───────┬───┘
         │       │
    YES  │       │  NO
         │       │
         ▼       ▼
  ┌─────────┐  ┌─────────┐
  │ Add     │  │ Manual  │
  │ autoplay│  │  Play   │
  │ &mute   │  │         │
  └────┬────┘  └────┬────┘
       │            │
       └──────┬─────┘
              │
              ▼
      ┌──────────────┐
      │ Render Video │
      │   iframe     │
      └──────────────┘
```

## Component Interaction

```
┌─────────────────────────────────────────────────────────┐
│                    ADMIN LAYER                           │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐ │
│  │   Form UI    │→ │ Validation   │→ │  Database    │ │
│  │   Inputs     │  │  & Sanitize  │  │   Update     │ │
│  └──────────────┘  └──────────────┘  └──────────────┘ │
└─────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────┐
│                   DATABASE LAYER                         │
│  ┌──────────────────────────────────────────────────┐  │
│  │  homepage_sections table                         │  │
│  │  ┌────────────┬──────────┬──────────┬─────────┐ │  │
│  │  │section_key │ content  │ settings │is_active│ │  │
│  │  ├────────────┼──────────┼──────────┼─────────┤ │  │
│  │  │   hero     │  {JSON}  │  {JSON}  │    1    │ │  │
│  │  └────────────┴──────────┴──────────┴─────────┘ │  │
│  └──────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────┐
│                  FRONTEND LAYER                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐ │
│  │   Query DB   │→ │  Parse JSON  │→ │   Render     │ │
│  │              │  │  Extract ID  │  │   HTML       │ │
│  └──────────────┘  └──────────────┘  └──────────────┘ │
└─────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────┐
│                 PRESENTATION LAYER                       │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐ │
│  │     HTML     │→ │     CSS      │→ │   Browser    │ │
│  │   Structure  │  │   Styling    │  │   Display    │ │
│  └──────────────┘  └──────────────┘  └──────────────┘ │
└─────────────────────────────────────────────────────────┘
```

## File Dependencies

```
admin/pages/homepage.php
    │
    ├─→ requires: includes/config.php
    ├─→ requires: includes/database.php
    ├─→ requires: includes/functions.php
    ├─→ requires: includes/auth-check.php
    └─→ includes: admin/includes/header.php
                  admin/includes/footer.php

index.php
    │
    ├─→ requires: includes/config.php
    ├─→ requires: includes/database.php
    ├─→ requires: includes/functions.php
    ├─→ includes: includes/header.php
    │             includes/footer.php
    └─→ uses: assets/css/style.css

database/add_hero_video.php
    │
    ├─→ requires: includes/config.php
    └─→ requires: includes/database.php
```

## URL Processing Flow

```
INPUT: https://www.youtube.com/watch?v=dQw4w9WgXcQ
   │
   ├─→ Regex Match: /watch\?v=([^&]+)/
   │
   ├─→ Extract: dQw4w9WgXcQ
   │
   ├─→ Build Embed: https://youtube.com/embed/dQw4w9WgXcQ
   │
   ├─→ Add Parameters:
   │   ├─→ ?rel=0
   │   ├─→ &modestbranding=1
   │   ├─→ &controls=1
   │   ├─→ &autoplay=1
   │   ├─→ &mute=1
   │   ├─→ &loop=1
   │   └─→ &playlist=dQw4w9WgXcQ
   │
   └─→ OUTPUT: https://youtube.com/embed/dQw4w9WgXcQ?
                rel=0&modestbranding=1&controls=1&
                autoplay=1&mute=1&loop=1&playlist=dQw4w9WgXcQ
```

## Responsive Behavior

```
┌─────────────────────────────────────────────────────────┐
│                    SCREEN SIZE                           │
└─────────────────────────────────────────────────────────┘
                            │
                ┌───────────┴───────────┐
                │                       │
         Desktop (>768px)        Mobile (≤768px)
                │                       │
                ▼                       ▼
    ┌───────────────────┐   ┌───────────────────┐
    │ 2-Column Layout   │   │ 1-Column Layout   │
    │ Text | Video      │   │ Text              │
    │                   │   │ Video             │
    │ 16:9 Ratio        │   │ 4:3 Ratio         │
    │ 24px Radius       │   │ 12px Radius       │
    └───────────────────┘   └───────────────────┘
```

This comprehensive flow diagram shows how all components work together to deliver the video embedding feature!
