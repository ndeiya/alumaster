This is a guide to develop the new "Projects" page for the Alumaster Website

//Guide//
Create  a new page  called "projects". This page will be the main page for the projects section of the website. This page will contain a list of all the projects that have been created by Alumaster. The page will be a masonry grid of expandable cards, each card will contain the following information:
- Project Name [example:Rana Motors ]
- Project Location [example: Kaneshie, Accra]
- Scope Offered [example: Alucobond cladding, Spider glass]
- Project Image [Card thumbnail]
Each expandable card when clicked with display a gallery in a popup window. The gallery will contain a "Before" set of images and an "After" set of images. Images can be found in the "assets/images/projects" directory. Each folder in the directory has two sub-folders, "before" and "after" containing a set of before and after images of the project. There's also a "project_details.txt" file which contians the project details.

//Design Guide//
Use the file named "code.html" in the "assets/images/projects/alumaster_projects_page" directory as the guide to design the projects page [EMPHASIS ON MAINTAINING THE SAME DESIGN AS IN THE "code.html" FILE]
code snippet: 
"<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Alumaster Projects</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "primary": "#137fec",
              "background-light": "#f6f7f8",
              "background-dark": "#101922",
              "text-primary-light": "#0d141b",
              "text-secondary-light": "#4c739a",
              "text-primary-dark": "#f6f7f8",
              "text-secondary-dark": "#a9b9cc",
              "border-light": "#cfdbe7",
              "border-dark": "#2a3b4d"
            },
            fontFamily: {
              "display": ["Inter", "sans-serif"]
            },
            borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
          },
        },
      }
    </script>
<style>
      .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        font-size: 20px;
      }
      .peer:target {
        display: flex;
      }
      .peer:target ~ #project-modal-backdrop {
        display: block;
      }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-text-primary-light dark:text-text-primary-dark">
<div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
<div class="layout-container flex h-full grow flex-col">
<header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-border-light dark:border-border-dark px-6 sm:px-10 md:px-20 lg:px-40 py-3 bg-white dark:bg-background-dark/80 backdrop-blur-sm sticky top-0 z-10">
<div class="flex items-center gap-4 text-text-primary-light dark:text-text-primary-dark">
<div class="size-6 text-primary">
<svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg"><path d="M13.8261 17.4264C16.7203 18.1174 20.2244 18.5217 24 18.5217C27.7756 18.5217 31.2797 18.1174 34.1739 17.4264C36.9144 16.7722 39.9967 15.2331 41.3563 14.1648L24.8486 40.6391C24.4571 41.267 23.5429 41.267 23.1514 40.6391L6.64374 14.1648C8.00331 15.2331 11.0856 16.7722 13.8261 17.4264Z" fill="currentColor"></path><path clip-rule="evenodd" d="M39.998 12.236C39.9944 12.2537 39.9875 12.2845 39.9748 12.3294C39.9436 12.4399 39.8949 12.5741 39.8346 12.7175C39.8168 12.7597 39.7989 12.8007 39.7813 12.8398C38.5103 13.7113 35.9788 14.9393 33.7095 15.4811C30.9875 16.131 27.6413 16.5217 24 16.5217C20.3587 16.5217 17.0125 16.131 14.2905 15.4811C12.0012 14.9346 9.44505 13.6897 8.18538 12.8168C8.17384 12.7925 8.16216 12.767 8.15052 12.7408C8.09919 12.6249 8.05721 12.5114 8.02977 12.411C8.00356 12.3152 8.00039 12.2667 8.00004 12.2612C8.00004 12.261 8 12.2607 8.00004 12.2612C8.00004 12.2359 8.0104 11.9233 8.68485 11.3686C9.34546 10.8254 10.4222 10.2469 11.9291 9.72276C14.9242 8.68098 19.1919 8 24 8C28.8081 8 33.0758 8.68098 36.0709 9.72276C37.5778 10.2469 38.6545 10.8254 39.3151 11.3686C39.9006 11.8501 39.9857 12.1489 39.998 12.236ZM4.95178 15.2312L21.4543 41.6973C22.6288 43.5809 25.3712 43.5809 26.5457 41.6973L43.0534 15.223C43.0709 15.1948 43.0878 15.1662 43.104 15.1371L41.3563 14.1648C43.104 15.1371 43.1038 15.1374 43.104 15.1371L43.1051 15.135L43.1065 15.1325L43.1101 15.1261L43.1199 15.1082C43.1276 15.094 43.1377 15.0754 43.1497 15.0527C43.1738 15.0075 43.2062 14.9455 43.244 14.8701C43.319 14.7208 43.4196 14.511 43.5217 14.2683C43.6901 13.8679 44 13.0689 44 12.2609C44 10.5573 43.003 9.22254 41.8558 8.2791C40.6947 7.32427 39.1354 6.55361 37.385 5.94477C33.8654 4.72057 29.133 4 24 4C18.867 4 14.1346 4.72057 10.615 5.94478C8.86463 6.55361 7.30529 7.32428 6.14419 8.27911C4.99695 9.22255 3.99999 10.5573 3.99999 12.2609C3.99999 13.1275 4.29264 13.9078 4.49321 14.3607C4.60375 14.6102 4.71348 14.8196 4.79687 14.9689C4.83898 15.0444 4.87547 15.1065 4.9035 15.1529C4.91754 15.1762 4.92954 15.1957 4.93916 15.2111L4.94662 15.223L4.95178 15.2312ZM35.9868 18.996L24 38.22L12.0131 18.996C12.4661 19.1391 12.9179 19.2658 13.3617 19.3718C16.4281 20.1039 20.0901 20.5217 24 20.5217C27.9099 20.5217 31.5719 20.1039 34.6383 19.3718C35.082 19.2658 35.5339 19.1391 35.9868 18.996Z" fill="currentColor" fill-rule="evenodd"></path></svg>
</div>
<h2 class="text-text-primary-light dark:text-text-primary-dark text-lg font-bold leading-tight tracking-[-0.015em]">Alumaster</h2>
</div>
<div class="flex flex-1 justify-end gap-8">
<div class="hidden md:flex items-center gap-9">
<a class="text-text-primary-light dark:text-text-primary-dark text-sm font-medium leading-normal" href="#">Home</a>
<a class="text-text-primary-light dark:text-text-primary-dark text-sm font-medium leading-normal" href="#">About</a>
<a class="text-text-primary-light dark:text-text-primary-dark text-sm font-medium leading-normal" href="#">Services</a>
<a class="text-primary text-sm font-bold leading-normal" href="#">Projects</a>
<a class="text-text-primary-light dark:text-text-primary-dark text-sm font-medium leading-normal" href="#">Contact</a>
</div>
<button class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-slate-50 text-sm font-bold leading-normal tracking-[0.015em]">
<span class="truncate">Get a Quote</span>
</button>
</div>
</header>
<main class="px-6 sm:px-10 md:px-20 lg:px-40 flex flex-1 justify-center py-10">
<div class="layout-content-container flex flex-col w-full max-w-7xl flex-1">
<div class="flex flex-wrap gap-2 px-4 pb-4">
<a class="text-text-secondary-light dark:text-text-secondary-dark text-base font-medium leading-normal" href="#">Home</a>
<span class="text-text-secondary-light dark:text-text-secondary-dark text-base font-medium leading-normal">/</span>
<span class="text-text-primary-light dark:text-text-primary-dark text-base font-medium leading-normal">Projects</span>
</div>
<div class="flex flex-wrap justify-between gap-3 p-4">
<div class="flex min-w-72 flex-col gap-3">
<p class="text-text-primary-light dark:text-text-primary-dark text-4xl font-black leading-tight tracking-[-0.033em]">Our Projects</p>
<p class="text-text-secondary-light dark:text-text-secondary-dark text-base font-normal leading-normal">Explore our portfolio of high-quality aluminum system solutions.</p>
</div>
</div>
<div class="p-4">
<div class="columns-1 md:columns-2 lg:columns-3 gap-6 space-y-6">
<a class="block break-inside-avoid rounded-xl border border-primary bg-white dark:bg-background-dark/50 shadow-lg transition-shadow hover:shadow-xl group overflow-hidden" href="#project-modal">
<div class="relative">
<img alt="Finished building exterior featuring sleek alucobond panels and large curtain wall glasses" class="w-full h-auto object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCeQLVyO0aHUYQtz24iQa_35l_uBbdayRJmmeE4aviGpkI7BnaLX9I07VSFekBuPgwtedZwD8O9HvvXohlY2dl080xCsbzyHxfflwfVvRfvgtTIC4b_bpWcHY0Xl8pIZifeSwrm8hYG9Gds1Gt9YeKCjObm46ici5Wl_m0vHqokXUFcukJWB11mY7DE6JbeaKZNI2_ElQea_X1W4Bw-NtA46indmyzv6Ur231CdvM1WGN7UPpAdZgCjI3iUZXZiSMcmkLJm8-vtJnYY"/>
<div class="absolute top-2 right-2 bg-primary text-white text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full">Most Recent</div>
</div>
<div class="p-4 sm:p-6">
<h3 class="text-text-primary-light dark:text-text-primary-dark text-lg font-bold leading-tight tracking-[-0.015em]">Mantrac Ghana Ltd.</h3>
<p class="text-text-secondary-light dark:text-text-secondary-dark text-sm mt-1">Kaneshie, Accra</p>
<p class="text-text-primary-light dark:text-text-primary-dark text-sm font-medium mt-2">Alucobond and Curtain Wall Glasses</p>
</div>
</a>
<a class="block break-inside-avoid rounded-xl border border-border-light dark:border-border-dark bg-white dark:bg-background-dark/50 shadow-sm transition-shadow hover:shadow-md group overflow-hidden" href="#project-modal">
<img alt="Accra Mall exterior after renovation with new structural glazing" class="w-full h-auto object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC2hID7Clsy8y9PqwRhNnGbCGfwXdiE2zCqlB8v1I2k6VyMebjLF5zAhT9s7122S0YXGGm5DEJNfv_dMRnIXwRErr_peK6bMp__bFx-vfxrpiWrQ8zeQ-MT3o8CpZQBCDODM2CkjaDgJn5PAPchhRv57g12e3MbgKBSIfHhWW4jthE6rCAV8tJEfqKjH8r0jSx4SuGw1pV6Qle1JSnFP1s1AGAdbS-cOqgx38bXnfq06b8rbmlqNe6-viNx9jaGrRZ_cCTeDwnhmDXp"/>
<div class="p-4 sm:p-6">
<h3 class="text-text-primary-light dark:text-text-primary-dark text-lg font-bold leading-tight tracking-[-0.015em]">Accra Mall Renovation</h3>
<p class="text-text-secondary-light dark:text-text-secondary-dark text-sm mt-1">Tetteh Quarshie, Accra</p>
<p class="text-text-primary-light dark:text-text-primary-dark text-sm font-medium mt-2">Structural Glazing and Composite Panels</p>
</div>
</a>
<a class="block break-inside-avoid rounded-xl border border-border-light dark:border-border-dark bg-white dark:bg-background-dark/50 shadow-sm transition-shadow hover:shadow-md group overflow-hidden" href="#project-modal">
<img alt="The distinctive facade of One Airport Square" class="w-full h-auto object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC16gDSfueqjCR_JoOGYgJY0gursKgY8zRELV5IbVJVijKHy808x2Wjt9ajuQ3djJo_N5EujyucXTbHTim4EwqsACScRXSsHhWoxOs68YwYOkA2dffwTjakUH_E-WCSQTwpleT7C_CY458KNJxGRRYeqbbV2rY6qxVMNNnMEJaSaVN4Ai-vqe99rE-oECJU942x7qhrWMrg6PPRlluTx3Gc7SJoiEOARwpS4A-t_vg2XkmD_aDSSLDEvCKM_DalrbRPjOcMRk_S7dAP"/>
<div class="p-4 sm:p-6">
<h3 class="text-text-primary-light dark:text-text-primary-dark text-lg font-bold leading-tight tracking-[-0.015em]">One Airport Square</h3>
<p class="text-text-secondary-light dark:text-text-secondary-dark text-sm mt-1">Airport City, Accra</p>
<p class="text-text-primary-light dark:text-text-primary-dark text-sm font-medium mt-2">Facade Systems and Louvers</p>
</div>
</a>
<a class="block break-inside-avoid rounded-xl border border-border-light dark:border-border-dark bg-white dark:bg-background-dark/50 shadow-sm transition-shadow hover:shadow-md group overflow-hidden" href="#project-modal">
<img alt="Construction site with scaffolding" class="w-full h-auto object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBv7hq335_oBmrbDmMfzNaWzzksZnRvFISJ5vcndMwTfZRIjBjHNr0g5Z0EhEJVPzOMAklHyI3I0MKdEAa1BA_S5nHiTfSXUMpYqcz_o0CaidCBHRY9WDK-sjyPm5o3TNm-DtGnzuypCe47ZsgU_8GuYR0z6gbnHrSsrSxznWmlUiF1-uuSj1iJwE9NgvaAaMEdWj5aEcuFW_cBBmS9rAsfThb0YGT9iEvesTknCEvKFZ_rf93WDHYnzIIIYPEmbNbY3A6eu5nuFbBs"/>
<div class="p-4 sm:p-6">
<h3 class="text-text-primary-light dark:text-text-primary-dark text-lg font-bold leading-tight tracking-[-0.015em]">New Office Complex</h3>
<p class="text-text-secondary-light dark:text-text-secondary-dark text-sm mt-1">Cantonments, Accra</p>
<p class="text-text-primary-light dark:text-text-primary-dark text-sm font-medium mt-2">Custom Aluminum Windows</p>
</div>
</a>
<a class="block break-inside-avoid rounded-xl border border-border-light dark:border-border-dark bg-white dark:bg-background-dark/50 shadow-sm transition-shadow hover:shadow-md group overflow-hidden" href="#project-modal">
<img alt="Modern residential building with aluminum railings" class="w-full h-auto object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBfaoeFCTWgZRLyCRXxdbrQpGNyj-UZk3jyw33jfQkkhczwBee73VvRjzAAfdeeN2neXPlTST4b40pJ5qT9hVOnQq9-FhStm_50fCW1BaSX-CzN2T_I9fk9q-fYuas8-hDFhXYoDPQmi5DPthmnnjo9qhOYCCaqvfxkrYrLAvSNs9ujagDILCU63vLh45kahfL9vsiTGPjSGVWobqC4puw2_I7-oGLlhGsmh1LTHX5PV695WteR6lyqjTOSYLKXsPyTqWZH3h9RXvHZ"/>
<div class="p-4 sm:p-6">
<h3 class="text-text-primary-light dark:text-text-primary-dark text-lg font-bold leading-tight tracking-[-0.015em]">Residential Tower</h3>
<p class="text-text-secondary-light dark:text-text-secondary-dark text-sm mt-1">East Legon, Accra</p>
<p class="text-text-primary-light dark:text-text-primary-dark text-sm font-medium mt-2">Balcony Railings &amp; Sliding Doors</p>
</div>
</a>
<a class="block break-inside-avoid rounded-xl border border-border-light dark:border-border-dark bg-white dark:bg-background-dark/50 shadow-sm transition-shadow hover:shadow-md group overflow-hidden" href="#project-modal">
<img alt="Weathered facade of an old building" class="w-full h-auto object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAN1C8wANsyPwrkcBq2RTzrOc6bMiQs6o0NxP_bHZge_ZSXuE4VcrndNnJALVF5PmaZytMbcn7vO0igbu-zJ3gtHZN6VRgBMSDuWvm4LfCG40HwVG1C6dtF_D9njOzyHN0JmUKPiWrQSPfMdKg2k7Q2LtVnHLyLXuJ10EhJKYOy9W0H7jSvvW5nyzvtYuh9POUiUj2aTw1NXUZpKMpA7xiZ4rbuz27pDLhtUeyJvi_5Lycop_OBi3I_MzQ5oUWK5T7COoCEOmXiOflz"/>
<div class="p-4 sm:p-6">
<h3 class="text-text-primary-light dark:text-text-primary-dark text-lg font-bold leading-tight tracking-[-0.015em]">Heritage Building Restoration</h3>
<p class="text-text-secondary-light dark:text-text-secondary-dark text-sm mt-1">Jamestown, Accra</p>
<p class="text-text-primary-light dark:text-text-primary-dark text-sm font-medium mt-2">Window Frame Replacement</p>
</div>
</a>
</div>
</div>
</div>
</main>
</div>
<a aria-hidden="true" class="fixed inset-0 bg-black/70 z-40 hidden" href="#" id="project-modal-backdrop"></a>
<div class="peer fixed inset-0 z-50 hidden items-center justify-center p-4" id="project-modal">
<div class="relative w-full max-w-6xl max-h-[90vh] overflow-y-auto bg-white dark:bg-background-dark rounded-xl shadow-2xl flex flex-col">
<div class="flex items-start justify-between p-6 border-b border-border-light dark:border-border-dark sticky top-0 bg-white dark:bg-background-dark z-10">
<div>
<h2 class="text-2xl font-bold text-text-primary-light dark:text-text-primary-dark">Mantrac Ghana Ltd.</h2>
<p class="text-sm text-text-secondary-light dark:text-text-secondary-dark mt-1">Kaneshie, Accra - Alucobond and Curtain Wall Glasses</p>
</div>
<a aria-label="Close modal" class="p-1 rounded-full hover:bg-border-light dark:hover:bg-border-dark transition-colors" href="#">
<span class="material-symbols-outlined !text-2xl">close</span>
</a>
</div>
<div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
<div class="flex flex-col gap-4">
<h3 class="text-xl font-bold text-text-primary-light dark:text-text-primary-dark">Before</h3>
<div class="grid grid-cols-2 gap-4">
<img alt="Construction site with scaffolding" class="rounded-lg object-cover aspect-square w-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBv7hq335_oBmrbDmMfzNaWzzksZnRvFISJ5vcndMwTfZRIjBjHNr0g5Z0EhEJVPzOMAklHyI3I0MKdEAa1BA_S5nHiTfSXUMpYqcz_o0CaidCBHRY9WDK-sjyPm5o3TNm-DtGnzuypCe47ZsgU_8GuYR0z6gbnHrSsrSxznWmlUiF1-uuSj1iJwE9NgvaAaMEdWj5aEcuFW_cBBmS9rAsfThb0YGT9iEvesTknCEvKFZ_rf93WDHYnzIIIYPEmbNbY3A6eu5nuFbBs"/>
<img alt="Weathered facade of an old building" class="rounded-lg object-cover aspect-square w-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAN1C8wANsyPwrkcBq2RTzrOc6bMiQs6o0NxP_bHZge_ZSXuE4VcrndNnJALVF5PmaZytMbcn7vO0igbu-zJ3gtHZN6VRgBMSDuWvm4LfCG40HwVG1C6dtF_D9njOzyHN0JmUKPiWrQSPfMdKg2k7Q2LtVnHLyLXuJ10EhJKYOy9W0H7jSvvW5nyzvtYuh9POUiUj2aTw1NXUZpKMpA7xiZ4rbuz27pDLhtUeyJvi_5Lycop_OBi3I_MzQ5oUWK5T7COoCEOmXiOflz"/>
<img alt="Old building under construction" class="rounded-lg object-cover aspect-square w-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDMePi6ruBTwIECc-V10PtjtJ2A1NY4xSCzMqZ2vi1hJ_cKtMa6ivDPS2Rp3IO2D_T-rQaaLdfzab3fkhc4Pjsue_YyEoPGCTbi88l54w7H0CdZk4jBG5s_ELfo4j7_74qNeHxBeVGNwZMpdU9P1Qgmcigw0bYIMmh1tGrNIrvf-jYPHB61HPvIFOi2o5g9f6keaOzltdm_dVlbUTI3VMXCWvoGxjXgzxkQ0TqRQxanbzf7arxeq1qkFMS9f7ZwD0EjtwpzvHcuuNiT"/>
<img alt="Empty construction plot" class="rounded-lg object-cover aspect-square w-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBHb2GX5L29i5-H_mDe-7j-Q7P3nMwnHB9rYeLdgyxEJA1wLzKFm6LIFeQP-oTiPXfx5TcsBiE63QkcCAso0tdrooGlPbbRnKEI7F7AdI-Btf1D2y0jm_pz4UNHfomz9qG9StRLw4zsUHPTWCOPQVPFadSb-V2wdfozD_BcFE5zdM5UZ-mFC2nLojxfFWHcR2Z5dWDKVumx2lpg5QHxzA4glr90DEf2xSIwKksDaRHivrfgEA6hokrBAZ4gUsOJ5CQ7TWiFfm9ZvbWT"/>
</div>
</div>
<div class="flex flex-col gap-4">
<h3 class="text-xl font-bold text-text-primary-light dark:text-text-primary-dark">After</h3>
<div class="grid grid-cols-2 gap-4">
<img alt="Finished building exterior with alucobond panels" class="rounded-lg object-cover aspect-square w-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCeQLVyO0aHUYQtz24iQa_35l_uBbdayRJmmeE4aviGpkI7BnaLX9I07VSFekBuPgwtedZwD8O9HvvXohlY2dl080xCsbzyHxfflwfVvRfvgtTIC4b_bpWcHY0Xl8pIZifeSwrm8hYG9Gds1Gt9YeKCjObm46ici5Wl_m0vHqokXUFcukJWB11mY7DE6JbeaKZNI2_ElQea_X1W4Bw-NtA46indmyzv6Ur231CdvM1WGN7UPpAdZgCjI3iUZXZiSMcmkLJm8-vtJnYY"/>
<img alt="Mall exterior after renovation" class="rounded-lg object-cover aspect-square w-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC2hID7Clsy8y9PqwRhNnGbCGfwXdiE2zCqlB8v1I2k6VyMebjLF5zAhT9s7122S0YXGGm5DEJNfv_dMRnIXwRErr_peK6bMp__bFx-vfxrpiWrQ8zeQ-MT3o8CpZQBCDODM2CkjaDgJn5PAPchhRv57g12e3MbgKBSIfHhWW4jthE6rCAV8tJEfqKjH8r0jSx4SuGw1pV6Qle1JSnFP1s1AGAdbS-cOqgx38bXnfq06b8rbmlqNe6-viNx9jaGrRZ_cCTeDwnhmDXp"/>
<img alt="Facade of One Airport Square" class="rounded-lg object-cover aspect-square w-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC16gDSfueqjCR_JoOGYgJY0gursKgY8zRELV5IbVJVijKHy808x2Wjt9ajuQ3djJo_N5EujyucXTbHTim4EwqsACScRXSsHhWoxOs68YwYOkA2dffwTjakUH_E-WCSQTwpleT7C_CY458KNJxGRRYeqbbV2rY6qxVMNNnMEJaSaVN4Ai-vqe99rE-oECJU942x7qhrWMrg6PPRlluTx3Gc7SJoiEOARwpS4A-t_vg2XkmD_aDSSLDEvCKM_DalrbRPjOcMRk_S7dAP"/>
<img alt="Modern office building closeup" class="rounded-lg object-cover aspect-square w-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBxnr7sp8OU6cRcUmLKgQYVSnJJ5LPW1LJ0Q9ef855ns68x_JFj25zbKkD2rN1NIZLoudC1ZEw9Q0XI9Et7bbgPHIdJuX-FOl2cAGCQVYhMOliYxA3h0mSj6s4CPm3dXPIaUoZQMwAnK_FVyUf1cfUTZwh6d0vjtbeEafIgCJb5ndzSrWYkPm26TAN8ljZA-REIrFtcdSYoVUWRnz5lBRX2A0ndPVKghZMFQ4QtobJvvv4XiAtCQ3YHeqywu7PLqjxHn3P-fBexhMra"/>
</div>
</div>
</div>
</div>
</div>
</div>

</body></html>"

Mockup image will be shared with agent before agent start developing the page.