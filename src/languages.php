<?php
session_start(); // Start the session here

function t($key) {
    global $translations;

    // Get the current language from the session
    $currentLang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

    // Ensure the translation array exists for the current language
    if (!isset($translations[$currentLang])) {
        $currentLang = 'en';
    }

    // Return the translated string or the original key if not found
    return isset($translations[$currentLang][$key]) ? $translations[$currentLang][$key] : $key;
}


$languages = ['nl' => 'Dutch', 'en' => 'English', 'id' => 'Indonesian'];

$translations = [
    'en' => [
        'About' => 'About',
        'About me' => 'About me',
        'Projects' => 'Projects',
        'Blogs' => 'Blogs',
        'Highlighted Skills' => 'Highlighted Skills',
        'Highlighted Project' => 'Highlighted Project',
        'National Videogame Museum Redesign Website.' => 'National Videogame Museum Redesign Website.',
        'Made by E. van Aubel' => 'Made by E. van Aubel',
        'media/handwriting/welcome.svg' => 'media/handwriting/welcome.svg',
        'media/handwriting/im-e-van-aubel.svg' => 'media/handwriting/im-e-van-aubel.svg',
        'media/handwriting/im-a-software-developer.svg' => 'media/handwriting/im-a-software-developer.svg',
        "These are my own articles/blog mostly about my own projects, rant's, thought's etc" => "These are my own articles/blog mostly about my own projects, rant's, thought's etc",
        "These are my previous projects in my school and all of them are avaliable in Github" => "These are my previous projects in my school and all of them are avaliable in Github",   
        "View my CV here:" => "View my CV here",
        "Go to my CV" => "Go to my CV",
        "Select Language:" => "Select Language:",
        "E. Van Aubel is a software development student who is scared of CSS and is living in Amsterdam, Netherlands. They are passionate about free and open-source software and privacy advocacy. Their skills include programming in Python and JavaScript, web development with CSS and HTML, and proficiency in the Laravel framework and Tailwind CSS. E. Van Aubel enjoys movies, anime, and music in their free time. They are equipped with an HP 15s-eq0xxx laptop featuring an AMD Ryzen 3 3200U processor. This young developer is well-positioned to contribute to innovative tech projects with their growing expertise in software development and commitment to open-source principles." => "E. Van Aubel is a software development student who is scared of CSS and is living in Amsterdam, Netherlands. They are passionate about free and open-source software and privacy advocacy. Their skills include programming in Python and JavaScript, web development with CSS and HTML, and proficiency in the Laravel framework and Tailwind CSS. E. Van Aubel enjoys movies, anime, and music in their free time. They are equipped with an HP 15s-eq0xxx laptop featuring an AMD Ryzen 3 3200U processor. This young developer is well-positioned to contribute to innovative tech projects with their growing expertise in software development and commitment to open-source principles."

    ],
    'nl' => [
        'About' => 'Over',
        'About me' => 'Over mij',
        'Projects' => 'Projecten',
        'Blogs' => 'Blogs',
        'Highlighted Skills' => 'Uitgelichte Vaardigheden',
        'Highlighted Project' => 'Uitgelichte Projecten',
        'National Videogame Museum Redesign Website.' => 'Herontwerp website Nationaal Videogame Museum.',
        'Made by E. van Aubel' => 'Gemaakt door E. van Aubel',
        'media/handwriting/welcome.svg' => 'media/handwriting/welkom.svg',
        'media/handwriting/im-e-van-aubel.svg' => 'media/handwriting/ik-ben-e-van-aubel.svg',
        'media/handwriting/im-a-software-developer.svg' => 'media/handwriting/ik-ben-een-software-developer.svg',
        "These are my own articles/blog mostly about my own projects, rant's, thought's etc" => "Dit zijn mijn eigen artikelen/blogs over mijn eigen projecten, rant's, gedachten enz.",
        "These are my previous projects in my school and all of them are avaliable in Github" => "Dit zijn mijn vorige projecten op mijn school en ze zijn allemaal beschikbaar op Github",
        "View my CV here:" => "Bekijk mijn CV hier:",
        "Go to my CV" => "Ga nu naar mijn CV",
        "Select Language:" => "Selecteer taal:",
        "E. Van Aubel is a software development student who is scared of CSS and is living in Amsterdam, Netherlands. They are passionate about free and open-source software and privacy advocacy. Their skills include programming in Python and JavaScript, web development with CSS and HTML, and proficiency in the Laravel framework and Tailwind CSS. E. Van Aubel enjoys movies, anime, and music in their free time. They are equipped with an HP 15s-eq0xxx laptop featuring an AMD Ryzen 3 3200U processor. This young developer is well-positioned to contribute to innovative tech projects with their growing expertise in software development and commitment to open-source principles." => "E. Van Aubel is een student softwareontwikkeling die bang is voor CSS en woont in Amsterdam, Nederland. Ze zijn gepassioneerd door vrije en open-source software en privacy advocacy. Hun vaardigheden omvatten programmeren in Python en JavaScript, webontwikkeling met CSS en HTML, en vaardigheid in het Laravel framework en Tailwind CSS. E. Van Aubel houdt in hun vrije tijd van films, anime en muziek. Hij is uitgerust met een HP 15s-eq0xxx laptop met een AMD Ryzen 3 3200U processor. Deze jonge ontwikkelaar is goed gepositioneerd om bij te dragen aan innovatieve technische projecten met hun groeiende expertise in softwareontwikkeling en toewijding aan open-source principes."
    ],
    'id' => [
        'About' => 'Tentang',
        'About me' => 'Tentang saya',
        'Projects' => 'Proyek',
        'Blogs' => 'Blogs',
        'Highlighted Skills' => 'Sorotan Keterampilan',
        'Highlighted Project' => 'Sorotan Proyek',
        'National Videogame Museum Redesign Website.' => 'Desain Ulang Situs Web Museum Videogame Nasional.',
        'Made by E. van Aubel' => 'Dibuat oleh E. van Aubel',
        'media/handwriting/welcome.svg' => 'media/handwriting/selamat-datang.svg',
        'media/handwriting/im-e-van-aubel.svg' => 'media/handwriting/nama-saya-e-van-aubel.svg',
        'media/handwriting/im-a-software-developer.svg' => 'media/handwriting/saya-seorang-software-developer.svg',
        "These are my own articles/blog mostly about my own projects, rant's, thought's etc" => "Ini adalah artikel/blog saya sendiri yang sebagian besar berisi tentang proyek-proyek saya sendiri, ocehan, pemikiran, dan lain-lain",
        "These are my previous projects in my school and all of them are avaliable in Github" => "Ini adalah proyek-proyek saya sebelumnya di sekolah saya dan semuanya tersedia di Github",
        "View my CV here:" => "Lihat CV saya di sini:",
        "Go to my CV" => "Pergi ke CV saya",
        "Select Language:" => "Pilih bahasa:",
        "E. Van Aubel is a software development student who is scared of CSS and is living in Amsterdam, Netherlands. They are passionate about free and open-source software and privacy advocacy. Their skills include programming in Python and JavaScript, web development with CSS and HTML, and proficiency in the Laravel framework and Tailwind CSS. E. Van Aubel enjoys movies, anime, and music in their free time. They are equipped with an HP 15s-eq0xxx laptop featuring an AMD Ryzen 3 3200U processor. This young developer is well-positioned to contribute to innovative tech projects with their growing expertise in software development and commitment to open-source principles." => "E. Van Aubel adalah seorang mahasiswa software developer yang takut dengan CSS dan tinggal di Amsterdam, Belanda. Mereka sangat menyukai free and open-source software serta advokasi privasi. Keahlian mereka meliputi pemrograman dalam Python dan JavaScript, pengembangan web dengan CSS dan HTML, serta kemahiran dalam kerangka kerja Laravel dan Tailwind CSS. E. Van Aubel menikmati film, anime, dan musik di waktu luang mereka. Mereka dilengkapi dengan laptop HP 15s-eq0xxx yang dilengkapi dengan prosesor AMD Ryzen 3 3200U. Pengembang muda ini memiliki posisi yang tepat untuk berkontribusi pada proyek-proyek teknologi inovatif dengan keahlian mereka yang terus berkembang dalam pengembangan perangkat lunak dan komitmen terhadap prinsip-prinsip sumber terbuka."
    ]
];

