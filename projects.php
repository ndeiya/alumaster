<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Fetch all active projects
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE status = 'active' ORDER BY is_featured DESC, display_order ASC, created_at DESC");
    $stmt->execute();
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch images for all projects
    $projectImages = [];
    if (!empty($projects)) {
        $projectIds = array_column($projects, 'id');
        $placeholders = implode(',', array_fill(0, count($projectIds), '?'));
        $stmt = $pdo->prepare("SELECT * FROM project_images WHERE project_id IN ($placeholders) ORDER BY display_order ASC");
        $stmt->execute($projectIds);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($images as $image) {
            $projectImages[$image['project_id']][$image['image_type']][] = $image;
        }
    }
} catch (Exception $e) {
    $projects = [];
    $projectImages = [];
}

$page_title = "Our Projects - Alumaster";
$page_description = "Explore our portfolio of high-quality aluminum system solutions across Ghana and West Africa.";
$body_class = 'projects-page';

// Additional head content for projects page
$additional_head = '
<!-- Tailwind CSS for Projects Page -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet"/>';

include 'includes/header.php';
?>
<script>
    tailwind.config = {
        darkMode: "class",
        corePlugins: {
            preflight: false, // Disable Tailwind's CSS reset to prevent conflicts with existing styles
        },
        theme: {
            extend: {
                colors: {
                    "primary": "#137fec",
                    "background-light": "#f6f7f8",
                    "text-primary-light": "#0d141b",
                    "text-secondary-light": "#4c739a",
                    "border-light": "#cfdbe7",
                },
                fontFamily: {
                    "display": ["Inter", "sans-serif"]
                },
            },
        },
    }
</script>

<style>
    /* Modal styles */
    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 9998;
    }
    .modal-backdrop.active {
        display: block;
    }
    .project-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .project-modal.active {
        display: flex;
    }
    .project-card-image {
        height: 250px;
        object-fit: cover;
    }
    .gallery-separator {
        position: relative;
    }
    .gallery-separator::before {
        content: '';
        position: absolute;
        left: -1rem;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(to bottom, #137fec, #0d5fb8);
        border-radius: 2px;
    }
    @media (max-width: 1023px) {
        .gallery-separator::before {
            display: none;
        }
        .gallery-separator::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            top: -1rem;
            height: 4px;
            background: linear-gradient(to right, #137fec, #0d5fb8);
            border-radius: 2px;
        }
    }
</style>

<!-- Projects Content -->
<div class="bg-background-light font-display text-text-primary-light py-10">
    <div class="container mx-auto px-6 sm:px-10 md:px-20 lg:px-40">
        <div class="max-w-7xl mx-auto">
            <!-- Breadcrumb -->
            <div class="flex flex-wrap gap-2 px-4 pb-4">
                <a class="text-text-secondary-light text-base font-medium leading-normal hover:text-primary" href="index.php">Home</a>
                <span class="text-text-secondary-light text-base font-medium leading-normal">/</span>
                <span class="text-text-primary-light text-base font-medium leading-normal">Projects</span>
            </div>

            <!-- Page Header -->
            <div class="flex flex-wrap justify-between gap-3 p-4">
                <div class="flex min-w-72 flex-col gap-3">
                    <h1 class="text-text-primary-light text-4xl font-black leading-tight tracking-[-0.033em]">Our Projects</h1>
                    <p class="text-text-secondary-light text-base font-normal leading-normal">Explore our portfolio of high-quality aluminum system solutions.</p>
                </div>
            </div>

            <!-- Projects Masonry Grid -->
            <div class="p-4">
                <div class="columns-1 md:columns-2 lg:columns-3 gap-6 space-y-6">
                    <?php foreach ($projects as $project): ?>
                    <button type="button" 
                            class="block break-inside-avoid rounded-xl border <?php echo $project['is_featured'] ? 'border-primary' : 'border-border-light'; ?> bg-white shadow-<?php echo $project['is_featured'] ? 'lg' : 'sm'; ?> transition-shadow hover:shadow-<?php echo $project['is_featured'] ? 'xl' : 'md'; ?> group overflow-hidden w-full text-left" 
                            onclick="openModal(<?php echo $project['id']; ?>)">
                        <div class="relative">
                            <?php if (!empty($project['thumbnail'])): ?>
                            <img alt="<?php echo htmlspecialchars($project['name']); ?>" 
                                 class="w-full project-card-image" 
                                 src="<?php echo htmlspecialchars($project['thumbnail']); ?>" 
                                 loading="lazy" />
                            <?php endif; ?>
                            
                            <?php if ($project['is_featured']): ?>
                            <div class="absolute top-2 right-2 bg-primary text-white text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full">
                                Most Recent
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="p-4 sm:p-6">
                            <h3 class="text-text-primary-light text-lg font-bold leading-tight tracking-[-0.015em]">
                                <?php echo htmlspecialchars($project['name']); ?>
                            </h3>
                            <p class="text-text-secondary-light text-sm mt-1">
                                <?php echo htmlspecialchars($project['location']); ?>
                            </p>
                            <?php if (!empty($project['scope'])): ?>
                            <p class="text-text-primary-light text-sm font-medium mt-2">
                                <?php echo htmlspecialchars($project['scope']); ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Backdrop -->
<div class="modal-backdrop" id="modalBackdrop" onclick="closeAllModals()"></div>

<!-- Project Modals -->
<?php foreach ($projects as $project): ?>
<div class="project-modal" id="project-modal-<?php echo $project['id']; ?>">
    <div class="relative w-full max-w-6xl max-h-[90vh] overflow-y-auto bg-white rounded-xl shadow-2xl flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-start justify-between p-6 border-b border-border-light sticky top-0 bg-white z-10">
            <div>
                <h2 class="text-2xl font-bold text-text-primary-light">
                    <?php echo htmlspecialchars($project['name']); ?>
                </h2>
                <p class="text-sm text-text-secondary-light mt-1">
                    <?php echo htmlspecialchars($project['location']); ?><?php if (!empty($project['scope'])): ?> - <?php echo htmlspecialchars($project['scope']); ?><?php endif; ?>
                </p>
            </div>
            <button type="button" 
                    aria-label="Close modal" 
                    class="p-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors" 
                    onclick="closeModal(<?php echo $project['id']; ?>)">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Modal Content -->
        <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
            <!-- Before Images -->
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-text-primary-light">Before</h3>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <?php 
                    $beforeImages = $projectImages[$project['id']]['before'] ?? [];
                    if (!empty($beforeImages)):
                        foreach ($beforeImages as $image): 
                    ?>
                    <img alt="Before image" 
                         class="rounded-lg object-cover aspect-square w-full hover:scale-105 transition-transform duration-300 cursor-pointer" 
                         src="<?php echo htmlspecialchars($image['image_path']); ?>" 
                         loading="lazy" />
                    <?php 
                        endforeach;
                    else:
                    ?>
                    <p class="col-span-2 text-text-secondary-light text-sm">No before images available</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- After Images -->
            <div class="flex flex-col gap-4 gallery-separator">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-text-primary-light">After</h3>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <?php 
                    $afterImages = $projectImages[$project['id']]['after'] ?? [];
                    if (!empty($afterImages)):
                        foreach ($afterImages as $image): 
                    ?>
                    <img alt="After image" 
                         class="rounded-lg object-cover aspect-square w-full hover:scale-105 transition-transform duration-300 cursor-pointer" 
                         src="<?php echo htmlspecialchars($image['image_path']); ?>" 
                         loading="lazy" />
                    <?php 
                        endforeach;
                    else:
                    ?>
                    <p class="col-span-2 text-text-secondary-light text-sm">No after images available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script>
    function openModal(projectId) {
        const modal = document.getElementById('project-modal-' + projectId);
        const backdrop = document.getElementById('modalBackdrop');
        
        if (modal && backdrop) {
            modal.classList.add('active');
            backdrop.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }
    
    function closeModal(projectId) {
        const modal = document.getElementById('project-modal-' + projectId);
        const backdrop = document.getElementById('modalBackdrop');
        
        if (modal && backdrop) {
            modal.classList.remove('active');
            backdrop.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
    
    function closeAllModals() {
        const modals = document.querySelectorAll('.project-modal');
        const backdrop = document.getElementById('modalBackdrop');
        
        modals.forEach(modal => {
            modal.classList.remove('active');
        });
        
        if (backdrop) {
            backdrop.classList.remove('active');
        }
        
        document.body.style.overflow = '';
    }
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeAllModals();
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
