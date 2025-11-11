-- ============================================
-- Add Video Fields to Hero Section
-- Run this in phpMyAdmin or MySQL command line
-- ============================================

-- Update hero section to add video fields
UPDATE homepage_sections 
SET content = JSON_SET(
    content,
    '$.video_url', '',
    '$.video_type', 'youtube',
    '$.show_video', false,
    '$.video_autoplay', true
)
WHERE section_key = 'hero';

-- Verify the update
SELECT 
    section_key,
    section_name,
    JSON_EXTRACT(content, '$.video_url') as video_url,
    JSON_EXTRACT(content, '$.video_type') as video_type,
    JSON_EXTRACT(content, '$.show_video') as show_video,
    JSON_EXTRACT(content, '$.video_autoplay') as video_autoplay
FROM homepage_sections 
WHERE section_key = 'hero';
