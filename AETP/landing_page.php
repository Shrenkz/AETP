<?php
include('./actions/db.php');

$sql = "SELECT * FROM page_content WHERE id = 1";
$result = $conn->query($sql);
$content = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/svg+xml" href="./svg/favicon.svg">
  <link rel="stylesheet" href="./css/landing_page.css">
  <title>Agri Eco-Tourism Park 3D Model</title>
</head>

<body>
  <header>
    <nav class="nav">
      <ul>
        <li><a href="#about">About</a></li>
        <li><a href="#hero">3D AETP</a></li>
        <li><a href="#features">Features</a></li>
      </ul>
    </nav>
  </header>

  <section class="hero" id="hero">
    <div class="hero-content">
      <h1><?php echo $content['hero_title']; ?></h1>
      <p><?php echo $content['hero_description']; ?></p>
      <a href="panoramic.html" class="btn">Explore Now</a>
    </div>
    <div id="threejs-container"></div>
  </section>

  <section id="about" class="section">
    <div class="container">
      <h2 class="section-title"><?php echo htmlspecialchars($content['about1_title']); ?></h2>
      <div class="about-content">
        <div class="about-text">
          <p class="about-description"><?php echo htmlspecialchars($content['about1_description']); ?></p>
        </div>
        <?php if ($content['about1_image']): ?>
          <div class="about-image">
            <img src="./admin/<?php echo htmlspecialchars($content['about1_image']); ?>" alt="About Image">
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="container">
      <h2 class="section-title"><?php echo htmlspecialchars($content['about2_title']); ?></h2>
      <p class="about-description"><?php echo htmlspecialchars($content['about2_description']); ?></p>
    </div>
  </section>


  <section id="features" class="section">
    <div class="container">
      <h2 class="section-title">Features</h2>
      <div class="features">
        <div class="feature-card">
          <div class="feature-icon">🌐</div>
          <h3 class="feature-title">Panoramic</h3>
          <p>This feature allows users to explore the CvSU Agri-Eco Tourism Park from every angle, providing a fully
            immersive experience. Users can virtually walk through the park, experiencing its natural beauty and unique
            features as if they were there in person. The 360° view enables users to gain a comprehensive understanding
            of the park's environment and its unique features.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">🗺️</div>
          <h3 class="feature-title">Map</h3>
          <p>The interactive 3D map is a crucial component that allows users to easily locate and explore different
            areas and facilities within the park. This feature offers a comprehensive overview of the park's layout,
            including its various agricultural areas, recreational spaces, and promotional facilities. By providing
            detailed visual and textual information, the 3D map ensures that users can find points of interest
            effortlessly and understand the park's layout better.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">ℹ️</div>
          <h3 class="feature-title">I-Display</h3>
          <p>The platform is designed to be not only a visual and navigational tool but also an educational resource. It
            includes detailed educational content on sustainable agriculture practices and environmental management.
            Users can access in-depth information about the park's agricultural techniques, ecological projects, and
            sustainability initiatives. This feature ensures that visitors gain valuable knowledge and a deeper
            appreciation for the park's promotional significance, supporting CvSU's mission to promote agricultural
            innovation and environmental education.</p>
        </div>
      </div>
    </div>
  </section>

  <div class="scroll-to-top">↑</div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
  <script>
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(30, window.innerWidth / window.innerHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({
      alpha: true
    });
    renderer.setClearColor(0x87CEEB, 0);
    renderer.shadowMap.enabled = true;
    const container = document.getElementById('threejs-container');
    renderer.setSize(container.offsetWidth, container.offsetHeight);
    container.appendChild(renderer.domElement);

    const ambientLight = new THREE.AmbientLight(0x404040, 1);
    scene.add(ambientLight);

    const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
    directionalLight.position.set(5, 5, 5);
    directionalLight.castShadow = true;
    scene.add(directionalLight);

    const geometry = new THREE.BoxGeometry(2, 2, 2);
    const material = new THREE.MeshStandardMaterial({
      color: 0x228B22
    });
    const cube = new THREE.Mesh(geometry, material);
    cube.castShadow = true;
    cube.receiveShadow = true;
    scene.add(cube);

    camera.position.z = 10;

    function animate() {
      requestAnimationFrame(animate);

      cube.rotation.x += 0.01;
      cube.rotation.y += 0.01;

      renderer.render(scene, camera);
    }
    animate();

    window.addEventListener('resize', () => {
      camera.aspect = container.offsetWidth / container.offsetHeight;
      camera.updateProjectionMatrix();
      renderer.setSize(container.offsetWidth, container.offsetHeight);
    });

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
          behavior: 'smooth'
        });
      });
    });

    const scrollToTopBtn = document.querySelector('.scroll-to-top');
    window.addEventListener('scroll', () => {
      if (window.pageYOffset > 300) {
        scrollToTopBtn.classList.add('show');
      } else {
        scrollToTopBtn.classList.remove('show');
      }
    });

    scrollToTopBtn.addEventListener('click', () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });

    const animateOnScroll = () => {
      const elements = document.querySelectorAll('.section-title, .feature-card');
      elements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        if (elementTop < windowHeight - 200) {
          element.style.animation = 'fadeInUp 1s ease forwards';
        }
      });
    };

    window.addEventListener('scroll', animateOnScroll);
    animateOnScroll();

    window.addEventListener('scroll', () => {
      const header = document.querySelector('header');
      const nav = document.querySelector('nav');
      const heroSection = document.querySelector('.hero');

      const heroBottom = heroSection.getBoundingClientRect().bottom;

      if (heroBottom <= 2) {
        header.classList.add('scroll-background');
        nav.classList.add('scroll-dark');
      } else {
        header.classList.remove('scroll-background');
        nav.classList.remove('scroll-dark');
      }
    });

    // Parallax effect on scroll
    window.addEventListener('scroll', function() {
      const scrollPosition = window.scrollY;
      const heroContent = document.querySelector('.hero-content');
      const threejscontainer = document.querySelector('#threejs-container');
      heroContent.style.transform = `translateY(${scrollPosition * 0.7}px)`;
      threejscontainer.style.transform = `translateY(${scrollPosition * 0.7}px)`;
    });
  </script>
</body>

</html>